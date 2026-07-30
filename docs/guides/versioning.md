---
title: Version API documentation
sidebar_position: 45
---

DocsPress can publish several API documentation versions from one repository into one WordPress site. Versioning is opt-in: without `versions-file`, collection, synchronization, URLs, and reverse pull requests behave exactly as before.

[Run the versioning example →](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-versioning.json&page-title=DocsPress%20Versioning%20Example)

The example starts on a historical v1 Page so the version switcher and outdated-version notice are visible immediately. It also seeds v3 from root files, v2 from a directory, v1 from filename suffixes, missing counterparts, version metadata, and the Pages-screen filters.

![Historical v1 documentation with the version dropdown in the header and a full-width notice linking to v3](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/versioning-frontend.jpg "The historical Page keeps its version-aware navigation while making the latest documentation one click away.")

## Configure the version registry

Add a repository-relative JSON file and pass it to the Action:

```yaml
- uses: Automattic/docspress@main
  with:
    wordpress-site: example.wordpress.com
    wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
    docs-dir: docs
    versions-file: docs-versions.json
    status: draft
    delete-mode: trash
    dry-run: true
```

```json
{
  "latest": "v3",
  "versions": [
    {
      "id": "v3",
      "label": "v3",
      "source": { "type": "root" }
    },
    {
      "id": "v2",
      "label": "v2",
      "source": { "type": "directory", "path": "v2" }
    },
    {
      "id": "v1",
      "label": "v1",
      "source": { "type": "suffix", "suffix": ".v1" }
    }
  ]
}
```

Entries are ordered for WordPress administration and the version switcher. IDs are lowercase URL-safe slugs. `latest` must name an entry. If a root source exists, it must be the repository latest version.

## Supported source layouts

### Root latest

Root discovery uses unclaimed Markdown below `docs-dir`. Files claimed by a directory, suffix, or manifest version are excluded:

```text
docs/hello.md       -> v3 /hello
docs/authentication/oauth.md -> v3 /authentication/oauth
```

Root Markdown belongs to the configured latest ID. DocsPress does not create a separate Current term.

### Version directories

Directory paths are relative to `docs-dir`:

```json
{ "id": "v2", "source": { "type": "directory", "path": "v2" } }
```

```text
docs/v2/hello.md    -> v2 /hello
docs/v2/authentication/api-keys.md -> v2 /authentication/api-keys
```

### Filename suffixes

The suffix is removed before the logical route is calculated:

```json
{ "id": "v1", "source": { "type": "suffix", "suffix": ".v1" } }
```

```text
docs/hello.v1.md    -> v1 /hello
docs/authentication/api-keys.v1.md -> v1 /authentication/api-keys
```

### Per-version manifests and redirects

A manifest path is repository-relative. Its Markdown sources are resolved relative to the manifest, exactly like the normal `manifest-file` format:

```json
{
  "id": "v1",
  "source": {
    "type": "manifest",
    "path": "versions/v1/manifest.json"
  },
  "redirects": "versions/v1/redirects.json"
}
```

Redirect entries use logical routes within that version. A redirect from `old` to `hello` in v1 points to `/docs/v1/hello/`, not to the same route in another version.

DocsPress rejects unsafe paths, invalid IDs, duplicate sources, unknown latest IDs, overlapping source ownership, parent cycles, and two files that produce the same logical route.

## WordPress data and administration

The DocsPress Blocks plugin is required when `versions-file` is enabled. Synchronization creates a REST-visible `docspress_version` taxonomy on Pages and stores:

- version order and active state;
- repository-default and effective-latest flags;
- Page version ID and logical route;
- stable identity such as `v1:authentication/api-keys`;
- source layout and exact source path;
- the managed docs root.

Removed versions become inactive instead of losing their taxonomy history. Pages follow `delete-mode`.

Pages remain stored below stable internal parents such as `/docs/v1/authentication/api-keys/`. On **Pages → All Pages**, the Docs version column is sortable, labels filter the list, Latest and Inactive states are visible, and the ordered dropdown includes All versions, Latest, and every known version with counts. When synchronized Pages include GitHub metadata, a **GitHub path** column links each exact repository-relative Markdown path to its configured repository and ref in a new tab; virtual containers and unrelated Pages display a dash. Managed Page assignment is read-only because synchronization owns it.

![WordPress Pages administration showing version filters, version-specific Page hierarchies, latest badges, and exact GitHub source paths](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/pages-admin.jpg "Filter the Pages table by version and follow an exact Markdown source path without mixing version trees.")

Use **Settings → DocsPress** to inspect the repository default and effective latest. An active administrator override wins; an invalid or inactive override falls back to the repository default. Changing the override updates routing immediately without renaming Pages.

![DocsPress version settings showing v3 as the repository default and effective latest with no administrator override](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/version-settings.jpg "The repository default remains visible beside the effective latest version and optional site override.")

## Public routes

The effective latest version uses clean URLs:

```text
/docs/hello/
/docs/authentication/oauth/
```

Historical versions retain the version prefix:

```text
/docs/v2/hello/
/docs/v1/authentication/api-keys/
```

The effective latest’s explicit prefix permanently redirects to the clean route. Canonical Page links, navigation, breadcrumbs, search, adjacent navigation, XML sitemaps, `.md` responses, and `llms.txt` use the same version context. `llms.txt` indexes the effective latest tree; historical `.md` URLs remain directly addressable.

The switcher matches Pages by logical route. If the selected version has no counterpart, it links to that version’s root and announces that the requested Page is unavailable.

## Version interface blocks

The bundled Header template places a compact Version Switcher dropdown immediately before Command Search. The Page template places the historical-version notice as a full-width status bar directly below the Header. They remain independent Site Editor blocks, so a site owner can move, remove, or restyle either one.

### Customize the header switcher

Open **Appearance → Editor → Design → Templates → Pages**, select the Header, and choose **Edit original**. Select Version Switcher to configure its label, presentation, badges, single-version behavior, and missing-page wording. The block can remain immediately before Command Search or move anywhere the Header layout allows.

![Site Editor Header template part with the Version Switcher selected and its settings visible](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/site-editor-version-switcher.jpg "Configure the dropdown in context while retaining native Header layout controls.")

`docspress/version-switcher` supports:

- label text and visibility;
- select-menu or link-list presentation;
- latest badge visibility;
- hiding when only one version exists;
- missing-page wording.

### Customize the historical-version notice

Return to the Pages template and select Version Notice to edit its message, latest-link label, icon, and dismissibility. The preview uses example version data so the bar remains editable even when the current Page is the latest version.

![Site Editor Pages template with the full-width Version Notice selected and its message, link, icon, and dismissibility controls visible](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/site-editor-version-notice.jpg "Edit the historical-version message while previewing its full-width placement below the Header.")

`docspress/version-notice` appears only on historical versions and supports:

- a message with safe `{current}` and `{latest}` placeholders;
- latest-link label;
- icon visibility;
- visitor dismissibility.

The Styles panel for either block exposes native colors and gradients, link color, typography, spacing and block gap, borders and radius, dimensions, sticky positioning, and shadow. Advanced controls include the anchor and custom classes. Full alignment plus the Version Notice block’s `header-version-notice` class creates the bundled bar; changing its alignment or removing that class returns the notice to its portable card treatment.

Both are API v3 server-rendered blocks. The DocsPress, WordPress.org, WordPress.com, and Jetpack style families provide complete component recipes while semantic theme tokens keep the blocks portable to another block theme.

Developers can customize resolved choices with `docspress_version_switcher_choices`, the notice text with `docspress_version_notice_message`, the effective latest ID with `docspress_effective_latest_version`, and generated destinations with `docspress_version_target_url`.

## Reverse synchronization

The source path stored on each Page is authoritative. WordPress edits return to the exact file:

```text
v3 /authentication/oauth -> docs/authentication/oauth.md
v2 /authentication/api-keys -> docs/v2/authentication/api-keys.md
v1 /authentication/api-keys -> docs/authentication/api-keys.v1.md
```

Manifest-backed title changes update the owning manifest. Links are converted only against Pages in their stable version tree, so a v1 edit cannot silently target a same-route v2 file.

DocsPress maintains one rolling `docspress/wordpress-sync` pull request. One changed version uses `docs(v1): sync changes from WordPress`; multiple versions use `docs(versions): sync N files from WordPress`. The body groups exact paths under version headings and identifies the effective latest. `pull-request-title` remains an explicit override.

## Migrating from Docusaurus

Docusaurus stores frozen releases in `versioned_docs/version-[name]/`, keeps an ordered `versions.json`, and normally serves its configured latest release at `/docs/` while the working `docs/` tree can appear at `/docs/next/`. Its version command copies the current docs tree and sidebar into versioned directories. Docusaurus also recommends file-path Markdown links so the build can resolve them within the correct version. See the [Docusaurus versioning guide](https://docusaurus.io/docs/versioning).

DocsPress can adopt copied Docusaurus trees with directory entries, or keep a repository’s existing suffix/manifests without forcing a copy-based release workflow. The important semantic difference is that DocsPress’s root source is the named repository latest version; a WordPress override can change the public effective latest without changing repository files. If a project needs a separately published unreleased `next` tree, model it as another explicit version source rather than an unnamed Current term.

<!-- wp:docspress/callout {"tone":"warning","title":"Version only when readers need it","content":"<p>Every active version increases editorial and maintenance cost. Prefer major API compatibility boundaries, keep the active set small, and deactivate obsolete versions when their support window ends.</p>","collapsible":false} /-->
