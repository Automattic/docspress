---
title: DocsPress Gutenberg blocks
sidebar_position: 30
sidebar_collapsed: true
---

DocsPress Blocks adds two landing-page blocks, thirteen documentation blocks, and two version interface blocks to Gutenberg. Use these guides to choose a block, configure it in the editor, or serialize it directly in Markdown for DocsPress publishing.

Every component guide includes at least three rendered examples: a practical baseline plus creative variations that exercise different content, presentation, or interaction states.

## Block catalog

| Block | Best for |
| --- | --- |
| [Hero](hero.md) | A prominent landing-page introduction with actions and media |
| [Audience Paths](audience-paths.md) | Routing readers to distinct documentation journeys |
| [Colorful Code](colorful-code.md) | One highlighted, annotated, or diff-aware code example |
| [Code Tabs](code-tabs.md) | Equivalent examples in multiple languages or tools |
| [Callout](callout.md) | Notes, tips, warnings, risks, and success messages |
| [Flow](flow.md) | A connected, numbered procedure |
| [Diagram](diagram.md) | A compact flow or sequence diagram |
| [API Request / Response](api-request.md) | A static or runnable HTTP exchange |
| [Fields / Schema](fields.md) | Parameters, properties, options, and environment variables |
| [Terminal Session](terminal-session.md) | A copyable command with optional read-only output |
| [Live Code Playground](code-playground.md) | Editable HTML, CSS, and JavaScript with a sandboxed preview |
| [Result](result.md) | The outcome of a task, check, or deployment |
| [File Tree](file-tree.md) | A compact, optionally collapsible project structure |
| [Prompt](prompt.md) | A reusable AI prompt with model, mode, and context |
| [Interactive Troubleshooter](troubleshooter.md) | A short branching decision tree |
| [Version Switcher](../../guides/versioning.md#customize-the-version-interface) | Moving between API versions while preserving the current logical Page |
| [Version Notice](../../guides/versioning.md#customize-the-version-interface) | Warning readers when they are viewing historical documentation |

Open the [Kitchen Sink](../kitchen-sink.md) to see all blocks together and compare their visual states.

## Add and edit a block

1. Open a Page or template in the WordPress block editor.
2. Select **Add block** and search for the block by its display name.
3. Edit primary content directly in the canvas.
4. Use the Block sidebar for structure, behavior, and presentation settings.
5. Preview keyboard interaction and responsive layout before publishing.

All DocsPress blocks support an HTML anchor, additional CSS classes, Global Styles colors and typography, spacing, borders, minimum height, sticky positioning, and shadows. Hero and Audience Paths also support wide and full alignment. Diagram, Live Code Playground, and Interactive Troubleshooter support wide alignment.

## Author blocks in Markdown

DocsPress preserves a dynamic block as a versioned config envelope with a block-specific Markdown preview:

````markdown
<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "note",
    "title": "Good to know",
    "content": "<p>Readable in both places.</p>"
  }
}
-->
> [!NOTE]
>
> **Good to know**
>
> Readable in both places.
<!-- /docspress:block -->
````

Follow these rules when generating or editing an envelope:

- Keep the boundary comments outside code fences for an actual block.
- Use valid JSON config. Escape quotes, newlines, backslashes, and control characters.
- Use only attributes registered by that block and only documented enum values.
- Update config and its Markdown preview together; config is authoritative during conversion.
- Keep rich-text attributes as safe HTML fragments, such as `<p>Helpful detail.</p>`.
- Install and activate a matching version of DocsPress Blocks on the WordPress site.

Legacy self-closing syntax remains accepted and can be migrated with `npm run migrate:markdown-blocks`:

```html
<!-- wp:docspress/block-name {"attribute":"value"} /-->
```

DocsPress converts HTML-sensitive attribute characters to WordPress-safe Unicode escapes during publishing. This prevents the block editor from treating otherwise equivalent JSON as invalid content.

## Choose the smallest useful block

Prefer ordinary Markdown headings, paragraphs, lists, and tables until a DocsPress block provides a concrete benefit: safer copy actions, semantic status, controlled interaction, responsive visualization, or editor-managed structured data. A smaller block is easier to maintain than a composite that repeats the same information.

Use [Colorful Code](colorful-code.md) for one source listing and [Code Tabs](code-tabs.md) only for genuine alternatives. Use [Terminal Session](terminal-session.md) for a command readers copy but do not run in the page. Use [API Request / Response](api-request.md) when the HTTP method, headers, body, response, or live execution is part of the lesson.
