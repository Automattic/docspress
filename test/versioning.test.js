import fs from "node:fs/promises";
import os from "node:os";
import path from "node:path";
import { describe, expect, it, vi } from "vitest";
import { collectDesiredPages } from "../src/docs.js";
import { GitHubPullRequestClient } from "../src/github.js";
import { createReverseChanges } from "../src/reverse.js";
import { readSentinel } from "../src/sentinel.js";
import { syncPages } from "../src/sync.js";
import { readVersionsRegistry } from "../src/versions.js";

const repositoryRoot = process.cwd();
const exampleRoot = path.join(repositoryRoot, "examples", "versioning");

async function collectExample() {
  const versionsRegistry = await readVersionsRegistry({
    cwd: exampleRoot,
    versionsFile: "versions.json"
  });
  const pages = await collectDesiredPages({
    cwd: exampleRoot,
    docsDir: "docs",
    versionsFile: "versions.json",
    versionsRegistry,
    rootSlug: "docs",
    rootTitle: "Versioned API",
    status: "publish",
    rewriteLinks: true
  });
  return { versionsRegistry, pages };
}

describe("DocsPress version registry and collector", () => {
  it("collects root, directory, and suffix sources into isolated stable trees", async () => {
    const { versionsRegistry, pages } = await collectExample();

    expect(versionsRegistry.versions.map(({ id }) => id)).toEqual(["v3", "v2", "v1"]);
    expect(pages).toHaveLength(13);
    expect(pages.find(({ key }) => key === "docs")).toMatchObject({
      versionContainer: true,
      sourcePath: "virtual:version-root"
    });
    expect(pages.find(({ key }) => key === "docs/v3/hello")).toMatchObject({
      sourcePath: "docs/hello.md",
      logicalRoute: "hello",
      stableIdentity: "v3:hello",
      sourceType: "root",
      legacyKeys: ["docs/hello"]
    });
    expect(pages.find(({ key }) => key === "docs/v2/authentication/api-keys")).toMatchObject({
      sourcePath: "docs/v2/authentication/api-keys.md",
      logicalRoute: "authentication/api-keys",
      sourceType: "directory"
    });
    expect(pages.find(({ key }) => key === "docs/v1/authentication/api-keys")).toMatchObject({
      sourcePath: "docs/authentication/api-keys.v1.md",
      logicalRoute: "authentication/api-keys",
      sourceType: "suffix"
    });
    expect(pages.some(({ key }) => key === "docs/v3/v2/hello")).toBe(false);
    expect(readSentinel(pages.find(({ key }) => key === "docs/v1/hello").content)).toMatchObject({
      docsVersion: "v1",
      source: "docs/hello.v1.md",
      identity: "v1:hello",
      sourceType: "suffix",
      latest: false
    });
  });

  it("rewrites links only inside the source version", async () => {
    const { pages } = await collectExample();
    expect(pages.find(({ key }) => key === "docs/v1/hello").content)
      .toContain('href="/docs/v1/authentication/api-keys/"');
    expect(pages.find(({ key }) => key === "docs/v2/hello").content)
      .toContain('href="/docs/v2/authentication/api-keys/"');
    expect(pages.find(({ key }) => key === "docs/v3/hello").content)
      .toContain('href="/docs/v3/authentication/oauth/"');
  });

  it("supports a manifest and redirects owned by one historical version", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-versions-"));
    await fs.mkdir(path.join(cwd, "docs"), { recursive: true });
    await fs.writeFile(path.join(cwd, "docs", "hello.md"), "# Current");
    await fs.writeFile(path.join(cwd, "legacy.md"), "# Legacy source");
    await fs.writeFile(path.join(cwd, "v1-manifest.json"), JSON.stringify({
      pages: [
        { id: "root", slug: "", title: "v1" },
        { id: "hello", parent: "root", slug: "hello", markdown_source: "legacy.md" }
      ]
    }));
    await fs.writeFile(path.join(cwd, "v1-redirects.json"), JSON.stringify({
      old: "hello"
    }));
    await fs.writeFile(path.join(cwd, "versions.json"), JSON.stringify({
      latest: "v2",
      versions: [
        { id: "v2", source: { type: "root" } },
        {
          id: "v1",
          source: { type: "manifest", path: "v1-manifest.json" },
          redirects: "v1-redirects.json"
        }
      ]
    }));

    const versionsRegistry = await readVersionsRegistry({ cwd, versionsFile: "versions.json" });
    const pages = await collectDesiredPages({
      cwd,
      docsDir: "docs",
      versionsRegistry,
      rootSlug: "docs",
      status: "publish"
    });

    expect(pages.find(({ key }) => key === "docs/v1/hello")).toMatchObject({
      sourcePath: "legacy.md",
      manifestFile: "v1-manifest.json"
    });
    expect(pages.find(({ key }) => key === "docs/v1/old").body)
      .toContain('href="/docs/v1/hello/"');
  });

  it.each([
    ["unsafe versions path", "../versions.json", /must stay inside/],
    ["missing latest", { versions: [{ id: "v1", source: { type: "root" } }] }, /latest must reference/],
    ["invalid id", { latest: "Version 1", versions: [{ id: "Version 1", source: { type: "root" } }] }, /invalid id/],
    ["unsafe directory", { latest: "v1", versions: [{ id: "v1", source: { type: "directory", path: "../v1" } }] }, /Invalid repository-relative/]
  ])("rejects %s", async (_label, input, pattern) => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-registry-"));
    if (typeof input === "string") {
      await expect(readVersionsRegistry({ cwd, versionsFile: input })).rejects.toThrow(pattern);
      return;
    }
    await fs.writeFile(path.join(cwd, "versions.json"), JSON.stringify(input));
    await expect(readVersionsRegistry({ cwd, versionsFile: "versions.json" })).rejects.toThrow(pattern);
  });

  it("rejects overlapping physical ownership and logical-route collisions", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-collision-"));
    await fs.mkdir(path.join(cwd, "docs", "v1"), { recursive: true });
    await fs.writeFile(path.join(cwd, "docs", "v1", "hello.v1.md"), "# Hello");
    await fs.writeFile(path.join(cwd, "versions.json"), JSON.stringify({
      latest: "v2",
      versions: [
        { id: "v2", source: { type: "root" } },
        { id: "v1-dir", source: { type: "directory", path: "v1" } },
        { id: "v1", source: { type: "suffix", suffix: ".v1" } }
      ]
    }));
    const versionsRegistry = await readVersionsRegistry({ cwd, versionsFile: "versions.json" });
    await expect(collectDesiredPages({ cwd, docsDir: "docs", versionsRegistry }))
      .rejects.toThrow(/claimed by both/);
  });

  it("reserves configured version IDs from the clean latest route", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-prefix-"));
    await fs.mkdir(path.join(cwd, "docs", "v1"), { recursive: true });
    await fs.writeFile(path.join(cwd, "docs", "v1", "index.md"), "# Conflicting latest route");
    await fs.writeFile(path.join(cwd, "versions.json"), JSON.stringify({
      latest: "v2",
      versions: [
        { id: "v2", source: { type: "root" } },
        { id: "v1", source: { type: "suffix", suffix: ".v1" } }
      ]
    }));
    const versionsRegistry = await readVersionsRegistry({ cwd, versionsFile: "versions.json" });
    await expect(collectDesiredPages({ cwd, docsDir: "docs", versionsRegistry }))
      .rejects.toThrow(/reserved version prefix 'v1'/);
  });
});

describe("version synchronization and reverse proposals", () => {
  it("synchronizes terms before assigning version taxonomy and managed metadata", async () => {
    const { versionsRegistry, pages } = await collectExample();
    const calls = [];
    let pageId = 100;
    const client = {
      listPages: async () => [],
      listTerms: async () => [],
      getSettings: async () => ({
        docspress_repository_latest_version: "",
        docspress_version_override: "v2",
        docspress_docs_root_slug: "docs"
      }),
      createTerm: async (_taxonomy, payload) => {
        calls.push(["term", payload]);
        return { id: payload.slug === "v3" ? 31 : payload.slug === "v2" ? 32 : 33, ...payload };
      },
      updateTerm: vi.fn(),
      updateSettings: async (payload) => calls.push(["settings", payload]),
      createPage: async (payload) => {
        calls.push(["page", payload]);
        pageId += 1;
        return { id: pageId, ...payload };
      },
      updatePage: vi.fn(),
      deletePage: vi.fn()
    };

    const result = await syncPages({
      desiredPages: pages,
      client,
      versionsRegistry,
      rootSlug: "docs",
      githubRepository: "Automattic/docspress",
      githubRef: "feature/versioning",
      githubServerUrl: "https://github.com",
      logger: { info() {} }
    });

    expect(result.effectiveLatest).toBe("v2");
    expect(calls.findIndex(([type]) => type === "term"))
      .toBeLessThan(calls.findIndex(([type]) => type === "page"));
    const v1Page = calls.find(([type, payload]) =>
      type === "page" && payload.meta?._docspress_page_identity === "v1:hello"
    )[1];
    expect(v1Page).toMatchObject({
      docspress_versions: [33],
      meta: {
        _docspress_version_id: "v1",
        _docspress_logical_route: "hello",
        _docspress_source_type: "suffix",
        _docspress_source_path: "docs/hello.v1.md",
        _docspress_github_path: "docs/hello.v1.md",
        _docspress_github_repository: "Automattic/docspress",
        _docspress_github_ref: "feature/versioning",
        _docspress_github_server_url: "https://github.com",
        _docspress_docs_root: "docs"
      }
    });
    expect(result.operations.find(({ key }) => key === "docs/v1/hello"))
      .toMatchObject({ version: "v1", sourcePath: "docs/hello.v1.md", logicalRoute: "hello" });
  });

  it("deactivates removed terms and repairs exactly one effective latest flag", async () => {
    const versionsRegistry = {
      latest: "v3",
      versions: [{
        id: "v3",
        label: "v3",
        order: 0,
        latest: true,
        source: { type: "root" }
      }]
    };
    const client = {
      listPages: async () => [],
      listTerms: async () => [
        {
          id: 31,
          slug: "v3",
          name: "v3",
          meta: {
            docspress_version_order: 0,
            docspress_version_active: "1",
            docspress_version_repository_latest: "1",
            docspress_version_effective_latest: "0"
          }
        },
        {
          id: 32,
          slug: "v2",
          name: "v2",
          meta: {
            docspress_version_active: "1",
            docspress_version_repository_latest: "0",
            docspress_version_effective_latest: "1"
          }
        }
      ],
      getSettings: async () => ({
        docspress_repository_latest_version: "v2",
        docspress_version_override: "removed",
        docspress_docs_root_slug: "docs"
      })
    };
    const result = await syncPages({
      desiredPages: [],
      client,
      versionsRegistry,
      dryRun: true,
      rootSlug: "docs",
      logger: { info() {} }
    });

    expect(result.effectiveLatest).toBe("v3");
    expect(result.versionOperations).toEqual(expect.arrayContaining([
      { action: "update-term", version: "v3" },
      { action: "deactivate-term", version: "v2" },
      { action: "update-settings", latest: "v3", rootSlug: "docs" }
    ]));
  });

  it("adopts a legacy unversioned managed Page into the latest tree without duplication", async () => {
    const { versionsRegistry, pages } = await collectExample();
    const desiredPages = [
      pages.find(({ key }) => key === "docs"),
      pages.find(({ key }) => key === "docs/v3"),
      pages.find(({ key }) => key === "docs/v3/hello")
    ];
    const latest = desiredPages[2];
    const legacySentinel = { ...readSentinel(latest.content), key: "docs/hello" };
    const legacyContent = latest.content.replace(
      /<!--\s*docspress:.*?\s*-->/s,
      `<!-- docspress:${JSON.stringify(legacySentinel)} -->`
    );
    const calls = [];
    let nextId = 100;
    const client = {
      listPages: async () => [{
        id: 9,
        slug: "hello",
        parent: 0,
        menuOrder: 0,
        content: legacyContent,
        title: latest.title,
        status: "publish",
        meta: {},
        terms: { docspress_versions: [] }
      }],
      listTerms: async () => versionsRegistry.versions.map((version, index) => ({
        id: 31 + index,
        slug: version.id,
        name: version.label,
        meta: {
          docspress_version_order: index,
          docspress_version_active: true,
          docspress_version_repository_latest: version.latest,
          docspress_version_effective_latest: version.latest
        }
      })),
      getSettings: async () => ({
        docspress_repository_latest_version: "v3",
        docspress_version_override: "",
        docspress_docs_root_slug: "docs"
      }),
      createPage: async (payload) => {
        nextId += 1;
        calls.push(["create", nextId, payload]);
        return { id: nextId, ...payload };
      },
      updatePage: async (id, payload) => {
        calls.push(["update", id, payload]);
        return { id, ...payload };
      },
      deletePage: vi.fn(),
      createTerm: vi.fn(),
      updateTerm: vi.fn(),
      updateSettings: vi.fn()
    };
    const result = await syncPages({
      desiredPages,
      client,
      versionsRegistry,
      rootSlug: "docs",
      logger: { info() {} }
    });

    expect(result.created).toBe(2);
    expect(result.updated).toBe(1);
    expect(result.deleted).toBe(0);
    expect(calls.filter(([action]) => action === "update")).toEqual([
      ["update", 9, expect.objectContaining({
        slug: "hello",
        parent: 102,
        docspress_versions: [31],
        meta: expect.objectContaining({
          _docspress_page_identity: "v3:hello",
          _docspress_source_path: "docs/hello.md"
        })
      })]
    ]);
  });

  it("writes a WordPress edit back to the exact suffix source with version-aware links", async () => {
    const { pages } = await collectExample();
    const desired = pages.find(({ key }) => key === "docs/v1/hello");
    const page = {
      id: 12,
      title: desired.title,
      content: desired.content.replace(
        /<p>You are reading[\s\S]*?<\/p>/,
        '<p>Edited in WordPress. Read <a href="/docs/v1/authentication/api-keys/">API-key authentication</a>.</p>'
      ),
      sentinel: readSentinel(desired.content),
      link: "https://example.test/docs/v1/hello/"
    };
    const changes = await createReverseChanges({
      cwd: exampleRoot,
      pages: [{ page, desired }],
      desiredPages: pages,
      effectiveLatest: "v3"
    });

    expect(changes).toEqual([
      expect.objectContaining({
        path: "docs/hello.v1.md",
        versionId: "v1",
        effectiveLatest: "v3",
        sourceType: "suffix"
      })
    ]);
    expect(changes[0].content).toContain("[API-key authentication](authentication/api-keys.v1.md)");
  });

  it("groups versioned PR paths and preserves an explicit title override", async () => {
    const createCalls = [];
    const octokit = {
      rest: {
        repos: { get: vi.fn(async () => ({ data: { default_branch: "main" } })) },
        git: {
          getRef: vi.fn(async ({ ref }) => {
            if (ref === "heads/docspress/wordpress-sync") {
              throw Object.assign(new Error("missing"), { status: 404 });
            }
            return { data: { object: { sha: "base" } } };
          }),
          getCommit: vi.fn(async () => ({ data: { tree: { sha: "tree" } } })),
          createBlob: vi.fn(async () => ({ data: { sha: "blob" } })),
          createTree: vi.fn(async () => ({ data: { sha: "next-tree" } })),
          createCommit: vi.fn(async (input) => {
            createCalls.push(input);
            return { data: { sha: "commit" } };
          }),
          createRef: vi.fn(async () => ({}))
        },
        pulls: {
          list: vi.fn(async () => ({ data: [] })),
          create: vi.fn(async (input) => {
            createCalls.push(input);
            return { data: { number: 8, html_url: "https://example.test/pr/8" } };
          })
        }
      }
    };
    const client = new GitHubPullRequestClient({
      repository: "o/r",
      token: "token",
      title: "docs(api): editorial sync",
      octokit
    });
    await client.syncChanges([
      { path: "docs/hello.v1.md", content: "v1", versionId: "v1", versionLabel: "Version 1", effectiveLatest: "v3" },
      { path: "docs/v2/hello.md", content: "v2", versionId: "v2", versionLabel: "Version 2", effectiveLatest: "v3" }
    ]);

    expect(createCalls[0].message).toBe("docs(api): editorial sync");
    expect(createCalls[1]).toMatchObject({ title: "docs(api): editorial sync" });
    expect(createCalls[1].body).toMatch(/Effective latest version: `v3`[\s\S]*### Version 1 \(`v1`\)[\s\S]*`docs\/hello\.v1\.md`[\s\S]*### Version 2 \(`v2`\)/);
  });
});

describe("WordPress version administration and routing contracts", () => {
  it("ships filtered Page management, stable routing, sitemaps, and customizable blocks", async () => {
    const php = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "includes", "versioning.php"),
      "utf8"
    );
    const switcher = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "blocks", "version-switcher", "block.php"),
      "utf8"
    );
    const switcherEditor = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "blocks", "version-switcher", "editor.js"),
      "utf8"
    );
    const switcherStyles = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "blocks", "version-switcher", "style.css"),
      "utf8"
    );
    const notice = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "blocks", "version-notice", "block.php"),
      "utf8"
    );
    const noticeEditor = await fs.readFile(
      path.join(repositoryRoot, "plugins", "docspress-blocks", "blocks", "version-notice", "editor.js"),
      "utf8"
    );
    const template = await fs.readFile(
      path.join(repositoryRoot, "theme", "templates", "page.html"),
      "utf8"
    );
    const header = await fs.readFile(
      path.join(repositoryRoot, "theme", "parts", "header.html"),
      "utf8"
    );
    const themeStyles = await fs.readFile(
      path.join(repositoryRoot, "theme", "style.css"),
      "utf8"
    );

    for (const contract of [
      "manage_pages_columns",
      "manage_edit-page_sortable_columns",
      "restrict_manage_posts",
      "pre_get_posts",
      "Latest (",
      "Inactive",
      "All versions",
      "docspress_versions_repository",
      "page_link",
      "docspress_blocks_versions_content_links",
      "add_option_docspress_repository_latest_version",
      "rest_after_insert_",
      "redirect_canonical",
      "wp_sitemaps_posts_query_args",
      "docspress_version_markdown",
      "docspress_blocks_versions_source_markdown",
      "GitHub path",
      "_docspress_github_path",
      "docspress_github_source_url",
      "target=\"_blank\""
    ]) {
      expect(php).toContain(contract);
    }
    expect(switcher).toContain("'api_version'     => 3");
    expect(switcher).toContain("docspress_version_switcher_choices");
    expect(switcher).toContain("unavailableLabel");
    expect(switcher).toContain("docspress-version-switcher__chevron");
    expect(switcherEditor).toContain("docspress-version-switcher__chevron");
    expect(switcherStyles).toContain(".docspress-version-switcher__chevron");
    expect(switcher).not.toContain("⌄");
    expect(switcherEditor).not.toContain("⌄");
    expect(notice).toContain("docspress_version_notice_message");
    expect(notice).toContain("'{current}'");
    expect(notice).toContain("dismissible");
    for (const control of [
      "Message",
      "Latest link label",
      "Show icon",
      "Allow visitors to dismiss"
    ]) {
      expect(noticeEditor).toContain(control);
    }
    expect(header.indexOf("wp:docspress/version-switcher"))
      .toBeLessThan(header.indexOf("wp:docspress/command-search"));
    expect(header).toContain('"presentation":"select"');
    expect(header).toContain('"showLabel":false');
    expect(header).toContain('"className":"header-version-switcher"');
    expect(template).not.toContain("wp:docspress/version-switcher");
    expect(template.indexOf("wp:docspress/version-notice"))
      .toBeLessThan(template.indexOf('"className":"docs-shell"'));
    expect(template.indexOf("wp:docspress/version-notice"))
      .toBeGreaterThan(template.indexOf('"slug":"header"'));
    expect(template).toContain('"align":"full","className":"header-version-notice"');
    expect(notice).toContain("array( 'wide', 'full' )");
    expect(themeStyles).toContain(
      ".wp-site-blocks > .alignfull.header-version-notice"
    );
    expect(template).toContain('"showVersions":false');
  });

  it("ships a deterministic historical-version Playground", async () => {
    const blueprint = JSON.parse(await fs.readFile(
      path.join(repositoryRoot, "theme", "blueprint-versioning.json"),
      "utf8"
    ));
    const fixture = JSON.parse(await fs.readFile(
      path.join(repositoryRoot, "theme", "playground", "generated-versioning.json"),
      "utf8"
    ));
    const setup = await fs.readFile(
      path.join(repositoryRoot, "theme", "playground", "setup-versioning.php"),
      "utf8"
    );

    expect(blueprint.landingPage).toBe("/docs/v1/hello/");
    expect(blueprint.steps.every((step) =>
      !step.pluginData?.ref || step.pluginData.ref === "main"
    )).toBe(true);
    expect(fixture.repositoryLatest).toBe("v3");
    expect(fixture.override).toBe("");
    expect(fixture.github).toEqual({
      serverUrl: "https://github.com",
      repository: "Automattic/docspress",
      ref: "main"
    });
    expect(fixture.terms.map(({ id }) => id)).toEqual(["v3", "v2", "v1"]);
    expect(fixture.pages.find(({ key }) => key === "docs/v1/hello"))
      .toMatchObject({
        sourcePath: "examples/versioning/docs/hello.v1.md",
        logicalRoute: "hello"
      });
    expect(setup).toContain("_docspress_github_repository");
    expect(setup).toContain("docspress_blocks_versions_rewrite_rules()");
  });
});
