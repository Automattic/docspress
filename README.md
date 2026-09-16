<p align="center">
  <img src="assets/autodocs-publish-wordmark.svg" alt="Autodocs Publish logo and wordmark" width="680">
</p>

<p align="center">
  <a href="https://www.npmjs.com/package/autodocs-publish"><img alt="npm version" src="https://img.shields.io/npm/v/autodocs-publish.svg"></a>
  <a href="LICENSE"><img alt="License: GPL-3.0-or-later" src="https://img.shields.io/badge/license-GPL--3.0--or--later-blue.svg"></a>
  <a href="https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fautodocs-publish%2Fmain%2Ftheme%2Fblueprint-docs.json&amp;page-title=Autodocs%20Publish%20Documentation"><img alt="Launch Autodocs Publish documentation in WordPress Playground" src="https://img.shields.io/badge/WordPress%20Playground-Launch-3858E9?logo=wordpress&amp;logoColor=white"></a>
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

## Rename compatibility

Existing content created before the rename stays readable. Autodocs Publish accepts legacy `docspress:`, `docspress:block`, and `docspress/*` inputs, but all new output uses `autodocs-publish:`, `autodocs-publish:block`, and `autodocs-publish/*` identifiers.

For an existing versioned site, activate Autodocs Publish Blocks 0.11.0 before changing the Action reference. This release keeps the old REST contract available during the rolling update and deactivates the old `docspress-blocks` package when the renamed plugin is activated. Activating the renamed theme copies the existing theme settings and Site Editor customizations without replacing newer Autodocs Publish values.

## Optional WordPress packages

You do not have to install the Autodocs Publish theme. An unversioned Autodocs Publish sync works with the site's existing theme and native WordPress blocks.

- [Download the latest Autodocs Publish theme](https://github.com/Automattic/autodocs-publish/releases/latest/download/autodocs-publish-theme.zip)
- [Download the latest Autodocs Publish Blocks plugin](https://github.com/Automattic/autodocs-publish/releases/latest/download/autodocs-publish-blocks.zip)

Install the theme for the complete documentation layout. Install the Blocks plugin for rich Autodocs Publish blocks; it is required when API versioning is enabled.

## Preview local docs from the CLI

Run this from the repository that contains your `docs/` directory:

```bash
npx @wp-playground/cli@3.1.46 start \
  --blueprint=https://raw.githubusercontent.com/Automattic/autodocs-publish/main/theme/blueprint-local-docs.json \
  --mount="$PWD/docs:/wordpress/autodocs-publish-source-docs" \
  --no-auto-mount \
  --reset
```

WordPress Playground imports the Markdown as editable Pages and opens `/docs/`.

## Playground examples

- [Default documentation site](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fautodocs-publish%2Fmain%2Ftheme%2Fblueprint-docs.json&page-title=Autodocs%20Publish%20Documentation)
- [Contextual sidebars](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fautodocs-publish%2Fmain%2Ftheme%2Fblueprint-sidebars.json&page-title=Autodocs%20Publish%20Contextual%20Sidebars)
- [Versioned API documentation](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fautodocs-publish%2Fmain%2Ftheme%2Fblueprint-versioning.json&page-title=Autodocs%20Publish%20Versioning%20Example)
- [Stock WordPress, no Autodocs Publish theme or plugin](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fautodocs-publish%2Fmain%2Fexamples%2Fstock-wordpress%2Fblueprint.json&page-title=Autodocs%20Publish%20on%20Stock%20WordPress)

The first two examples include the complete Autodocs Publish presentation layer. The stock WordPress example installs neither optional package and shows repository Markdown as editable native Gutenberg blocks.

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
      - uses: Automattic/autodocs-publish@main
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
  group: autodocs-publish-sync
  cancel-in-progress: false

jobs:
  sync:
    if: >-
      github.event_name != 'push' ||
      !contains(
        github.event.head_commit.message,
        format('from {0}/autodocs-publish/wordpress-sync', github.repository_owner)
      )
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: Automattic/autodocs-publish@main
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

Autodocs Publish uses a WordPress.com OAuth token stored as the repository secret `WP_ACCESS_TOKEN`.

1. Create an app at [WordPress.com Apps](https://developer.wordpress.com/apps/).
2. Set the redirect URL to `http://localhost:8787/callback`.
3. Authenticate the GitHub CLI with `gh auth login`.
4. Run the token helper from a trusted local terminal:

```bash
printf "WordPress.com client secret: "
IFS= read -r -s AUTODOCS_PUBLISH_CLIENT_SECRET
printf "\n"
npx autodocs-publish@0.5.0 token \
  --client-id YOUR_CLIENT_ID \
  --client-secret "$AUTODOCS_PUBLISH_CLIENT_SECRET" \
  --site example.wordpress.com \
  --repo OWNER/REPO \
  --set-secret
unset AUTODOCS_PUBLISH_CLIENT_SECRET
```

The helper opens WordPress.com for authorization and stores the resulting token without printing it. Confirm only the secret name:

```bash
gh secret list --repo OWNER/REPO
```

### Self-hosted WordPress (.org)

Autodocs Publish can use a REST API key from your host or authentication plugin when it accepts `Authorization: Bearer …`. Core WordPress does not create this key itself.

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

Autodocs Publish calls `https://docs.example.com/wp-json/wp/v2/pages`. Confirm that the key can read, create, update, and delete Pages before publishing.

For the complete setup, use [Authenticate WordPress](https://docs.press/docs/publish-existing-docs/authentication/).

## Documentation

Continue at [docs.press/docs](https://docs.press/docs/) for:

- [the first safe synchronization](https://docs.press/docs/publish-existing-docs/first-sync/)
- [GitHub-to-WordPress publishing](https://docs.press/docs/guides/github-to-wordpress/)
- [continuous two-way synchronization](https://docs.press/docs/guides/continuous-sync/)
- [API versioning](https://docs.press/docs/guides/versioning/)
- [Action inputs and outputs](https://docs.press/docs/reference/action-inputs/)
