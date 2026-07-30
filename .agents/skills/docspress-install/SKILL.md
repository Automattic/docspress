---
name: docspress-install
description: Install and configure DocsPress for a GitHub repository and its WordPress publishing target. Use when an agent must publish an existing or API-versioned Markdown documentation tree, enable WordPress-to-GitHub proposal pull requests, create or repair the DocsPress GitHub Actions workflow, configure WordPress authentication, prepare the companion DocsPress theme and blocks plugin, verify a dry run, or safely move documentation from draft to published Pages.
---

# Install DocsPress

Connect an existing Markdown documentation directory to WordPress without overwriting manual pages or exposing credentials. Treat repository changes, GitHub writes, and WordPress writes as separate approval boundaries.

## 1. Inspect before changing anything

1. Resolve the repository root, active branch, remote, default branch, and working-tree state.
2. Find documentation directories and existing workflows. Prefer `rg --files` and inspect `.github/workflows/` before creating files.
3. Reuse an existing docs directory. Default to `docs/` only when there is no stronger repository convention. Detect whether it contains multiple maintained API releases, a version registry, version directories, or filename suffixes; do not infer versioning from a changelog alone.
4. If no usable Markdown documentation exists, invoke `$generate-docs-from-source`, complete its verified docs workflow, then return here.
5. Inspect an existing DocsPress workflow and update it in place. Never create a second competing sync workflow.
6. Determine the WordPress target:
   - WordPress.com: use `https://public-api.wordpress.com` and a global-scope OAuth token.
   - Self-hosted WordPress: use the site origin as `wordpress-url`; proceed only if that site already accepts the supplied Bearer token for `/wp-json/wp/v2/pages`.
7. Minimize configuration questions to values that cannot be inferred: target site/domain, nonstandard docs directory, or whether the companion theme and plugin are wanted. Always request separate approval before repository writes, GitHub writes, WordPress Page writes, plugin activation, theme activation, or public publication.

Do not ask the user to paste access tokens, OAuth client secrets, passwords, or cookies into chat. Do not print secret values during verification.

## 2. Confirm the documentation contract

Use these safe defaults unless the repository already defines intentional alternatives:

| Setting | Default |
| --- | --- |
| `mode` | `publish` |
| `docs-dir` | detected docs directory, otherwise `docs` |
| `versions-file` | empty; versioning stays disabled |
| `root-slug` | `docs` |
| `root-title` | `Docs` |
| `create-h1` | `false` |
| `rewrite-links` | `true` |
| `status` | `draft` |
| `delete-mode` | `trash` |
| `dry-run` | `true` |

Use `manifest-file` only when the desired titles, slugs, order, or parent relationships cannot be derived from the directory tree. Use `redirects-file` only when a supplied migration requires old paths to remain discoverable. Do not combine either top-level input with `versions-file`; versioned manifests and redirects belong to their owning version entry.

DocsPress manages only pages carrying its sentinel. Treat unmanaged-page conflicts as a stop condition; never delete or overwrite those pages to make a run pass.

### Enable API versioning only when the repository needs it

Without `versions-file`, keep the existing unversioned collection, synchronization, URLs, and reverse pull requests unchanged.

When readers need more than one maintained API release, create or reuse one ordered repository-relative JSON registry:

```json
{
  "latest": "v3",
  "versions": [
    { "id": "v3", "label": "v3", "source": { "type": "root" } },
    { "id": "v2", "label": "v2", "source": { "type": "directory", "path": "v2" } },
    { "id": "v1", "label": "v1", "source": { "type": "suffix", "suffix": ".v1" } }
  ]
}
```

Pass its path as `versions-file`. Preserve the repository’s existing layout: root discovery for the named latest version, a directory below `docs-dir`, a filename suffix before `.md`, or a repository-relative per-version manifest. A version entry may also own a redirects file.

Validate the registry with the collector from the pinned DocsPress revision:

- IDs are unique lowercase URL-safe slugs and `latest` names one configured entry.
- A root source, when present, is the repository latest; it excludes files claimed by other sources.
- Every Markdown file has one source owner, every path stays within its allowed root, and normalized logical routes are unique within a version.
- Directory, suffix, manifest, and redirect paths exist and do not escape the repository.
- Relative links resolve inside the source version; a same-route file in another version is never a substitute.

Versioning requires the matching DocsPress Blocks plugin. Synchronization creates the `docspress_version` Page taxonomy, preserves exact source paths and logical routes as managed metadata, and keeps one active term as the effective latest. The repository latest is the default; a valid active override under **Settings → DocsPress** changes public routing without renaming Pages.

## 3. Create or repair the GitHub Actions workflow

Create `.github/workflows/sync-docs.yml` when no DocsPress workflow exists. Start with a manual trigger. Detect the default branch and docs path now, but add an automatic default-branch trigger only after the manual dry-run and draft-write lifecycle is proven.

Resolve both actions to verified full commit SHAs. Verify that the DocsPress SHA belongs to the official `Automattic/docspress` repository and inspect `action.yml` at that exact revision before using its inputs. Follow a stricter existing repository pinning policy when present. Never give a floating action broad access to a WordPress token.

```yaml
name: Sync docs to WordPress

on:
  workflow_dispatch:

permissions:
  contents: read

jobs:
  sync:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@VERIFIED_FULL_COMMIT_SHA
      - uses: Automattic/docspress@VERIFIED_FULL_COMMIT_SHA
        with:
          wordpress-site: example.wordpress.com
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          # versions-file: docs-versions.json
          root-slug: docs
          root-title: Docs
          create-h1: false
          rewrite-links: true
          status: draft
          delete-mode: trash
          dry-run: true
```

For self-hosted WordPress, also set `wordpress-url: https://example.com`. Do not append `/wp-json`; DocsPress does that itself.

Do not enable reverse sync during the initial installation. After manual dry-run, draft-write, and default-branch publishing are proven, the user may opt into `mode: reconcile`. Add an off-hour five-minute schedule, non-cancelling concurrency, and the minimum GitHub write permissions:

```yaml
on:
  push:
    branches: [main]
    paths: ["docs/**", ".github/workflows/sync-docs.yml"]
  schedule:
    - cron: "3/5 * * * *"
  workflow_dispatch:

permissions:
  contents: write
  pull-requests: write

concurrency:
  group: docspress-sync
  cancel-in-progress: false
```

Set `mode: reconcile` on the pinned DocsPress step. Confirm the repository permits GitHub Actions to create pull requests, or use an approved GitHub App/PAT through `github-token`. Explain that the WordPress token still accesses only WordPress; the GitHub token maintains the action-owned `docspress/wordpress-sync` branch and rolling pull request. With versioning, WordPress edits return to the exact root-, directory-, suffix-, or manifest-owned paths stored on each Page. The generated pull request title and body group changes by version unless `pull-request-title` explicitly overrides the title. Enabling this mode authorizes ongoing WordPress metadata writes and GitHub branch/PR writes, so obtain explicit approval for both boundaries.

Do not invent a release tag or copy an unverified SHA. If an immutable revision cannot be verified, stop and ask before using a floating ref such as `@main`.

## 4. Configure authentication

### WordPress.com

1. Have the user create a WordPress.com application at `https://developer.wordpress.com/apps/` with redirect URI `http://localhost:8787/callback`.
2. Resolve an exact DocsPress npm version matching the verified official action revision. Do not execute a floating `npx docspress token` command.
3. Have the user run the verified helper in their own trusted local terminal. The current helper accepts the OAuth client secret as a command-line argument, so the agent must not execute it, capture its output, or ask the user to paste the secret into chat. Tell the user that the masked prompt below avoids shell-history exposure but the secret can still be briefly visible to local process inspection. For this skill revision, the matching package version is `0.2.0`:

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

The helper requests the broad `global` scope and, with `--set-secret`, stores `WP_ACCESS_TOKEN`. Keep that token out of logs and expose it only to the pinned DocsPress step. Do not use the helper without `--set-secret` in an agent-observed session because that mode prints the token and a secret-setting command.

### Self-hosted WordPress

DocsPress sends `Authorization: Bearer …`. Core WordPress does not provision that token through this project. Confirm the site already has a bearer-token authentication mechanism and that the token can list, create, update, and trash Pages. Store it as `WP_ACCESS_TOKEN`; never commit it.

Verify only the secret name:

```bash
gh secret list --repo OWNER/REPO
```

## 5. Prepare the WordPress presentation layer

Unversioned REST sync works without the companion theme or blocks plugin. Install them when the user wants the full DocsPress reading experience. The matching DocsPress Blocks plugin is mandatory when `versions-file` is enabled.

1. Package `theme/` as a WordPress theme directory named `docspress/` with `style.css` at its root, using only a verified official DocsPress checkout and revision.
2. Package `plugins/docspress-blocks/` with `docspress-blocks.php` at its root from that same verified revision.
3. Prefer a staging site or draft Pages for the first installation.
4. Upload through WordPress Admin, or use WP-CLI when authorized:

```bash
wp theme install /absolute/path/docspress-theme.zip
wp plugin install /absolute/path/docspress-blocks.zip --activate
```

Installing or activating a plugin and activating a theme change WordPress state. Request separate approval before plugin installation/activation and before running `wp theme activate docspress` or performing the equivalent Admin action.

After the first sync, open **Appearance → Editor**. Set the documentation root on the Docs Navigation block in the Page template and keep `create-h1: false` because the theme renders the Page title.

The bundled block theme supplies:

- a customizable Header with the Version Switcher dropdown immediately before Command Search;
- a full-width, customizable Version Notice below the Header on historical Pages;
- clean effective-latest routes, historical version prefixes, version-aware search/navigation/breadcrumbs/sitemaps, exact `.md` routes, and `llms.txt`;
- a Pages screen with version filtering, Latest/Inactive badges, and clickable GitHub source paths for managed docs;
- a movable **Was This Helpful?** template block with per-Page aggregate results in the editor;
- optional native threaded comments through the editable Comments template part.

Keep these as Site Editor blocks and template parts so the active style family and Global Styles control colors, gradients, typography, spacing, borders, dimensions, positioning, shadows, anchors, and custom classes. Do not hard-code presentation into synchronized Markdown.

## 6. Verify in increasing-risk order

1. Confirm every workflow path and optional manifest, redirect, and version-registry file exists.
2. Validate YAML with an available YAML parser or `actionlint` when installed.
3. Run repository tests, lint, build, and `git diff --check` when those commands exist.
4. Confirm `WP_ACCESS_TOKEN` appears in `gh secret list` without reading its value.
5. Commit and push only when the user asked for repository publication.
6. Dispatch the manual workflow only after approval with `dry-run: true`; inspect the Actions summary and `summary-json`.
7. Stop on unmanaged conflicts, authentication errors, unexpected deletes, or an incorrect page tree.
8. Explain that `delete-mode: trash` is a Page mutation once dry-run is disabled. After explicit approval for Page creation, updates, and trash operations, change only `dry-run` to `false` and keep `status: draft`.
9. Inspect the generated WordPress hierarchy, blocks, rewritten links, exact GitHub paths, and edit links. For versioning, also verify taxonomy terms, latest state, Pages filters, clean/latest and historical routes, switcher counterparts, missing-page fallback, `.md`, `llms.txt`, and reverse-sync destinations.
10. Changing dry-run to false creates an ongoing write-capable workflow. Obtain explicit approval before committing that state. Add an automatic default-branch `push` trigger only after the manual lifecycle succeeds and the user approves ongoing synchronization.
11. Change `status` to `publish` only after the user approves public publication.

## Completion report

Report:

- docs directory and root URL;
- versioning state, registry path, repository latest, and effective latest;
- workflow path and trigger branch;
- WordPress mode and site, without secrets;
- companion theme/plugin installation state and Site Editor components enabled;
- verification commands and results;
- current safety state: dry-run, draft, or published;
- remaining user action, such as creating OAuth credentials, adding the secret, approving activation, pushing, or publishing.
