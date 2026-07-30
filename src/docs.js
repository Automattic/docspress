import fs from "node:fs/promises";
import path from "node:path";
import fg from "fast-glob";
import { headingBlock, paragraphBlock, sourceLinkBlock } from "./gutenberg.js";
import { markdownToBlocks } from "./markdown.js";
import { hashPageState } from "./page-state.js";
import { prependSentinel } from "./sentinel.js";
import { escapeAttribute, escapeHtml, normalizeBoolean, slugify, titleFromSlug, toPosixPath } from "./utils.js";
import { readVersionsRegistry } from "./versions.js";

const INDEX_FILENAMES = new Set(["index", "readme"]);

export async function collectDesiredPages(options) {
  const versionsRegistry = options.versionsRegistry
    || (options.versionsFile ? await readVersionsRegistry(options) : null);
  if (versionsRegistry) {
    return collectVersionedPages({ ...options, versionsRegistry });
  }

  const context = createContext(options);
  const byRoute = options.manifestFile
    ? await collectManifestPages(context, options)
    : await collectFilePages(context);

  ensurePlaceholderHierarchy(byRoute, options.rootTitle);
  await applyRedirects(byRoute, options, context);
  ensurePlaceholderHierarchy(byRoute, options.rootTitle);
  const linkResolver = createLinkResolver(byRoute, context, options);
  convertMarkdownPages(byRoute, options, linkResolver);

  return Array.from(byRoute.values())
    .map((page) => finalizePage(page, options))
    .sort((a, b) => a.depth - b.depth || a.key.localeCompare(b.key));
}

async function collectVersionedPages(options) {
  if (options.manifestFile || options.redirectsFile) {
    throw new Error("manifest-file and redirects-file cannot be combined with versions-file; configure them on individual version entries.");
  }

  const context = createContext(options);
  const registry = options.versionsRegistry;
  const files = await fg(["**/*.md", "**/*.markdown"], {
    cwd: context.absoluteDocsDir,
    onlyFiles: true,
    dot: false,
    unique: true
  });
  const claims = new Map();
  const collectedByVersion = new Map();

  for (const version of registry.versions.filter(({ source }) => source.type === "manifest")) {
    const pages = await collectManifestPages(context, {
      ...options,
      manifestFile: version.source.path
    });
    claimManifestSources(pages, version, claims, context);
    collectedByVersion.set(version.id, pages);
  }

  for (const version of registry.versions.filter(({ source }) => source.type === "directory")) {
    const prefix = `${version.source.path}/`;
    const mappings = files
      .filter((file) => toPosixPath(file).startsWith(prefix))
      .map((file) => ({ file, logicalFile: toPosixPath(file).slice(prefix.length) }));
    claimFileMappings(mappings, version, claims);
    collectedByVersion.set(version.id, await collectFilePages(context, mappings));
  }

  for (const version of registry.versions.filter(({ source }) => source.type === "suffix")) {
    const mappings = files
      .map((file) => suffixFileMapping(file, version.source.suffix))
      .filter(Boolean);
    claimFileMappings(mappings, version, claims);
    collectedByVersion.set(version.id, await collectFilePages(context, mappings));
  }

  for (const version of registry.versions.filter(({ source }) => source.type === "root")) {
    const mappings = files
      .filter((file) => !claims.has(toPosixPath(file)))
      .map((file) => ({ file, logicalFile: toPosixPath(file) }));
    claimFileMappings(mappings, version, claims);
    collectedByVersion.set(version.id, await collectFilePages(context, mappings));
  }

  const versionPrefixes = new Set(registry.versions.map(({ id }) => id));
  const latestRoutes = collectedByVersion.get(registry.latest) || new Map();
  for (const route of latestRoutes.keys()) {
    const firstSegment = route.split("/")[0];
    if (route && versionPrefixes.has(firstSegment)) {
      throw new Error(`Latest-version logical route '${route}' conflicts with the reserved version prefix '${firstSegment}'.`);
    }
  }

  const rootSlug = slugify(options.rootSlug || "docs", "docs");
  const desired = [];
  const neutralRoot = placeholderPage([], options.rootTitle || "Docs");
  neutralRoot.sourcePath = "virtual:version-root";
  neutralRoot.versionContainer = true;
  neutralRoot.body = "<!-- wp:paragraph -->\n<p>This Page contains the versioned documentation trees managed by DocsPress.</p>\n<!-- /wp:paragraph -->";
  desired.push(finalizePage(neutralRoot, {
    ...options,
    routePrefixSegments: [rootSlug]
  }));

  for (const version of registry.versions) {
    const byRoute = collectedByVersion.get(version.id) || new Map();
    ensurePlaceholderHierarchy(byRoute, options.rootTitle);
    if (version.redirectsFile) {
      await applyRedirects(byRoute, {
        ...options,
        redirectsFile: version.redirectsFile,
        routePrefixSegments: [rootSlug, version.id]
      }, context);
      ensurePlaceholderHierarchy(byRoute, options.rootTitle);
    }

    for (const page of byRoute.values()) {
      page.docsVersion = version;
      page.logicalRoute = page.routeKey;
      page.stableIdentity = `${version.id}:${page.routeKey}`;
      page.sourceType = version.source.type;
      page.manifestFile = version.source.type === "manifest" ? version.source.path : "";
    }

    const linkResolver = createLinkResolver(byRoute, context, {
      ...options,
      routePrefixSegments: [rootSlug, version.id]
    });
    convertMarkdownPages(byRoute, options, linkResolver);

    for (const page of byRoute.values()) {
      desired.push(finalizePage(page, {
        ...options,
        routePrefixSegments: [rootSlug, version.id],
        legacyLatestPrefix: version.latest ? [rootSlug] : null
      }));
    }
  }

  return desired.sort((a, b) => a.depth - b.depth || a.key.localeCompare(b.key));
}

function createContext(options) {
  const cwd = options.cwd || process.cwd();
  const docsDir = options.docsDir || "docs";
  const absoluteDocsDir = path.resolve(cwd, docsDir);
  const docsDirForSource = toPosixPath(path.relative(cwd, absoluteDocsDir)) || docsDir;

  return {
    cwd,
    docsDir,
    absoluteDocsDir,
    docsDirForSource
  };
}

async function collectFilePages(context, suppliedMappings = null) {
  const mappings = suppliedMappings || (await fg(["**/*.md", "**/*.markdown"], {
    cwd: context.absoluteDocsDir,
    onlyFiles: true,
    dot: false,
    unique: true
  })).map((file) => ({ file, logicalFile: file }));

  const byRoute = new Map();

  for (const mapping of mappings.sort((a, b) => a.file.localeCompare(b.file))) {
    const file = toPosixPath(mapping.file);
    const logicalFile = toPosixPath(mapping.logicalFile);
    const absolutePath = path.join(context.absoluteDocsDir, file);
    const markdown = await fs.readFile(absolutePath, "utf8");
    const routeSegments = routeSegmentsForFile(logicalFile);
    const routeKey = routeSegments.join("/");

    if (byRoute.has(routeKey)) {
      throw new Error(`Multiple Markdown files map to the same docs page: ${byRoute.get(routeKey).sourcePath} and ${file}`);
    }

    const sourcePath = `${context.docsDirForSource}/${toPosixPath(file)}`;

    byRoute.set(routeKey, {
      kind: "file",
      routeKey,
      routeSegments,
      sourcePath,
      sourceMarkdown: markdown
    });
  }

  return byRoute;
}

function suffixFileMapping(file, suffix) {
  const normalized = toPosixPath(file);
  const parsed = path.posix.parse(normalized);
  if (!parsed.name.endsWith(suffix)) {
    return null;
  }

  const base = parsed.name.slice(0, -suffix.length);
  if (!base) {
    return null;
  }

  return {
    file: normalized,
    logicalFile: path.posix.join(parsed.dir, `${base}${parsed.ext}`)
  };
}

function claimFileMappings(mappings, version, claims) {
  for (const mapping of mappings) {
    const source = toPosixPath(mapping.file);
    const claimed = claims.get(source);
    if (claimed && claimed !== version.id) {
      throw new Error(`Markdown source ${source} is claimed by both ${claimed} and ${version.id}.`);
    }
    claims.set(source, version.id);
  }
}

function claimManifestSources(pages, version, claims, context) {
  for (const page of pages.values()) {
    if (!page.sourcePath || page.sourcePath.includes(":")) {
      continue;
    }
    const repositorySource = toPosixPath(page.sourcePath);
    const docsPrefix = `${context.docsDirForSource}/`;
    const source = repositorySource.startsWith(docsPrefix)
      ? repositorySource.slice(docsPrefix.length)
      : `@repo:${repositorySource}`;
    const claimed = claims.get(source);
    if (claimed && claimed !== version.id) {
      throw new Error(`Markdown source ${source} is claimed by both ${claimed} and ${version.id}.`);
    }
    claims.set(source, version.id);
  }
}

async function collectManifestPages(context, options) {
  const manifestFile = toPosixPath(options.manifestFile);
  const manifestPath = path.resolve(context.cwd, manifestFile);
  const manifestDir = path.dirname(manifestPath);
  const data = JSON.parse(await fs.readFile(manifestPath, "utf8"));
  const entries = Array.isArray(data) ? data : data.pages || data.items || [];

  if (!Array.isArray(entries)) {
    throw new Error(`Docspress manifest must be a JSON array or an object with a pages array: ${manifestFile}`);
  }

  const normalized = entries.map((entry, index) => normalizeManifestEntry(entry, index));
  const byId = new Map();
  const byRoute = new Map();

  for (const entry of normalized) {
    if (byId.has(entry.id)) {
      throw new Error(`Docspress manifest contains duplicate page id: ${entry.id}`);
    }
    byId.set(entry.id, entry);
  }

  for (const entry of normalized) {
    const routeSegments = manifestRouteSegments(entry, byId);
    const routeKey = routeSegments.join("/");

    if (byRoute.has(routeKey)) {
      throw new Error(`Multiple manifest entries map to the same docs page: ${byRoute.get(routeKey).manifestId} and ${entry.id}`);
    }

    if (entry.source) {
      const sourcePath = await resolveManifestSource(entry.source, manifestDir, context.cwd);
      const markdown = await fs.readFile(path.resolve(context.cwd, sourcePath), "utf8");
      byRoute.set(routeKey, {
        kind: "file",
        manifestId: entry.id,
        routeKey,
        routeSegments,
        sourcePath,
        sourceMarkdown: markdown,
        titleOverride: entry.title
      });
      continue;
    }

    byRoute.set(routeKey, {
      ...placeholderPage(routeSegments, entry.title || fallbackTitleForRoute(routeSegments, options.rootTitle)),
      kind: "manifest-placeholder",
      manifestId: entry.id,
      sourcePath: `manifest:${manifestFile}#${entry.id}`
    });
  }

  return byRoute;
}

function normalizeManifestEntry(entry, index) {
  if (!entry || typeof entry !== "object") {
    throw new Error(`Docspress manifest entry ${index + 1} must be an object.`);
  }

  const slug = entry.slug === "/" ? "" : slugify(entry.slug ?? entry.id ?? entry.key ?? "", "");
  const id = String(entry.id ?? entry.key ?? entry.slug ?? (slug === "" ? "root" : slug) ?? index).trim();
  const parent = entry.parent === undefined || entry.parent === null ? null : String(entry.parent);
  const source = entry.markdown_source || entry.markdownSource || entry.source || null;

  if (!id) {
    throw new Error(`Docspress manifest entry ${index + 1} is missing id or slug.`);
  }

  return {
    id,
    parent,
    slug,
    title: typeof entry.title === "string" ? entry.title.trim() : "",
    source: source ? String(source) : null
  };
}

function manifestRouteSegments(entry, byId, seen = new Set()) {
  if (seen.has(entry.id)) {
    throw new Error(`Docspress manifest contains a parent cycle at ${entry.id}`);
  }

  seen.add(entry.id);
  const parent = entry.parent ? byId.get(entry.parent) : null;
  if (entry.parent && !parent) {
    throw new Error(`Docspress manifest page ${entry.id} references missing parent ${entry.parent}`);
  }

  const parentSegments = parent ? manifestRouteSegments(parent, byId, seen) : [];
  return entry.slug ? [...parentSegments, entry.slug] : parentSegments;
}

async function resolveManifestSource(source, manifestDir, cwd) {
  const raw = String(source || "");
  if (!raw || raw.includes(":") || raw.includes("..") || path.isAbsolute(raw)) {
    throw new Error(`Invalid Docspress manifest source: ${raw || "(empty)"}`);
  }

  const absolutePath = path.resolve(manifestDir, raw);
  const relativePath = path.relative(cwd, absolutePath);
  if (!relativePath || relativePath.startsWith("..") || path.isAbsolute(relativePath)) {
    throw new Error(`The Docspress manifest source must stay inside the checked-out repository: ${raw}`);
  }

  const [realCwd, realSourcePath] = await Promise.all([
    fs.realpath(cwd),
    fs.realpath(absolutePath)
  ]);
  const realRelativePath = path.relative(realCwd, realSourcePath);
  if (!realRelativePath || realRelativePath.startsWith("..") || path.isAbsolute(realRelativePath)) {
    throw new Error(`The Docspress manifest source must stay inside the checked-out repository: ${raw}`);
  }

  return toPosixPath(relativePath);
}

function convertMarkdownPages(byRoute, options, linkResolver) {
  for (const page of byRoute.values()) {
    if (!page.sourceMarkdown) {
      continue;
    }

    const fallbackTitle = page.titleOverride || fallbackTitleForRoute(page.routeSegments, options.rootTitle);
    const converted = markdownToBlocks(page.sourceMarkdown, {
      fallbackTitle,
      createH1: options.createH1,
      resolveLink: (url) => linkResolver(url, page.sourcePath)
    });

    page.title = page.titleOverride || converted.title;
    page.body = converted.blocks;
    page.frontmatter = converted.data;
    Object.assign(page, normalizeSidebarFrontmatter(converted.data, page.sourcePath));
  }
}

function normalizeSidebarFrontmatter(frontmatter, sourcePath) {
  const normalized = {};

  if (Object.hasOwn(frontmatter, "sidebar_position")) {
    const position = frontmatter.sidebar_position;
    if (!Number.isSafeInteger(position)) {
      throw new Error(`Invalid sidebar_position in ${sourcePath}: expected a signed integer.`);
    }
    normalized.sidebarPosition = position;
  }

  if (Object.hasOwn(frontmatter, "sidebar_collapsed")) {
    const collapsed = frontmatter.sidebar_collapsed;
    if (typeof collapsed !== "boolean") {
      throw new Error(`Invalid sidebar_collapsed in ${sourcePath}: expected true or false.`);
    }
    normalized.sidebarCollapsed = collapsed;
  }

  return normalized;
}

function routeSegmentsForFile(file) {
  const parsed = path.posix.parse(toPosixPath(file));
  const dirSegments = parsed.dir ? parsed.dir.split("/").filter(Boolean).map((part) => slugify(part)) : [];
  const baseSlug = slugify(parsed.name);

  if (INDEX_FILENAMES.has(parsed.name.toLowerCase())) {
    return dirSegments;
  }

  return [...dirSegments, baseSlug];
}

function fallbackTitleForRoute(routeSegments, rootTitle) {
  if (routeSegments.length === 0) {
    return rootTitle || "Docs";
  }

  return titleFromSlug(routeSegments[routeSegments.length - 1]);
}

function ensurePlaceholderHierarchy(byRoute, rootTitle) {
  if (!byRoute.has("")) {
    byRoute.set("", placeholderPage([], rootTitle || "Docs"));
  }

  for (const routeKey of Array.from(byRoute.keys())) {
    const routeSegments = routeKey ? routeKey.split("/") : [];
    for (let index = 1; index < routeSegments.length; index += 1) {
      const ancestorSegments = routeSegments.slice(0, index);
      const ancestorKey = ancestorSegments.join("/");
      if (!byRoute.has(ancestorKey)) {
        byRoute.set(ancestorKey, placeholderPage(ancestorSegments, titleFromSlug(ancestorSegments.at(-1))));
      }
    }
  }
}

function placeholderPage(routeSegments, title) {
  return {
    kind: "placeholder",
    routeKey: routeSegments.join("/"),
    routeSegments,
    sourcePath: `virtual:${routeSegments.length === 0 ? "root" : routeSegments.join("/")}`,
    title,
    body: "<!-- wp:paragraph -->\n<p>This section is generated from the docs directory.</p>\n<!-- /wp:paragraph -->"
  };
}

async function applyRedirects(byRoute, options, context) {
  if (!options.redirectsFile) {
    return;
  }

  const redirects = await readRedirectsFile(options.redirectsFile, context.cwd);
  const rootSlug = slugify(options.rootSlug || "docs", "docs");

  for (const redirect of redirects) {
    const routeSegments = routeSegmentsForRedirect(redirect.from, rootSlug);
    const routeKey = routeSegments.join("/");
    if (!routeKey) {
      throw new Error("Docspress redirect entries cannot target the managed root page.");
    }
    if (byRoute.has(routeKey) && byRoute.get(routeKey).kind !== "redirect") {
      throw new Error(`Docspress redirect '${redirect.from}' conflicts with an existing docs page.`);
    }

    const targetUrl = redirectTargetUrl(redirect.to, rootSlug, options.routePrefixSegments);
    byRoute.set(routeKey, {
      kind: "redirect",
      routeKey,
      routeSegments,
      sourcePath: `redirects:${toPosixPath(options.redirectsFile)}#${routeKey}`,
      title: redirect.title || `Moved: ${titleFromSlug(routeSegments.at(-1))}`,
      body: paragraphBlock(`This page moved to <a href="${escapeAttribute(targetUrl)}">${escapeHtml(targetUrl)}</a>.`)
    });
  }
}

async function readRedirectsFile(redirectsFile, cwd) {
  const redirectsPath = path.resolve(cwd, redirectsFile);
  const data = JSON.parse(await fs.readFile(redirectsPath, "utf8"));
  const entries = Array.isArray(data) ? data : data.redirects || data;

  if (Array.isArray(entries)) {
    return entries.map((entry, index) => {
      if (!entry || typeof entry !== "object") {
        throw new Error(`Docspress redirect entry ${index + 1} must be an object.`);
      }
      return {
        from: String(entry.from || ""),
        to: String(entry.to || ""),
        title: typeof entry.title === "string" ? entry.title : ""
      };
    });
  }

  if (entries && typeof entries === "object") {
    return Object.entries(entries).map(([from, to]) => ({ from, to: String(to), title: "" }));
  }

  throw new Error(`Docspress redirects file must be an object, array, or object with redirects: ${redirectsFile}`);
}

function routeSegmentsForRedirect(value, rootSlug) {
  const normalized = normalizeRoutePath(value, rootSlug);
  return normalized ? normalized.split("/") : [];
}

function redirectTargetUrl(value, rootSlug, routePrefixSegments = null) {
  const raw = String(value || "").trim();
  if (/^[a-z][a-z0-9+.-]*:/i.test(raw) || raw.startsWith("//") || raw.startsWith("#")) {
    return raw;
  }

  const normalized = normalizeRoutePath(raw, rootSlug);
  const prefix = routePrefixSegments || [rootSlug];
  return `/${[...prefix, normalized].filter(Boolean).join("/")}/`;
}

function createLinkResolver(byRoute, context, options) {
  const rewriteLinks = options.rewriteLinks === undefined ? true : normalizeBoolean(options.rewriteLinks);
  const aliases = new Map();
  const routePrefixSegments = options.routePrefixSegments
    || [slugify(options.rootSlug || "docs", "docs")];
  const rootSlug = routePrefixSegments[0];

  for (const page of byRoute.values()) {
    const fullKey = [...routePrefixSegments, ...page.routeSegments].join("/");
    addAlias(aliases, page.routeKey, fullKey);
    addAlias(aliases, `${page.routeKey}/`, fullKey);
    addAlias(aliases, fullKey, fullKey);
    addAlias(aliases, `${fullKey}/`, fullKey);

    const sourceRelative = docsRelativeSource(page.sourcePath, context.docsDirForSource);
    if (sourceRelative) {
      addSourceAliases(aliases, sourceRelative, fullKey);
    }
  }

  return (url, sourcePath) => {
    if (!rewriteLinks) {
      return url;
    }
    return rewriteMarkdownLink(url, sourcePath, aliases, context, rootSlug);
  };
}

function addSourceAliases(aliases, sourceRelative, fullKey) {
  addAlias(aliases, sourceRelative, fullKey);
  const parsed = path.posix.parse(sourceRelative);
  const withoutExt = path.posix.join(parsed.dir, parsed.name);
  addAlias(aliases, withoutExt, fullKey);

  if (INDEX_FILENAMES.has(parsed.name.toLowerCase())) {
    addAlias(aliases, parsed.dir, fullKey);
    addAlias(aliases, `${parsed.dir}/`, fullKey);
  }
}

function addAlias(aliases, alias, fullKey) {
  const normalized = normalizeAlias(alias);
  if (normalized !== null) {
    aliases.set(normalized, fullKey);
  }
}

function rewriteMarkdownLink(url, sourcePath, aliases, context, rootSlug) {
  const raw = String(url || "");
  if (!raw || raw.startsWith("#") || raw.startsWith("//") || /^[a-z][a-z0-9+.-]*:/i.test(raw)) {
    return raw;
  }

  const match = raw.match(/^([^?#]*)(\?[^#]*)?(#.*)?$/);
  const linkPath = match?.[1] || "";
  const query = match?.[2] || "";
  const hash = match?.[3] || "";
  if (!linkPath) {
    return raw;
  }

  const candidate = linkCandidate(linkPath, sourcePath, context, rootSlug);
  const fullKey = aliases.get(normalizeAlias(candidate));
  if (!fullKey) {
    return raw;
  }

  return `/${fullKey}/${query}${hash}`;
}

function linkCandidate(linkPath, sourcePath, context, rootSlug) {
  const rawPath = String(linkPath || "");
  const stripped = rawPath.replace(/^\/+/, "");
  const sourceRelative = docsRelativeSource(sourcePath, context.docsDirForSource);

  if (rawPath.startsWith("/")) {
    return stripKnownPrefix(stripped, context.docsDirForSource, rootSlug);
  }

  const repoRelative = stripKnownPrefix(stripped, context.docsDirForSource, rootSlug);
  if (repoRelative !== stripped) {
    return repoRelative;
  }

  const sourceDir = sourceRelative ? path.posix.dirname(sourceRelative) : "";
  return path.posix.normalize(path.posix.join(sourceDir === "." ? "" : sourceDir, stripped));
}

function stripKnownPrefix(value, docsDir, rootSlug) {
  for (const prefix of [docsDir, rootSlug]) {
    if (value === prefix) {
      return "";
    }
    if (value.startsWith(`${prefix}/`)) {
      return value.slice(prefix.length + 1);
    }
  }
  return value;
}

function docsRelativeSource(sourcePath, docsDir) {
  if (!sourcePath || sourcePath.startsWith("virtual:") || sourcePath.startsWith("manifest:") || sourcePath.startsWith("redirects:")) {
    return "";
  }

  const normalized = normalizeAlias(sourcePath);
  const docsPrefix = normalizeAlias(docsDir);
  if (normalized === docsPrefix) {
    return "";
  }
  if (normalized?.startsWith(`${docsPrefix}/`)) {
    return normalized.slice(docsPrefix.length + 1);
  }
  return normalized;
}

function normalizeAlias(value) {
  if (value === null || value === undefined) {
    return null;
  }
  const normalized = path.posix.normalize(String(value).replace(/\\/g, "/").replace(/^\/+/, ""));
  return normalized === "." ? "" : normalized.replace(/\/+$/, "");
}

function normalizeRoutePath(value, rootSlug) {
  return stripKnownPrefix(normalizeAlias(value) || "", rootSlug, rootSlug);
}

function finalizePage(page, options) {
  const rootSlug = slugify(options.rootSlug || "docs", "docs");
  const routePrefixSegments = options.routePrefixSegments || [rootSlug];
  const fullSegments = [...routePrefixSegments, ...page.routeSegments];
  const key = fullSegments.join("/");
  const parentSegments = fullSegments.slice(0, -1);
  const parentKey = parentSegments.length > 0 ? parentSegments.join("/") : null;
  const slug = fullSegments.at(-1);
  const status = options.status || "publish";
  const createH1 = normalizeBoolean(options.createH1);
  let body = createH1 && page.kind === "placeholder"
    ? `${headingBlock(1, escapeHtml(page.title))}\n\n${page.body}`
    : page.body;
  const editUrl = editUrlForPage(page, options);
  if (editUrl) {
    body = `${body}\n\n${sourceLinkBlock(editUrl, options.editLinkText || "Edit this page on GitHub")}`;
  }
  const stablePayload = {
    key,
    sourcePath: page.sourcePath,
    title: page.title,
    slug,
    parentKey,
    status,
    body,
    docsVersion: page.docsVersion?.id || null,
    logicalRoute: page.logicalRoute ?? null,
    stableIdentity: page.stableIdentity || null,
    sourceType: page.sourceType || null,
    manifestFile: page.manifestFile || null,
    versionContainer: Boolean(page.versionContainer)
  };
  const hash = hashPageState(stablePayload);
  const sentinel = {
    key,
    source: page.sourcePath,
    hash
  };
  if (page.docsVersion?.id) {
    sentinel.docsVersion = page.docsVersion.id;
    sentinel.logicalRoute = page.logicalRoute || "";
    sentinel.identity = page.stableIdentity;
    sentinel.sourceType = page.sourceType;
    sentinel.latest = Boolean(page.docsVersion.latest);
    if (page.manifestFile) {
      sentinel.manifestFile = page.manifestFile;
    }
  }
  if (typeof page.sourceMarkdown === "string") {
    sentinel.sourceContentBase64 = Buffer.from(page.sourceMarkdown, "utf8").toString("base64");
  }
  if (Object.hasOwn(page, "sidebarPosition")) {
    sentinel.sidebarPosition = page.sidebarPosition;
  }
  if (Object.hasOwn(page, "sidebarCollapsed")) {
    sentinel.sidebarCollapsed = page.sidebarCollapsed;
  }
  const content = prependSentinel(body, sentinel);

  return {
    ...page,
    key,
    parentKey,
    slug,
    status,
    body,
    hash,
    content,
    depth: fullSegments.length,
    versionContainer: Boolean(page.versionContainer),
    legacyKeys: options.legacyLatestPrefix && page.routeSegments.length > 0
      ? [[...options.legacyLatestPrefix, ...page.routeSegments].join("/")]
      : []
  };
}

function editUrlForPage(page, options) {
  if (!normalizeBoolean(options.editLink) || !page.sourcePath || page.sourcePath.includes(":")) {
    return "";
  }

  const repository = options.githubRepository || process.env.GITHUB_REPOSITORY || "";
  if (!repository) {
    return "";
  }

  const serverUrl = (options.githubServerUrl || process.env.GITHUB_SERVER_URL || "https://github.com").replace(/\/+$/, "");
  const ref = options.githubRef || process.env.GITHUB_REF_NAME || "main";
  return `${serverUrl}/${repository}/edit/${ref}/${page.sourcePath.split("/").map(encodeURIComponent).join("/")}`;
}
