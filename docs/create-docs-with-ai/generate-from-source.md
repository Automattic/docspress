---
title: Generate documentation from source
sidebar_position: 10
---

Turn repository evidence into a DocsPress-compatible Markdown tree without inventing commands, behavior, or support guarantees.

## 1. Install the repository skills

Install both DocsPress skills so generation can hand off to publication after review:

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

## 2. Ask for an evidence pass first

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Use $generate-docs-from-source to inspect the public surface of this repository. Build a coverage map from package metadata, exports, commands, configuration reads, tests, examples, and existing docs. Show the proposed docs tree before writing pages, and identify any contradictions instead of guessing.",
    "model": "Coding agent",
    "mode": "plan",
    "thinking": true,
    "context": "$generate-docs-from-source, @repository, package.json, src/, test/, docs/",
    "caption": "Separate repository research from documentation writing."
  }
}
-->
#### Separate repository research from documentation writing.

> Use $generate-docs-from-source to inspect the public surface of this repository. Build a coverage map from package metadata, exports, commands, configuration reads, tests, examples, and existing docs. Show the proposed docs tree before writing pages, and identify any contradictions instead of guessing.

_Model: Coding agent · Mode: plan · Thinking: on · Context: $generate-docs-from-source, @repository, package.json, src/, test/, docs/_
<!-- /docspress:block -->

The agent should treat tests and executable examples as stronger evidence than comments. It should preserve useful existing documentation and avoid documenting private helpers as public APIs.

## 3. Generate the smallest complete tree

A typical project may need:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "docs/",
    "tree": "index.md\ngetting-started/\n  index.md\n  installation.md\n  configuration.md\nguides/\n  first-workflow.md\nreference/\n  api.md\n  cli.md\ntroubleshooting.md",
    "caption": "The source determines which pages exist; this is a shape, not a required template."
  }
}
-->
#### docs/

```text
index.md
getting-started/
  index.md
  installation.md
  configuration.md
guides/
  first-workflow.md
reference/
  api.md
  cli.md
troubleshooting.md
```

_The source determines which pages exist; this is a shape, not a required template._
<!-- /docspress:block -->

Each page should have one clear outcome, verified examples, relative links, and a stable route. Use ordinary Markdown for ordinary prose. Use DocsPress blocks for prompts, connected flows, terminal sessions, API exchanges, callouts, collapsible file trees, code alternatives, and verification results when those semantics are useful.

## 4. Keep publication disabled

The generation pass may prepare a DocsPress workflow when one is missing, but it should start with `workflow_dispatch`, `status: draft`, and `dry-run: true`.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "Do not publish generated claims before review",
    "content": "\u003cp\u003eGeneration completes when the Markdown tree is evidence-backed and validated. WordPress authentication, Page writes, repository pushes, and public publication require their own approvals.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!WARNING]
>
> **Do not publish generated claims before review**
>
> Generation completes when the Markdown tree is evidence-backed and validated. WordPress authentication, Page writes, repository pushes, and public publication require their own approvals.
<!-- /docspress:block -->

Continue with [Review and publish](review-and-publish.md).
