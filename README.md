<p align="center">
  <img src="assets/logo.png" alt="Docspress logo and wordmark" width="680">
</p>

<p align="center">
  <a href="https://www.npmjs.com/package/docspress"><img alt="npm version" src="https://img.shields.io/npm/v/docspress.svg"></a>
  <a href="LICENSE"><img alt="License: GPL-3.0-or-later" src="https://img.shields.io/badge/license-GPL--3.0--or--later-blue.svg"></a>
  <a href="https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json&amp;page-title=DocsPress%20Documentation"><img alt="Launch DocsPress documentation in WordPress Playground" src="https://img.shields.io/badge/WordPress%20Playground-Launch-3858E9?logo=wordpress&amp;logoColor=white"></a>
</p>

<p align="center">
  <strong>Markdown in GitHub. WordPress as the publishing surface.</strong><br>
  Sync Markdown documentation into WordPress Pages as Gutenberg-compatible block content.
</p>

## Quick start

1. Put the Markdown documentation in `docs/`.
2. [Create a WordPress access token](#authentication).
3. Add one of the [GitHub Actions workflows](#github-actions) below.
4. Run it first with `status: draft` and `dry-run: true`.

The complete product and setup documentation lives at [docs.press/docs](https://docs.press/docs/).

## Preview local docs from the CLI

Run this from the repository that contains your `docs/` directory:

```bash
npx @wp-playground/cli@latest start \
  --blueprint=https://raw.githubusercontent.com/Automattic/docspress/main/theme/blueprint-local-docs.json \
  --mount="$PWD/docs:/wordpress/docspress-source-docs" \
  --no-auto-mount \
  --reset
```

WordPress Playground imports the Markdown as editable Pages and opens `/docs/`.

## Playground examples

- [Default documentation site](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json&page-title=DocsPress%20Documentation)
- [Versioned API documentation](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-versioning.json&page-title=DocsPress%20Versioning%20Example)

Both examples are temporary, editable WordPress sites with the DocsPress theme and blocks installed.

## GitHub Actions

### Publish Markdown to WordPress

Create `.github/workflows/sync-docs.yml`:

```yaml
name: Publish documentation

on:
  push:
    branches: [main]
    paths:
      - "docs/**"
      - ".github/workflows/sync-docs.yml"
  workflow_dispatch:

permissions:
  contents: read

jobs:
  sync:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: Automattic/docspress@main
        with:
          mode: publish
          wordpress-site: example.wordpress.com
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          root-slug: docs
          status: draft
          delete-mode: trash
          dry-run: true
```

Review the Actions summary, then set `dry-run: false`. Publish the Pages only after the draft site looks right.

### Reconcile GitHub and WordPress

After one-way publishing is working, use a single `reconcile` workflow for edits from either side:

```yaml
name: Reconcile documentation

on:
  push:
    branches: [main]
    paths:
      - "docs/**"
      - ".github/workflows/sync-docs.yml"
  schedule:
    - cron: "3/5 * * * *"
  workflow_dispatch:

permissions:
  contents: write
  pull-requests: write

concurrency:
  group: docspress-sync
  cancel-in-progress: false

jobs:
  sync:
    if: >-
      github.event_name != 'push' ||
      !contains(
        github.event.head_commit.message,
        format('from {0}/docspress/wordpress-sync', github.repository_owner)
      )
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: Automattic/docspress@main
        with:
          mode: reconcile
          wordpress-site: example.wordpress.com
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          root-slug: docs
          status: publish
          delete-mode: trash
```

WordPress-only edits become a rolling Markdown pull request. A true two-sided edit is reported as a conflict instead of overwriting either version.

## Authentication

### WordPress.com

DocsPress uses a WordPress.com OAuth token stored as the repository secret `WP_ACCESS_TOKEN`.

1. Create an app at [WordPress.com Apps](https://developer.wordpress.com/apps/).
2. Set the redirect URL to `http://localhost:8787/callback`.
3. Authenticate the GitHub CLI with `gh auth login`.
4. Run the token helper from a trusted local terminal:

```bash
printf "WordPress.com client secret: "
IFS= read -r -s DOCSPRESS_CLIENT_SECRET
printf "\n"
npx docspress@0.4.1 token \
  --client-id YOUR_CLIENT_ID \
  --client-secret "$DOCSPRESS_CLIENT_SECRET" \
  --site example.wordpress.com \
  --repo OWNER/REPO \
  --set-secret
unset DOCSPRESS_CLIENT_SECRET
```

The helper opens WordPress.com for authorization and stores the resulting token without printing it. Confirm only the secret name:

```bash
gh secret list --repo OWNER/REPO
```

### Self-hosted WordPress (.org)

DocsPress can use a REST API key from your host or authentication plugin when it accepts `Authorization: Bearer …`. Core WordPress does not create this key itself.

Store the key without printing it:

```bash
printf "WordPress REST API key: "
IFS= read -r -s WORDPRESS_REST_API_KEY
printf "\n"
printf "%s" "$WORDPRESS_REST_API_KEY" |
  gh secret set WP_ACCESS_TOKEN --repo OWNER/REPO
unset WORDPRESS_REST_API_KEY
```

Set `wordpress-url` to the site origin without `/wp-json`:

```yaml
wordpress-url: https://docs.example.com
wordpress-site: docs.example.com
wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
```

DocsPress calls `https://docs.example.com/wp-json/wp/v2/pages`. Confirm that the key can read, create, update, and delete Pages before publishing.

For the complete setup, use [Authenticate WordPress](https://docs.press/docs/publish-existing-docs/authentication/).

## Documentation

Continue at [docs.press/docs](https://docs.press/docs/) for:

- [the first safe synchronization](https://docs.press/docs/publish-existing-docs/first-sync/)
- [GitHub-to-WordPress publishing](https://docs.press/docs/guides/github-to-wordpress/)
- [continuous two-way synchronization](https://docs.press/docs/guides/continuous-sync/)
- [API versioning](https://docs.press/docs/guides/versioning/)
- [Action inputs and outputs](https://docs.press/docs/reference/action-inputs/)
