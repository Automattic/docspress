---
title: Use contextual sidebars
sidebar_position: 35
sidebar_collapsed: false
---

DocsPress normally renders one automatic sidebar from the synchronized WordPress Page tree. Keep that zero-configuration behavior for small and medium documentation sites. Add contextual sidebars only when distinct areas such as API reference, extensions, or CLI documentation need focused local navigation.

[Run the contextual-sidebars example →](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-sidebars.json&page-title=DocsPress%20Contextual%20Sidebars)

The example starts in the API reference. Its sidebar contains only API Pages. Use the breadcrumbs and cross-links in the example Pages to visit the default guide tree or Extensions section and see the automatic sidebar and previous/next links switch context.

## Start with the simple sidebar

Do not add `sidebars-file` when the complete documentation hierarchy belongs in one sidebar:

```text
docs/
├── index.md
├── guides/
│   ├── index.md
│   └── quickstart.md
└── reference/
    ├── index.md
    └── configuration.md
```

DocsPress continues to derive one nested Page tree from those files. Existing repositories receive no new metadata or navigation behavior unless they explicitly enable the advanced configuration.

## Add a route registry

Create `docs/sidebars.yml` when the documentation has sections that should become independent navigation contexts:

```yaml
version: 1
default: docs

sidebars:
  docs: .
  api: apis
  extensions: extensions
```

Then pass the repository-relative file to the Action:

```yaml
- uses: Automattic/docspress@main
  with:
    wordpress-site: example.wordpress.com
    wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
    docs-dir: docs
    sidebars-file: docs/sidebars.yml
    status: draft
    dry-run: true
```

The DocsPress theme is required to render these contexts. Another theme can support the same feature by reading the synchronized `_docspress_sidebar_id` and `_docspress_sidebar_root` Page metadata.

## Understand route matching

Sidebar roots are logical routes relative to `docs-dir`, not filesystem paths with extensions:

| Page source | Logical route | Sidebar |
| --- | --- | --- |
| `docs/index.md` | `.` | `docs` |
| `docs/guides/quickstart.md` | `guides/quickstart` | `docs` |
| `docs/apis/index.md` | `apis` | `api` |
| `docs/apis/rest-api.md` | `apis/rest-api` | `api` |
| `docs/extensions/build.md` | `extensions/build` | `extensions` |

The registry follows four rules:

1. `default` must name one configured sidebar.
2. The default sidebar must use the root `.`.
3. Every non-default root must resolve to a real documentation route.
4. The most-specific matching root wins, so a future `apis/rest` context can override `apis` for that subtree.

The same logical-route contract works with folder-derived Pages, `manifest-file`, and every tree produced by `versions-file`.

## What changes on the site

On a Page assigned to a contextual sidebar, the DocsPress theme:

- starts automatic Docs Navigation at that sidebar's root Page;
- excludes Pages assigned to other sidebar IDs;
- scopes Adjacent Navigation so previous and next links cannot cross contexts;
- keeps the current Page, section collapse settings, Page order, and mobile drawer behavior intact.

Contextual sidebars do not change Command Search, the header Navigation block, or classic-menu mode. Search remains site-wide. Use an ordinary WordPress Navigation block for links that should remain available across every documentation context.

## Copy the boilerplate

The repository includes a complete example under [`examples/contextual-sidebars`](https://github.com/Automattic/docspress/tree/main/examples/contextual-sidebars):

```text
examples/contextual-sidebars/
├── README.md
└── docs/
    ├── sidebars.yml
    ├── index.md
    ├── guides/
    │   ├── index.md
    │   └── quickstart.md
    ├── apis/
    │   ├── index.md
    │   ├── rest-api.md
    │   └── webhooks.md
    └── extensions/
        ├── index.md
        └── build-an-extension.md
```

The one-link demonstration is defined by [`theme/blueprint-sidebars.json`](https://github.com/Automattic/docspress/blob/main/theme/blueprint-sidebars.json). Maintainers can regenerate its deterministic Page fixture with:

```bash
npm run playground:sidebars
```

## Return to one sidebar

Remove `sidebars-file` from the workflow to return to the original automatic Page tree. The next synchronization removes the source-owned contextual metadata while preserving the Pages, hierarchy, `sidebar_position`, and `sidebar_collapsed` values.
