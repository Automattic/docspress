# Docspress

Docspress is a GitHub Action that syncs Markdown docs from a repository into WordPress Pages as Gutenberg-compatible block content.

Markdown is the source of truth. Docspress only updates or deletes WordPress pages that contain its own hidden sentinel comment, so existing manually-created WordPress pages are protected and reported as conflicts.

## Usage

```yaml
name: Sync docs to WordPress

on:
  push:
    branches: [main]
    paths:
      - "docs/**/*.md"
      - ".github/workflows/sync-docs.yml"
  workflow_dispatch:

jobs:
  sync:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: f/docspress@main
        with:
          wordpress-site: fkadev.blog
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          root-slug: docs
          root-title: Docs
          status: draft
          dry-run: true
```

## Inputs

| Input | Default | Description |
| --- | --- | --- |
| `wordpress-url` | `https://public-api.wordpress.com` | WordPress API base URL. |
| `wordpress-site` | required | WordPress.com site ID or domain, such as `fkadev.blog`. |
| `wordpress-access-token` | required | OAuth bearer token that can edit pages. |
| `docs-dir` | `docs` | Markdown docs directory. |
| `root-slug` | `docs` | Managed root page slug. |
| `root-title` | `Docs` | Managed root page title when no root `index.md` exists. |
| `status` | `publish` | Status for created or updated pages. |
| `delete-mode` | `trash` | Use `trash` or `force` for removed Markdown files. |
| `dry-run` | `false` | Plan changes without writing to WordPress. |

## WordPress.com authentication

WordPress.com API writes require an OAuth bearer token. Store it as a GitHub Actions secret:

```bash
gh secret set WP_ACCESS_TOKEN --repo f/docspress-demo --body "YOUR_WORDPRESS_COM_OAUTH_TOKEN"
```

## Docs mapping

- `docs/index.md` or `docs/README.md` becomes `/docs/`.
- `docs/getting-started.md` becomes `/docs/getting-started/`.
- `docs/guides/index.md` becomes `/docs/guides/`.
- Missing parent sections are created as managed placeholder pages.

The page title comes from frontmatter `title`, then the first H1, then the filename. When the first H1 is used as the title, it is removed from the body to avoid duplication.

## Development

```bash
npm install
npm test
npm run lint
npm run build
```

`dist/index.js` is committed so workflows can run the action without installing dependencies.
