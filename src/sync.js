import { readSentinel } from "./sentinel.js";

export async function syncPages(options) {
  const {
    desiredPages,
    client,
    dryRun = false,
    deleteMode = "trash",
    rootSlug = "docs",
    versioning = false,
    versionTaxonomy = "docspress_version",
    logger = console
  } = options;

  const existingPages = await client.listPages();
  const indexed = indexExistingPages(existingPages);
  const desiredKeys = new Set(desiredPages.map((page) => page.key));
  const result = createResult(dryRun);
  const idByKey = new Map();
  const versionTermIds = versioning && !dryRun
    ? await ensureVersionTerms({ desiredPages, client, versionTaxonomy })
    : new Map();
  let syntheticId = -1;

  for (const [key, page] of indexed.managedByKey.entries()) {
    idByKey.set(key, page.id);
  }

  for (const desired of desiredPages) {
    const existingAtPath = indexed.byPath.get(desired.key);
    const managed = indexed.managedByKey.get(desired.key);

    if (desired.parentKey && !idByKey.has(desired.parentKey)) {
      addConflict(result, desired.key, `Parent page is unavailable: ${desired.parentKey}`);
      continue;
    }

    if (existingAtPath && !managed) {
      addConflict(result, desired.key, "An unmanaged WordPress page already uses this path.");
      continue;
    }

    const parentId = desired.parentKey ? idByKey.get(desired.parentKey) : 0;
    const versionTermId = versionTermIdForPage(desired, versionTermIds);
    const payload = pagePayload(desired, parentId, {
      versioning,
      versionTaxonomy,
      versionTermId
    });

    if (managed) {
      const taxonomyMatches = !versioning || dryRun || termsMatch(
        managed.terms?.[versionTaxonomy],
        versionTermId ? [versionTermId] : []
      );

      if (managed.sentinel?.hash === desired.hash && managed.parent === parentId && taxonomyMatches) {
        result.unchanged += 1;
        result.operations.push({ action: "unchanged", key: desired.key, id: managed.id });
        continue;
      }

      result.updated += 1;
      result.operations.push({ action: "update", key: desired.key, id: managed.id });
      logger.info?.(`${dryRun ? "Would update" : "Updating"} ${desired.key}`);
      if (!dryRun) {
        const updated = await client.updatePage(managed.id, payload);
        idByKey.set(desired.key, updated.id);
      } else {
        idByKey.set(desired.key, managed.id);
      }
      continue;
    }

    result.created += 1;
    result.operations.push({ action: "create", key: desired.key });
    logger.info?.(`${dryRun ? "Would create" : "Creating"} ${desired.key}`);
    if (!dryRun) {
      const created = await client.createPage(payload);
      idByKey.set(desired.key, created.id);
    } else {
      idByKey.set(desired.key, syntheticId);
      syntheticId -= 1;
    }
  }

  const deletions = Array.from(indexed.managedByKey.values())
    .filter((page) => isUnderRoot(page.sentinel?.key, rootSlug) && !desiredKeys.has(page.sentinel.key))
    .sort((a, b) => b.path.split("/").length - a.path.split("/").length);

  for (const page of deletions) {
    result.deleted += 1;
    result.operations.push({ action: "delete", key: page.sentinel.key, id: page.id });
    logger.info?.(`${dryRun ? "Would delete" : "Deleting"} ${page.sentinel.key}`);
    if (!dryRun) {
      await client.deletePage(page.id, { force: deleteMode === "force" });
    }
  }

  return result;
}

async function ensureVersionTerms({ desiredPages, client, versionTaxonomy }) {
  const versionsBySlug = new Map();

  for (const page of desiredPages) {
    if (page.docsVersion?.slug && !versionsBySlug.has(page.docsVersion.slug)) {
      versionsBySlug.set(page.docsVersion.slug, page.docsVersion);
    }
  }

  const termIds = new Map();
  for (const version of versionsBySlug.values()) {
    const term = await client.ensureTerm(versionTaxonomy, {
      name: version.name || version.slug,
      slug: version.slug
    });
    termIds.set(version.slug, term.id);
  }

  return termIds;
}

function versionTermIdForPage(page, versionTermIds) {
  return page.docsVersion?.slug ? versionTermIds.get(page.docsVersion.slug) : null;
}

function pagePayload(page, parentId, options = {}) {
  const payload = {
    title: page.title,
    content: page.content,
    slug: page.slug,
    status: page.status,
    parent: parentId || 0
  };

  if (options.versioning) {
    payload[options.versionTaxonomy] = options.versionTermId ? [options.versionTermId] : [];
  }

  return payload;
}

function createResult(dryRun) {
  return {
    dryRun,
    created: 0,
    updated: 0,
    deleted: 0,
    unchanged: 0,
    conflicts: 0,
    conflictDetails: [],
    operations: []
  };
}

function addConflict(result, key, reason) {
  result.conflicts += 1;
  result.conflictDetails.push({ key, reason });
  result.operations.push({ action: "conflict", key, reason });
}

function isUnderRoot(key, rootSlug) {
  return key === rootSlug || key?.startsWith(`${rootSlug}/`);
}

function termsMatch(actual, expected) {
  const actualIds = normalizeTermIds(actual);
  const expectedIds = normalizeTermIds(expected);

  if (actualIds.length !== expectedIds.length) {
    return false;
  }

  return actualIds.every((id, index) => id === expectedIds[index]);
}

function normalizeTermIds(value) {
  if (!Array.isArray(value)) {
    return [];
  }

  return value
    .map((id) => Number(id))
    .filter((id) => Number.isInteger(id) && id > 0)
    .sort((a, b) => a - b);
}

export function indexExistingPages(pages) {
  const byId = new Map();
  const byPath = new Map();
  const managedByKey = new Map();

  for (const page of pages) {
    byId.set(page.id, {
      ...page,
      sentinel: readSentinel(page.content)
    });
  }

  for (const page of byId.values()) {
    page.path = pathForPage(page, byId);
    byPath.set(page.path, page);

    if (page.sentinel?.key) {
      managedByKey.set(page.sentinel.key, page);
    }
  }

  return {
    byId,
    byPath,
    managedByKey
  };
}

function pathForPage(page, byId, seen = new Set()) {
  if (!page.parent || seen.has(page.id) || !byId.has(page.parent)) {
    return page.slug;
  }

  seen.add(page.id);
  const parent = byId.get(page.parent);
  return `${pathForPage(parent, byId, seen)}/${page.slug}`;
}
