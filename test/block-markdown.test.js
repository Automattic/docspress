import { parse } from "@wordpress/block-serialization-default-parser";
import { describe, expect, it } from "vitest";
import { CUSTOM_BLOCK_DEFAULTS } from "../src/block-markdown.js";
import { markdownToBlocks } from "../src/markdown.js";
import { blocksToMarkdown, mergeWordPressIntoSource, upgradeLegacyBlockSyntax } from "../src/reverse.js";
import { prependSentinel } from "../src/sentinel.js";

const customPreviewCases = [
  ["docspress/api-request", { method: "POST", endpoint: "/v1/docs", responseStatus: "201 Created" }, "#### POST /v1/docs"],
  ["docspress/audience-paths", { eyebrow: "Start", title: "Choose", description: "Pick a path.", paths: [{ title: "Existing docs", description: "Use Markdown.", url: "/docs/", cta: "Open docs" }] }, "## Choose"],
  ["docspress/callout", { tone: "warning", title: "Careful", content: "<p>Keep <strong>Markdown</strong> readable.</p>" }, "> [!WARNING]"],
  ["docspress/code-tabs", { tabs: [{ label: "JavaScript", language: "js", filename: "index.js", code: "const docs = true;" }] }, "#### JavaScript — index.js"],
  ["docspress/code-playground", { title: "Demo", html: "<button>Run</button>", css: "button { color: blue; }", javascript: "run();" }, "**HTML**"],
  ["docspress/colorful-code", { language: "js", filename: "index.js", code: "const docs = true;", caption: "Example" }, "```js"],
  ["docspress/diagram", { title: "Flow", type: "flow", source: "A -> B", caption: "Pipeline" }, "```mermaid"],
  ["docspress/fields", { title: "Options", description: "Configuration.", fields: [{ name: "site", type: "string", required: true, defaultValue: "", description: "Site ID." }] }, "| Field | Type | Required | Default | Description |"],
  ["docspress/file-tree", { root: "docs/", tree: "index.md\napi.md", caption: "Documentation tree." }, "#### docs/"],
  ["docspress/flow", { start: 3, steps: [{ title: "Verify", content: "<p>Run the tests.</p>" }] }, "3. **Verify**"],
  ["docspress/hero", { eyebrow: "Docs", title: "Ship better docs", description: "One source.", primaryLabel: "Read", primaryUrl: "/docs/" }, "## Ship better docs"],
  ["docspress/prompt", { prompt: "Review the docs.", model: "GPT-5", mode: "code", thinking: true, context: "docs/", caption: "Review prompt" }, "> Review the docs."],
  ["docspress/result", { status: "success", title: "Checks passed", content: "<p>Everything is current.</p>", meta: "42 tests" }, "> [!TIP]"],
  ["docspress/terminal-session", { title: "Verify", shell: "bash", prompt: "$", command: "npm test", output: "42 passed" }, "$ npm test"],
  ["docspress/troubleshooter", { title: "Choose a fix", intro: "Answer this.", questions: [{ id: "docs", question: "Do docs exist?", yesLabel: "Yes", noLabel: "No" }], outcomes: [] }, "- **Do docs exist?** — Yes / No"],
  ["docspress/version-notice", { message: "You are viewing {current}.", latestLinkLabel: "Latest", showIcon: true, dismissible: false }, "> [!WARNING]"],
  ["docspress/version-switcher", { label: "API version", showLabel: true, presentation: "links" }, "**API version:** _WordPress version switcher_"]
];

const baseCoreBlockNames = [
  "accordion",
  "accordion-heading",
  "accordion-item",
  "accordion-panel",
  "audio",
  "avatar",
  "block",
  "button",
  "buttons",
  "calendar",
  "categories",
  "code",
  "column",
  "columns",
  "comment-author-name",
  "comment-content",
  "comment-date",
  "comment-edit-link",
  "comment-reply-link",
  "comment-template",
  "comments",
  "comments-pagination",
  "comments-pagination-next",
  "comments-pagination-numbers",
  "comments-pagination-previous",
  "cover",
  "details",
  "embed",
  "file",
  "footnotes",
  "freeform",
  "gallery",
  "group",
  "heading",
  "home-link",
  "html",
  "image",
  "latest-comments",
  "latest-posts",
  "legacy-widget",
  "list",
  "list-item",
  "loginout",
  "media-text",
  "missing",
  "more",
  "navigation",
  "navigation-link",
  "navigation-submenu",
  "nextpage",
  "page-list",
  "paragraph",
  "pattern",
  "post-author",
  "post-author-biography",
  "post-author-name",
  "post-comments-form",
  "post-content",
  "post-date",
  "post-excerpt",
  "post-featured-image",
  "post-navigation-link",
  "post-template",
  "post-terms",
  "post-title",
  "preformatted",
  "pullquote",
  "query",
  "query-no-results",
  "query-pagination",
  "query-pagination-next",
  "query-pagination-numbers",
  "query-pagination-previous",
  "query-title",
  "quote",
  "read-more",
  "rss",
  "search",
  "separator",
  "shortcode",
  "site-logo",
  "site-tagline",
  "site-title",
  "social-link",
  "social-links",
  "spacer",
  "table",
  "tag-cloud",
  "template-part",
  "term-description",
  "verse",
  "video",
  "widget-group"
];

function rawCustomBlock(name, attrs) {
  return `<!-- wp:${name} ${JSON.stringify(attrs)} /-->`;
}

function firstNamedBlock(serialized) {
  return parse(serialized).find((block) => block.blockName);
}

function desiredFromMarkdown(source) {
  const converted = markdownToBlocks(source, { fallbackTitle: "Docs" });
  return {
    key: "docs",
    sourcePath: "docs/index.md",
    title: converted.title,
    titleOverride: "",
    body: converted.blocks,
    sourceMarkdown: source
  };
}

function pageFromBody(desired, body) {
  return {
    id: 1,
    title: desired.title,
    content: prependSentinel(body, { key: desired.key, source: desired.sourcePath, hash: "base" }),
    sentinel: { key: desired.key, source: desired.sourcePath, hash: "base" },
    link: "https://example.com/docs/"
  };
}

describe("readable DocsPress Markdown blocks", () => {
  it.each(customPreviewCases)("renders %s as semantic Markdown and round-trips its attributes", (name, attrs, preview) => {
    const raw = rawCustomBlock(name, attrs);
    const markdown = blocksToMarkdown(raw);

    expect(markdown).toContain(`"name": "${name}"`);
    expect(markdown).toContain(preview);
    expect(markdown).not.toContain(`<!-- wp:${name}`);

    const roundTripped = firstNamedBlock(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks);
    expect(roundTripped?.blockName).toBe(name);
    expect(roundTripped?.attrs).toEqual(attrs);
  });

  it("covers every registered DocsPress block with defaults and a readable preview", () => {
    expect(Object.keys(CUSTOM_BLOCK_DEFAULTS).sort()).toEqual(
      customPreviewCases.map(([name]) => name).sort()
    );

    for (const [name, attrs] of Object.entries(CUSTOM_BLOCK_DEFAULTS)) {
      const markdown = blocksToMarkdown(rawCustomBlock(name, attrs));
      expect(markdown, name).toContain(`"name": "${name}"`);
      expect(markdown, name).toMatch(/(?:^|\n)(?:>|#|\d+\.|- |\*\*|```|\| |_[^_])/);
      expect(markdown, name).not.toContain(`<!-- wp:${name}`);
    }
  });

  it("uses a blockquote alert for callouts while keeping Gutenberg-only config hidden", () => {
    const attrs = {
      tone: "warning",
      title: "Do not skip review",
      content: "<p>Keep <strong>both</strong> directions safe.</p>",
      collapsible: true,
      open: false
    };
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/callout", attrs));

    expect(markdown).toContain("> [!WARNING]");
    expect(markdown).toContain("> **Do not skip review**");
    expect(markdown).toContain("> Keep **both** directions safe.");
    expect(markdown).toContain('"collapsible": true');
    expect(markdown).toContain('"open": false');
  });

  it("renders compact flow diagrams as GitHub-compatible Mermaid and preserves the source attributes", () => {
    const attrs = {
      title: "Publishing flow",
      type: "flow",
      source: [
        "Markdown -> DocsPress: collect",
        "DocsPress --> WordPress: publish",
        "WordPress -> Reader"
      ].join("\n"),
      caption: "The publishing path."
    };
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/diagram", attrs));

    expect(markdown).toContain([
      "```mermaid",
      "flowchart LR",
      '  n1["Markdown"]',
      '  n2["DocsPress"]',
      '  n3["WordPress"]',
      '  n4["Reader"]',
      '  n1 -->|"collect"| n2',
      '  n2 -->|"publish"| n3',
      "  n3 --> n4",
      "```"
    ].join("\n"));
    expect(markdown).not.toContain("```text\nMarkdown -> DocsPress");

    const roundTripped = firstNamedBlock(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks);
    expect(roundTripped?.attrs).toEqual(attrs);
  });

  it("renders compact sequence diagrams as Mermaid participants and messages", () => {
    const attrs = {
      title: "Request lifecycle",
      type: "sequence",
      source: "Browser -> API: GET /pages\nAPI -> Browser",
      caption: ""
    };
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/diagram", attrs));

    expect(markdown).toContain([
      "```mermaid",
      "sequenceDiagram",
      '  participant n1 as "Browser"',
      '  participant n2 as "API"',
      "  n1->>n2: GET /pages",
      "  n2->>n1: API to Browser",
      "```"
    ].join("\n"));
  });

  it("escapes Mermaid control characters and matches the WordPress diagram limits", () => {
    const actors = ['Source "A"', "Target | B", "Actor 3", "Actor 4", "Actor 5", "Actor 6", "Actor 7", "Actor 8"];
    const actorSetup = [
      `${actors[0]} -> ${actors[1]}: publish #1; now`,
      `${actors[2]} -> ${actors[3]}: setup 2`,
      `${actors[4]} -> ${actors[5]}: setup 3`,
      `${actors[6]} -> ${actors[7]}: setup 4`
    ];
    const repeatedEdges = Array.from({ length: 25 }, (_value, index) => (
      `${actors[index % actors.length]} -> ${actors[(index + 1) % actors.length]}: label ${index}`
    ));
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/diagram", {
      type: "flow",
      source: [
        "# ignored",
        ...actorSetup,
        ...repeatedEdges,
        "Outside first 30 -> Ignored: too late"
      ].join("\n")
    }));
    const preview = markdown.match(/```mermaid\n([\s\S]*?)\n```/)?.[1] || "";

    expect(preview).toContain('n1["Source #34;A#34;"]');
    expect(preview).toContain('n2["Target #124; B"]');
    expect(preview).toContain('n1 -->|"publish #35;1#59; now"| n2');
    expect(preview.match(/^\s+n\d+ -->/gm)).toHaveLength(24);
    expect(preview).not.toContain("Outside first 30");
  });

  it("keeps invalid diagram source readable instead of emitting broken Mermaid", () => {
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/diagram", {
      type: "flow",
      source: "# Notes\nNo relationship here"
    }));

    expect(markdown).toContain("```text\n# Notes\nNo relationship here\n```");
    expect(markdown).not.toContain("```mermaid");
  });

  it("keeps procedural Flow blocks as semantic ordered Markdown", () => {
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/flow", {
      start: 2,
      steps: [
        { title: "Configure", content: "<p>Set the values.</p>" },
        { title: "Verify", content: "<p>Run the checks.</p>" }
      ]
    }));

    expect(markdown).toContain("2. **Configure**");
    expect(markdown).toContain("3. **Verify**");
    expect(markdown).not.toContain("```mermaid");
  });

  it("treats config as authoritative and the visible preview as a Markdown projection", () => {
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/callout", {
      tone: "warning",
      title: "Config title",
      content: "<p>Config content.</p>"
    }))
      .replace("> **Config title**", "> **Preview-only title**")
      .replace("> Config content.", "> Preview-only content.");

    const block = firstNamedBlock(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks);
    expect(block?.attrs.title).toBe("Config title");
    expect(block?.attrs.content).toBe("<p>Config content.</p>");
  });

  it("applies config edits in the Markdown-to-Gutenberg direction", () => {
    const markdown = blocksToMarkdown(rawCustomBlock("docspress/callout", {
      tone: "warning",
      title: "Careful",
      content: "<p>Review first.</p>"
    })).replace('"tone": "warning"', '"tone": "danger"');

    const block = firstNamedBlock(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks);
    expect(block?.attrs.tone).toBe("danger");
  });

  it("fails closed for malformed config and mismatched lossless blocks", () => {
    expect(() => markdownToBlocks(
      "<!-- docspress:block\n{not json}\n-->\nVisible.\n<!-- /docspress:block -->",
      { fallbackTitle: "Docs" }
    )).toThrow(/invalid JSON/);

    const mismatchedConfig = JSON.stringify({
      version: 1,
      name: "core/paragraph",
      attrs: {},
      serialized: "<!-- wp:heading --><h2>Wrong</h2><!-- /wp:heading -->"
    }, null, 2).replace(/--/g, "\\u002d\\u002d");
    const mismatched = `<!-- docspress:block
${mismatchedConfig}
-->
Visible.
<!-- /docspress:block -->`;
    expect(() => markdownToBlocks(mismatched, { fallbackTitle: "Docs" })).toThrow(/exactly one core\/paragraph/);
  });

  it("does not interpret DocsPress boundaries inside fenced or inline code", () => {
    const source = `\`\`\`html
<!-- docspress:block
{"version":1,"name":"docspress/callout","attrs":{}}
-->
<!-- /docspress:block -->
\`\`\`

\`<!-- docspress:block -->\`
`;
    const converted = markdownToBlocks(source, { fallbackTitle: "Docs" });
    const names = parse(converted.blocks).map((block) => block.blockName).filter(Boolean);

    expect(names).toEqual(["core/code", "core/paragraph"]);
    expect(converted.blocks).not.toContain("wp:docspress/callout");
  });
});

describe("comprehensive core block conversion", () => {
  it("converts plain core paragraphs to pure Markdown with no block wrapper", () => {
    const raw = "<!-- wp:paragraph -->\n<p>Hello <strong>Markdown</strong>.</p>\n<!-- /wp:paragraph -->";
    const markdown = blocksToMarkdown(raw);

    expect(markdown).toBe("Hello **Markdown**.\n");
    expect(markdown).not.toContain("docspress:block");
    expect(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks).toBe(raw);
  });

  it("converts modern nested list-item serialization to a normal Markdown list", () => {
    const raw = `<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li>One</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Two<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Nested</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->`;
    const markdown = blocksToMarkdown(raw);

    expect(markdown).toContain("1. One");
    expect(markdown).toContain("2. Two");
    expect(markdown).toContain("Nested");
    expect(markdown).not.toContain("wp:list");
    expect(firstNamedBlock(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks)?.blockName).toBe("core/list");
  });

  it("keeps meaningful paragraph attributes losslessly while exposing readable Markdown", () => {
    const raw = '<!-- wp:paragraph {"style":{"color":{"text":"#123456"}},"className":"notice"} -->\n<p class="notice" style="color:#123456">Colored <strong>text</strong>.</p>\n<!-- /wp:paragraph -->';
    const markdown = blocksToMarkdown(raw);

    expect(markdown).toContain("Colored **text**.");
    expect(markdown).toContain('"name": "core/paragraph"');
    expect(markdown).not.toContain('"attrs"');
    expect(markdown).not.toContain("<!-- wp:paragraph");
    expect(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks).toBe(raw);
  });

  it.each(baseCoreBlockNames)("has a lossless readable fallback for core/%s", (shortName) => {
    const raw = `<!-- wp:${shortName} /-->`;
    const markdown = blocksToMarkdown(raw);

    expect(markdown).not.toContain(`<!-- wp:${shortName}`);
    expect(markdown.trim()).not.toBe("");
    expect(markdownToBlocks(markdown, { fallbackTitle: "Docs" }).blocks).toBe(raw);
  });

  it("returns WordPress-inserted paragraphs and modern lists as ordinary Markdown", () => {
    const source = "---\ntitle: Docs\n---\n\nBefore.\n\n- One\n- Two\n";
    const desired = desiredFromMarkdown(source);
    const modernList = `<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>One</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Two edited</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->`;
    const live = [
      "<!-- wp:paragraph -->\n<p>Before.</p>\n<!-- /wp:paragraph -->",
      "<!-- wp:paragraph -->\n<p>Inserted in WordPress.</p>\n<!-- /wp:paragraph -->",
      modernList
    ].join("\n\n");
    const merged = mergeWordPressIntoSource({
      existing: source,
      desired,
      page: pageFromBody(desired, live)
    });

    expect(merged).toBe("---\ntitle: Docs\n---\n\nBefore.\n\nInserted in WordPress.\n\n- One\n- Two edited\n");
    expect(merged).not.toContain("<!-- wp:paragraph");
    expect(merged).not.toContain("<!-- wp:list");
  });

  it("migrates legacy blocks in place without interpreting examples in code fences", () => {
    const source = `---
title: Migration
---

<!-- wp:paragraph -->
<p>Readable <strong>paragraph</strong>.</p>
<!-- /wp:paragraph -->

<!-- wp:docspress/callout {"tone":"warning","title":"Review","content":"<p>Check the diff.</p>"} /-->

\`\`\`html
<!-- wp:docspress/callout {"tone":"note"} /-->
\`\`\`
`;
    const upgraded = upgradeLegacyBlockSyntax(source);

    expect(upgraded).toContain("Readable **paragraph**.");
    expect(upgraded).toContain("> [!WARNING]");
    expect(upgraded).toContain('"name": "docspress/callout"');
    expect(upgraded).toContain('```html\n<!-- wp:docspress/callout {"tone":"note"} /-->\n```');
    expect(markdownToBlocks(upgraded, { fallbackTitle: "Docs" }).blocks)
      .toBe(markdownToBlocks(source, { fallbackTitle: "Docs" }).blocks);
  });

  it("migrates a self-closing custom block whose JSON contains block-comment examples", () => {
    const source = String.raw`<!-- wp:docspress/api-request {"method":"POST","endpoint":"/pages","responseBody":"{\n  \"content\": \"<!-- docspress:{...} -->\\n<!-- wp:paragraph -->...\"\n}","responseBodyFormat":"json"} /-->`;
    const upgraded = upgradeLegacyBlockSyntax(source);

    expect(upgraded).toContain('"name": "docspress/api-request"');
    expect(upgraded).toContain("#### POST /pages");
    expect(upgraded).not.toContain("<!-- wp:docspress/api-request");
    expect(firstNamedBlock(markdownToBlocks(upgraded, { fallbackTitle: "Docs" }).blocks)?.attrs.responseBody)
      .toContain("<!-- wp:paragraph -->");
  });

  it("refreshes an existing diagram envelope with its generated Mermaid preview", () => {
    const current = blocksToMarkdown(rawCustomBlock("docspress/diagram", {
      title: "Publishing flow",
      type: "flow",
      source: "Markdown -> WordPress: publish"
    }));
    const stale = current.replace(
      /```mermaid[\s\S]*?```/,
      "```text\nMarkdown -> WordPress: publish\n```"
    );
    const upgraded = upgradeLegacyBlockSyntax(stale);

    expect(upgraded).toContain("```mermaid\nflowchart LR");
    expect(upgraded).toContain('n1 -->|"publish"| n2');
    expect(upgradeLegacyBlockSyntax(upgraded)).toBe(upgraded);
    expect(markdownToBlocks(upgraded, { fallbackTitle: "Docs" }).blocks)
      .toBe(markdownToBlocks(stale, { fallbackTitle: "Docs" }).blocks);
  });
});
