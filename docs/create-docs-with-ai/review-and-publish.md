---
title: Review and publish generated docs
sidebar_position: 20
---

Verify the generated Markdown against the repository, then hand the accepted tree to the normal publishing workflow in Autodocs Publish.

## Review the generated tree

Confirm:

- Every page is nonempty and has a unique route and title.
- Relative links and local images resolve from the file containing them.
- Commands, options, exports, environment variables, defaults, and errors match source or tests.
- Examples are runnable, or clearly marked when execution is not practical.
- Every `wp:autodocs-publish/*` attribute object is valid JSON and uses supported attributes.
- No placeholder, credential, private helper, or unsupported claim slipped into public documentation.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/prompt",
  "attrs": {
    "prompt": "Review the generated docs against this repository. Check every public API, command, configuration name, example, relative link, and Autodocs Publish block attribute. Return incorrect or unsupported claims first, then make only evidence-backed documentation fixes.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$generate-docs-from-source, @repository, docs/, src/, test/",
    "caption": "Run a source-backed editorial review before publication."
  }
}
-->
#### Run a source-backed editorial review before publication.

> Review the generated docs against this repository. Check every public API, command, configuration name, example, relative link, and Autodocs Publish block attribute. Return incorrect or unsupported claims first, then make only evidence-backed documentation fixes.

_Model: Coding agent · Mode: code · Thinking: on · Context: $generate-docs-from-source, @repository, docs/, src/, test/_
<!-- /autodocs-publish:block -->

## Run repository checks

Use the project’s real scripts in proportion to the change. At minimum, validate links and block JSON, run the relevant tests or examples, and check the final diff.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/terminal-session",
  "attrs": {
    "title": "Check the documentation diff",
    "shell": "bash",
    "prompt": "$",
    "command": "git diff \u002d\u002dcheck\ngit status \u002d\u002dshort",
    "output": ""
  }
}
-->
#### Check the documentation diff

```bash
$ git diff --check
$ git status --short
```
<!-- /autodocs-publish:block -->

## Hand off to publication

Once the generated tree is accepted, continue through [Publish existing docs](../publish-existing-docs/index.md). The generated Markdown is now the existing documentation tree for `$autodocs-publish-install`.

1. [Install or verify the agent skills](../publish-existing-docs/install-agent-skills.md).
2. [Authenticate WordPress](../publish-existing-docs/authentication.md).
3. [Run the first synchronization](../publish-existing-docs/first-sync.md) as a manual draft dry run.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/result",
  "attrs": {
    "status": "success",
    "title": "Generated docs are ready for a safe preview",
    "content": "\u003cp\u003eThe reviewed Markdown tree can now enter the same dry-run and draft lifecycle as any hand-authored documentation.\u003c/p\u003e",
    "meta": "reviewed Markdown → draft WordPress Pages"
  }
}
-->
> [!TIP]
>
> **Generated docs are ready for a safe preview**
>
> The reviewed Markdown tree can now enter the same dry-run and draft lifecycle as any hand-authored documentation.
>
> _reviewed Markdown → draft WordPress Pages_
<!-- /autodocs-publish:block -->
