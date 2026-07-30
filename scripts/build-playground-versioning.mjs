#!/usr/bin/env node
import fs from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { collectDesiredPages } from "../src/docs.js";
import { readVersionsRegistry } from "../src/versions.js";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, "..");
const examplePath = "examples/versioning";
const outputPath = path.join(rootDir, "theme", "playground", "generated-versioning.json");
const versionsRegistry = await readVersionsRegistry({
  cwd: rootDir,
  versionsFile: `${examplePath}/versions.json`
});
const pages = await collectDesiredPages({
  cwd: rootDir,
  docsDir: `${examplePath}/docs`,
  versionsFile: `${examplePath}/versions.json`,
  versionsRegistry,
  rootSlug: "docs",
  rootTitle: "Versioned API",
  createH1: false,
  rewriteLinks: true,
  editLink: false,
  status: "publish"
});

const payload = {
  generatedBy: "scripts/build-playground-versioning.mjs",
  rootSlug: "docs",
  repositoryLatest: versionsRegistry.latest,
  effectiveLatest: versionsRegistry.latest,
  override: "",
  github: {
    serverUrl: "https://github.com",
    repository: "Automattic/docspress",
    ref: "main"
  },
  terms: versionsRegistry.versions.map((version) => ({
    id: version.id,
    label: version.label,
    order: version.order,
    active: true,
    repositoryLatest: version.latest,
    effectiveLatest: version.latest
  })),
  pages: pages.map((page) => ({
    key: page.key,
    parentKey: page.parentKey,
    slug: page.slug,
    title: page.title,
    content: page.content,
    sourcePath: page.sourcePath,
    depth: page.depth,
    menuOrder: page.sidebarPosition ?? 0,
    version: page.docsVersion?.id || "",
    logicalRoute: page.logicalRoute || "",
    stableIdentity: page.stableIdentity || "",
    sourceType: page.sourceType || "",
    docsRoot: "docs",
    versionContainer: Boolean(page.versionContainer)
  }))
};

await fs.writeFile(outputPath, `${JSON.stringify(payload, null, 2)}\n`);
console.log(`Generated ${payload.pages.length} versioned Playground pages at ${path.relative(rootDir, outputPath)}.`);
