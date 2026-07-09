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
      - "docs/**/*.json"
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
          manifest-file: docs/docspress.manifest.json
          redirects-file: docs/redirects.json
          root-slug: docs
          root-title: Docs
          create-h1: false
          rewrite-links: true
          edit-link: true
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
| `manifest-file` | empty | Optional JSON manifest that defines page slugs, parents, titles, and Markdown source files. |
| `redirects-file` | empty | Optional JSON map of old docs paths to new docs paths or external URLs. Creates managed moved-page placeholders. |
| `root-slug` | `docs` | Managed root page slug. |
| `root-title` | `Docs` | Managed root page title when no root `index.md` exists. |
| `create-h1` | `false` | Add the page title as an H1 block at the top of generated content. |
| `rewrite-links` | `true` | Rewrite local Markdown links to generated WordPress page URLs. |
| `edit-link` | `false` | Append an "Edit this page on GitHub" link to Markdown-backed pages. |
| `edit-link-text` | `Edit this page on GitHub` | Link text used when `edit-link` is enabled. |
| `github-repository` | `GITHUB_REPOSITORY` | Repository used for edit links, such as `owner/repo`. |
| `github-ref` | `GITHUB_REF_NAME` | Branch or ref used for edit links. |
| `github-server-url` | `GITHUB_SERVER_URL` | GitHub server URL used for edit links. |
| `status` | `publish` | Status for created or updated pages. Use `draft` for private review or `publish` for public pages. |
| `delete-mode` | `trash` | Use `trash` or `force` for removed Markdown files. |
| `dry-run` | `false` | Plan changes without writing to WordPress. |

## WordPress.com authentication

WordPress.com API writes require an OAuth bearer token. Create that token on WordPress.com, then store it as a GitHub Actions secret.

For personal projects or demos that only access your own WordPress.com site, use the authorization-code flow. This works with two-factor authentication because you authorize in the browser instead of sending your WordPress.com password to the token endpoint.

1. Create an app at [WordPress.com Apps](https://developer.wordpress.com/apps/) and copy its `client_id` and `client_secret`.

   For the `f/docspress-demo` repository, use these app form values:

   | Field | Value |
   | --- | --- |
   | Name | `Docspress Demo` |
   | Description | `Sync Markdown docs from GitHub to WordPress Pages as Gutenberg content.` |
   | Website URL | `https://github.com/f/docspress-demo` |
   | Redirect URLs | `http://localhost:8787/callback` |
   | Javascript Origins | Leave blank |
   | Type | `Web` |
   | Follow Developer blog | Optional; leave unchecked unless you want the emails |
   | Owner | Use your personal owner, such as `fatihkadirakin`, for personal/demo apps |

   The redirect URL must match the local callback URL used by the token helper below.

2. Run the token helper with `npx`. The helper requests WordPress.com's `global` OAuth scope because the WP v2 Pages API requires it for listing, creating, updating, and deleting pages.

```bash
npx docspress token \
  --client-id YOUR_CLIENT_ID \
  --client-secret YOUR_CLIENT_SECRET \
  --site fkadev.blog \
  --repo f/docspress-demo
```

The helper opens WordPress.com in your browser, waits for the `http://localhost:8787/callback` redirect, exchanges the authorization code for an access token, and prints the token.

If you previously created a token with a narrower scope such as `posts media`, regenerate it with this helper and replace the `WP_ACCESS_TOKEN` secret. WordPress.com will reject the action with `Required scope: global` when the token is too narrow.

3. Store the returned access token as `WP_ACCESS_TOKEN` in the repository that runs Docspress:

```bash
gh secret set WP_ACCESS_TOKEN --repo OWNER/REPO
```

Paste the token when prompted, then press `Ctrl-D`.

You can also let the helper store the secret directly:

```bash
npx docspress token \
  --client-id YOUR_CLIENT_ID \
  --client-secret YOUR_CLIENT_SECRET \
  --site fkadev.blog \
  --repo f/docspress-demo \
  --set-secret
```

Docspress sends the resulting OAuth token to WordPress.com as `Authorization: Bearer ...`.

## Docs mapping

- `docs/index.md` or `docs/README.md` becomes `/docs/`.
- `docs/getting-started.md` becomes `/docs/getting-started/`.
- `docs/guides/index.md` becomes `/docs/guides/`.
- Missing parent sections are created as managed placeholder pages.

The page title comes from frontmatter `title`, then the first H1, then the filename. When the first H1 is used as the title, it is removed from the body to avoid duplication.

Set `create-h1: true` if you want Docspress to add the WordPress page title as the first H1 block in the generated content. When the Markdown already starts with the same H1, Docspress reuses that title and avoids creating a duplicate.

### Link rewriting

By default, local Markdown links are rewritten to the generated WordPress page URL:

```md
[Getting Started](guides/getting-started.md)
[Action Inputs](/docs/reference/action-inputs.md)
```

With `root-slug: docs`, those become:

```html
<a href="/docs/guides/getting-started/">Getting Started</a>
<a href="/docs/reference/action-inputs/">Action Inputs</a>
```

External links, anchors, `mailto:` links, and unknown local files are left unchanged. Set `rewrite-links: false` to preserve Markdown links exactly as written.

### Manifest mode

Without `manifest-file`, Docspress discovers every Markdown file under `docs-dir`. Set `manifest-file` when you want stable slugs, titles, and parent relationships that are not purely derived from filenames.

```json
{
  "pages": [
    { "id": "root", "title": "Docs", "slug": "", "markdown_source": "index.md" },
    { "id": "guides", "title": "Guides", "slug": "guides" },
    {
      "id": "getting-started",
      "title": "Getting Started",
      "slug": "getting-started",
      "parent": "guides",
      "markdown_source": "guides/getting-started.md"
    }
  ]
}
```

`markdown_source` paths are resolved relative to the manifest file. Entries without `markdown_source` become managed placeholder pages.

### Redirect map

Set `redirects-file` to keep old docs paths alive after renames. On WordPress.com this creates a managed "moved" page with a link to the new destination; it is not a server-level 301 redirect.

```json
{
  "redirects": {
    "old-getting-started": "guides/getting-started",
    "legacy/api": "https://developer.wordpress.org/rest-api/"
  }
}
```

Relative destinations are resolved under `root-slug`; absolute URLs are used as-is.

### Edit links

Set `edit-link: true` to append a source link to every Markdown-backed page:

```yaml
edit-link: true
edit-link-text: Improve this page on GitHub
github-repository: f/docspress-demo
github-ref: main
```

Generated placeholder and redirect pages do not get edit links.

## Markdown and Gutenberg blocks

Docspress maps common Markdown to Gutenberg-compatible core blocks, including paragraphs, headings, lists, quotes, code blocks, images, tables, separators, and raw HTML.

Docspress also supports Gutenberg Handbook-style code tabs:

````md
{% codetabs %}
{% JSX %}
```jsx
<Button variant="primary" />
```
{% Plain %}
```js
wp.element.createElement(Button);
```
{% end %}
````

These become a Gutenberg HTML block containing `code-tabs`, `code-tab`, and `code-tab-block` markup. Themes can style that markup if they want tab-like behavior.

You can also write serialized Gutenberg block comments directly in Markdown when you need a block Docspress does not map yet. Keep the block comments and their HTML unindented, with blank lines around the block:

```html
<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>Written as a raw Gutenberg block.</p></blockquote>
<!-- /wp:quote -->
```

Self-closing block comments work too:

```html
<!-- wp:separator /-->
```

Docspress preserves these Gutenberg annotations as-is instead of wrapping them in an HTML block. WordPress is still responsible for validating the serialized block markup, so malformed block comments may need to be fixed in the Markdown source.

## Development

```bash
npm install
npm test
npm run lint
npm run build
```

`dist/index.js` is committed so workflows can run the action without installing dependencies.
