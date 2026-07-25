---
title: Why DocsPress?
sidebar_position: 20
---

DocsPress is the advantageous choice when documentation belongs in GitHub but WordPress is already the trusted publishing, editorial, and presentation platform. It combines a developer-native Markdown workflow with a highly customizable WordPress site that editors can reshape without rebuilding the theme.

<!-- wp:docspress/callout {"tone":"success","title":"Keep the workflow you already have","content":"<p>Developers keep Markdown, pull requests, and code review. Readers and editors keep WordPress Pages, previews, menus, themes, users, and plugins.</p>","collapsible":false} /-->

## DocsPress and Docusaurus solve different deployment problems

[Docusaurus](https://docusaurus.io/) is a React-based static-site generator. It turns Markdown or MDX into a separately built documentation application that must be deployed to static hosting. DocsPress turns Markdown into managed WordPress Pages on an existing site.

| Capability | DocsPress | Docusaurus |
| --- | --- | --- |
| Source of truth | Markdown beside the code | Markdown or MDX beside the code |
| Published surface | Native WordPress Pages and Gutenberg blocks | A generated React static site |
| Hosting | Reuses an existing WordPress site | Requires a static-site deployment target |
| Editorial review | WordPress draft Pages, roles, previews, and publishing controls | Usually pull requests and deployment previews |
| Rich components | Gutenberg core blocks plus constrained DocsPress blocks | React components embedded through MDX |
| Navigation | WordPress Page hierarchy or configurable menus | Sidebars generated or configured in the Docusaurus project |
| Search | Theme-integrated Page search and command palette | First-class Algolia DocSearch; community local-search options |
| Visual configuration | Highly customizable Site Editor, complete Global Style presets, editable templates and parts, and per-block controls without a theme build | React/Infima configuration, CSS, and component swizzling |
| Reader discussion | Optional native comments on individual Pages and posts | Usually requires a third-party commenting integration |
| Platform ecosystem | WordPress users, plugins, REST APIs, menus, and site operations | Node.js, React, Docusaurus plugins, and static-host tooling |
| Built-in versioned docs | Not currently provided by the DocsPress Action | Dedicated versioning CLI and versioned docs directories |

## Where DocsPress is more advantageous

### One publishing platform

DocsPress avoids introducing a second public website, hosting contract, deployment configuration, analytics surface, and design system just for documentation. The docs can live beside product pages, support content, and the rest of an established WordPress site.

### Native editorial review

The first synchronization can create draft Pages for inspection. WordPress administrators can verify hierarchy, blocks, links, menus, and presentation before public publication. This is especially useful when documentation publication has an editorial or compliance step after code review.

### Constrained, theme-aware components

DocsPress blocks cover code, tabs, callouts, connected flows, HTTP exchanges, terminal output, results, collapsible file trees, and AI prompts. Authors choose semantic options, while Global Styles control colors, typography, spacing, borders, radii, dimensions, shadows, and light/dark behavior. That keeps generated documentation visually coherent without asking every author to design React components.

### WordPress-native navigation and customization

The bundled block theme can use the synchronized Page hierarchy automatically or use a hand-built WordPress menu. Site owners edit the header, sidebar, command search, article actions, typography, colors, footer, widths, and reading tools directly in the Site Editor. Every shell component is an independently configurable block.

### Highly customizable without a theme build

DocsPress uses native Global Styles, templates, template parts, block styles, and per-block controls instead of a separate theme-options screen. Site owners can change colors, typography, font sizes, spacing, borders, radii, shadows, layout widths, navigation, search, and reading tools from the same editor used for the rest of WordPress.

Those choices reach the full site: the documentation shell, homepage, posts, archives, search results, comments, core blocks, and DocsPress content blocks. Global defaults keep the experience coherent, while local block overrides allow a specific component or template to be different.

The default design plus the WordPress.org, WordPress.com, and Jetpack families provide complete typography and component recipes. Nine accompanying color variations provide matching light and dark palettes. Switching a style resets every exposed design value rather than inheriting stray edits from the previous preset, and editors can customize the selected result further.

This customization remains portable WordPress block-theme data. It does not require a React build, PHP option layer, or legacy Customizer workflow. Follow [Customize the theme in the Site Editor](guides/customize-theme.md) for the complete editing map.

### Optional discussions where readers need them

DocsPress can keep comments closed on reference material and enable them only on the Pages or posts where questions and feedback are useful. The theme presents threaded replies through its editable Comments template part, while WordPress continues to own comment status, registration rules, moderation, notifications, paging, and spam controls. Removing the Comments template part hides that presentation without deleting existing comments.

<!-- wp:docspress/callout {"tone":"tip","title":"Comments are opt-in per publishing surface","content":"<p>Use WordPress’s per-Page discussion setting to decide where replies are accepted, then edit the Comments template part to control how that conversation appears across the site.</p>","collapsible":false} /-->

### A smaller operational change for WordPress teams

DocsPress adds one GitHub Action and one managed Page tree to infrastructure the team already operates. Docusaurus is an excellent fit when the team explicitly wants a React static application; DocsPress is simpler when adding another Node-powered public site would duplicate existing WordPress capabilities.

<!-- wp:docspress/result {"status":"success","title":"Best fit: WordPress is already the destination","content":"<p>DocsPress preserves developer-native authoring while making documentation a highly customizable, first-class part of the WordPress site.</p>","meta":"GitHub authoring · WordPress publishing · Site Editor design"} /-->

## When Docusaurus may be the better fit

Choose Docusaurus when you specifically need a standalone static React application, extensive MDX component composition, built-in documentation version snapshots, or its mature internationalization and search ecosystem. Its official documentation describes [the React application structure](https://docusaurus.io/docs/installation), [static deployment options](https://docusaurus.io/docs/deployment), [search integrations](https://docusaurus.io/docs/search), and [versioning workflow](https://docusaurus.io/docs/versioning).

<!-- wp:docspress/callout {"tone":"note","title":"A practical decision","content":"<p>If your organization already publishes through WordPress, start with DocsPress. If the documentation must be an independent React product, start with Docusaurus.</p>","collapsible":false} /-->
