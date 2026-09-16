---
title: Install the agent skills
---

Install both repository-aware skills so an agent can publish existing documentation now and still generate missing coverage from source when needed.

## Install the skills

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/terminal-session",
  "attrs": {
    "title": "Install Autodocs Publish skills",
    "shell": "bash",
    "prompt": "$",
    "command": "npx skills add Automattic/autodocs-publish \u002d\u002dall \u002d\u002dfull-depth\nnpx skills list",
    "output": ""
  }
}
-->
#### Install Autodocs Publish skills

```bash
$ npx skills add Automattic/autodocs-publish --all --full-depth
$ npx skills list
```
<!-- /autodocs-publish:block -->

The CLI discovers both nested skills, installs them for the supported project agents, and records the source in `skills-lock.json`. Commit the generated skill files and lock file with the repository so future agents receive the same repeatable contract instead of a one-time prompt hidden in chat history.

## Ask the agent to inspect first

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/prompt",
  "attrs": {
    "prompt": "Use $autodocs-publish-install for this repository. Inspect the current docs tree and workflows before changing anything. Reuse existing Markdown. If no usable docs exist, invoke $generate-docs-from-source. Prepare a manual draft dry run and report every external action that still needs approval.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$autodocs-publish-install, $generate-docs-from-source, @repository, skills-lock.json, docs/, .github/workflows/",
    "caption": "Installation prompt for a repository-aware coding agent."
  }
}
-->
#### Installation prompt for a repository-aware coding agent.

> Use $autodocs-publish-install for this repository. Inspect the current docs tree and workflows before changing anything. Reuse existing Markdown. If no usable docs exist, invoke $generate-docs-from-source. Prepare a manual draft dry run and report every external action that still needs approval.

_Model: Coding agent · Mode: code · Thinking: on · Context: $autodocs-publish-install, $generate-docs-from-source, @repository, skills-lock.json, docs/, .github/workflows/_
<!-- /autodocs-publish:block -->

The skills instruct the agent to preserve unrelated worktree changes, derive claims from code and tests, use the Autodocs Publish Gutenberg blocks correctly, avoid plaintext credentials, and stage WordPress writes behind explicit approvals.

## Verify the installation

Confirm the project installation through the same CLI:

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/terminal-session",
  "attrs": {
    "title": "Verify Autodocs Publish skills",
    "shell": "bash",
    "prompt": "$",
    "command": "npx skills list",
    "output": ""
  }
}
-->
#### Verify Autodocs Publish skills

```bash
$ npx skills list
```
<!-- /autodocs-publish:block -->

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/result",
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
<!-- /autodocs-publish:block -->
