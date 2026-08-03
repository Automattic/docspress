# DocsPress block theme

DocsPress is a native WordPress block theme for documentation sites. Templates, template parts, navigation, search, article tools, colors, typography, spacing, borders, shadows, and layout are edited in **Appearance → Editor**. There is no Customizer settings layer.

[Read the complete documentation in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json&page-title=DocsPress%20Documentation). The Blueprint activates the theme and companion blocks plugin, imports the converter-generated documentation hierarchy, signs in, and opens `/docs/`.

[Launch the complete Site Editor demo](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-browser.json&page-title=DocsPress%20Theme%20Playground) to seed the same documentation plus posts and discussion examples, then open the Site Editor.

To convert any repository's local `docs/` directory into editable WordPress Pages, run the official Playground CLI from that repository:

```bash
npx @wp-playground/cli@latest start \
  --blueprint=https://raw.githubusercontent.com/Automattic/docspress/main/theme/blueprint-local-docs.json \
  --mount="$PWD/docs:/wordpress/docspress-source-docs" \
  --no-auto-mount \
  --reset
```

The mounted-docs Blueprint performs the import inside WordPress; it does not require a DocsPress CLI wrapper. `--reset` gives every conversion a clean disposable WordPress site, so an interrupted or previously auto-mounted Playground cannot leave partial plugin or theme directories behind.

## Requirements

- WordPress 6.6 or newer
- PHP 7.4 or newer
- The bundled DocsPress Blocks plugin for the documentation content blocks

The theme follows the WordPress 7.0 Site Editor model and uses `theme.json` version 3, HTML templates, HTML template parts, Global Style variations, block style variations, API v3 dynamic blocks, and native block supports.

## Edit the whole site

Open **Appearance → Editor**:

- **Design → Styles** changes the site-wide palette, typography, layout, spacing, shadows, and individual block defaults.
- **Design → Styles → Browse styles** switches between the DocsPress, WordPress.org, WordPress.com, and Jetpack visual systems.
- **Design → Styles → Colors → Palette** switches the Blueberry/Lemon/Purple, Blue/Ink/Warm, or Green/Electric/Forest color variation without replacing the selected family’s typography and shape language.
- **Design → Styles → Blocks** adjusts a specific core or DocsPress block everywhere.
- **Design → Templates** edits Page, Front Page, Single, Blog Home, Archive, Search, 404, Index, wide-content, and no-sidebar layouts.
- **Design → Patterns → Manage all my patterns → Template parts** edits the header, footer, and comments.
- Selecting any block exposes its content, behavior, dimensions, color, typography, spacing, border, and advanced controls in the Block Settings sidebar.

The theme intentionally stores the shell as blocks. The Site Logo block starts with the bundled DocsPress icon; use its standard **Replace** control to upload another logo. The logo, header brand title, “on WordPress” label, primary Navigation block, GitHub Social Icons block, search trigger, color-mode control, mobile documentation trigger, footer copy, and footer navigation are independently movable, replaceable, or removable. The header brand title is ordinary linked Paragraph content, so editing it and saving the Header publishes the visible text immediately; it does not depend on a separate global Site Title save.

## Global style variations

The default DocsPress design is joined by three complete style families derived from the current public color and type systems of WordPress.org, WordPress.com, and Jetpack. Each family then exposes three color variations through the native Palette picker:

| Style family | Color variations | Character |
| --- | --- | --- |
| WordPress.org | Blueberry, Lemon, Purple | editorial serif titles, rule-led labels, crisp 2px corners |
| WordPress.com | Blue, Ink, Warm | product blue, dark publishing ink, warm editorial paper |
| Jetpack | Green, Electric, Forest | bold product sans, green signal bars, high-contrast surfaces |

Choose the parent under **Browse styles**, then choose its palette under **Colors → Palette**. The files follow WordPress’s native global-style plus color-only variation model. A family controls typography, corners, and component recipes—including the Page heading’s label, title, and divider—while its variants replace the complete light/dark semantic palette. The default DocsPress family keeps the outlined yellow pill and offset orange shadow; WordPress.org uses an editorial rule, WordPress.com an underlined publishing label, and Jetpack a compact green signal bar. This keeps navigation, code, surfaces, borders, copy, muted text, and DocsPress content blocks coherent without recoloring one family’s pattern for every style.

Every family is a complete preset rather than a partial override. Selecting one explicitly reapplies the body, heading-level, caption, button, link, code, and quote recipes—including font-size presets, font family, appearance, weight, line height, letter spacing, case, alignment, decoration, colors, and page spacing—so edits made under the previously selected style do not leak into the next one. H1–H6 use explicit reset markers for shared heading properties and explicit values for their size, spacing, and line-height scale; this clears old level-specific edits while keeping the **All headings** controls authoritative. The Display size remains available for deliberate hero text, but normal H1 elements begin at Heading 2 so page and post titles do not become billboard-sized. Heading families default to a bold appearance (900 for DocsPress and 700 for the brand families) while remaining editable through **All headings**. Color-only variations use the same rule for every color-bearing element and block while intentionally leaving the selected family’s typography and spacing intact.

Three contextual block style variations are also available:

- **Soft panel** for Group, Columns, and Post Content
- **Outline card** for Group, Column, Query, and Post Template
- **Signal band** for Group and Cover

Core Navigation adds **Underline** and **Framed**, Button adds **Text with arrow**, and Post Template adds **Documentation cards**.

## Site Editor components

The theme registers ten dynamic shell blocks. They render live WordPress data on the front end and expose their parameters in Block Settings:

| Block | Component parameters |
| --- | --- |
| Docs navigation | title, width, Page root slug, Page-tree or menu source, menu slug, sort order, root visibility, maximum depth, filter, filter label, version switcher, empty state |
| Command search | trigger label, placeholder, suggested/no-results labels, docs root, result limit, width, height, corner radius, overlay opacity and blur, paths, excerpts, keyboard hints |
| Breadcrumbs | home link, home label, separator |
| Table of contents | title, width, minimum heading level, maximum heading level |
| Page Summary | editor fallback text; the live Page displays only a manually written excerpt |
| Edit links | WordPress and GitHub visibility and labels, repository URL, Git ref |
| Was This Helpful | enabled state, question, positive/negative labels, and thank-you message |
| Adjacent navigation | docs root, sort order, root visibility, depth, previous/next labels, Page-title visibility |
| Color-mode toggle | accessible label, visible-label option, and default mode |
| Documentation menu toggle | accessible label |

All ten also opt into native color, gradient, typography, spacing, border, dimensions, sticky positioning, shadow, anchor, and CSS-class tools. The ten blocks in DocsPress Blocks expose the same design supports; Hero and Audience Paths additionally expose their purpose-built content and composition controls.

## Templates

```text
theme/
├── templates/
│   ├── index.html
│   ├── page.html
│   ├── page-no-sidebar.html
│   ├── page-wide.html
│   ├── front-page.html
│   ├── single.html
│   ├── home.html
│   ├── archive.html
│   ├── search.html
│   └── 404.html
├── parts/
│   ├── header.html
│   ├── footer.html
│   └── comments.html
├── styles/
│   ├── theme/
│   │   ├── wordpress-org.json
│   │   ├── wordpress-com.json
│   │   └── jetpack.json
│   ├── color/
│   │   ├── wordpress-org/*.json
│   │   ├── wordpress-com/*.json
│   │   └── jetpack/*.json
│   └── block/*.json
├── assets/js/
│   ├── block-components.js
│   └── docs.js
├── inc/
│   ├── blocks.php
│   ├── llms.php
│   └── performance.php
├── functions.php
├── style.css
└── theme.json
```

The Page template composes the documentation navigation, breadcrumbs, title, manual Page summary, edit links, content, Was This Helpful prompt, adjacent navigation, and table of contents as normal blocks. Remove, reorder, duplicate, or style any of them in the Site Editor. Page Summary avoids duplicating the first paragraph when a Page has no manual excerpt. Was This Helpful is enabled by default, stores aggregate response counts as Page metadata, and exposes those counts plus a per-Page visibility switch in the Page editor’s **Page feedback** details panel. Assign **Page without documentation sidebar** or **Wide content** per Page when the standard documentation shell is not appropriate.

## Documentation hierarchy

The Docs Navigation, Command Search, and Adjacent Navigation blocks default to a root Page path of `docs`. Change **Documentation root slug** in each block when the synchronization workflow uses another `root-slug`.

Automatic navigation follows the synchronized Page hierarchy. `sidebar_position` maps to native Page `menu_order`; `sidebar_collapsed` sets an individual section’s initial disclosure state. The optional Action-level `sidebars-file` adds advanced route-based contexts while sites without it retain one automatic tree. Docs Navigation and Adjacent Navigation automatically stay within the current Page's configured context. The Docs Navigation block separately controls whether the full desktop sidebar can be collapsed, whether it starts collapsed, and the visitor-facing collapse/expand labels. It can instead render any classic WordPress menu by name, slug, or ID for sites that already maintain one.

## GitHub editing

The Edit Links block reads the `_docspress_source_path` metadata set by synchronization. Its repository and branch are block attributes, so different templates or Pages may point to different source repositories without global theme settings.

## Local Playground

From the repository root:

```bash
npx @wp-playground/cli@latest start \
  --path=theme \
  --mount="$(pwd)/plugins/docspress-blocks:/wordpress/wp-content/plugins/docspress-blocks" \
  --blueprint=theme/blueprint.json \
  --port=9400
```

Regenerate the seeded documentation after changing `docs/`:

```bash
npm run playground:docs
```

## Architecture references

- [Site Editor](https://wordpress.org/documentation/article/site-editor/)
- [Global Settings and Styles](https://developer.wordpress.org/themes/global-settings-and-styles/)
- [Style variations](https://developer.wordpress.org/themes/global-settings-and-styles/style-variations/)
- [Color and typography variations](https://developer.wordpress.org/news/2024/07/mixing-and-matching-styles-colors-and-typography-in-wordpress-6-6/)
- [Templates](https://developer.wordpress.org/themes/core-concepts/templates/)
- [Template parts](https://developer.wordpress.org/themes/templates/template-parts/)
- [Block style variations](https://developer.wordpress.org/themes/features/block-style-variations/)
- [Block supports](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/)

These are the current WordPress primitives the theme extends; site configuration remains exportable, portable block-theme data rather than opaque theme modifications.
