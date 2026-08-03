import fs from "node:fs/promises";
import os from "node:os";
import path from "node:path";
import { describe, expect, it } from "vitest";
import { applySidebarsRegistry, readSidebarsRegistry } from "../src/sidebars.js";

const repositoryRoot = process.cwd();

describe("sidebars registry", () => {
  it("reads the simple YAML route map", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-sidebars-"));
    await fs.writeFile(path.join(cwd, "sidebars.yml"), [
      "version: 1",
      "default: docs",
      "sidebars:",
      "  docs: .",
      "  extensions: extensions",
      "  api: apis"
    ].join("\n"));

    await expect(readSidebarsRegistry({ cwd, sidebarsFile: "sidebars.yml" })).resolves.toEqual({
      file: "sidebars.yml",
      default: "docs",
      entries: [
        { id: "docs", root: "", order: 0 },
        { id: "extensions", root: "extensions", order: 1 },
        { id: "api", root: "apis", order: 2 }
      ]
    });
  });

  it("supports the same contract in JSON", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-sidebars-"));
    await fs.writeFile(path.join(cwd, "sidebars.json"), JSON.stringify({
      version: 1,
      default: "docs",
      sidebars: {
        docs: ".",
        cli: "wc-cli"
      }
    }));

    const registry = await readSidebarsRegistry({ cwd, sidebarsFile: "sidebars.json" });
    expect(registry.entries.map(({ id, root }) => ({ id, root }))).toEqual([
      { id: "docs", root: "" },
      { id: "cli", root: "wc-cli" }
    ]);
  });

  it("assigns the most specific route root and marks each sidebar root", () => {
    const pages = new Map([
      ["", { routeKey: "" }],
      ["guides", { routeKey: "guides" }],
      ["apis", { routeKey: "apis" }],
      ["apis/rest", { routeKey: "apis/rest" }],
      ["apis/rest/v3", { routeKey: "apis/rest/v3" }]
    ]);
    const registry = {
      default: "docs",
      entries: [
        { id: "docs", root: "", order: 0 },
        { id: "api", root: "apis", order: 1 },
        { id: "rest", root: "apis/rest", order: 2 }
      ]
    };

    applySidebarsRegistry(pages, registry);

    expect(pages.get("")).toMatchObject({ sidebarId: "docs", sidebarRoot: true });
    expect(pages.get("guides")).toMatchObject({ sidebarId: "docs", sidebarRoot: false });
    expect(pages.get("apis")).toMatchObject({ sidebarId: "api", sidebarRoot: true });
    expect(pages.get("apis/rest")).toMatchObject({ sidebarId: "rest", sidebarRoot: true });
    expect(pages.get("apis/rest/v3")).toMatchObject({ sidebarId: "rest", sidebarRoot: false });
  });

  it.each([
    ["a configured default", "version: 1\nsidebars:\n  docs: .", /default must reference/],
    ["a root default", "version: 1\ndefault: docs\nsidebars:\n  docs: guides", /must use the root/],
    ["unique roots", "version: 1\ndefault: docs\nsidebars:\n  docs: .\n  other: .", /same root/],
    ["valid IDs", "version: 1\ndefault: Docs\nsidebars:\n  Docs: .", /Invalid Docspress sidebar id/],
    ["safe roots", "version: 1\ndefault: docs\nsidebars:\n  docs: .\n  secret: ../secret", /invalid root/]
  ])("requires %s", async (_label, source, pattern) => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-sidebars-"));
    await fs.writeFile(path.join(cwd, "sidebars.yml"), source);

    await expect(readSidebarsRegistry({ cwd, sidebarsFile: "sidebars.yml" })).rejects.toThrow(pattern);
  });

  it("requires an explicit YAML or JSON extension", async () => {
    const cwd = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-sidebars-"));
    await fs.writeFile(path.join(cwd, "sidebars.txt"), "default: docs\nsidebars:\n  docs: .\n");

    await expect(readSidebarsRegistry({ cwd, sidebarsFile: "sidebars.txt" })).rejects.toThrow(
      /must use \.json, \.yaml, or \.yml/
    );
  });

  it("rejects configured roots that do not exist in the logical route tree", () => {
    const pages = new Map([["", { routeKey: "" }]]);
    const registry = {
      default: "docs",
      entries: [
        { id: "docs", root: "", order: 0 },
        { id: "api", root: "apis", order: 1 }
      ]
    };

    expect(() => applySidebarsRegistry(pages, registry)).toThrow(/references a missing route: apis/);
  });

  it("rejects a sidebars file symlink that leaves the repository", async () => {
    const parent = await fs.mkdtemp(path.join(os.tmpdir(), "docspress-sidebars-parent-"));
    const cwd = path.join(parent, "checkout");
    await fs.mkdir(cwd);
    await fs.writeFile(path.join(parent, "outside.yml"), "default: docs\nsidebars:\n  docs: .\n");
    await fs.symlink(path.join(parent, "outside.yml"), path.join(cwd, "sidebars.yml"));

    await expect(readSidebarsRegistry({ cwd, sidebarsFile: "sidebars.yml" })).rejects.toThrow(
      /must stay inside the checked-out repository/
    );
  });

  it("ships a deterministic contextual-sidebar Playground and documentation link", async () => {
    const exampleRoot = path.join(repositoryRoot, "examples", "contextual-sidebars");
    const blueprint = JSON.parse(await fs.readFile(
      path.join(repositoryRoot, "theme", "blueprint-sidebars.json"),
      "utf8"
    ));
    const fixture = JSON.parse(await fs.readFile(
      path.join(repositoryRoot, "theme", "playground", "generated-sidebars.json"),
      "utf8"
    ));
    const setup = await fs.readFile(
      path.join(repositoryRoot, "theme", "playground", "setup-sidebars.php"),
      "utf8"
    );
    const guide = await fs.readFile(
      path.join(repositoryRoot, "docs", "guides", "contextual-sidebars.md"),
      "utf8"
    );
    const readme = await fs.readFile(path.join(repositoryRoot, "README.md"), "utf8");
    const registry = await readSidebarsRegistry({
      cwd: exampleRoot,
      sidebarsFile: "docs/sidebars.yml"
    });

    expect(blueprint.landingPage).toBe("/docs/apis/rest-api/");
    expect(blueprint.steps).toEqual(expect.arrayContaining([
      expect.objectContaining({ step: "installPlugin" }),
      expect.objectContaining({ step: "installTheme" }),
      expect.objectContaining({
        step: "runPHP",
        code: expect.stringContaining("/docspress/playground/setup-sidebars.php")
      })
    ]));
    expect(registry.entries.map(({ id, root }) => ({ id, root }))).toEqual([
      { id: "docs", root: "" },
      { id: "api", root: "apis" },
      { id: "extensions", root: "extensions" }
    ]);
    expect(fixture.generatedBy).toBe("scripts/build-playground-sidebars.mjs");
    expect(fixture.pages).toHaveLength(8);
    expect(fixture.pages.find(({ key }) => key === "docs/apis/rest-api"))
      .toMatchObject({ sidebarId: "api", sidebarRoot: false });
    expect(fixture.pages.find(({ key }) => key === "docs/extensions"))
      .toMatchObject({ sidebarId: "extensions", sidebarRoot: true });
    expect(setup).toContain("_docspress_sidebar_id");
    expect(setup).toContain("_docspress_sidebar_root");
    for (const content of [guide, readme]) {
      expect(content).toContain("blueprint-sidebars.json");
    }
  });
});
