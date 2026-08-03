# Contextual sidebars boilerplate

This example keeps the normal documentation tree as its default sidebar and gives `apis/` and `extensions/` focused navigation contexts.

[Open the example in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-sidebars.json&page-title=DocsPress%20Contextual%20Sidebars).

Copy `docs/sidebars.yml` and the example folder structure into a DocsPress repository, then enable the advanced input:

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

The DocsPress theme automatically scopes its Docs Navigation and Adjacent Navigation blocks to the current Page's assigned context. Remove `sidebars-file` to return to one automatic sidebar.

Repository maintainers can rebuild the fixture consumed by `theme/blueprint-sidebars.json` with `npm run playground:sidebars`.
