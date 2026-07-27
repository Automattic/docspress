---
title: Diagram
sidebar_position: 70
---

Use `docspress/diagram` to turn a compact relationship list into an accessible flow or sequence SVG. It is designed for small architecture and request-flow explanations, with no browser diagram library.

## When to use it

Choose Diagram when the relationship among actors is harder to understand in prose. Use [Flow](flow.md) for instructions readers perform and [File Tree](file-tree.md) for hierarchy. For a large architecture, split the subject into several focused diagrams.

## Write diagram source

Write one relationship per line:

```text
Source -> Target: optional label
```

Lines beginning with `#` are comments. The parser considers the first 30 source lines and renders at most eight actors and 24 relationships. Extra relationships are discarded, so keep the model deliberately small.

## Attributes

<!-- wp:docspress/fields {"title":"Diagram attributes","description":"Labels and relationship source accepted by <code>docspress/diagram</code>.","fields":[{"name":"title","type":"string","required":false,"defaultValue":"Publishing flow","description":"<p>Plain-text heading and accessible diagram label.</p>","values":"","deprecated":false},{"name":"type","type":"enum","required":false,"defaultValue":"flow","description":"<p>Visual arrangement of actors and connections.</p>","values":"flow, sequence","deprecated":false},{"name":"source","type":"string","required":true,"defaultValue":"Starter relationships","description":"<p>One <code>Source -> Target: label</code> relationship per line.</p>","values":"First 30 lines, 8 actors, 24 relationships","deprecated":false},{"name":"caption","type":"string","required":false,"defaultValue":"","description":"<p>Optional formatted explanation below the SVG.</p>","values":"","deprecated":false}],"searchable":false,"compact":true} /-->

The block supports `wide` alignment in addition to the [shared design controls](index.md#add-and-edit-a-block).

## Creative examples

### Documentation publishing sequence

<!-- wp:docspress/diagram {"title":"Documentation publishing flow","type":"sequence","source":"Author -> DocsPress: commit Markdown\nDocsPress -> WordPress: publish blocks\nWordPress -> Reader: serve documentation","caption":"The source remains concise and editable in Gutenberg."} /-->

### Reader feedback loop

<!-- wp:docspress/diagram {"title":"From reader question to verified improvement","type":"flow","source":"Reader -> Support: ask a question\nSupport -> Issue: capture the missing detail\nIssue -> Author: assign the documentation change\nAuthor -> Preview: publish a draft\nPreview -> Reader: validate the answer\nReader -> Issue: confirm resolution","caption":"A feedback loop makes the reader part of documentation quality."} /-->

### Cache-miss request sequence

<!-- wp:docspress/diagram {"title":"What happens on a documentation cache miss","type":"sequence","source":"Browser -> Edge: request page\nEdge -> WordPress: cache miss\nWordPress -> Database: load Page and navigation\nDatabase -> WordPress: return content\nWordPress -> Edge: render response\nEdge -> Browser: cache and deliver","caption":"The sequence keeps infrastructure actors and response direction explicit."} /-->

## Published behavior and accessibility

DocsPress produces a theme-native SVG with an image role and accessible label. Labels are plain text, escaped before rendering, and remain available in the Markdown source. No third-party diagram code runs in the browser.

Use distinct actor names, short edge labels, and one direction of reading. Describe the important conclusion in nearby prose so the page does not depend on vision alone.
