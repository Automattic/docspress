---
title: Authoring documentation
sidebar_position: 30
sidebar_collapsed: true
---

Write ordinary Markdown for ordinary content and use Autodocs Publish blocks where documentation needs richer semantics such as commands, results, API exchanges, or reusable prompts.

## Authoring contract

- Put the documentation root at `docs/index.md`.
- Use folder `index.md` files for intentional section landing pages.
- Add frontmatter `title` and begin body sections at `##` when the Autodocs Publish theme renders the Page title.
- Link pages with relative Markdown paths; Autodocs Publish rewrites known documentation routes by default.
- Use real, verified examples with fake credentials and non-production identifiers.
- Keep `autodocs-publish:block` config valid and update its semantic Markdown preview with it.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/callout",
  "attrs": {
    "tone": "note",
    "title": "One Markdown tree, two block systems",
    "content": "\u003cp\u003eCommon Markdown becomes WordPress core blocks. Readable Autodocs Publish envelopes become custom dynamic blocks rendered by the companion plugin.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!NOTE]
>
> **One Markdown tree, two block systems**
>
> Common Markdown becomes WordPress core blocks. Readable Autodocs Publish envelopes become custom dynamic blocks rendered by the companion plugin.
<!-- /autodocs-publish:block -->

## Learn the formats

- [Page structure and routes](page-structure.md) explains filenames, titles, parent Pages, links, manifests, and edit links.
- [Markdown and Gutenberg](markdown-and-gutenberg.md) maps Markdown features and explains when to choose each Autodocs Publish block.
- [Autodocs Publish block reference](../reference/gutenberg-blocks/index.md) lists exact attributes and allowed values.
- [Kitchen Sink](../reference/kitchen-sink.md) exercises every custom block and semantic state.
