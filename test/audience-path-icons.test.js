import fs from "node:fs/promises";
import path from "node:path";
import { describe, expect, it } from "vitest";

const root = process.cwd();
const blockRoot = path.join(
  root,
  "plugins",
  "docspress-blocks",
  "blocks",
  "audience-paths"
);

async function iconRegistrySource() {
  return fs.readFile(path.join(blockRoot, "icons.php"), "utf8");
}

function registryIds(source) {
  return [...source.matchAll(/^\t\t'([a-z-]+)'\s+=> array\(/gm)].map(
    (match) => match[1]
  );
}

function registryAliases(source) {
  return [...source.matchAll(/'aliases'\s+=> array\(([^)]*)\)/g)].flatMap(
    (match) => [...match[1].matchAll(/'([^']+)'/g)].map((alias) => alias[1])
  );
}

describe("Audience Paths icon system", () => {
  it("keeps the semantic registry broad and alias-safe", async () => {
    const source = await iconRegistrySource();
    const ids = registryIds(source);
    const aliases = registryAliases(source);
    const duplicateAliases = aliases.filter(
      (alias, index) => aliases.indexOf(alias) !== index
    );

    expect(ids).toHaveLength(33);
    expect(new Set(ids).size).toBe(ids.length);
    expect(duplicateAliases).toEqual([]);
    expect(ids).toEqual(
      expect.arrayContaining([
        "site",
        "code",
        "agency",
        "contribute",
        "products",
        "terminal",
        "testing",
        "troubleshoot",
        "document",
        "sparkles",
        "api",
        "repository",
        "security"
      ])
    );
  });

  it("maps every legacy icon token used by the Jetpack documentation pilot", async () => {
    const source = await iconRegistrySource();
    const knownValues = new Set([
      ...registryIds(source),
      ...registryAliases(source)
    ]);
    const legacyValues = [
      "?",
      "{ }",
      "→",
      "01",
      "02",
      "03",
      "04",
      "AG",
      "AI",
      "API",
      "AREA",
      "AUTO",
      "BETA",
      "BUG",
      "CI",
      "CLI",
      "CODE",
      "DBG",
      "DEV",
      "DOC",
      "ENV",
      "FAST",
      "FEAT",
      "FIX",
      "GO",
      "GROW",
      "HELP",
      "HOOK",
      "JP",
      "L10N",
      "MAP",
      "MD",
      "OWN",
      "PHP",
      "PICK",
      "PLAN",
      "PR",
      "QA",
      "REG",
      "REPO",
      "RULE",
      "RUN",
      "SEC",
      "SET",
      "SHIP",
      "SYNC",
      "TEST",
      "TOOL",
      "UI",
      "WIN",
      "WP",
      "WPC",
      "β"
    ];

    expect(legacyValues.filter((value) => !knownValues.has(value))).toEqual([]);
  });

  it("uses the same registry for frontend rendering and the editor picker", async () => {
    const [block, editor] = await Promise.all([
      fs.readFile(path.join(blockRoot, "block.php"), "utf8"),
      fs.readFile(path.join(blockRoot, "editor.js"), "utf8")
    ]);

    expect(block).toContain("docspress_blocks_audience_path_icon_editor_data()");
    expect(block).toContain("docspress_blocks_render_audience_path_icon");
    expect(editor).toContain("window.docspressAudiencePathIcons");
    expect(editor).toContain("label: __( 'Icon', 'docspress-blocks' )");
    expect(editor).not.toContain("Use a short symbol, emoji, or abbreviation.");
  });
});
