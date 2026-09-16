---
title: Publish existing docs
sidebar_position: 10
sidebar_collapsed: true
---

Connect an existing Markdown documentation tree to WordPress without allowing an unreviewed workflow run to publish Pages or open pull requests.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/prompt",
  "attrs": {
    "prompt": "Use $autodocs-publish-install to inspect this repository, reuse its existing Markdown documentation, and prepare a safe draft synchronization to WordPress.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$autodocs-publish-install, @repository, docs/, .github/workflows/",
    "caption": "Use this path when the repository already has usable Markdown documentation."
  }
}
-->
#### Use this path when the repository already has usable Markdown documentation.

> Use $autodocs-publish-install to inspect this repository, reuse its existing Markdown documentation, and prepare a safe draft synchronization to WordPress.

_Model: Coding agent · Mode: code · Thinking: on · Context: $autodocs-publish-install, @repository, docs/, .github/workflows/_
<!-- /autodocs-publish:block -->

If the repository does not have a usable docs tree yet, begin with [Create docs with AI](../create-docs-with-ai/index.md) instead.

## Prerequisites

- A GitHub repository containing or ready to receive `docs/`.
- A WordPress.com site or a self-hosted WordPress site whose Pages endpoint accepts the supplied Bearer token.
- Permission to add a repository workflow and the `WP_ACCESS_TOKEN` Actions secret.
- The Autodocs Publish Blocks plugin when documentation uses `wp:autodocs-publish/*` custom blocks.
- The Autodocs Publish theme when you want the bundled documentation reading experience.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/callout",
  "attrs": {
    "tone": "warning",
    "title": "Keep credentials outside the repository",
    "content": "\u003cp\u003eNever commit an OAuth client secret or WordPress access token. The workflow reads only \u003ccode\u003esecrets.WP_ACCESS_TOKEN\u003c/code\u003e.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!WARNING]
>
> **Keep credentials outside the repository**
>
> Never commit an OAuth client secret or WordPress access token. The workflow reads only `secrets.WP_ACCESS_TOKEN`.
<!-- /autodocs-publish:block -->

## Recommended sequence

1. [Install the agent skills](install-agent-skills.md) and ask the agent to reuse the existing docs directory.
2. [Authenticate WordPress](authentication.md).
3. [Run the first synchronization](first-sync.md) manually with `mode: reconcile`, drafts, and dry-run enabled.
4. Inspect the Action summary, expected Page hierarchy, and proposed Markdown files.
5. Approve a real draft write and review any WordPress-to-GitHub pull request.
6. Enable [GitHub-to-WordPress](../guides/github-to-wordpress.md), [WordPress-to-GitHub](../guides/wordpress-to-github.md), or combined [continuous synchronization](../guides/continuous-sync.md) only after the manual lifecycle succeeds.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/result",
  "attrs": {
    "status": "neutral",
    "title": "Existing docs stay in control",
    "content": "\u003cp\u003eThe starting workflow is manual, dry-run only, and targets draft Pages. WordPress publication and GitHub pull requests remain separate approval decisions.\u003c/p\u003e",
    "meta": "existing Markdown · dry-run · draft"
  }
}
-->
> [!NOTE]
>
> **Existing docs stay in control**
>
> The starting workflow is manual, dry-run only, and targets draft Pages. WordPress publication and GitHub pull requests remain separate approval decisions.
>
> _existing Markdown · dry-run · draft_
<!-- /autodocs-publish:block -->
