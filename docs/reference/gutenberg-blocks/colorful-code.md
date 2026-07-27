---
title: Colorful Code
sidebar_position: 30
---

Use `docspress/colorful-code` for one copyable source listing. It supports line highlighting, line numbers, unified diffs, final-state copying, and up to 20 line-specific annotations.

## When to use it

Choose Colorful Code when one example is the lesson. Use [Code Tabs](code-tabs.md) for equivalent alternatives and [Terminal Session](terminal-session.md) for a command with output. Use a normal fenced Markdown code block when highlighting and copy behavior are unnecessary.

## Edit the block

Enter code in the editor and choose its language and display filename. Add one-based highlights such as `2,4-6`. For a change explanation, select unified diff mode and write normal `@@`, `-`, and `+` lines. Add annotations to the exact lines that need explanation.

When `copyMode` is `final`, Copy omits diff metadata and removed lines and strips the leading `+` from added lines. This lets readers copy the resulting file instead of the diff.

## Attributes

<!-- wp:docspress/fields {"title":"Colorful Code attributes","description":"Source, emphasis, and copy behavior accepted by <code>docspress/colorful-code</code>.","fields":[{"name":"language","type":"enum","required":false,"defaultValue":"javascript","description":"<p>Syntax language.</p>","values":"bash, css, html, javascript, json, jsx, markdown, php, plaintext, python, shell, sql, tsx, typescript, yaml","deprecated":false},{"name":"filename","type":"string","required":false,"defaultValue":"","description":"<p>Display label; falls back to the language.</p>","values":"","deprecated":false},{"name":"code","type":"string","required":true,"defaultValue":"JavaScript starter","description":"<p>Plain source text.</p>","values":"","deprecated":false},{"name":"highlightedLines","type":"string","required":false,"defaultValue":"","description":"<p>One-based lines and ranges, such as <code>2,4-6</code>.</p>","values":"","deprecated":false},{"name":"showLineNumbers","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows one-based line numbers.</p>","values":"true, false","deprecated":false},{"name":"caption","type":"string","required":false,"defaultValue":"","description":"<p>Optional formatted caption.</p>","values":"","deprecated":false},{"name":"diffMode","type":"enum","required":false,"defaultValue":"none","description":"<p>Classifies added, removed, and metadata lines.</p>","values":"none, unified","deprecated":false},{"name":"copyMode","type":"enum","required":false,"defaultValue":"all","description":"<p>Copies the entire listing or only the final diff state.</p>","values":"all, final","deprecated":false},{"name":"annotations","type":"array","required":false,"defaultValue":"","description":"<p>Up to 20 objects with a one-based <code>line</code> and formatted <code>content</code>.</p>","values":"0–20 items","deprecated":false}],"searchable":true,"compact":true} /-->

Each annotation uses a line number from 1–9999 and formatted `content`. Blank annotations are discarded. Extremely large highlight ranges are bounded during rendering.

## Standard example

<!-- wp:docspress/colorful-code {"language":"javascript","filename":"publish.js","code":"const result = await publish();\nconsole.log(result);","highlightedLines":"1","showLineNumbers":true,"caption":"A single highlighted example."} /-->

## Diff with an annotation

<!-- wp:docspress/colorful-code {"language":"json","filename":"response.diff","code":"@@ page @@\n-  \"status\": \"draft\"\n+  \"status\": \"publish\"","showLineNumbers":true,"diffMode":"unified","copyMode":"final","annotations":[{"line":3,"content":"<p>This is the final status copied by the reader.</p>"}],"caption":"An annotated response change."} /-->

## Accessible focus style

<!-- wp:docspress/colorful-code {"language":"css","filename":"focus.css","code":".docs-link {\n  color: #3858e9;\n}\n\n.docs-link:focus-visible {\n  outline: 3px solid currentColor;\n  outline-offset: 4px;\n}","highlightedLines":"5-7","showLineNumbers":true,"caption":"The keyboard-only focus treatment is highlighted and explained.","annotations":[{"line":5,"content":"<p><code>:focus-visible</code> avoids drawing the custom ring for ordinary pointer clicks.</p>"},{"line":7,"content":"<p>The offset keeps the ring separate from the link shape.</p>"}],"diffMode":"none","copyMode":"all"} /-->

## Published behavior and accessibility

The source area can receive keyboard focus for horizontal scrolling. Copy has an accessible status announcement. Annotation controls are buttons connected to their explanation panels, and the source remains readable when JavaScript is unavailable.

Use annotations only for details that are hard to explain before or after the example. Verify that line numbers still match after every edit, and never put secrets or production credentials in source examples.

<!-- wp:docspress/callout {"tone":"warning","title":"Keep annotations synchronized","content":"<p>Annotation line numbers do not follow code edits automatically. Recheck every annotation after inserting, deleting, or reordering lines.</p>","collapsible":false,"open":true} /-->
