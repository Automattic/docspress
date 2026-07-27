---
title: Kitchen Sink
---

This Page is the acceptance surface for the DocsPress theme and all fifteen DocsPress Blocks. Switch Global Style variations and light/dark mode while checking native color, typography, spacing, border, dimensions, interactions, and content controls.

<!-- wp:docspress/callout {"tone":"note","title":"Playground acceptance page","content":"<p>The local Playground appends its live component inventory to this source-backed Page after seeding it.</p>","collapsible":false} /-->

## Audience Paths

<!-- wp:docspress/audience-paths {"compact":true,"eyebrow":"Choose a starting point","title":"Where are your docs today?","description":"Independent documentation roots can meet readers at the state of their repository.","paths":[{"title":"I already have Markdown docs","description":"Connect an existing docs folder and begin with a safe draft sync.","url":"/docs/publish-existing-docs/","cta":"Publish existing docs","icon":"document","accent":"blue","newTab":false},{"title":"I need to create docs","description":"Generate source-grounded documentation with AI before publishing.","url":"/docs/create-docs-with-ai/","cta":"Create docs with AI","icon":"sparkles","accent":"gold","newTab":false},{"title":"I'm evaluating DocsPress","description":"Review the product benefits and open a working preview before changing a repository.","url":"/docs/why-docspress/","cta":"Explore why DocsPress","icon":"compass","accent":"green","newTab":false}],"columns":3,"tone":"ink","textAlign":"left","showNumbers":true} /-->

## Colorful Code

<!-- wp:docspress/colorful-code {"language":"yaml","filename":".github/workflows/sync-docs.yml","code":"name: Sync docs\non:\n  push:\n    paths: [\"docs/**\"]\nsteps:\n  - uses: Automattic/docspress@COMMIT_SHA","highlightedLines":"2-4,6","showLineNumbers":true,"caption":"Filename, language, line numbers, highlighted ranges, caption, and copy."} /-->

<!-- wp:docspress/colorful-code {"language":"plaintext","filename":"Without line numbers","code":"Markdown in.\nWordPress out.","highlightedLines":"","showLineNumbers":false,"caption":"Plain text with line numbers disabled."} /-->

<!-- wp:docspress/colorful-code {"language":"json","filename":"page-response.diff","code":"@@ page 43 @@\n-  \"status\": \"draft\"\n+  \"status\": \"publish\"\n+  \"modified\": \"2026-07-27T07:40:00Z\"","highlightedLines":"","showLineNumbers":true,"caption":"Unified diff colors, final-state copy, and line-level explanations.","diffMode":"unified","copyMode":"final","annotations":[{"line":3,"content":"<p>The published status is the value retained by <strong>Copy</strong>.</p>"},{"line":4,"content":"<p>The modification time comes from the live response.</p>"}]} /-->

## Code Tabs

<!-- wp:docspress/code-tabs {"tabs":[{"label":"npm","language":"bash","filename":"Terminal","code":"npm install docspress"},{"label":"pnpm","language":"bash","filename":"Terminal","code":"pnpm add docspress"},{"label":"Yarn","language":"bash","filename":"Terminal","code":"yarn add docspress"},{"label":"Bun","language":"bash","filename":"Terminal","code":"bun add docspress"},{"label":"JavaScript","language":"javascript","filename":"example.js","code":"console.log('DocsPress');"},{"label":"PHP","language":"php","filename":"example.php","code":"<?php echo 'DocsPress';"},{"label":"Python","language":"python","filename":"example.py","code":"print('DocsPress')"},{"label":"JSON","language":"json","filename":"example.json","code":"{ \"name\": \"DocsPress\" }"}],"showLineNumbers":false,"caption":"The maximum eight compact tabs with independent labels, languages, filenames, and code."} /-->

## Callouts

<!-- wp:docspress/callout {"tone":"note","title":"Note","content":"<p>Neutral context that belongs beside the current step.</p>","collapsible":false} /-->

<!-- wp:docspress/callout {"tone":"tip","title":"Tip","content":"<p>A useful shortcut or recommended practice.</p>","collapsible":false} /-->

<!-- wp:docspress/callout {"tone":"warning","title":"Warning","content":"<p>A condition readers should check before continuing.</p>","collapsible":false} /-->

<!-- wp:docspress/callout {"tone":"danger","title":"Danger","content":"<p>A destructive or security-sensitive action.</p>","collapsible":false} /-->

<!-- wp:docspress/callout {"tone":"success","title":"Success","content":"<p>A confirmed positive state or completed milestone.</p>","collapsible":false} /-->

<!-- wp:docspress/callout {"tone":"note","title":"Collapsible and open","content":"<p>Readers can hide this longer explanation.</p>","collapsible":true,"open":true} /-->

<!-- wp:docspress/callout {"tone":"tip","title":"Collapsible and closed","content":"<p>This content begins hidden and remains keyboard accessible.</p>","collapsible":true,"open":false} /-->

## Flow

<!-- wp:docspress/flow {"start":1,"steps":[{"title":"Template","content":"<p>Choose <code>full</code> or <code>empty</code> for the starting content.</p>"},{"title":"Deploy target","content":"<p>Select the host that matches the project environment.</p>"},{"title":"Install dependencies","content":"<p>Let the package manager finish, then verify the generated site.</p>"}]} /-->

## Diagram

<!-- wp:docspress/diagram {"title":"Documentation publishing flow","type":"flow","source":"Markdown -> DocsPress: collect\nDocsPress -> WordPress: publish\nWordPress -> Reader: serve","caption":"A flow diagram rendered as accessible, theme-native SVG without a third-party runtime."} /-->

<!-- wp:docspress/diagram {"title":"Runnable request lifecycle","type":"sequence","source":"Reader -> API Console: run GET\nAPI Console -> WordPress: fetch without credentials\nWordPress -> API Console: JSON response\nAPI Console -> Reader: format status and body","caption":"Sequence mode uses the same compact, editable relationship syntax."} /-->

## API Request / Response

<!-- wp:docspress/api-request {"method":"GET","endpoint":"/wp-json/","headers":"Accept: application/json","requestBody":"","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"{\n  \"name\": \"DocsPress\",\n  \"namespaces\": [\"wp/v2\"]\n}","responseBodyFormat":"json","runnable":true,"editable":true,"allowUnsafe":false,"baseUrl":"","allowedOrigins":"","timeout":10000} /-->

<!-- wp:docspress/api-request {"method":"POST","endpoint":"/wp-json/wp/v2/pages","headers":"Content-Type: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN","requestBody":"{\n  \"title\": \"API reference\",\n  \"status\": \"draft\"\n}","requestBodyFormat":"json","responseStatus":"201 Created","responseBody":"{\n  \"id\": 43,\n  \"status\": \"draft\"\n}","responseBodyFormat":"json"} /-->

<!-- wp:docspress/api-request {"method":"PUT","endpoint":"/wp-json/wp/v2/pages/43","headers":"Content-Type: application/x-www-form-urlencoded","requestBody":"title=REST+API+Reference","requestBodyFormat":"raw","responseStatus":"200 OK","responseBody":"Updated page 43: REST API Reference","responseBodyFormat":"raw"} /-->

<!-- wp:docspress/api-request {"method":"PATCH","endpoint":"/wp-json/wp/v2/pages/43","headers":"Content-Type: application/json","requestBody":"{ \"status\": \"publish\" }","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"{ \"id\": 43, \"status\": \"publish\" }","responseBodyFormat":"json"} /-->

<!-- wp:docspress/api-request {"method":"DELETE","endpoint":"/wp-json/wp/v2/pages/43?force=true","headers":"Authorization: Bearer $WP_ACCESS_TOKEN","requestBody":"","requestBodyFormat":"raw","responseStatus":"204 No Content","responseBody":"","responseBodyFormat":"raw"} /-->

## Fields / Schema

<!-- wp:docspress/fields {"title":"Publish options","description":"Typed request fields, defaults, constraints, and lifecycle states remain scannable as the reference grows.","fields":[{"name":"site","type":"string","required":true,"defaultValue":"","description":"WordPress site domain or numeric site ID.","values":"","deprecated":false},{"name":"status","type":"enum","required":false,"defaultValue":"draft","description":"Publication status for synchronized Pages.","values":"draft, publish, private","deprecated":false},{"name":"dryRun","type":"boolean","required":false,"defaultValue":"false","description":"Preview reconciliation without writing changes.","values":"true, false","deprecated":false},{"name":"legacyMode","type":"boolean","required":false,"defaultValue":"","description":"Use the pre-block-theme publishing pipeline.","values":"true, false","deprecated":true}],"searchable":true,"compact":false} /-->

## Terminal Session

<!-- wp:docspress/terminal-session {"title":"Run package verification","shell":"bash","prompt":"$","command":"npm run package","output":"Lint passed\nTests passed\nAction bundle rebuilt"} /-->

<!-- wp:docspress/terminal-session {"title":"Inspect the site","shell":"wp-cli","prompt":">","command":"wp option get docspress_playground_runtime --format=json","output":""} /-->

## Live Code Playground

<!-- wp:docspress/code-playground {"title":"Interactive publish button","html":"<button class=\"demo-button\">Publish docs</button>\n<p class=\"status\" aria-live=\"polite\">Ready</p>","css":".demo-button {\n  padding: 0.75rem 1rem;\n  border: 0;\n  border-radius: 0.4rem;\n  background: #3858e9;\n  color: white;\n  font: inherit;\n}\n.status { font-family: ui-monospace, monospace; }","javascript":"const button = document.querySelector( '.demo-button' );\nbutton.addEventListener( 'click', () => {\n  document.querySelector( '.status' ).textContent = 'Published';\n  console.log( 'Documentation published' );\n} );","height":340,"autoRun":true,"showConsole":true,"allowNetwork":false} /-->

## Result

<!-- wp:docspress/result {"status":"success","title":"All checks passed","content":"<p>The generated Pages match the repository tree.</p>","meta":"20 pages"} /-->

<!-- wp:docspress/result {"status":"neutral","title":"No changes required","content":"<p>WordPress already matches the current commit.</p>","meta":"0 updates"} /-->

<!-- wp:docspress/result {"status":"warning","title":"Drafts need review","content":"<p>Three new Pages are waiting for editorial approval.</p>","meta":"3 drafts"} /-->

<!-- wp:docspress/result {"status":"error","title":"Synchronization failed","content":"<p>The access token cannot create Pages on this site.</p>","meta":"HTTP 403"} /-->

## File Tree

<!-- wp:docspress/file-tree {"root":"Automattic/docspress/","tree":".claude/\n  skills/\ndocs/\n  index.md\n  publish-existing-docs/\n  create-docs-with-ai/\n  reference/\n    kitchen-sink.md\nplugins/\n  docspress-blocks/\ntheme/\npackage.json","caption":"Nested folders, files, a custom root label, and caption.","collapsible":true,"open":true} /-->

<!-- wp:docspress/file-tree {"root":"docs/","tree":"index.md\nguides/\n  continuous-sync.md\nreference/\n  gutenberg-blocks/\n    index.md\n    api-request.md\n    fields.md\ntroubleshooting.md","caption":"A compact tree that begins collapsed.","collapsible":true,"open":false} /-->

## Prompt

<!-- wp:docspress/prompt {"prompt":"Explain DocsPress to a new contributor in three short paragraphs.","model":"GPT-5","mode":"chat","thinking":false,"context":"@documentation, https://github.com/Automattic/docspress","caption":"Contributor explainer"} /-->

<!-- wp:docspress/prompt {"prompt":"Review the synchronization function for race conditions. Return risks first, then the smallest safe patch.","model":"Claude Sonnet","mode":"code","thinking":true,"context":"@repository, src/sync.js, test/sync.test.js, #trace","caption":"Race-condition review"} /-->

<!-- wp:docspress/prompt {"prompt":"Which Action inputs affect the generated WordPress Page hierarchy?","model":"Gemini Pro","mode":"ask","thinking":false,"context":"action.yml, src/docs.js","caption":"Configuration question"} /-->

<!-- wp:docspress/prompt {"prompt":"Use $docspress-install to create a phased migration plan from a static documentation site to DocsPress.","model":"Planning agent","mode":"plan","thinking":true,"context":"$docspress-install, docs/, #architecture, https://example.com/current-docs","caption":"Migration plan with a skill reference"} /-->

## Interactive Troubleshooter

<!-- wp:docspress/troubleshooter {"title":"Choose the right publishing workflow","intro":"Answer two quick questions to reach a source-grounded next step.","startId":"source","questions":[{"id":"source","question":"Do you already have Markdown documentation?","yesLabel":"Yes, the docs exist","yesNext":"connected","noLabel":"Not yet","noNext":"generate"},{"id":"connected","question":"Is the repository connected to WordPress?","yesLabel":"Yes, it is connected","yesNext":"sync","noLabel":"No, connect it","noNext":"install"}],"outcomes":[{"id":"install","status":"warning","title":"Connect the publishing target","content":"<p>Run the DocsPress installer, add the WordPress access token, and verify the repository connection before publishing.</p>"},{"id":"sync","status":"success","title":"Publish the documentation","content":"<p>Run the sync command, review the proposed changes, and verify the rendered documentation on WordPress.</p>"},{"id":"generate","status":"neutral","title":"Generate a documentation starter","content":"<p>Generate a small documentation tree from the source, then review every example against the implementation before publishing.</p>"}],"showProgress":true} /-->

<!-- wp:docspress/result {"status":"success","title":"Kitchen Sink complete","content":"<p>Every DocsPress documentation block, semantic variant, and meaningful option is represented on this Page. Hero and Audience Paths are the design-focused landing blocks.</p>","meta":"13 documentation blocks · 2 landing blocks"} /-->

## Playground runtime

This inventory is generated from the running WordPress installation.

| Component | Type | Version | Status |
| --- | --- | --- | --- |
| WordPress | Core | 7.0.2 | Running |
| DocsPress | Theme | 0.9.17 | Active |
| Akismet Anti-spam: Spam Protection | Plugin | 5.7 | Active |
| DocsPress Blocks | Plugin | 0.9.12 | Active |
| Jetpack | Plugin | 16.1-a.3 | Active |
