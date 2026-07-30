---
title: Version API documentation
sidebar_position: 45
---

DocsPress can publish several API documentation versions from one repository into one WordPress site. Versioning is opt-in: without `versions-file`, collection, synchronization, URLs, and reverse pull requests behave exactly as before.

<!-- wp:docspress/callout {"tone":"success","title":"Existing documentation keeps its current behavior","content":"<p>Add <code>versions-file</code> only when one WordPress site must publish more than one API release. Repositories without it keep their existing collection, routes, synchronization, and reverse pull requests.</p>","collapsible":false} /-->

[Run the versioning example →](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-versioning.json&page-title=DocsPress%20Versioning%20Example)

The example starts on a historical v1 Page so the version switcher and outdated-version notice are visible immediately. It also seeds v3 from root files, v2 from a directory, v1 from filename suffixes, missing counterparts, version metadata, and the Pages-screen filters.

![Historical v1 documentation with the version dropdown in the header and a full-width notice linking to v3](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/versioning-frontend.jpg "The historical Page keeps its version-aware navigation while making the latest documentation one click away.")

## Configure the version registry

Add a repository-relative JSON file and pass it to the Action. DocsPress uses the registry throughout collection, synchronization, public routing, and reverse pull requests.

<!-- wp:docspress/flow {"start":1,"steps":[{"title":"Declare ordered releases","content":"<p>Name the repository latest version and map every active release to a root, directory, suffix, or manifest source.</p>"},{"title":"Synchronize version data","content":"<p>DocsPress validates ownership, creates or updates version terms, then builds each release as a separate managed Page tree.</p>"},{"title":"Serve the selected release","content":"<p>WordPress exposes the effective latest at clean URLs and historical releases below stable version prefixes.</p>"}]} /-->

<!-- wp:docspress/colorful-code {"language":"yaml","filename":".github/workflows/sync-docs.yml","code":"- uses: Automattic/docspress@main\n  with:\n    wordpress-site: example.wordpress.com\n    wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}\n    docs-dir: docs\n    versions-file: docs-versions.json\n    status: draft\n    delete-mode: trash\n    dry-run: true","highlightedLines":"6","showLineNumbers":true,"caption":"Point the Action at the ordered version registry. Start with a draft dry run before publishing.","diffMode":"none","copyMode":"all","annotations":[{"line":6,"content":"<p>Versioning remains disabled until this input is present.</p>"}]} /-->

<!-- wp:docspress/colorful-code {"language":"json","filename":"docs-versions.json","code":"{\n  \"latest\": \"v3\",\n  \"versions\": [\n    {\n      \"id\": \"v3\",\n      \"label\": \"v3\",\n      \"source\": { \"type\": \"root\" }\n    },\n    {\n      \"id\": \"v2\",\n      \"label\": \"v2\",\n      \"source\": { \"type\": \"directory\", \"path\": \"v2\" }\n    },\n    {\n      \"id\": \"v1\",\n      \"label\": \"v1\",\n      \"source\": { \"type\": \"suffix\", \"suffix\": \".v1\" }\n    }\n  ]\n}","highlightedLines":"2,4-18","showLineNumbers":true,"caption":"Order controls the WordPress administration view and version switcher.","diffMode":"none","copyMode":"all","annotations":[{"line":2,"content":"<p>The repository latest must name one configured version.</p>"}]} /-->

Entries are ordered for WordPress administration and the version switcher. IDs are lowercase URL-safe slugs. `latest` must name an entry. If a root source exists, it must be the repository latest version.

<!-- wp:docspress/fields {"title":"Version registry fields","description":"The registry is deliberately small; source-specific values are required only by the selected layout.","fields":[{"name":"latest","type":"string","required":true,"defaultValue":"","description":"Version ID treated as the repository default latest release.","values":"configured version ID","deprecated":false},{"name":"versions[].id","type":"string","required":true,"defaultValue":"","description":"Lowercase URL-safe version identifier used in taxonomy terms and historical routes.","values":"for example v1, v2, v3","deprecated":false},{"name":"versions[].label","type":"string","required":false,"defaultValue":"","description":"Reader-facing label. The ID is used when the label is omitted.","values":"","deprecated":false},{"name":"versions[].source.type","type":"enum","required":true,"defaultValue":"","description":"How this version owns Markdown files.","values":"root, directory, suffix, manifest","deprecated":false},{"name":"versions[].source.path","type":"string","required":false,"defaultValue":"","description":"Directory below docs-dir or repository-relative manifest path, depending on the source type.","values":"","deprecated":false},{"name":"versions[].source.suffix","type":"string","required":false,"defaultValue":"","description":"Filename marker removed before logical routes are calculated.","values":"for example .v1","deprecated":false},{"name":"versions[].redirects","type":"string","required":false,"defaultValue":"","description":"Repository-relative redirect manifest scoped to this version.","values":"","deprecated":false}],"searchable":true,"compact":false} /-->

## Supported source layouts

The layouts can coexist. This example keeps the current v3 files at the root of `docs/`, v2 in a directory, and v1 in filename suffixes:

<!-- wp:docspress/file-tree {"root":"repository/","tree":"docs-versions.json\ndocs/\n  hello.md\n  authentication/\n    oauth.md\n    api-keys.v1.md\n  v2/\n    hello.md\n    authentication/\n      api-keys.md","caption":"One registry can collect root, directory, and filename-suffix sources without moving every release into the same convention.","collapsible":true,"open":true} /-->

<!-- wp:docspress/code-tabs {"tabs":[{"label":"Root latest","language":"json","filename":"v3 source","code":"{\n  \"id\": \"v3\",\n  \"label\": \"v3\",\n  \"source\": { \"type\": \"root\" }\n}"},{"label":"Directory","language":"json","filename":"v2 source","code":"{\n  \"id\": \"v2\",\n  \"label\": \"v2\",\n  \"source\": {\n    \"type\": \"directory\",\n    \"path\": \"v2\"\n  }\n}"},{"label":"Filename suffix","language":"json","filename":"v1 source","code":"{\n  \"id\": \"v1\",\n  \"label\": \"v1\",\n  \"source\": {\n    \"type\": \"suffix\",\n    \"suffix\": \".v1\"\n  }\n}"},{"label":"Manifest","language":"json","filename":"manifest source","code":"{\n  \"id\": \"v1\",\n  \"source\": {\n    \"type\": \"manifest\",\n    \"path\": \"versions/v1/manifest.json\"\n  },\n  \"redirects\": \"versions/v1/redirects.json\"\n}"}],"showLineNumbers":true,"caption":"Choose the source contract that matches each existing release."} /-->

### Root latest

Root discovery uses unclaimed Markdown below `docs-dir`. Files claimed by a directory, suffix, or manifest version are excluded:

<!-- wp:docspress/colorful-code {"language":"plaintext","filename":"Collected v3 routes","code":"docs/hello.md                     -> v3 /hello\ndocs/authentication/oauth.md      -> v3 /authentication/oauth","highlightedLines":"","showLineNumbers":false,"caption":"Unclaimed root Markdown belongs to the registry latest version.","diffMode":"none","copyMode":"all","annotations":[]} /-->

Root Markdown belongs to the configured latest ID. DocsPress does not create a separate Current term.

### Version directories

Directory paths are relative to `docs-dir`:

<!-- wp:docspress/colorful-code {"language":"plaintext","filename":"Collected v2 routes","code":"docs/v2/hello.md                       -> v2 /hello\ndocs/v2/authentication/api-keys.md     -> v2 /authentication/api-keys","highlightedLines":"","showLineNumbers":false,"caption":"The directory prefix identifies the version and is removed from its logical routes.","diffMode":"none","copyMode":"all","annotations":[]} /-->

### Filename suffixes

The suffix is removed before the logical route is calculated:

<!-- wp:docspress/colorful-code {"language":"plaintext","filename":"Collected v1 routes","code":"docs/hello.v1.md                       -> v1 /hello\ndocs/authentication/api-keys.v1.md     -> v1 /authentication/api-keys","highlightedLines":"","showLineNumbers":false,"caption":"The suffix marks ownership without adding a public path segment to the logical route.","diffMode":"none","copyMode":"all","annotations":[]} /-->

### Per-version manifests and redirects

A manifest path is repository-relative. Its Markdown sources are resolved relative to the manifest, exactly like the normal `manifest-file` format:

Redirect entries use logical routes within that version. A redirect from `old` to `hello` in v1 points to `/docs/v1/hello/`, not to the same route in another version.

<!-- wp:docspress/callout {"tone":"warning","title":"Every Markdown file must have one owner","content":"<p>DocsPress rejects unsafe paths, invalid IDs, duplicate sources, unknown latest IDs, overlapping source ownership, parent cycles, and two files that produce the same logical route.</p>","collapsible":false} /-->

## WordPress data and administration

The DocsPress Blocks plugin is required when `versions-file` is enabled. Synchronization creates a REST-visible `docspress_version` taxonomy on Pages. Version terms record order, active state, repository-default latest, and effective latest; managed Pages retain the fields needed for routing and exact reverse synchronization.

<!-- wp:docspress/fields {"title":"Managed Page version data","description":"Synchronization owns these REST-visible Page fields and repairs drift even when visible content is unchanged.","fields":[{"name":"_docspress_version_id","type":"string","required":true,"defaultValue":"","description":"Taxonomy-aligned version ID for this Page.","values":"configured version ID","deprecated":false},{"name":"_docspress_logical_route","type":"string","required":true,"defaultValue":"","description":"Route shared by counterpart Pages across versions.","values":"for example authentication/api-keys","deprecated":false},{"name":"_docspress_page_identity","type":"string","required":true,"defaultValue":"","description":"Stable identity combining version and logical route.","values":"for example v1:authentication/api-keys","deprecated":false},{"name":"_docspress_source_type","type":"enum","required":true,"defaultValue":"","description":"Source layout that owns the Page.","values":"root, directory, suffix, manifest","deprecated":false},{"name":"_docspress_source_path","type":"string","required":true,"defaultValue":"","description":"Exact Markdown destination used for reverse synchronization.","values":"","deprecated":false},{"name":"_docspress_docs_root","type":"string","required":true,"defaultValue":"docs","description":"Managed documentation root used to isolate routes and lookups.","values":"","deprecated":false}],"searchable":true,"compact":false} /-->

<!-- wp:docspress/diagram {"title":"Version data stays attached through both directions","type":"sequence","source":"GitHub source -> DocsPress Action: collect exact path and logical route\nDocsPress Action -> WordPress Page: synchronize taxonomy and metadata\nWordPress Page -> DocsPress Action: read version-owned edits\nDocsPress Action -> GitHub source: propose the exact destination","caption":"The version ID and exact source path remain authoritative instead of being inferred from a same-route Page."} /-->

Removed versions become inactive instead of losing their taxonomy history. Pages follow `delete-mode`.

Pages remain stored below stable internal parents such as `/docs/v1/authentication/api-keys/`. On **Pages → All Pages**, the Docs version column is sortable, labels filter the list, Latest and Inactive states are visible, and the ordered dropdown includes All versions, Latest, and every known version with counts. When synchronized Pages include GitHub metadata, a **GitHub path** column links each exact repository-relative Markdown path to its configured repository and ref in a new tab; virtual containers and unrelated Pages display a dash. Managed Page assignment is read-only because synchronization owns it.

![WordPress Pages administration showing version filters, version-specific Page hierarchies, latest badges, and exact GitHub source paths](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/pages-admin.jpg "Filter the Pages table by version and follow an exact Markdown source path without mixing version trees.")

Use **Settings → DocsPress** to inspect the repository default and effective latest. An active administrator override wins; an invalid or inactive override falls back to the repository default. Changing the override updates routing immediately without renaming Pages.

![DocsPress version settings showing v3 as the repository default and effective latest with no administrator override](https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/versioning/version-settings.jpg "The repository default remains visible beside the effective latest version and optional site override.")

## Public routes

The effective latest version uses clean URLs while historical versions retain their version prefix:

<!-- wp:docspress/code-tabs {"tabs":[{"label":"Effective latest","language":"plaintext","filename":"v3 public routes","code":"/docs/hello/\n/docs/authentication/oauth/"},{"label":"Historical versions","language":"plaintext","filename":"v2 and v1 public routes","code":"/docs/v2/hello/\n/docs/v1/authentication/api-keys/"}],"showLineNumbers":false,"caption":"Changing the effective latest updates public routing without renaming the stable internal Page trees."} /-->

The effective latest’s explicit prefix permanently redirects to the clean route. Canonical Page links, navigation, breadcrumbs, search, adjacent navigation, XML sitemaps, `.md` responses, and `llms.txt` use the same version context. `llms.txt` indexes the effective latest tree; historical `.md` URLs remain directly addressable.

The switcher matches Pages by logical route. If the selected version has no counterpart, it links to that version’s root and announces that the requested Page is unavailable.

<!-- wp:docspress/result {"status":"neutral","title":"Missing counterparts stay inside the selected version","content":"<p>The switcher never substitutes a same-route Page from another release. It opens the selected version root and exposes the unavailable-page state to the reader.</p>","meta":"Version-safe fallback"} /-->

## Version interface blocks

The bundled Header template places a compact Version Switcher dropdown immediately before Command Search. The Page template places the historical-version notice as a full-width status bar directly below the Header. They remain independent Site Editor blocks, so a site owner can move, remove, or restyle either one.

<!-- wp:docspress/callout {"tone":"tip","title":"The reader interface remains theme-editable","content":"<p>Version Switcher and Version Notice are dynamic API v3 blocks. Their placement is bundled as a useful default, while block supports and Site Editor controls remain available to the active theme.</p>","collapsible":false} /-->

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

<!-- wp:docspress/colorful-code {"language":"plaintext","filename":"Exact reverse-sync destinations","code":"v3 /authentication/oauth\n  -> docs/authentication/oauth.md\n\nv2 /authentication/api-keys\n  -> docs/v2/authentication/api-keys.md\n\nv1 /authentication/api-keys\n  -> docs/authentication/api-keys.v1.md","highlightedLines":"2,5,8","showLineNumbers":true,"caption":"The owning version determines the destination; matching logical routes in other releases are ignored.","diffMode":"none","copyMode":"all","annotations":[{"line":8,"content":"<p>Filename-suffix ownership survives WordPress editing and pull-request generation.</p>"}]} /-->

Manifest-backed title changes update the owning manifest. Links are converted only against Pages in their stable version tree, so a v1 edit cannot silently target a same-route v2 file.

DocsPress maintains one rolling `docspress/wordpress-sync` pull request. One changed version uses `docs(v1): sync changes from WordPress`; multiple versions use `docs(versions): sync N files from WordPress`. The body groups exact paths under version headings and identifies the effective latest. `pull-request-title` remains an explicit override.

## Migrating from Docusaurus

Docusaurus stores frozen releases in `versioned_docs/version-[name]/`, keeps an ordered `versions.json`, and normally serves its configured latest release at `/docs/` while the working `docs/` tree can appear at `/docs/next/`. Its version command copies the current docs tree and sidebar into versioned directories. Docusaurus also recommends file-path Markdown links so the build can resolve them within the correct version. See the [Docusaurus versioning guide](https://docusaurus.io/docs/versioning).

DocsPress can adopt copied Docusaurus trees with directory entries, or keep a repository’s existing suffix/manifests without forcing a copy-based release workflow. The important semantic difference is that DocsPress’s root source is the named repository latest version; a WordPress override can change the public effective latest without changing repository files. If a project needs a separately published unreleased `next` tree, model it as another explicit version source rather than an unnamed Current term.

<!-- wp:docspress/callout {"tone":"warning","title":"Version only when readers need it","content":"<p>Every active version increases editorial and maintenance cost. Prefer major API compatibility boundaries, keep the active set small, and deactivate obsolete versions when their support window ends.</p>","collapsible":false} /-->
