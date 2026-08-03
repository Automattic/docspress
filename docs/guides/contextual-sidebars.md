---
title: Use contextual sidebars
sidebar_position: 35
sidebar_collapsed: false
---

DocsPress normally renders one automatic sidebar from the synchronized WordPress Page tree. Keep that zero-configuration behavior for small and medium documentation sites. Add contextual sidebars only when distinct areas such as API reference, extensions, or CLI documentation need focused local navigation.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "success",
    "title": "See three sidebar contexts switch live",
    "content": "\u003cp\u003e\u003ca href=\"https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-sidebars.json\u0026amp;page-title=DocsPress%20Contextual%20Sidebars\"\u003eRun the contextual-sidebars example\u003c/a\u003e. It starts in the API reference, whose sidebar contains only API Pages. Follow the breadcrumbs and cross-links to the default guide tree or Extensions section to see the automatic sidebar and previous/next links switch context.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!TIP]
>
> **See three sidebar contexts switch live**
>
> [Run the contextual-sidebars example](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-sidebars.json&page-title=DocsPress%20Contextual%20Sidebars). It starts in the API reference, whose sidebar contains only API Pages. Follow the breadcrumbs and cross-links to the default guide tree or Extensions section to see the automatic sidebar and previous/next links switch context.
<!-- /docspress:block -->

## Start with the simple sidebar

Do not add `sidebars-file` when the complete documentation hierarchy belongs in one sidebar:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "docs/",
    "tree": "index.md\nguides/\n  index.md\n  quickstart.md\nreference/\n  index.md\n  configuration.md",
    "caption": "One Page tree needs no sidebar registry.",
    "collapsible": true,
    "open": true
  }
}
-->
#### docs/

```text
index.md
guides/
  index.md
  quickstart.md
reference/
  index.md
  configuration.md
```

_One Page tree needs no sidebar registry._
<!-- /docspress:block -->

DocsPress continues to derive one nested Page tree from those files. Existing repositories receive no new metadata or navigation behavior unless they explicitly enable the advanced configuration.

## Add a route registry

Create `docs/sidebars.yml` when the documentation has sections that should become independent navigation contexts:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": "docs/sidebars.yml",
    "code": "version: 1\ndefault: docs\n\nsidebars:\n  docs: .\n  api: apis\n  extensions: extensions",
    "highlightedLines": "2,5-7",
    "showLineNumbers": true,
    "caption": "Keep the root Page tree as the default, then name each focused route.",
    "diffMode": "none",
    "copyMode": "all",
    "annotations": [
      {
        "line": 2,
        "content": "\u003cp\u003eThe default must name the sidebar whose route is \u003ccode\u003e.\u003c/code\u003e.\u003c/p\u003e"
      }
    ]
  }
}
-->
**docs/sidebars.yml — Keep the root Page tree as the default, then name each focused route.**

```yaml
version: 1
default: docs

sidebars:
  docs: .
  api: apis
  extensions: extensions
```
<!-- /docspress:block -->

Then pass the repository-relative file to the Action:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": ".github/workflows/sync-docs.yml",
    "code": "- uses: Automattic/docspress@main\n  with:\n    wordpress-site: example.wordpress.com\n    wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}\n    docs-dir: docs\n    sidebars-file: docs/sidebars.yml\n    status: draft\n    dry-run: true",
    "highlightedLines": "6",
    "showLineNumbers": true,
    "caption": "Opt into contextual sidebars with one repository-relative Action input.",
    "diffMode": "none",
    "copyMode": "all",
    "annotations": [
      {
        "line": 6,
        "content": "\u003cp\u003eWithout this input, DocsPress keeps the original automatic sidebar.\u003c/p\u003e"
      }
    ]
  }
}
-->
**.github/workflows/sync-docs.yml — Opt into contextual sidebars with one repository-relative Action input.**

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
<!-- /docspress:block -->

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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/flow",
  "attrs": {
    "start": 1,
    "steps": [
      {
        "title": "Name the default",
        "content": "\u003cp\u003e\u003ccode\u003edefault\u003c/code\u003e must name one configured sidebar.\u003c/p\u003e"
      },
      {
        "title": "Keep the default at the root",
        "content": "\u003cp\u003eThe default sidebar must use the root \u003ccode\u003e.\u003c/code\u003e route.\u003c/p\u003e"
      },
      {
        "title": "Resolve every focused route",
        "content": "\u003cp\u003eEach non-default root must match a real documentation route.\u003c/p\u003e"
      },
      {
        "title": "Let the most-specific route win",
        "content": "\u003cp\u003eA future \u003ccode\u003eapis/rest\u003c/code\u003e context can override \u003ccode\u003eapis\u003c/code\u003e for that subtree.\u003c/p\u003e"
      }
    ]
  }
}
-->
1. **Name the default**

   `default` must name one configured sidebar.

2. **Keep the default at the root**

   The default sidebar must use the root `.` route.

3. **Resolve every focused route**

   Each non-default root must match a real documentation route.

4. **Let the most-specific route win**

   A future `apis/rest` context can override `apis` for that subtree.
<!-- /docspress:block -->

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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "examples/contextual-sidebars/",
    "tree": "README.md\ndocs/\n  sidebars.yml\n  index.md\n  guides/\n    index.md\n    quickstart.md\n  apis/\n    index.md\n    rest-api.md\n    webhooks.md\n  extensions/\n    index.md\n    build-an-extension.md",
    "caption": "A complete three-context documentation tree ready to copy.",
    "collapsible": true,
    "open": true
  }
}
-->
#### examples/contextual-sidebars/

```text
README.md
docs/
  sidebars.yml
  index.md
  guides/
    index.md
    quickstart.md
  apis/
    index.md
    rest-api.md
    webhooks.md
  extensions/
    index.md
    build-an-extension.md
```

_A complete three-context documentation tree ready to copy._
<!-- /docspress:block -->

The one-link demonstration is defined by [`theme/blueprint-sidebars.json`](https://github.com/Automattic/docspress/blob/main/theme/blueprint-sidebars.json). Maintainers can regenerate its deterministic Page fixture with:

```bash
npm run playground:sidebars
```

## Return to one sidebar

Remove `sidebars-file` from the workflow to return to the original automatic Page tree.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "The original sidebar is restored",
    "content": "\u003cp\u003eThe next synchronization removes the source-owned contextual metadata while preserving the Pages, hierarchy, \u003ccode\u003esidebar_position\u003c/code\u003e, and \u003ccode\u003esidebar_collapsed\u003c/code\u003e values.\u003c/p\u003e",
    "meta": "No Page deletion · no hierarchy change"
  }
}
-->
> [!TIP]
>
> **The original sidebar is restored**
>
> The next synchronization removes the source-owned contextual metadata while preserving the Pages, hierarchy, `sidebar_position`, and `sidebar_collapsed` values.
>
> _No Page deletion · no hierarchy change_
<!-- /docspress:block -->
