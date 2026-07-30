---
title: Theme reference
---

The DocsPress theme is a highly customizable native WordPress block theme. It uses `theme.json` version 3, HTML templates and template parts, Global Style variations, block style variations, API v3 dynamic blocks, and native block supports.

## Requirements

- WordPress 6.6 or newer
- PHP 7.4 or newer
- DocsPress Blocks for `docspress/*` content blocks

## One-link documentation Blueprint

[`theme/blueprint-docs.json`](https://github.com/Automattic/docspress/blob/main/theme/blueprint-docs.json) is the browser documentation entry point. Its standard [`blueprint-url`](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json&page-title=DocsPress%20Documentation) installs the theme and blocks plugin from GitHub, runs `theme/playground/setup.php`, and lands on `/docs/`.

The setup script imports `theme/playground/generated-docs.json`, which is produced from the repository’s Markdown by the same DocsPress collector and converter used by the publishing workflow. Run `npm run playground:docs` after changing `docs/`; the next launch receives the updated hierarchy without maintaining a separate documentation website.

## Templates

| Template | Purpose |
| --- | --- |
| Page | Documentation sidebar, article tools, content, adjacent links, and table of contents |
| Page without documentation sidebar | Centered Page with article tools but no docs navigation |
| Wide content | Wide Page or post content |
| Front Page | Editable front Page content followed by an independently editable latest-posts Query |
| Single | Post content, taxonomy, navigation, and comments |
| Blog Home | Posts-page Query |
| Archive | Inherited archive Query |
| Search | Search-results Query |
| 404 | Editable not-found layout and search |
| Index | Required block-theme fallback |

Header, Footer, and Comments are editable template parts.

## Global style families and color variations

The theme includes three complete global style families under `theme/styles/theme/` and nine color-only variations under `theme/styles/color/`:

| Style family | Color variations |
| --- | --- |
| WordPress.org | Blueberry, Lemon, Purple |
| WordPress.com | Blue, Ink, Warm |
| Jetpack | Green, Electric, Forest |

The parent family defines typography, corner treatment, component recipes, and a default palette. The Page heading recipe changes its label, marker, title scale, and divider between DocsPress, WordPress.org, WordPress.com, and Jetpack instead of recoloring the DocsPress treatment. Each child variation is color-only, so WordPress presents it under **Styles → Colors → Palette** instead of as another top-level style. Every palette defines the same light and dark semantic color slugs. Components consume those slugs, so switching palettes updates the entire system while preserving the selected family.

The theme also ships JSON block style variations for Soft panel, Outline card, and Signal band, plus registered styles for Navigation, Button, and Post Template.

## Site Editor blocks

| Block | Attributes |
| --- | --- |
| `docspress/docs-navigation` | `title`, `width`, `rootSlug`, `source`, `menuSlug`, `sort`, `showRoot`, `maxDepth`, `showFilter`, `filterPlaceholder`, `showVersions`, `emptyMessage`, `showCollapse`, `startCollapsed`, `collapseLabel`, `expandLabel` |
| `docspress/command-search` | `label`, `placeholder`, `suggestedLabel`, `noResultsLabel`, `resultsLimit`, `rootSlug`, `width`, `height`, `radius`, `overlayOpacity`, `overlayBlur`, `showPaths`, `showExcerpts`, `showHints` |
| `docspress/breadcrumbs` | `showHome`, `homeLabel`, `separator` |
| `docspress/table-of-contents` | `title`, `width`, `minLevel`, `maxLevel` |
| `docspress/page-summary` | `fallbackText` |
| `docspress/edit-links` | `showWordPress`, `wordpressLabel`, `showGitHub`, `githubLabel`, `repositoryUrl`, `ref` |
| `docspress/was-this-helpful` | `enabled`, `question`, `helpfulLabel`, `unhelpfulLabel`, `thanksMessage` |
| `docspress/adjacent-navigation` | `rootSlug`, `sort`, `showRoot`, `maxDepth`, `previousLabel`, `nextLabel`, `showTitles` |
| `docspress/color-mode-toggle` | `label`, `showLabel`, `defaultMode` |
| `docspress/docs-menu-toggle` | `label` |
| `docspress/version-switcher` | `label`, `showLabel`, `presentation`, `showLatestBadge`, `hideSingle`, `unavailableLabel` |
| `docspress/version-notice` | `message`, `latestLinkLabel`, `showIcon`, `dismissible` |

The ten theme blocks and two version blocks support native color, background, link color, gradients, typography, spacing, borders, minimum height, sticky positioning, shadow, anchor, and CSS-class controls. Their server renderers ensure the editor-facing parameters control live Page, navigation, heading, source-path, and version data. Page Summary displays a manually written Page excerpt or its optional fallback, preventing WordPress from generating a duplicate summary from the first paragraph.

Was This Helpful is enabled by default in the Page template immediately before Adjacent Navigation. A visitor can record one Helpful or Not helpful response per Page in that browser. Aggregate counts are stored in the Page metadata fields `docspress_helpful_votes` and `docspress_unhelpful_votes`; editors can review the counts, total, and helpful rate in the Page editor’s **Page feedback** details panel. Use **Show feedback on this Page** in that panel to hide the prompt on one Page without deleting its existing data, or disable the template block to hide it site-wide. [Collect feedback with Was This Helpful](../guides/was-this-helpful.md) covers the complete visitor, editor, storage, REST, and extension workflow.

In a content-only Header view, **DocsPress: Command Search** includes a disclosure chevron. Select it to open the block's Search content and Dialog options without first entering the full Header block-editing mode.

## `theme.json` controls

Site editors receive:

- custom colors, gradients, and duotones;
- six font families and seven fluid size presets;
- eight spacing presets;
- border color, radius, style, and width;
- two shadow presets;
- aspect ratio and minimum height;
- sticky positioning;
- content and wide layout widths;
- per-block and per-element styles.

Theme-specific CSS variables under `settings.custom` cover the header, sidebar, table-of-contents, article and search dimensions, heading weight, typography roles, radius, and family-specific Page heading recipe. Global Style families can replace these values; color-only variations replace the semantic light/dark palette.

The default design and each family variation serialize a complete style contract for Text, Links, Headings H1–H6, Captions, Buttons, Code, and Quotes. Their font-size scale and every exposed typography value are explicit, so applying a different style replaces earlier Global Styles edits instead of inheriting missing values. Heading levels use explicit `null` reset markers for properties owned by **All headings**, then define only their level-specific size, letter spacing, and line height; this prevents stale H1–H6 edits without blocking later family, weight, case, alignment, or color changes made at the Heading element. Display remains an opt-in size for hero text; the automatic H1–H6 scale starts at Heading 2 and steps down through Small. Headings default to weight 900 in DocsPress and 700 in the brand families, with the shared **All headings** control remaining authoritative. Each color-only variation likewise reapplies all root, element, state, and relevant block colors without changing the active typography family.

The Site Editor’s root Design canvas and Styles canvas both preview the complete Page template rather than the homepage content entity. This keeps the editable header, documentation navigation sidebar, article surface, table of contents, actions, and footer visible while evaluating theme-wide changes.

## Content integration

The Docs Navigation, Command Search, and Adjacent Navigation blocks resolve a synchronized root by Page path, defaulting to `docs`. The Edit Links block combines `_docspress_source_path` metadata with its repository and ref attributes. Table of Contents uses the rendered current post content and assigns stable anchors to H1–H6 headings. Was This Helpful stores its aggregate response counts as registered Page metadata and exposes them through the WordPress REST API.

`sidebar_position` maps to `menu_order`; `sidebar_collapsed` is stored as managed Page metadata. Version navigation reads the taxonomy registered by DocsPress Blocks. The Header template places a compact Version Switcher dropdown immediately before Command Search, while the Page template places the Version Notice as a full-width status bar directly below the Header; both remain ordinary, independently editable blocks. The duplicate selector control on Docs Navigation is retained in saved markup for compatibility but no longer renders a second switcher.

## File layout

```text
theme/
├── templates/*.html
├── parts/*.html
├── styles/
│   ├── theme/*.json
│   ├── color/*/*.json
│   └── block/*.json
├── assets/js/block-components.js
├── assets/js/docs.js
├── inc/blocks.php
├── inc/llms.php
├── inc/performance.php
├── functions.php
├── style.css
└── theme.json
```

Use [Customize the theme in the Site Editor](../guides/customize-theme.md) for the editing workflow and [Gutenberg blocks](gutenberg-blocks/index.md) for content-block attributes.
