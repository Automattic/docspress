import { parse as parseBlocks } from "@wordpress/block-serialization-default-parser";

export const AUTODOCS_PUBLISH_BLOCK_VERSION = 1;

export const CUSTOM_BLOCK_DEFAULTS = {
  "autodocs-publish/api-request": {
    method: "GET",
    endpoint: "/wp-json/wp/v2/pages",
    headers: "Accept: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN",
    requestBody: "",
    requestBodyFormat: "json",
    responseStatus: "200 OK",
    responseBody: "{\n  \"id\": 42,\n  \"slug\": \"getting-started\"\n}",
    responseBodyFormat: "json",
    runnable: false,
    editable: true,
    allowUnsafe: false,
    baseUrl: "",
    allowedOrigins: "",
    timeout: 10000
  },
  "autodocs-publish/audience-paths": {
    eyebrow: "Choose a starting point",
    title: "Where are your docs today?",
    description: "Follow the path that matches your repository.",
    paths: [
      {
        title: "I already have Markdown docs",
        description: "Connect an existing docs folder to WordPress and begin with a safe draft sync.",
        url: "/docs/publish-existing-docs/",
        cta: "Publish existing docs",
        icon: "MD",
        accent: "blue",
        newTab: false
      },
      {
        title: "I need to create docs",
        description: "Generate source-grounded documentation with AI, review it, then publish it.",
        url: "/docs/create-docs-with-ai/",
        cta: "Create docs with AI",
        icon: "AI",
        accent: "gold",
        newTab: false
      }
    ],
    columns: 2,
    tone: "theme",
    textAlign: "left",
    compact: false,
    showNumbers: false,
    showIcons: true,
    showLinks: true,
    panelColor: "",
    accentColor: ""
  },
  "autodocs-publish/callout": {
    tone: "note",
    title: "Good to know",
    content: "<p>Add the detail readers need at exactly the right moment.</p>",
    collapsible: false,
    open: true
  },
  "autodocs-publish/code-tabs": {
    tabs: [
      { label: "JavaScript", language: "javascript", filename: "example.js", code: "const docs = await publish();" },
      { label: "PHP", language: "php", filename: "example.php", code: "$docs = autodocs_publish_publish();" }
    ],
    showLineNumbers: true,
    caption: ""
  },
  "autodocs-publish/code-playground": {
    title: "Live example",
    html: "<button class=\"demo-button\">Publish docs</button>",
    css: ".demo-button {\n  padding: 0.75rem 1rem;\n  border: 0;\n  border-radius: 0.4rem;\n  background: #3858e9;\n  color: white;\n  font: inherit;\n}",
    javascript: "document.querySelector( '.demo-button' ).addEventListener( 'click', () => {\n  console.log( 'Documentation published' );\n} );",
    height: 320,
    autoRun: true,
    showConsole: true,
    allowNetwork: false
  },
  "autodocs-publish/colorful-code": {
    language: "javascript",
    filename: "",
    code: "const hello = \"Autodocs Publish\";\nconsole.log( hello );",
    highlightedLines: "",
    showLineNumbers: true,
    caption: "",
    diffMode: "none",
    copyMode: "all",
    annotations: []
  },
  "autodocs-publish/diagram": {
    title: "Publishing flow",
    type: "flow",
    source: "Markdown -> Autodocs Publish: collect\nAutodocs Publish -> WordPress: publish\nWordPress -> Reader: serve",
    caption: ""
  },
  "autodocs-publish/fields": {
    title: "Configuration fields",
    description: "Typed options, defaults, and constraints in one scannable reference.",
    fields: [
      {
        name: "site",
        type: "string",
        required: true,
        defaultValue: "",
        description: "WordPress site domain or numeric site ID.",
        values: "",
        deprecated: false
      },
      {
        name: "status",
        type: "string",
        required: false,
        defaultValue: "draft",
        description: "Publication status for synchronized Pages.",
        values: "draft, publish, private",
        deprecated: false
      },
      {
        name: "dryRun",
        type: "boolean",
        required: false,
        defaultValue: "false",
        description: "Preview reconciliation without writing changes.",
        values: "true, false",
        deprecated: false
      }
    ],
    searchable: true,
    compact: false
  },
  "autodocs-publish/file-tree": {
    root: "project/",
    tree: "docs/\n  getting-started.md\n  api/\n    endpoints.md\npackage.json",
    caption: "",
    collapsible: true,
    open: true
  },
  "autodocs-publish/flow": {
    start: 1,
    steps: [
      { title: "Choose", content: "<p>Select the option that matches your project.</p>" },
      { title: "Configure", content: "<p>Set the values required by your environment.</p>" },
      { title: "Verify", content: "<p>Run the check and confirm the expected result.</p>" }
    ]
  },
  "autodocs-publish/hero": {
    eyebrow: "Documentation, publishing, and community",
    title: "Docs that stay connected to your GitHub repo",
    description: "Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.",
    primaryLabel: "Browse documentation",
    primaryUrl: "",
    primaryNewTab: false,
    secondaryLabel: "Latest updates",
    secondaryUrl: "",
    secondaryNewTab: false,
    mediaId: 0,
    mediaUrl: "",
    mediaAlt: "",
    visualLabel: "",
    visualVariant: "image",
    layout: "split",
    mediaPosition: "right",
    mediaWidth: 44,
    imageScale: 100,
    height: "standard",
    tone: "theme",
    textAlign: "left",
    showGrid: false,
    showOrbit: false,
    panelColor: "",
    visualColor: "",
    accentColor: ""
  },
  "autodocs-publish/prompt": {
    prompt: "Use $autodocs-publish-install to review this repository's documentation setup. Return a short plan before writing code.",
    model: "GPT-5",
    mode: "code",
    thinking: true,
    context: "$autodocs-publish-install, @repository, src/sync.js, docs/",
    caption: "Prompt example"
  },
  "autodocs-publish/result": {
    status: "success",
    title: "Deployment completed",
    content: "<p>All documentation pages are up to date.</p>",
    meta: "12 pages · 1.8s"
  },
  "autodocs-publish/terminal-session": {
    title: "Terminal",
    shell: "bash",
    prompt: "$",
    command: "npx autodocs-publish publish ./docs",
    output: "✓ Read 12 documents\n✓ Published 12 WordPress pages"
  },
  "autodocs-publish/troubleshooter": {
    title: "Find the next step",
    intro: "Answer two quick questions to get the right Autodocs Publish workflow.",
    startId: "source",
    questions: [
      {
        id: "source",
        question: "Do you already have Markdown documentation?",
        yesLabel: "Yes, the docs exist",
        yesNext: "connected",
        noLabel: "Not yet",
        noNext: "generate"
      },
      {
        id: "connected",
        question: "Is the repository connected to WordPress?",
        yesLabel: "Yes, it is connected",
        yesNext: "sync",
        noLabel: "No, connect it",
        noNext: "install"
      }
    ],
    outcomes: [
      {
        id: "install",
        status: "warning",
        title: "Connect the publishing target",
        content: "<p>Run the Autodocs Publish installer, add the WordPress access token, and verify the repository connection before publishing.</p>"
      },
      {
        id: "sync",
        status: "success",
        title: "Publish the documentation",
        content: "<p>Run the sync command, review the proposed changes, and verify the rendered documentation on WordPress.</p>"
      },
      {
        id: "generate",
        status: "neutral",
        title: "Generate a documentation starter",
        content: "<p>Generate a small documentation tree from the source, then review every example against the implementation before publishing.</p>"
      }
    ],
    showProgress: true
  },
  "autodocs-publish/version-notice": {
    message: "You are viewing {current}. The latest version is {latest}.",
    latestLinkLabel: "Switch to latest",
    showIcon: true,
    dismissible: false
  },
  "autodocs-publish/version-switcher": {
    label: "Version",
    showLabel: true,
    presentation: "select",
    showLatestBadge: true,
    hideSingle: true,
    unavailableLabel: "Page unavailable"
  }
};

const AUTODOCS_PUBLISH_BLOCK_PATTERN = /<!--\s*(autodocs-publish|docspress):block\s*([\s\S]*?)-->\s*\n?([\s\S]*?)<!--\s*\/\1:block\s*-->/g;
const AUTODOCS_PUBLISH_MARKER_PATTERN = /<!--\s*\/?(?:autodocs-publish|docspress):block(?:\s|--)/;
const BLOCK_NAME_PATTERN = /^[a-z][a-z0-9_-]*\/[a-z][a-z0-9_-]*$/;

export function normalizeAutodocsPublishBlockName(name) {
  const value = String(name || "");
  return value.startsWith("docspress/")
    ? `autodocs-publish/${value.slice("docspress/".length)}`
    : value;
}

export function isAutodocsPublishBlockName(name) {
  const value = String(name || "");
  return value.startsWith("autodocs-publish/") || value.startsWith("docspress/");
}

export function markdownBlockSyntaxToGutenberg(source, protectedRanges = []) {
  const replacements = [];
  const pattern = new RegExp(AUTODOCS_PUBLISH_BLOCK_PATTERN.source, "g");
  let match;

  while ((match = pattern.exec(source))) {
    if (rangeContains(protectedRanges, match.index)) {
      continue;
    }
    const config = parseBlockConfig(match[2]);
    replacements.push({
      start: match.index,
      end: pattern.lastIndex,
      value: config.serialized || serializeSelfClosingBlock(config.name, config.attrs)
    });
  }

  const masked = maskRanges(source, protectedRanges);
  const residual = masked.replace(pattern, "");
  if (AUTODOCS_PUBLISH_MARKER_PATTERN.test(residual)) {
    throw new Error("Autodocs Publish Markdown block syntax has an unmatched or malformed boundary.");
  }

  let result = source;
  for (const replacement of replacements.sort((left, right) => right.start - left.start)) {
    result = `${result.slice(0, replacement.start)}${replacement.value}${result.slice(replacement.end)}`;
  }
  return result;
}

export function findMarkdownBlockRanges(source, protectedRanges = []) {
  const ranges = [];
  const pattern = new RegExp(AUTODOCS_PUBLISH_BLOCK_PATTERN.source, "g");
  let match;

  while ((match = pattern.exec(source))) {
    if (!rangeContains(protectedRanges, match.index)) {
      ranges.push({ start: match.index, end: pattern.lastIndex, type: "autodocs-publish-block" });
    }
  }
  return ranges;
}

export function customBlockToMarkdown(name, attributes, service) {
  const normalizedName = normalizeAutodocsPublishBlockName(name);
  const attrs = { ...(attributes || {}) };
  const preview = renderCustomBlockPreview(normalizedName, attrs, service);
  return serializeMarkdownBlock({ name: normalizedName, attrs }, preview);
}

export function coreBlockToMarkdownEnvelope(block, raw, service) {
  const preview = renderCoreBlockPreview(block, raw, service);
  return serializeMarkdownBlock({
    name: block.blockName,
    serialized: raw.trim()
  }, preview);
}

function parseBlockConfig(raw) {
  let config;
  try {
    config = JSON.parse(String(raw || "").trim());
  } catch (error) {
    throw new Error(`Autodocs Publish Markdown block config is invalid JSON: ${error.message}`);
  }
  if (config?.version !== AUTODOCS_PUBLISH_BLOCK_VERSION) {
    throw new Error(`Autodocs Publish Markdown block version must be ${AUTODOCS_PUBLISH_BLOCK_VERSION}.`);
  }
  if (!BLOCK_NAME_PATTERN.test(config?.name || "")) {
    throw new Error("Autodocs Publish Markdown block config must contain a valid namespaced block name.");
  }
  const name = normalizeAutodocsPublishBlockName(config.name);
  const serialized = config.serialized === undefined
    ? undefined
    : normalizeSerializedBlock(config.serialized);
  if (serialized !== undefined) {
    validateSerializedBlock(serialized, name);
  } else if (!name.startsWith("autodocs-publish/")) {
    throw new Error("Core and third-party Markdown block configs must include their lossless serialized form.");
  }
  if (config.attrs !== undefined && (!config.attrs || Array.isArray(config.attrs) || typeof config.attrs !== "object")) {
    throw new Error("Autodocs Publish Markdown block attrs must be an object.");
  }
  return {
    name,
    attrs: config.attrs || {},
    serialized
  };
}

function normalizeSerializedBlock(value) {
  return typeof value === "string"
    ? value.replace(/(<!--\s*\/?wp:)docspress\//g, "$1autodocs-publish/")
    : value;
}

function validateSerializedBlock(value, expectedName) {
  if (typeof value !== "string") {
    throw new Error("Autodocs Publish serialized block config must be a string.");
  }
  const named = parseBlocks(value).filter((block) => block.blockName);
  if (named.length !== 1 || named[0].blockName !== expectedName) {
    throw new Error(`Autodocs Publish serialized block config must contain exactly one ${expectedName} block.`);
  }
}

function serializeSelfClosingBlock(name, attrs) {
  const attributes = attrs && Object.keys(attrs).length > 0
    ? ` ${safeJson(attrs)}`
    : "";
  return `<!-- wp:${name}${attributes} /-->`;
}

function serializeMarkdownBlock(config, preview) {
  const canonicalConfig = {
    ...config,
    name: normalizeAutodocsPublishBlockName(config.name)
  };
  if (canonicalConfig.serialized !== undefined) {
    canonicalConfig.serialized = normalizeSerializedBlock(canonicalConfig.serialized);
  }
  const payload = safeJson({
    version: AUTODOCS_PUBLISH_BLOCK_VERSION,
    ...canonicalConfig
  }, 2);
  const safePreview = String(preview || "")
    .replace(/<!--\s*\/?(?:autodocs-publish|docspress):block/g, "&lt;!-- autodocs-publish:block")
    .trim() || `**WordPress block: \`${canonicalConfig.name}\`**`;
  return `<!-- autodocs-publish:block\n${payload}\n-->\n${safePreview}\n<!-- /autodocs-publish:block -->`;
}

function safeJson(value, spacing) {
  return JSON.stringify(value, null, spacing)
    .replace(/--/g, "\\u002d\\u002d")
    .replace(/</g, "\\u003c")
    .replace(/>/g, "\\u003e")
    .replace(/&/g, "\\u0026");
}

function renderCustomBlockPreview(name, attrs, service) {
  const renderers = {
    "autodocs-publish/api-request": renderApiRequest,
    "autodocs-publish/audience-paths": renderAudiencePaths,
    "autodocs-publish/callout": renderCallout,
    "autodocs-publish/code-tabs": renderCodeTabs,
    "autodocs-publish/code-playground": renderCodePlayground,
    "autodocs-publish/colorful-code": renderColorfulCode,
    "autodocs-publish/diagram": renderDiagram,
    "autodocs-publish/fields": renderFields,
    "autodocs-publish/file-tree": renderFileTree,
    "autodocs-publish/flow": renderFlow,
    "autodocs-publish/hero": renderHero,
    "autodocs-publish/prompt": renderPrompt,
    "autodocs-publish/result": renderResult,
    "autodocs-publish/terminal-session": renderTerminal,
    "autodocs-publish/troubleshooter": renderTroubleshooter,
    "autodocs-publish/version-notice": renderVersionNotice,
    "autodocs-publish/version-switcher": renderVersionSwitcher
  };
  const renderer = renderers[name];
  return renderer
    ? renderer(attrs, service)
    : `**Autodocs Publish block: \`${name}\`**`;
}

function renderCallout(attrs, service) {
  const type = {
    tip: "TIP",
    success: "TIP",
    warning: "WARNING",
    danger: "CAUTION",
    error: "CAUTION",
    important: "IMPORTANT"
  }[attrs.tone] || "NOTE";
  const content = htmlToMarkdown(attrs.content, service);
  return quoteMarkdown([
    `[!${type}]`,
    attrs.title ? `**${escapeInline(attrs.title)}**` : "",
    content
  ].filter(Boolean).join("\n\n"));
}

function renderResult(attrs, service) {
  const type = {
    success: "TIP",
    warning: "WARNING",
    error: "CAUTION",
    danger: "CAUTION"
  }[attrs.status] || "NOTE";
  const parts = [
    `[!${type}]`,
    attrs.title ? `**${escapeInline(attrs.title)}**` : "",
    htmlToMarkdown(attrs.content, service),
    attrs.meta ? `_${attrs.meta}_` : ""
  ];
  return quoteMarkdown(parts.filter(Boolean).join("\n\n"));
}

function renderTerminal(attrs) {
  const command = String(attrs.command || "")
    .split("\n")
    .map((line) => `${attrs.prompt || "$"} ${line}`)
    .join("\n");
  return [
    attrs.title ? `#### ${escapeHeading(attrs.title)}` : "",
    fencedCode(command, attrs.shell || "text"),
    attrs.output ? `**Output**\n\n${fencedCode(attrs.output, "text")}` : ""
  ].filter(Boolean).join("\n\n");
}

function renderPrompt(attrs) {
  const details = [
    attrs.model ? `Model: ${attrs.model}` : "",
    attrs.mode ? `Mode: ${attrs.mode}` : "",
    attrs.thinking === false ? "Thinking: off" : attrs.thinking === true ? "Thinking: on" : "",
    attrs.context ? `Context: ${attrs.context}` : ""
  ].filter(Boolean).join(" · ");
  return [
    `#### ${escapeHeading(attrs.caption || "Prompt")}`,
    quoteMarkdown(String(attrs.prompt || "")),
    details ? `_${details}_` : ""
  ].filter(Boolean).join("\n\n");
}

function renderColorfulCode(attrs) {
  const label = [attrs.filename, attrs.caption].filter(Boolean).join(" — ");
  return [
    label ? `**${escapeInline(label)}**` : "",
    fencedCode(attrs.code || "", attrs.language || "text")
  ].filter(Boolean).join("\n\n");
}

function renderCodeTabs(attrs) {
  const tabs = Array.isArray(attrs.tabs) ? attrs.tabs : [];
  const rendered = tabs.map((tab) => [
    `#### ${escapeHeading([tab.label, tab.filename].filter(Boolean).join(" — ") || "Code")}`,
    fencedCode(tab.code || "", tab.language || "text")
  ].join("\n\n"));
  if (attrs.caption) {
    rendered.push(`_${attrs.caption}_`);
  }
  return rendered.join("\n\n");
}

function renderCodePlayground(attrs) {
  return [
    attrs.title ? `#### ${escapeHeading(attrs.title)}` : "",
    attrs.html ? `**HTML**\n\n${fencedCode(attrs.html, "html")}` : "",
    attrs.css ? `**CSS**\n\n${fencedCode(attrs.css, "css")}` : "",
    attrs.javascript ? `**JavaScript**\n\n${fencedCode(attrs.javascript, "javascript")}` : ""
  ].filter(Boolean).join("\n\n");
}

function renderFileTree(attrs) {
  return [
    attrs.root ? `#### ${escapeHeading(attrs.root)}` : "",
    fencedCode(attrs.tree || "", "text"),
    attrs.caption ? `_${attrs.caption}_` : ""
  ].filter(Boolean).join("\n\n");
}

function renderDiagram(attrs) {
  const mermaid = diagramSourceToMermaid(attrs.source, attrs.type);
  return [
    attrs.title ? `#### ${escapeHeading(attrs.title)}` : "",
    fencedCode(mermaid || attrs.source || "", mermaid ? "mermaid" : "text"),
    attrs.caption ? `_${attrs.caption}_` : ""
  ].filter(Boolean).join("\n\n");
}

function diagramSourceToMermaid(source, type) {
  const { actors, edges } = parseDiagramSource(source);
  if (edges.length === 0) {
    return "";
  }

  const actorIds = new Map(actors.map((actor, index) => [actor, `n${index + 1}`]));
  if (type === "sequence") {
    return [
      "sequenceDiagram",
      ...actors.map((actor) => `  participant ${actorIds.get(actor)} as "${escapeMermaidText(actor)}"`),
      ...edges.map((edge) => {
        const label = edge.label || `${edge.from} to ${edge.to}`;
        return `  ${actorIds.get(edge.from)}->>${actorIds.get(edge.to)}: ${escapeMermaidText(label)}`;
      })
    ].join("\n");
  }

  return [
    "flowchart LR",
    ...actors.map((actor) => `  ${actorIds.get(actor)}["${escapeMermaidText(actor)}"]`),
    ...edges.map((edge) => {
      const label = edge.label
        ? `-->|"${escapeMermaidText(edge.label)}"|`
        : "-->";
      return `  ${actorIds.get(edge.from)} ${label} ${actorIds.get(edge.to)}`;
    })
  ].join("\n");
}

function parseDiagramSource(source) {
  const actors = [];
  const edges = [];
  const lines = String(source || "").split(/\r\n|\r|\n/).slice(0, 30);

  for (const rawLine of lines) {
    const line = rawLine.trim();
    if (!line || line.startsWith("#")) {
      continue;
    }
    const match = line.match(/^(.+?)\s*(?:-->|->)\s*(.+?)(?:\s*:\s*(.+))?$/u);
    if (!match) {
      continue;
    }
    const from = match[1].trim();
    const to = match[2].trim();
    const label = (match[3] || "").trim();
    if (!from || !to) {
      continue;
    }

    for (const actor of [from, to]) {
      if (!actors.includes(actor) && actors.length < 8) {
        actors.push(actor);
      }
    }
    if (actors.includes(from) && actors.includes(to) && edges.length < 24) {
      edges.push({ from, to, label });
    }
  }

  return { actors, edges };
}

function escapeMermaidText(value) {
  return String(value || "")
    .replace(/\s+/g, " ")
    .replace(/[#";|[\]{}()<>%&`\\]/g, (character) => `#${character.codePointAt(0)};`);
}

function renderFields(attrs) {
  const fields = Array.isArray(attrs.fields) ? attrs.fields : [];
  const rows = fields.map((field) => [
    inlineCode(field.name || ""),
    escapeTable(field.type || ""),
    field.required ? "Yes" : "No",
    escapeTable(field.defaultValue || ""),
    escapeTable(field.description || "")
  ]);
  return [
    attrs.title ? `#### ${escapeHeading(attrs.title)}` : "",
    attrs.description || "",
    markdownTable(["Field", "Type", "Required", "Default", "Description"], rows)
  ].filter(Boolean).join("\n\n");
}

function renderFlow(attrs, service) {
  const start = Number(attrs.start) || 1;
  const steps = Array.isArray(attrs.steps) ? attrs.steps : [];
  return steps.map((step, index) => {
    const content = indentMarkdown(htmlToMarkdown(step.content, service), "   ");
    return `${start + index}. **${escapeInline(step.title || `Step ${start + index}`)}**${content ? `\n\n${content}` : ""}`;
  }).join("\n\n");
}

function renderApiRequest(attrs) {
  const defaults = CUSTOM_BLOCK_DEFAULTS["autodocs-publish/api-request"];
  const method = apiAttribute(attrs, defaults, "method");
  const endpoint = apiAttribute(attrs, defaults, "endpoint");
  const headers = apiAttribute(attrs, defaults, "headers");
  const requestBody = apiAttribute(attrs, defaults, "requestBody");
  const responseStatus = apiAttribute(attrs, defaults, "responseStatus");
  const responseBody = apiAttribute(attrs, defaults, "responseBody");
  const requestFormat = apiPayloadLanguage(apiAttribute(attrs, defaults, "requestBodyFormat"));
  const responseFormat = apiPayloadLanguage(apiAttribute(attrs, defaults, "responseBodyFormat"));
  const requestLabel = `${normalizedApiMethod(method)} ${String(endpoint).trim()}`.trim();
  const requestContent = [
    String(headers).trim() ? `**Headers**\n\n${fencedCode(headers, "http")}` : "",
    String(requestBody).trim() ? `**Body**\n\n${fencedCode(requestBody, requestFormat)}` : ""
  ].filter(Boolean).join("\n\n");
  const responseContent = String(responseBody).trim()
    ? `**Body**\n\n${fencedCode(responseBody, responseFormat)}`
    : "";
  const responseLabel = String(responseStatus).trim();

  return [
    markdownDetails(
      `<strong>Request:</strong> <code>${escapeDetailsText(requestLabel)}</code>`,
      requestContent
    ),
    markdownDetails(
      responseLabel
        ? `<strong>Response:</strong> <code>${escapeDetailsText(responseLabel)}</code>`
        : "<strong>Response</strong>",
      responseContent
    )
  ].join("\n\n");
}

function apiAttribute(attrs, defaults, name) {
  return attrs[name] === undefined || attrs[name] === null
    ? defaults[name]
    : attrs[name];
}

function normalizedApiMethod(value) {
  const method = String(value || "").trim().toUpperCase();
  return ["GET", "POST", "PUT", "PATCH", "DELETE"].includes(method)
    ? method
    : "GET";
}

function apiPayloadLanguage(value) {
  return String(value || "").toLowerCase() === "json"
    ? "json"
    : "text";
}

function markdownDetails(summary, content) {
  const body = String(content || "").trim();
  return `<details>
<summary>${summary}</summary>${body ? `\n\n${body}` : ""}

</details>`;
}

function escapeDetailsText(value) {
  return String(value || "")
    .replace(/\s+/g, " ")
    .trim()
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function renderAudiencePaths(attrs) {
  const paths = Array.isArray(attrs.paths) ? attrs.paths : [];
  const items = paths.map((item) => [
    `### ${escapeHeading(item.title || "Path")}`,
    item.description || "",
    item.url ? `[${item.cta || "Open"}](${item.url})` : item.cta || ""
  ].filter(Boolean).join("\n\n"));
  return [
    attrs.eyebrow ? `_${attrs.eyebrow}_` : "",
    attrs.title ? `## ${escapeHeading(attrs.title)}` : "",
    attrs.description || "",
    ...items
  ].filter(Boolean).join("\n\n");
}

function renderHero(attrs) {
  const actions = [
    attrs.primaryUrl ? `[${attrs.primaryLabel || "Open"}](${attrs.primaryUrl})` : attrs.primaryLabel || "",
    attrs.secondaryUrl ? `[${attrs.secondaryLabel || "Learn more"}](${attrs.secondaryUrl})` : attrs.secondaryLabel || ""
  ].filter(Boolean).join(" · ");
  return [
    attrs.eyebrow ? `_${attrs.eyebrow}_` : "",
    attrs.title ? `## ${escapeHeading(attrs.title)}` : "",
    attrs.description || "",
    actions,
    attrs.mediaUrl ? `![${attrs.mediaAlt || ""}](${attrs.mediaUrl})` : ""
  ].filter(Boolean).join("\n\n");
}

function renderTroubleshooter(attrs, service) {
  const questions = (Array.isArray(attrs.questions) ? attrs.questions : []).map((question) =>
    `- **${escapeInline(question.question || question.id || "Question")}** — ${question.yesLabel || "Yes"} / ${question.noLabel || "No"}`
  );
  const outcomes = (Array.isArray(attrs.outcomes) ? attrs.outcomes : []).map((outcome) => [
    `### ${escapeHeading(outcome.title || outcome.id || "Outcome")}`,
    htmlToMarkdown(outcome.content, service)
  ].filter(Boolean).join("\n\n"));
  return [
    attrs.title ? `## ${escapeHeading(attrs.title)}` : "",
    attrs.intro || "",
    questions.join("\n"),
    ...outcomes
  ].filter(Boolean).join("\n\n");
}

function renderVersionNotice(attrs) {
  return quoteMarkdown([
    "[!WARNING]",
    attrs.message || "You are viewing historical documentation.",
    attrs.latestLinkLabel ? `**${escapeInline(attrs.latestLinkLabel)}**` : ""
  ].filter(Boolean).join("\n\n"));
}

function renderVersionSwitcher(attrs) {
  return `**${escapeInline(attrs.label || "Version")}:** _WordPress version switcher_`;
}

function renderCoreBlockPreview(block, raw, service) {
  const name = block.blockName;
  if (name === "core/separator") {
    return "---";
  }
  if (name === "core/more") {
    return "*More content follows.*";
  }
  if (name === "core/nextpage") {
    return "*Page break.*";
  }
  if (name === "core/spacer") {
    return "<br>";
  }
  const markdown = service.turndown(stripBlockComments(raw)).trim();
  return markdown || `**WordPress block: \`${name}\`**`;
}

function htmlToMarkdown(value, service) {
  const markdown = service.turndown(String(value || "")).trim();
  return markdown;
}

function stripBlockComments(value) {
  return String(value || "").replace(/<!--\s*\/?wp:[\s\S]*?-->/g, "").trim();
}

function quoteMarkdown(value) {
  return String(value || "")
    .split("\n")
    .map((line) => line ? `> ${line}` : ">")
    .join("\n");
}

function fencedCode(value, language = "") {
  const content = String(value || "");
  const longest = Math.max(0, ...(content.match(/`+/g) || []).map((run) => run.length));
  const fence = "`".repeat(Math.max(3, longest + 1));
  const safeLanguage = String(language || "").match(/^[\w+-]+$/)?.[0] || "text";
  return `${fence}${safeLanguage}\n${content}\n${fence}`;
}

function markdownTable(headers, rows) {
  const head = `| ${headers.map(escapeTable).join(" | ")} |`;
  const divider = `| ${headers.map(() => "---").join(" | ")} |`;
  const body = rows.map((row) => `| ${row.map(escapeTable).join(" | ")} |`).join("\n");
  return [head, divider, body].filter(Boolean).join("\n");
}

function inlineCode(value) {
  const content = String(value || "");
  const fence = content.includes("`") ? "``" : "`";
  return `${fence}${content}${fence}`;
}

function escapeTable(value) {
  return String(value ?? "")
    .replace(/\r?\n/g, "<br>")
    .replace(/\|/g, "\\|");
}

function escapeInline(value) {
  return String(value || "").replace(/([\\`*_[\]])/g, "\\$1");
}

function escapeHeading(value) {
  return String(value || "").replace(/([\\`*_[\]#])/g, "\\$1");
}

function indentMarkdown(value, indent) {
  return String(value || "")
    .split("\n")
    .map((line) => `${indent}${line}`)
    .join("\n");
}

function rangeContains(ranges, offset) {
  return ranges.some((range) => offset >= range.start && offset < range.end);
}

function maskRanges(source, ranges) {
  const characters = source.split("");
  for (const range of ranges) {
    for (let index = range.start; index < range.end; index += 1) {
      if (characters[index] !== "\n" && characters[index] !== "\r") {
        characters[index] = " ";
      }
    }
  }
  return characters.join("");
}
