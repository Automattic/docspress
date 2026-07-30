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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Colorful Code attributes",
    "description": "Source, emphasis, and copy behavior accepted by \u003ccode\u003edocspress/colorful-code\u003c/code\u003e.",
    "fields": [
      {
        "name": "language",
        "type": "enum",
        "required": false,
        "defaultValue": "javascript",
        "description": "\u003cp\u003eSyntax language.\u003c/p\u003e",
        "values": "bash, css, html, javascript, json, jsx, markdown, php, plaintext, python, shell, sql, tsx, typescript, yaml",
        "deprecated": false
      },
      {
        "name": "filename",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eDisplay label; falls back to the language.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "code",
        "type": "string",
        "required": true,
        "defaultValue": "JavaScript starter",
        "description": "\u003cp\u003ePlain source text.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "highlightedLines",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOne-based lines and ranges, such as \u003ccode\u003e2,4-6\u003c/code\u003e.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "showLineNumbers",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows one-based line numbers.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "caption",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional formatted caption.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "diffMode",
        "type": "enum",
        "required": false,
        "defaultValue": "none",
        "description": "\u003cp\u003eClassifies added, removed, and metadata lines.\u003c/p\u003e",
        "values": "none, unified",
        "deprecated": false
      },
      {
        "name": "copyMode",
        "type": "enum",
        "required": false,
        "defaultValue": "all",
        "description": "\u003cp\u003eCopies the entire listing or only the final diff state.\u003c/p\u003e",
        "values": "all, final",
        "deprecated": false
      },
      {
        "name": "annotations",
        "type": "array",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eUp to 20 objects with a one-based \u003ccode\u003eline\u003c/code\u003e and formatted \u003ccode\u003econtent\u003c/code\u003e.\u003c/p\u003e",
        "values": "0–20 items",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Colorful Code attributes

Source, emphasis, and copy behavior accepted by <code>docspress/colorful-code</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `language` | enum | No | javascript | <p>Syntax language.</p> |
| `filename` | string | No |  | <p>Display label; falls back to the language.</p> |
| `code` | string | Yes | JavaScript starter | <p>Plain source text.</p> |
| `highlightedLines` | string | No |  | <p>One-based lines and ranges, such as <code>2,4-6</code>.</p> |
| `showLineNumbers` | boolean | No | true | <p>Shows one-based line numbers.</p> |
| `caption` | string | No |  | <p>Optional formatted caption.</p> |
| `diffMode` | enum | No | none | <p>Classifies added, removed, and metadata lines.</p> |
| `copyMode` | enum | No | all | <p>Copies the entire listing or only the final diff state.</p> |
| `annotations` | array | No |  | <p>Up to 20 objects with a one-based <code>line</code> and formatted <code>content</code>.</p> |
<!-- /docspress:block -->

Each annotation uses a line number from 1–9999 and formatted `content`. Blank annotations are discarded. Extremely large highlight ranges are bounded during rendering.

## Standard example

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "javascript",
    "filename": "publish.js",
    "code": "const result = await publish();\nconsole.log(result);",
    "highlightedLines": "1",
    "showLineNumbers": true,
    "caption": "A single highlighted example."
  }
}
-->
**publish.js — A single highlighted example.**

```javascript
const result = await publish();
console.log(result);
```
<!-- /docspress:block -->

## Diff with an annotation

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "json",
    "filename": "response.diff",
    "code": "@@ page @@\n-  \"status\": \"draft\"\n+  \"status\": \"publish\"",
    "showLineNumbers": true,
    "diffMode": "unified",
    "copyMode": "final",
    "annotations": [
      {
        "line": 3,
        "content": "\u003cp\u003eThis is the final status copied by the reader.\u003c/p\u003e"
      }
    ],
    "caption": "An annotated response change."
  }
}
-->
**response.diff — An annotated response change.**

```json
@@ page @@
-  "status": "draft"
+  "status": "publish"
```
<!-- /docspress:block -->

## Accessible focus style

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "css",
    "filename": "focus.css",
    "code": ".docs-link {\n  color: #3858e9;\n}\n\n.docs-link:focus-visible {\n  outline: 3px solid currentColor;\n  outline-offset: 4px;\n}",
    "highlightedLines": "5-7",
    "showLineNumbers": true,
    "caption": "The keyboard-only focus treatment is highlighted and explained.",
    "annotations": [
      {
        "line": 5,
        "content": "\u003cp\u003e\u003ccode\u003e:focus-visible\u003c/code\u003e avoids drawing the custom ring for ordinary pointer clicks.\u003c/p\u003e"
      },
      {
        "line": 7,
        "content": "\u003cp\u003eThe offset keeps the ring separate from the link shape.\u003c/p\u003e"
      }
    ],
    "diffMode": "none",
    "copyMode": "all"
  }
}
-->
**focus.css — The keyboard-only focus treatment is highlighted and explained.**

```css
.docs-link {
  color: #3858e9;
}

.docs-link:focus-visible {
  outline: 3px solid currentColor;
  outline-offset: 4px;
}
```
<!-- /docspress:block -->

## Published behavior and accessibility

The source area can receive keyboard focus for horizontal scrolling. Copy has an accessible status announcement. Annotation controls are buttons connected to their explanation panels, and the source remains readable when JavaScript is unavailable.

Use annotations only for details that are hard to explain before or after the example. Verify that line numbers still match after every edit, and never put secrets or production credentials in source examples.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "Keep annotations synchronized",
    "content": "\u003cp\u003eAnnotation line numbers do not follow code edits automatically. Recheck every annotation after inserting, deleting, or reordering lines.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!WARNING]
>
> **Keep annotations synchronized**
>
> Annotation line numbers do not follow code edits automatically. Recheck every annotation after inserting, deleting, or reordering lines.
<!-- /docspress:block -->
