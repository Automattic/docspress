# DocsPress Blocks

Documentation-focused Gutenberg blocks for the DocsPress theme. The plugin has no build step and uses WordPress's bundled block-editor packages.

## Blocks

- **Hero** — a responsive, theme-native homepage introduction with inline editing for every visible text label, configurable actions, transparent artwork, split-panel and editorial spotlight compositions, and opt-in decorative or color overrides.
- **Audience Paths** — one to six starting-point cards that route to independent documentation roots, with spacious and compact layouts, inline copy, destination URLs, a curated SVG icon system, accents, new-tab behavior, responsive columns, and optional presentation overrides.
- **Colorful Code** — filename chrome, language-aware token colors, line numbers, highlighted ranges, unified diff states, final-state copy, line annotations, captions, and copy-to-clipboard.
- **Code Tabs** — up to eight keyboard-accessible examples with independent labels, languages, filenames, and code.
- **Callout** — note, tip, warning, danger, and success tones, with an optional collapsible presentation.
- **Flow** — a connected, automatically numbered procedure with inline step editing, add/remove/reorder controls, and a configurable starting number.
- **API Request / Response** — one structured HTTP exchange with method, endpoint, bold/light header pairs, independently selectable JSON or raw request and response bodies, response status, and an optional origin-restricted browser runner with reset, timeout, safety confirmation, live timing, and cURL copy.
- **Fields / Schema** — searchable typed field reference for API parameters, configuration, environment variables, and CLI options, including defaults, allowed values, required state, and deprecation.
- **Live Code Playground** — editable HTML, CSS, and JavaScript with a sandboxed iframe, network-deny default, Run/Reset controls, and bounded console output.
- **Diagram** — dependency-free flow and sequence diagrams generated as accessible SVG from compact editable relationships.
- **Interactive Troubleshooter** — a keyboard-accessible question tree with configurable routes, outcomes, progress, Back, and Start over.
- **Terminal Session** — a copyable command separated from its read-only output, with editable prompt, shell, and title labels.
- **Result** — a compact success, neutral, warning, or error outcome for builds, checks, and verification steps.
- **File Tree** — an indentation-aware repository view with native collapsible folder disclosures and editable initial state.
- **Prompt** — a first-class, copyable AI prompt with model, mode, optional Thinking state, highlighted `$skill-name` references, classified context chips, and an editable caption.

The inserter also includes **Homepage hero**, **Documentation starting paths**, **Documentation page starter**, **API request example**, **Runnable API console**, **API reference toolkit**, **Interactive guide**, and **AI prompt example** patterns under the **DocsPress** category.

Every block inherits the active Global Style variation in both Gutenberg and the published site, including semantic light/dark colors, typography, radius, borders, and article width. The plugin declares native Block Supports for colors and gradients, typography, spacing, borders, minimum height, sticky positioning, shadows, anchors, and custom classes. Hero and Audience Paths also support wide/full alignment and retain their purpose-built composition controls.

The DocsPress block theme includes WordPress.org, WordPress.com, and Jetpack style families. WordPress.org variants keep crisp 2px corners, while WordPress.com and Jetpack use 4px recipes; block styles do not impose a larger minimum radius or a separate card shadow. Hero and Audience Paths default to the active tokens with clean surfaces and modest type scales. The Hero starts with the simple split composition used on fkadev.blog; its editorial spotlight, inverse styles, decorations, and custom colors are explicit opt-ins.

The editorial spotlight can use either the dark midnight treatment or the warm paper treatment. Paper intentionally creates a light editorial island inside a dark documentation shell.

## Folder structure

Every block owns its registration, renderer, editor UI, front-end styles, and editor-only styles. To add or maintain a block, work inside its folder instead of editing a plugin-wide bundle:

```text
docspress-blocks/
├── blocks/
│   ├── hero/
│   ├── audience-paths/
│   ├── colorful-code/
│   │   ├── block.php
│   │   ├── editor.js
│   │   ├── style.css
│   │   └── editor.css
│   ├── code-tabs/
│   ├── callout/
│   ├── flow/
│   ├── api-request/
│   ├── fields/
│   ├── code-playground/
│   ├── diagram/
│   ├── troubleshooter/
│   ├── terminal-session/
│   ├── result/
│   ├── file-tree/
│   └── prompt/
├── assets/
│   ├── editor-shared.js
│   ├── code.css
│   ├── code-editor.css
│   └── view.js
├── includes/
│   ├── code-surface.php
│   └── patterns.php
└── docspress-blocks.php
```

`assets/` contains only behavior and presentation genuinely shared by multiple blocks. `includes/code-surface.php` is the common server renderer for code surfaces, while `includes/patterns.php` keeps inserter patterns separate from block registration. The root plugin file is only the bootstrap and shared-asset registry.

## Gutenberg serialization

The blocks are dynamic. WordPress stores concise canonical block comments and the plugin renders accessible markup on the front end. A homepage hero can be serialized with every presentation choice kept in block attributes:

```html
<!-- wp:docspress/hero {"title":"Docs that stay connected to your GitHub repo","primaryLabel":"Choose your path","primaryUrl":"#choose-your-path","secondaryLabel":"Latest updates","secondaryUrl":"/#latest-updates","mediaUrl":"https://example.com/hero.png","mediaAlt":"Two project mascots celebrating together."} /-->
```

Starting paths keep each reader’s destination explicit:

```html
<!-- wp:docspress/audience-paths {"anchor":"choose-your-path","align":"wide","compact":false,"paths":[{"title":"I already have Markdown docs","description":"Connect an existing docs folder to WordPress and begin with a safe draft sync.","url":"/docs/publish-existing-docs/","cta":"Publish existing docs","icon":"document","accent":"blue","newTab":false},{"title":"I need to create docs","description":"Generate source-grounded documentation with AI, review it, then publish it.","url":"/docs/create-docs-with-ai/","cta":"Create docs with AI","icon":"sparkles","accent":"gold","newTab":false}]} /-->
```

Set `"compact":true` for task routers inside documentation articles. Compact paths keep the same content, responsive columns, and accessible whole-card links while reducing the panel spacing, card height, and type scale.

The editor exposes the icon registry as a named selector instead of accepting arbitrary text. Existing blocks do not need migration: familiar legacy values such as `MD`, `AI`, `WP`, `DEV`, `AG`, `CLI`, `QA`, and `FIX` resolve to their semantic vector icons at render time. New content stores stable IDs such as `document`, `sparkles`, `site`, `code`, `agency`, `terminal`, `testing`, and `troubleshoot`.

A colorful workflow example looks like this:

```html
<!-- wp:docspress/colorful-code {"language":"yaml","filename":".github/workflows/docs.yml","highlightedLines":"5-6","code":"name: Publish docs\nsteps:\n  - uses: actions/checkout@v4\n  - uses: Automattic/docspress@main"} /-->
```

Code tabs use one `tabs` attribute:

```html
<!-- wp:docspress/code-tabs {"tabs":[{"label":"npm","language":"bash","filename":"Terminal","code":"npx docspress token --site example.com"},{"label":"GitHub CLI","language":"bash","filename":"Terminal","code":"gh secret set WP_ACCESS_TOKEN"}]} /-->
```

Callouts can stay open or become collapsible:

```html
<!-- wp:docspress/callout {"tone":"warning","title":"Protect credentials","content":"<p>Never place access tokens in browser-side examples.</p>","collapsible":false} /-->
```

Flows keep procedures connected while remaining concise Gutenberg comments:

```html
<!-- wp:docspress/flow {"start":1,"steps":[{"title":"Choose","content":"<p>Select the project option.</p>"},{"title":"Configure","content":"<p>Set the required values.</p>"},{"title":"Verify","content":"<p>Confirm the expected result.</p>"}]} /-->
```

API exchanges keep their request and response together. Runnable examples should normally use a relative same-origin GET; external origins require an exact author allow-list, credentials are omitted, and mutating methods require both author enablement and reader confirmation. Long live response bodies use a constrained, keyboard-focusable scroll region:

```html
<!-- wp:docspress/api-request {"method":"GET","endpoint":"/wp-json/","headers":"Accept: application/json","requestBody":"","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"{\n  \"name\": \"WordPress\"\n}","responseBodyFormat":"json","runnable":true,"editable":true,"allowUnsafe":false,"timeout":10000} /-->
```

Terminal sessions distinguish commands from their output, while Result summarizes the outcome:

```html
<!-- wp:docspress/terminal-session {"title":"Publish a preview","shell":"bash","prompt":"$","command":"npx docspress publish ./docs --status=draft","output":"✓ Created 12 draft pages"} /-->

<!-- wp:docspress/result {"status":"success","title":"Preview published","content":"<p>The page tree is ready to review.</p>","meta":"12 pages · 1.8s"} /-->
```

File trees use two spaces per nesting level and a trailing slash for folders. Native folder disclosures can begin open or closed:

```html
<!-- wp:docspress/file-tree {"root":"repository/","tree":"docs/\n  introduction.md\n  api/\n    endpoints.md","caption":"Documentation source tree.","collapsible":true,"open":true} /-->
```

Prompts remain readable, crawlable HTML instead of screenshots or iframes:

```html
<!-- wp:docspress/prompt {"prompt":"Use $docspress-install to review this synchronization logic and propose the smallest safe patch.","model":"GPT-5","mode":"code","thinking":true,"context":"$docspress-install, @repository, src/sync.js, test/sync.test.js","caption":"Synchronization review prompt"} /-->
```

Typed fields, native diagrams, browser sandboxes, and troubleshooting trees remain concise dynamic block comments:

```html
<!-- wp:docspress/fields {"title":"Publish options","fields":[{"name":"site","type":"string","required":true,"description":"WordPress site domain.","defaultValue":"","values":"","deprecated":false}],"searchable":true,"compact":false} /-->

<!-- wp:docspress/diagram {"title":"Publishing flow","type":"flow","source":"Markdown -> DocsPress: collect\nDocsPress -> WordPress: publish","caption":"A theme-native flow."} /-->

<!-- wp:docspress/code-playground {"title":"Live example","html":"<button>Publish</button>","css":"button { color: blue; }","javascript":"console.log( 'Ready' );","height":320,"autoRun":true,"showConsole":true,"allowNetwork":false} /-->

<!-- wp:docspress/troubleshooter {"title":"Find the next step","startId":"source","questions":[{"id":"source","question":"Do docs exist?","yesLabel":"Yes","yesNext":"sync","noLabel":"No","noNext":"generate"}],"outcomes":[{"id":"sync","status":"success","title":"Publish","content":"<p>Run a draft sync.</p>"},{"id":"generate","status":"neutral","title":"Generate docs","content":"<p>Create source-grounded Markdown first.</p>"}],"showProgress":true} /-->
```

The theme's Playground seed at [`../../theme/playground/setup.php`](../../theme/playground/setup.php) creates every example Page as serialized Gutenberg block HTML. The Home page uses Hero and Audience Paths with working publish-existing and create-with-AI roots, while the Kitchen Sink covers every semantic state and meaningful configuration combination across all thirteen documentation blocks. Its live component table lists every plugin installed by the blueprint.

## Run with the theme

From the repository root:

```bash
npx @wp-playground/cli@latest start \
  --path=theme \
  --mount="$(pwd)/plugins/docspress-blocks:/wordpress/wp-content/plugins/docspress-blocks" \
  --blueprint=theme/blueprint.json \
  --port=9400
```

The blueprint activates this mounted plugin before it seeds the demo Pages.
