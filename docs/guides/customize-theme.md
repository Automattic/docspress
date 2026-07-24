---
title: Customize the theme in the Site Editor
---

DocsPress is a native block theme. Open **Appearance → Editor** to change the complete documentation site with WordPress blocks and Global Styles. There is no separate Customizer panel or hidden theme-settings layer.

## Choose a visual system

Open **Design → Styles → Browse styles**. The default DocsPress style includes three parent visual systems modeled on WordPress.org, WordPress.com, and Jetpack.

After selecting a family, open **Styles → Colors → Palette** to choose its color variation:

| Style family | Color variations |
| --- | --- |
| WordPress.org | Blueberry, Lemon, Purple |
| WordPress.com | Blue, Ink, Warm |
| Jetpack | Green, Electric, Forest |

The parent style controls the family’s typography, corners, and component recipes. The Page heading is one example: DocsPress uses the outlined label with an offset shadow, WordPress.org uses an editorial rule, WordPress.com uses an underlined publishing label, and Jetpack uses a green signal bar with bold sans-serif type. Each palette variation replaces the full light and dark semantic color system without replacing that family recipe, updating the documentation shell and DocsPress content blocks together. Because these are native WordPress color-only style variations, editors can also mix a palette with another family when desired.

To tune the result, open **Styles → Colors**, **Typography**, **Layout**, or **Blocks**. WordPress exposes the configured palettes, gradients, duotones, font families, fluid sizes, spacing scale, shadows, borders, minimum height, and layout widths without a theme build.

## Replace the site logo

Open the Header template part and select the **Site Logo** block. A new site starts with the bundled DocsPress icon as the single logo. Use **Replace** to upload or choose another image from the Media Library; WordPress updates that Site Logo everywhere. The block’s **Use as Site Icon** setting is enabled in the default header, so replacing it can also update the browser and app icon.

## Edit templates and template parts

Open **Design → Templates** to edit Page, Front Page, Single, Blog Home, Archive, Search, 404, Index, wide-content, and no-sidebar layouts.

The normal Page template is a composition of independently editable blocks:

1. Header template part
2. Documentation navigation
3. Breadcrumbs
4. Post Title
5. Page Summary
6. Edit links
7. Post Content
8. Adjacent navigation
9. Table of contents
10. Footer template part

Select a component to move, duplicate, replace, style, or delete it. Edit **Patterns → Manage all my patterns → Template parts** to change the Header, Footer, or Comments everywhere.

<!-- wp:docspress/callout {"tone":"tip","title":"Keep layout changes portable","content":"<p>Make site-wide shell changes in Templates and Template Parts. Use a Page’s editor only for that Page’s content, and assign the Wide content or Page without documentation sidebar template when it needs a different shell.</p>","collapsible":false} /-->

## Configure documentation navigation

Select **Docs navigation** in the Page template and open Block Settings. You can change:

- sidebar title;
- sidebar width;
- synchronized Page root slug;
- automatic Page hierarchy or classic-menu source;
- menu name, slug, or ID;
- Page sort order;
- root Page visibility;
- maximum nesting depth;
- filter visibility and placeholder;
- version selector visibility;
- desktop collapse/expand control, initial state, and labels;
- empty-state text.

The default root slug is `docs`, matching the DocsPress Action’s default `root-slug`. `sidebar_position` maps to native Page order, while `sidebar_collapsed` controls a managed section’s initial disclosure state.

## Configure command search

Select **Command search** in the Header. Its settings control the trigger label, input placeholder, suggested/no-results copy, Page root, result limit, window width, height, and corner radius, overlay opacity and blur, hierarchy paths, excerpts, and keyboard hints.

Search remains keyboard accessible and uses the Page tree beneath that block’s root slug. The setting belongs to the block instance, so a second template can search another documentation root.

## Configure reading tools

Each reading component has its own Block Settings:

- **Breadcrumbs:** home link, home label, separator.
- **Table of contents:** title, column width, and heading range from H1 through H6.
- **Page Summary:** optional fallback text; it prefers a manually written excerpt and never auto-generates a duplicate from the first content paragraph.
- **Edit links:** WordPress/GitHub links, labels, repository, and Git ref.
- **Adjacent navigation:** root, ordering, depth, labels, and Page titles.
- **Color-mode toggle:** accessible label, optional visible label, and light, dark, or device-following default.
- **Documentation menu toggle:** accessible mobile-menu label.

Every component also receives native color, gradient, typography, spacing, border, dimensions, position, shadow, anchor, and CSS-class controls from WordPress.

## Customize documentation blocks

The DocsPress Blocks plugin inherits Global Styles in the editor and on the published site. Its ten blocks expose native design tools in addition to their semantic settings:

- Hero and Audience Paths provide content, actions, artwork, layout, and presentation controls.
- Code, Code Tabs, API Request / Response, Terminal Session, Result, File Tree, Callout, and Prompt provide the settings appropriate to their content.
- All ten can be tuned with color, typography, spacing, borders, minimum height, sticky positioning, shadow, alignment where applicable, anchors, and classes.

Use **Styles → Blocks** for a site-wide default or select an individual block for a local override.

## Build the homepage

Choose a static front Page under **Settings → Reading**, then edit that Page with normal blocks. The Playground example uses DocsPress Hero and Audience Paths in the Page content, followed by the Front Page template’s independently editable latest-posts Query.

To change or remove the updates grid, open **Design → Templates → Front Page** and select the Query block. The template and homepage Page content remain independent, so editors can change its query, card design, columns, labels, or visibility without touching the Page.

## Try it without installing

[Open the DocsPress browser Playground](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-browser.json&page-title=DocsPress%20Theme%20Playground). It signs in and opens the Site Editor with representative Pages, posts, comments, blocks, templates, and style variations already available.
