#!/usr/bin/env node
import fs from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { collectDesiredPages } from "../src/docs.js";
import { readSidebarsRegistry } from "../src/sidebars.js";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, "..");
const examplePath = "examples/contextual-sidebars";
const sidebarsFile = `${examplePath}/docs/sidebars.yml`;
const outputPath = path.join(rootDir, "theme", "playground", "generated-sidebars.json");
const sidebarsRegistry = await readSidebarsRegistry({
  cwd: rootDir,
  sidebarsFile
});
const pages = await collectDesiredPages({
  cwd: rootDir,
  docsDir: `${examplePath}/docs`,
  sidebarsFile,
  sidebarsRegistry,
  rootSlug: "docs",
  rootTitle: "Contextual sidebars",
  createH1: false,
  rewriteLinks: true,
  editLink: false,
  status: "publish"
});

const payload = {
  generatedBy: "scripts/build-playground-sidebars.mjs",
  rootSlug: "docs",
  sidebars: sidebarsRegistry.entries.map(({ id, root, order }) => ({ id, root, order })),
  github: {
    serverUrl: "https://github.com",
    repository: "Automattic/docspress",
    ref: "main"
  },
  pages: pages.map((page) => ({
    key: page.key,
    parentKey: page.parentKey,
    slug: page.slug,
    title: page.title,
    content: page.content,
    sourcePath: page.sourcePath,
    depth: page.depth,
    menuOrder: page.sidebarPosition ?? 0,
    sidebarId: page.sidebarId,
    sidebarRoot: Boolean(page.sidebarRoot)
  }))
};

await fs.writeFile(outputPath, `${JSON.stringify(payload, null, 2)}\n`);
console.log(`Generated ${payload.pages.length} contextual-sidebar Playground pages at ${path.relative(rootDir, outputPath)}.`);
