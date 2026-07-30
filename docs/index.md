---
title: DocsPress documentation
sidebar_position: 0
sidebar_collapsed: false
---

DocsPress is an AI-first documentation system for teams that want people and coding agents reading the same source. Markdown stays beside the code it explains; WordPress turns it into a branded, editable documentation experience made of native Pages and Gutenberg blocks.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "success",
    "title": "This entire documentation site fits in one Playground link",
    "content": "\u003cp\u003e\u003ca href=\"https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json\u0026amp;page-title=DocsPress%20Documentation\"\u003eOpen the complete DocsPress documentation in WordPress Playground\u003c/a\u003e. The Blueprint creates a temporary editable WordPress site, imports this converter-generated Page tree, and lands on \u003ccode\u003e/docs/\u003c/code\u003e.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!TIP]
>
> **This entire documentation site fits in one Playground link**
>
> [Open the complete DocsPress documentation in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json&page-title=DocsPress%20Documentation). The Blueprint creates a temporary editable WordPress site, imports this converter-generated Page tree, and lands on `/docs/`.
<!-- /docspress:block -->

## AI-first from discovery to maintenance

DocsPress gives text-first clients a native path through the documentation. The root [`/llms.txt`](guides/ai-friendly-documentation.md) file indexes every published, source-backed Page. Replace a Page route’s trailing slash with `.md` to fetch its exact reviewed Markdown with the `text/markdown` content type—no HTML shell to parse and no reconstructed source.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/flow",
  "attrs": {
    "start": 1,
    "steps": [
      {
        "title": "Discover with llms.txt",
        "content": "\u003cp\u003eAn agent reads one small index of absolute links instead of crawling navigation HTML or guessing routes.\u003c/p\u003e"
      },
      {
        "title": "Read exact Markdown",
        "content": "\u003cp\u003eEvery source-backed Page has a \u003ccode\u003e.md\u003c/code\u003e representation with its frontmatter, examples, tables, and supported block comments intact.\u003c/p\u003e"
      },
      {
        "title": "Improve the source",
        "content": "\u003cp\u003eRepository-aware DocsPress skills help coding agents generate or update documentation from verified code evidence, ready for review.\u003c/p\u003e"
      }
    ]
  }
}
-->
1. **Discover with llms.txt**

   An agent reads one small index of absolute links instead of crawling navigation HTML or guessing routes.

2. **Read exact Markdown**

   Every source-backed Page has a `.md` representation with its frontmatter, examples, tables, and supported block comments intact.

3. **Improve the source**

   Repository-aware DocsPress skills help coding agents generate or update documentation from verified code evidence, ready for review.
<!-- /docspress:block -->

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "One corpus for people and agents",
    "content": "\u003cp\u003eReaders get the complete WordPress experience. Agents and retrieval systems get discoverable, stable Markdown from the same reviewed source.\u003c/p\u003e",
    "meta": "llms.txt · text/markdown · source-owned Pages"
  }
}
-->
> [!TIP]
>
> **One corpus for people and agents**
>
> Readers get the complete WordPress experience. Agents and retrieval systems get discoverable, stable Markdown from the same reviewed source.
>
> _llms.txt · text/markdown · source-owned Pages_
<!-- /docspress:block -->

Read [Make documentation AI-friendly](guides/ai-friendly-documentation.md) for the endpoint contract, permissions, and verification commands.

## Make the complete site yours

DocsPress includes a native block theme rather than a fixed documentation frontend. Use the WordPress Site Editor to change the header, documentation navigation, article tools, table of contents, footer, homepage, posts, archives, search results, comments, and alternate Page layouts.

Global Styles can switch the whole system—not only its colors. The default DocsPress design and the WordPress.org, WordPress.com, and Jetpack families each define coordinated typography, color, spacing, corner, heading, and component treatments. Nine additional palettes provide light and dark brand directions, while native per-block controls let one surface differ without breaking the site-wide system.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "success",
    "title": "Fully customizable in the WordPress Site Editor",
    "content": "\u003cp\u003eApply your brand without maintaining a separate frontend or opening a legacy Customizer panel. The same Global Styles update the documentation shell, normal content, comments, core blocks, and DocsPress blocks.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!TIP]
>
> **Fully customizable in the WordPress Site Editor**
>
> Apply your brand without maintaining a separate frontend or opening a legacy Customizer panel. The same Global Styles update the documentation shell, normal content, comments, core blocks, and DocsPress blocks.
<!-- /docspress:block -->

Follow [Customize the theme in the Site Editor](guides/customize-theme.md) for the complete editing map, or read [Why DocsPress?](why-docspress.md) to compare this WordPress-native model with a separately built documentation site.

## Use blocks made for technical documentation

Markdown stays portable, but technical explanations do not have to look like plain prose. DocsPress adds editable Gutenberg blocks for connected flows, file trees, prompts, terminal sessions, code tabs, API requests, callouts, results, audience paths, and more.

Use Markdown syntax when it communicates clearly, then add a structured block when sequence, hierarchy, commands, or outcomes deserve a stronger visual treatment. The converter preserves the supported block comments in source, so the GitHub and WordPress representations stay aligned.

Read [Gutenberg blocks](reference/gutenberg-blocks/index.md) for the full schema or open the [block kitchen sink](reference/kitchen-sink.md) to see every component together.

## Choose a starting point

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "eyebrow": "Start here",
    "title": "Where are your docs today?",
    "description": "Choose the workflow that matches the current state of your repository.",
    "paths": [
      {
        "title": "I already have Markdown docs",
        "description": "Connect an existing docs folder to WordPress and begin with a safe draft sync.",
        "url": "/docs/publish-existing-docs/",
        "cta": "Publish existing docs",
        "icon": "document",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "I need to create docs",
        "description": "Generate source-grounded documentation with AI, review it, then publish it.",
        "url": "/docs/create-docs-with-ai/",
        "cta": "Create docs with AI",
        "icon": "sparkles",
        "accent": "gold",
        "newTab": false
      }
    ],
    "columns": 2,
    "tone": "theme",
    "textAlign": "left",
    "showNumbers": false
  }
}
-->
_Start here_

## Where are your docs today?

Choose the workflow that matches the current state of your repository.

### I already have Markdown docs

Connect an existing docs folder to WordPress and begin with a safe draft sync.

[Publish existing docs](/docs/publish-existing-docs/)

### I need to create docs

Generate source-grounded documentation with AI, review it, then publish it.

[Create docs with AI](/docs/create-docs-with-ai/)
<!-- /docspress:block -->

Both paths use repository-aware skills and end with the same reviewed Markdown-to-WordPress workflow. The difference is whether a usable documentation tree already exists.

## Install DocsPress with your coding agent

Install both skills into the repository that owns the documentation:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Install DocsPress skills",
    "shell": "bash",
    "prompt": "$",
    "command": "npx skills add Automattic/docspress \u002d\u002dall \u002d\u002dfull-depth\nnpx skills list",
    "output": ""
  }
}
-->
#### Install DocsPress skills

```bash
$ npx skills add Automattic/docspress --all --full-depth
$ npx skills list
```
<!-- /docspress:block -->

Then follow [Publish existing docs](publish-existing-docs/index.md) or [Create docs with AI](create-docs-with-ai/index.md). Each path provides a focused, copy-ready agent prompt.

## How DocsPress works

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "your-repository/",
    "tree": ".claude/\n  skills/\n    docspress-install/\n    generate-docs-from-source/\n.github/\n  workflows/\n    sync-docs.yml\ndocs/\n  index.md\n  publish-existing-docs/\n    index.md\n    first-sync.md\n  create-docs-with-ai/\n    index.md\n    review-and-publish.md",
    "caption": "Choose a starting workflow, then publish the reviewed Markdown tree as WordPress Pages."
  }
}
-->
#### your-repository/

```text
.claude/
  skills/
    docspress-install/
    generate-docs-from-source/
.github/
  workflows/
    sync-docs.yml
docs/
  index.md
  publish-existing-docs/
    index.md
    first-sync.md
  create-docs-with-ai/
    index.md
    review-and-publish.md
```

_Choose a starting workflow, then publish the reviewed Markdown tree as WordPress Pages._
<!-- /docspress:block -->

1. Authors and agents can update Markdown under `docs/`, while editors can update existing managed Pages in Gutenberg.
2. The GitHub Action converts Markdown into Gutenberg blocks and Gutenberg changes back into focused Markdown edits.
3. DocsPress compares both versions with their shared management marker.
4. GitHub-only changes update WordPress; WordPress-only changes open a pull request; two-sided changes stop as conflicts.

Read [Publish existing docs](publish-existing-docs/index.md) for the safe synchronization sequence, [Create docs with AI](create-docs-with-ai/index.md) when documentation must be generated first, or [Authoring documentation](authoring/index.md) for the Markdown contract.

See [Why DocsPress?](why-docspress.md) for a practical comparison with Docusaurus and the cases where keeping WordPress as the publishing surface removes an entire parallel docs stack.

## Keep documentation synchronized

Prove the connection with `workflow_dispatch`, `status: draft`, and `dry-run: true`. After the dry run and draft Page tree are approved, add this path-scoped trigger:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": ".github/workflows/sync-docs.yml",
    "code": "on:\n  push:\n    branches: [main]\n    paths:\n      - \"docs/**/*.md\"\n      - \"docs/**/*.markdown\"\n      - \"docs/**/*.json\"\n      - \".github/workflows/sync-docs.yml\"\n  workflow_dispatch:",
    "highlightedLines": "2-8",
    "showLineNumbers": true,
    "caption": "Once approved, documentation changes on the default branch can synchronize automatically."
  }
}
-->
**.github/workflows/sync-docs.yml — Once approved, documentation changes on the default branch can synchronize automatically.**

```yaml
on:
  push:
    branches: [main]
    paths:
      - "docs/**/*.md"
      - "docs/**/*.markdown"
      - "docs/**/*.json"
      - ".github/workflows/sync-docs.yml"
  workflow_dispatch:
```
<!-- /docspress:block -->

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "One source of truth",
    "content": "\u003cp\u003eEvery merged documentation change can flow from GitHub to the same WordPress Page hierarchy without maintaining a second copy.\u003c/p\u003e",
    "meta": "Markdown → Gutenberg → WordPress"
  }
}
-->
> [!TIP]
>
> **One source of truth**
>
> Every merged documentation change can flow from GitHub to the same WordPress Page hierarchy without maintaining a second copy.
>
> _Markdown → Gutenberg → WordPress_
<!-- /docspress:block -->

Read [GitHub to WordPress](guides/github-to-wordpress.md) and [WordPress to GitHub](guides/wordpress-to-github.md) separately, or follow the complete [continuous synchronization guide](guides/continuous-sync.md) before enabling automatic writes.

## Explore the documentation

- [Publish existing docs](/docs/publish-existing-docs/): connect a Markdown tree, authenticate, and run the first safe sync.
- [Create docs with AI](/docs/create-docs-with-ai/): generate a source-grounded documentation tree, review it, and hand it to the publishing workflow.
- [Why DocsPress?](/docs/why-docspress/): compare the WordPress-native model with a Docusaurus static site.
- [Authoring](/docs/authoring/): structure pages and use Markdown or DocsPress Gutenberg blocks.
- [Guides](/docs/guides/): customize the complete theme in the Site Editor, synchronize in either direction, prevent merge loops, and control routes with manifests or redirects.
- [Reference](/docs/reference/): Action inputs, CLI behavior, REST reconciliation, theme, and block schemas.
- [Troubleshooting](/docs/troubleshooting/): diagnose authentication, conflicts, links, and workflow failures.
