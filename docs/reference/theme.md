---
title: Theme reference
---

The DocsPress theme is a native WordPress block theme. It uses `theme.json` version 3, HTML templates and template parts, Global Style variations, block style variations, API v3 dynamic blocks, and native block supports.

## Requirements

- WordPress 6.6 or newer
- PHP 7.4 or newer
- DocsPress Blocks for `docspress/*` content blocks

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
| `docspress/adjacent-navigation` | `rootSlug`, `sort`, `showRoot`, `maxDepth`, `previousLabel`, `nextLabel`, `showTitles` |
| `docspress/color-mode-toggle` | `label`, `showLabel`, `defaultMode` |
| `docspress/docs-menu-toggle` | `label` |

All nine support native color, background, link color, gradients, typography, spacing, borders, minimum height, sticky positioning, shadow, anchor, and CSS-class controls. Their server renderers ensure the editor-facing parameters control live Page, navigation, heading, and source-path data. Page Summary displays a manually written Page excerpt or its optional fallback, preventing WordPress from generating a duplicate summary from the first paragraph.

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

## Content integration

The Docs Navigation, Command Search, and Adjacent Navigation blocks resolve a synchronized root by Page path, defaulting to `docs`. The Edit Links block combines `_docspress_source_path` metadata with its repository and ref attributes. Table of Contents uses the rendered current post content and assigns stable anchors to H1–H6 headings.

`sidebar_position` maps to `menu_order`; `sidebar_collapsed` is stored as managed Page metadata. Version navigation reads the taxonomy registered by the DocsPress synchronization plugin when available.

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

Use [Customize the theme in the Site Editor](../guides/customize-theme.md) for the editing workflow and [Gutenberg blocks](gutenberg-blocks.md) for content-block attributes.
