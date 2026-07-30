---
title: Install the agent skills
---

Install both repository-aware skills so an agent can publish existing documentation now and still generate missing coverage from source when needed.

## Install the skills

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

The CLI discovers both nested skills, installs them for the supported project agents, and records the source in `skills-lock.json`. Commit the generated skill files and lock file with the repository so future agents receive the same repeatable contract instead of a one-time prompt hidden in chat history.

## Ask the agent to inspect first

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Use $docspress-install for this repository. Inspect the current docs tree and workflows before changing anything. Reuse existing Markdown. If no usable docs exist, invoke $generate-docs-from-source. Prepare a manual draft dry run and report every external action that still needs approval.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$docspress-install, $generate-docs-from-source, @repository, skills-lock.json, docs/, .github/workflows/",
    "caption": "Installation prompt for a repository-aware coding agent."
  }
}
-->
#### Installation prompt for a repository-aware coding agent.

> Use $docspress-install for this repository. Inspect the current docs tree and workflows before changing anything. Reuse existing Markdown. If no usable docs exist, invoke $generate-docs-from-source. Prepare a manual draft dry run and report every external action that still needs approval.

_Model: Coding agent · Mode: code · Thinking: on · Context: $docspress-install, $generate-docs-from-source, @repository, skills-lock.json, docs/, .github/workflows/_
<!-- /docspress:block -->

The skills instruct the agent to preserve unrelated worktree changes, derive claims from code and tests, use the DocsPress Gutenberg blocks correctly, avoid plaintext credentials, and stage WordPress writes behind explicit approvals.

## Verify the installation

Confirm the project installation through the same CLI:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Verify DocsPress skills",
    "shell": "bash",
    "prompt": "$",
    "command": "npx skills list",
    "output": ""
  }
}
-->
#### Verify DocsPress skills

```bash
$ npx skills list
```
<!-- /docspress:block -->

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "The repository can teach the agent",
    "content": "\u003cp\u003eThe next agent can discover the same installation, authoring, validation, and safety workflow from versioned files.\u003c/p\u003e",
    "meta": "2 skills installed"
  }
}
-->
> [!TIP]
>
> **The repository can teach the agent**
>
> The next agent can discover the same installation, authoring, validation, and safety workflow from versioned files.
>
> _2 skills installed_
<!-- /docspress:block -->
