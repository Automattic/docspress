---
title: Guides
sidebar_position: 40
sidebar_collapsed: false
---

Move from a reviewed manual draft to maintainable synchronization in either direction, then add routing controls only when the directory tree is not enough.

## Available guides

- [Make documentation AI-friendly](ai-friendly-documentation.md) explains how `llms.txt` and exact per-Page Markdown routes make published documentation easier for agents and retrieval systems to discover and consume.
- [Collect feedback with Was This Helpful](was-this-helpful.md) shows how to customize the visitor prompt, review per-Page totals, control visibility, and extend the aggregate response hook.
- [Customize the theme in the Site Editor](customize-theme.md) shows how to make the complete site highly customizable with Global Style variations, templates, template parts, navigation, command search, comments, and per-block controls.
- [GitHub to WordPress](github-to-wordpress.md) explains how merged Markdown creates, updates, or removes managed WordPress Pages.
- [WordPress to GitHub](wordpress-to-github.md) explains how Gutenberg edits become Markdown changes on a rolling pull request.
- [Keep documentation synchronized](continuous-sync.md) combines both directions with conflict detection and merge-loop prevention.
- [Use manifests and redirects](manifests-and-redirects.md) covers stable routes, explicit parents, virtual section Pages, and moved-page placeholders.
- [Use contextual sidebars](contextual-sidebars.md) keeps the simple automatic tree as the default and adds route-scoped navigation for larger documentation sites.
- [Version API documentation](versioning.md) publishes root, directory, suffix, or manifest-backed API releases with native WordPress management, clean latest routes, customizable switching, and exact reverse-sync destinations.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Prefer the directory tree",
    "content": "\u003cp\u003eUse normal files and folders until you need a stable route independent of a source filename. Manifests add control, but also add another mapping to maintain.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!TIP]
>
> **Prefer the directory tree**
>
> Use normal files and folders until you need a stable route independent of a source filename. Manifests add control, but also add another mapping to maintain.
<!-- /docspress:block -->
