---
title: Token helper CLI
---

The `autodocs-publish` npm executable currently provides one command: `token`.

## Top-level help

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/terminal-session",
  "attrs": {
    "title": "Autodocs Publish CLI",
    "shell": "bash",
    "prompt": "$",
    "command": "npx autodocs-publish@0.5.0 \u002d\u002dhelp",
    "output": "Autodocs Publish CLI\n\nUsage:\n  autodocs-publish token \u002d\u002dclient-id CLIENT_ID \u002d\u002dclient-secret CLIENT_SECRET \u002d\u002dsite example.wordpress.com \u002d\u002drepo OWNER/REPO\n\nCommands:\n  token    Create and optionally store a WordPress.com OAuth token."
  }
}
-->
#### Autodocs Publish CLI

```bash
$ npx autodocs-publish@0.5.0 --help
```

**Output**

```text
Autodocs Publish CLI

Usage:
  autodocs-publish token --client-id CLIENT_ID --client-secret CLIENT_SECRET --site example.wordpress.com --repo OWNER/REPO

Commands:
  token    Create and optionally store a WordPress.com OAuth token.
```
<!-- /autodocs-publish:block -->

Unknown commands exit with an error. The CLI does not publish documentation from a local command; synchronization runs through the GitHub Action.

## Token options

| Option | Required | Purpose |
| --- | --- | --- |
| `--client-id` | yes | WordPress.com application client ID. |
| `--client-secret` | yes | WordPress.com application client secret. Keep it outside chat and shell history. |
| `--site` | no | Site domain or ID supplied to the authorization screen. |
| `--scope` | no | OAuth scope; defaults to `global`. |
| `--redirect-uri` | no | Local HTTP callback; defaults to `http://localhost:8787/callback`. |
| `--repo` | required with `--set-secret` | GitHub repository in `OWNER/REPO` form. |
| `--set-secret` | no | Stores the access token as `WP_ACCESS_TOKEN` through GitHub CLI instead of printing it. |

The redirect must use `http` and the hostname must be `localhost` or `127.0.0.1`. The helper generates and validates OAuth state before exchanging the authorization code.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/callout",
  "attrs": {
    "tone": "danger",
    "title": "Do not let an agent observe token output",
    "content": "\u003cp\u003eWithout \u003ccode\u003e\u002d\u002dset-secret\u003c/code\u003e, the helper prints the access token and a secret-setting command. Use \u003ccode\u003e\u002d\u002dset-secret\u003c/code\u003e in a trusted local terminal.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!CAUTION]
>
> **Do not let an agent observe token output**
>
> Without `--set-secret`, the helper prints the access token and a secret-setting command. Use `--set-secret` in a trusted local terminal.
<!-- /autodocs-publish:block -->

See [Authenticate WordPress](../publish-existing-docs/authentication.md) for the recommended invocation.
