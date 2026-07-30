---
title: Authenticate WordPress
---

Before publishing existing docs, configure the Bearer token DocsPress sends to the WordPress Pages REST endpoint. Store it as the GitHub Actions secret `WP_ACCESS_TOKEN`.

## WordPress.com

Create an application at [WordPress.com Apps](https://developer.wordpress.com/apps/) with this callback URL:

```text
http://localhost:8787/callback
```

The current helper requests the WordPress.com `global` scope. Run the exact trusted package version locally and use `--set-secret` so the token is written to GitHub without being printed:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "bash",
    "filename": "Trusted local terminal",
    "code": "printf \"WordPress.com client secret: \"\nIFS= read -r -s DOCSPRESS_CLIENT_SECRET\nprintf \"\\n\"\nnpx docspress@0.2.0 token \\\n  \u002d\u002dclient-id YOUR_CLIENT_ID \\\n  \u002d\u002dclient-secret \"$DOCSPRESS_CLIENT_SECRET\" \\\n  \u002d\u002dsite example.wordpress.com \\\n  \u002d\u002drepo OWNER/REPO \\\n  \u002d\u002dset-secret\nunset DOCSPRESS_CLIENT_SECRET",
    "highlightedLines": "2,5-10",
    "showLineNumbers": true,
    "caption": "The masked prompt keeps the client secret out of shell history; the helper stores WP_ACCESS_TOKEN through GitHub CLI."
  }
}
-->
**Trusted local terminal — The masked prompt keeps the client secret out of shell history; the helper stores WP\_ACCESS\_TOKEN through GitHub CLI.**

```bash
printf "WordPress.com client secret: "
IFS= read -r -s DOCSPRESS_CLIENT_SECRET
printf "\n"
npx docspress@0.2.0 token \
  --client-id YOUR_CLIENT_ID \
  --client-secret "$DOCSPRESS_CLIENT_SECRET" \
  --site example.wordpress.com \
  --repo OWNER/REPO \
  --set-secret
unset DOCSPRESS_CLIENT_SECRET
```
<!-- /docspress:block -->

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "danger",
    "title": "Run this yourself",
    "content": "\u003cp\u003eThe helper currently passes the client secret as a process argument. Use a trusted local machine, avoid screen or session recording, and do not ask an agent to capture the command or its output.\u003c/p\u003e",
    "collapsible": true,
    "open": false
  }
}
-->
> [!CAUTION]
>
> **Run this yourself**
>
> The helper currently passes the client secret as a process argument. Use a trusted local machine, avoid screen or session recording, and do not ask an agent to capture the command or its output.
<!-- /docspress:block -->

Verify only the secret name:

```bash
gh secret list --repo OWNER/REPO
```

## Self-hosted WordPress

Set `wordpress-url` to the site origin, without `/wp-json`. DocsPress will call `/wp-json/wp/v2/pages` and send `Authorization: Bearer …`.

Core WordPress does not create that Bearer token for DocsPress. Configure a trusted authentication mechanism that accepts it, then store the resulting token as `WP_ACCESS_TOKEN`.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "The Action input is still required",
    "content": "\u003cp\u003eSupply \u003ccode\u003ewordpress-site\u003c/code\u003e in the workflow even in self-hosted mode because the Action metadata marks it required. The self-hosted REST URL itself is derived from \u003ccode\u003ewordpress-url\u003c/code\u003e.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!WARNING]
>
> **The Action input is still required**
>
> Supply `wordpress-site` in the workflow even in self-hosted mode because the Action metadata marks it required. The self-hosted REST URL itself is derived from `wordpress-url`.
<!-- /docspress:block -->

Continue with [the first synchronization](first-sync.md).
