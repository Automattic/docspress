---
title: Create docs with AI
sidebar_position: 20
sidebar_collapsed: true
---

Build a verified Markdown documentation tree from the repository itself, review it, and then publish it through the same safe DocsPress workflow.

## 1. Add the DocsPress skills

Run this in the repository before asking your coding agent to create the documentation:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Add DocsPress skills",
    "shell": "bash",
    "prompt": "$",
    "command": "npx skills add Automattic/docspress \u002d\u002dall \u002d\u002dfull-depth",
    "output": ""
  }
}
-->
#### Add DocsPress skills

```bash
$ npx skills add Automattic/docspress --all --full-depth
```
<!-- /docspress:block -->

## 2. Create the docs

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Use $generate-docs-from-source to inspect this repository and create verified DocsPress-compatible documentation from its source code and tests. Preserve useful existing docs, show me the proposed docs tree, and do not publish anything yet.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$generate-docs-from-source, @repository, src/, test/, docs/",
    "caption": "Use this path when the repository has incomplete, stale, or no usable documentation."
  }
}
-->
#### Use this path when the repository has incomplete, stale, or no usable documentation.

> Use $generate-docs-from-source to inspect this repository and create verified DocsPress-compatible documentation from its source code and tests. Preserve useful existing docs, show me the proposed docs tree, and do not publish anything yet.

_Model: Coding agent · Mode: code · Thinking: on · Context: $generate-docs-from-source, @repository, src/, test/, docs/_
<!-- /docspress:block -->

If the repository already has a usable Markdown tree, skip generation and [publish the existing docs](../publish-existing-docs/index.md).

## What this path does

The generation workflow asks a repository-aware coding agent to:

1. Inventory public APIs, commands, configuration, tests, examples, and existing documentation.
2. Build a coverage map that ties every documentation claim to repository evidence.
3. Create or improve a navigable `docs/` hierarchy.
4. Use DocsPress Gutenberg blocks only when their semantics improve the page.
5. Validate routes, links, examples, block attributes, and documented behavior.
6. Hand the reviewed tree to `$docspress-install` for a manual draft dry run.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "note",
    "title": "Generation and publication are separate decisions",
    "content": "\u003cp\u003eCreating Markdown changes only the repository. Publishing Pages, activating WordPress components, pushing commits, and dispatching workflows remain separate approval boundaries.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!NOTE]
>
> **Generation and publication are separate decisions**
>
> Creating Markdown changes only the repository. Publishing Pages, activating WordPress components, pushing commits, and dispatching workflows remain separate approval boundaries.
<!-- /docspress:block -->

## Continue

- [Generate documentation from source](generate-from-source.md).
- [Review the generated tree and publish safely](review-and-publish.md).

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "neutral",
    "title": "Start with evidence, not a template",
    "content": "\u003cp\u003eThe agent derives the documentation structure from the project’s real public surface and tests before writing pages.\u003c/p\u003e",
    "meta": "source → coverage map → Markdown → review"
  }
}
-->
> [!NOTE]
>
> **Start with evidence, not a template**
>
> The agent derives the documentation structure from the project’s real public surface and tests before writing pages.
>
> _source → coverage map → Markdown → review_
<!-- /docspress:block -->
