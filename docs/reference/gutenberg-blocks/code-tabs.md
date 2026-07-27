---
title: Code Tabs
sidebar_position: 40
---

Use `docspress/code-tabs` for equivalent examples readers can switch between, such as package managers, programming languages, operating systems, or API clients.

## When to use it

Choose Code Tabs only when every tab accomplishes the same task. Use [Colorful Code](colorful-code.md) when examples build on one another or need highlights, diffs, or annotations. Separate sequential examples with headings instead of hiding steps in tabs.

## Edit the block

Add, remove, and reorder tabs in Gutenberg. Give each tab a short unique label, select its language, and optionally set a filename. Keep the same conceptual scope and expected result across tabs.

## Attributes

<!-- wp:docspress/fields {"title":"Code Tabs attributes","description":"Alternatives and presentation accepted by <code>docspress/code-tabs</code>.","fields":[{"name":"tabs","type":"array","required":true,"defaultValue":"Two starter tabs","description":"<p>Up to eight equivalent source examples.</p>","values":"1–8 items","deprecated":false},{"name":"showLineNumbers","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows one-based line numbers in every panel.</p>","values":"true, false","deprecated":false},{"name":"caption","type":"string","required":false,"defaultValue":"","description":"<p>Optional formatted caption for the complete tab set.</p>","values":"","deprecated":false}],"searchable":false,"compact":true} /-->

Each `tabs` item accepts:

<!-- wp:docspress/fields {"title":"Tab object","description":"Fields for one equivalent source example.","fields":[{"name":"label","type":"string","required":true,"defaultValue":"","description":"<p>Short, unique tab label.</p>","values":"","deprecated":false},{"name":"language","type":"enum","required":false,"defaultValue":"plaintext","description":"<p>Syntax language. Invalid values normalize to plaintext.</p>","values":"bash, css, html, javascript, json, jsx, markdown, php, plaintext, python, shell, sql, tsx, typescript, yaml","deprecated":false},{"name":"filename","type":"string","required":false,"defaultValue":"","description":"<p>Optional display filename.</p>","values":"","deprecated":false},{"name":"code","type":"string","required":true,"defaultValue":"","description":"<p>Plain source text.</p>","values":"","deprecated":false}],"searchable":false,"compact":true} /-->

Invalid languages fall back to `plaintext`. Only the first eight valid tabs render. An empty tab array renders no block.

## Creative examples

### Package-manager alternatives

<!-- wp:docspress/code-tabs {"tabs":[{"label":"npm","language":"bash","filename":"Terminal","code":"npm install docspress"},{"label":"pnpm","language":"bash","filename":"Terminal","code":"pnpm add docspress"}],"showLineNumbers":false,"caption":"Equivalent package-manager commands."} /-->

### Fetch one documentation Page

<!-- wp:docspress/code-tabs {"tabs":[{"label":"cURL","language":"bash","filename":"Terminal","code":"curl https://example.test/wp-json/wp/v2/pages/42"},{"label":"JavaScript","language":"javascript","filename":"fetch-page.js","code":"const response = await fetch( 'https://example.test/wp-json/wp/v2/pages/42' );\nconst page = await response.json();"},{"label":"Python","language":"python","filename":"fetch_page.py","code":"import requests\n\npage = requests.get(\n    'https://example.test/wp-json/wp/v2/pages/42',\n    timeout=10,\n).json()"}],"showLineNumbers":true,"caption":"The same read-only request in three clients, using a non-production hostname."} /-->

### Express one configuration in three formats

<!-- wp:docspress/code-tabs {"tabs":[{"label":"JSON","language":"json","filename":"docspress.json","code":"{\n  \"docsDir\": \"docs\",\n  \"status\": \"draft\"\n}"},{"label":"YAML","language":"yaml","filename":"docspress.yml","code":"docsDir: docs\nstatus: draft"},{"label":"PHP","language":"php","filename":"docspress.php","code":"<?php\nreturn [\n    'docsDir' => 'docs',\n    'status' => 'draft',\n];"}],"showLineNumbers":true,"caption":"Equivalent configuration values in formats used by different toolchains."} /-->

## Published behavior and accessibility

The block uses a semantic tab list and tab panels. Readers can move between tabs with the keyboard, and only the active panel participates in the normal tab order. Copy operates on the active example and announces its result.

Put the most broadly useful choice first. Keep tab labels compact, avoid more than four choices when possible, and confirm that every example remains functionally equivalent after updates.

<!-- wp:docspress/callout {"tone":"note","title":"Tabs are alternatives, not steps","content":"<p>Every tab should solve the same task. If readers must use more than one panel, present the examples sequentially instead.</p>","collapsible":false,"open":true} /-->
