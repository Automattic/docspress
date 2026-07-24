---
title: Move legacy Customizer settings to the Site Editor
sidebar_position: 55
---

This page is for sites upgrading from the retired Customizer-based DocsPress theme. The current theme has no Customizer interface or runtime settings layer: visibility is expressed by inserting or removing a block, text and behavior live on the relevant block, and site-wide visual settings live in Global Styles.

<!-- wp:docspress/callout {"tone":"note","title":"New sites can skip this migration map","content":"<p>Open <strong>Appearance → Editor</strong> and follow <a href=\"/docs/guides/customize-theme/\">Customize the theme in the Site Editor</a>. Use the tables below only when translating saved settings from the retired Customizer implementation.</p>","collapsible":false} /-->

## Visual system and layout

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_design_preset` | **Styles → Browse styles**: DocsPress, WordPress.org, WordPress.com, or Jetpack family |
| `docspress_article_width` | **Styles → Layout → Content**; the live-compatible default is 770px |
| `docspress_sidebar_width` | Docs Navigation block → **Sidebar width** |
| `docspress_toc_width` | Table of Contents block → **Column width** |
| `docspress_border_radius` | **Styles → Blocks → Border** or the selected block’s Border controls |
| `docspress_content_density` | **Styles → Spacing / Typography** or the selected template Group’s padding, block gap, and line height |
| `docspress_ui_font` | **Styles → Typography → Text**, plus Header, Navigation, and interface blocks |
| `docspress_content_font` | **Styles → Typography → Text** or Post Content |
| `docspress_heading_font` | **Styles → Typography → Headings** |
| `docspress_content_font_size` | **Styles → Typography → Text** or Post Content |
| `docspress_heading_weight` | **Styles → Typography → Headings** |

## Documentation navigation

| Retired Customizer setting | Docs Navigation block setting |
| --- | --- |
| `docspress_docs_root` | Documentation root path |
| `docspress_sidebar_source` | Source: automatic Page tree or classic menu |
| `docspress_sidebar_menu` | Menu slug, name, or ID |
| `docspress_sidebar_sort` | Automatic Page order |
| `docspress_sidebar_show_root` | Show root Page |
| `docspress_sidebar_depth` | Maximum depth |
| `docspress_sidebar_title` | Heading |
| `docspress_show_sidebar_search` | Show Page filter |
| `docspress_search_placeholder` | Filter placeholder |
| `docspress_show_version_selector` | Show version selector |

The block also adds desktop collapse/expand controls and labels that did not exist in the retired Customizer.

## Homepage

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_homepage_layout` | **Templates → Front Page** block composition |
| `docspress_homepage_show_latest_posts` | Insert or remove the latest-posts Query block |
| `docspress_homepage_posts_title` | Edit the Query section Heading |
| `docspress_homepage_posts_count` | Query block → Items per page |

The Hero, Audience Paths, latest-posts heading, Query, Post Template, and each card sub-block are independently editable.

## Header

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_header_menu` | Header → Navigation block |
| `docspress_show_brand_suffix` | Insert or remove the `brand-wordpress` Paragraph |
| `docspress_brand_suffix` | Edit the `brand-wordpress` Paragraph |
| `docspress_show_header_search` | Insert or remove Command Search |
| `docspress_header_search_label` | Command Search → Trigger label |
| `docspress_show_color_toggle` | Insert or remove Color Mode Toggle |
| `docspress_default_color_mode` | Color Mode Toggle → Default mode |
| `docspress_show_repository` | Insert or remove the GitHub Social Icon |
| `docspress_github_url` | Social Icon → URL |
| `custom_logo` | Site Logo → Replace; the bundled DocsPress icon is the initial Media Library image |

## Command search

| Retired Customizer setting | Command Search block setting |
| --- | --- |
| `docspress_search_dialog_placeholder` | Field placeholder |
| `docspress_search_suggested_label` | Suggested results label |
| `docspress_search_no_results_label` | No-results message |
| `docspress_search_results_limit` | Maximum results |
| `docspress_search_width` | Width |
| `docspress_search_height` | Height |
| `docspress_search_radius_mode`, `docspress_search_radius` | Corner radius; selecting the family default value is equivalent to the former inherit mode |
| `docspress_search_overlay_opacity` | Backdrop opacity |
| `docspress_search_overlay_blur` | Backdrop blur |
| `docspress_search_show_paths` | Show Page paths |
| `docspress_search_show_excerpts` | Show excerpts |
| `docspress_search_show_hints` | Show keyboard hints |

## Page reading tools

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_show_toc` | Insert or remove Table of Contents |
| `docspress_toc_title` | Table of Contents → Heading |
| `docspress_show_breadcrumbs` | Insert or remove Breadcrumbs |
| `docspress_show_pagination` | Insert or remove Previous / Next |
| `docspress_show_summary` | Insert or remove Page Summary; edit its fallback or write a manual Page excerpt |
| `docspress_show_kicker` | Insert or remove the `entry-kicker` Paragraph |
| `docspress_kicker_label` | Edit the `entry-kicker` Paragraph |
| `docspress_show_edit_link` | Edit Links → Show WordPress edit link |
| `docspress_wordpress_edit_label` | Edit Links → WordPress label |
| `docspress_show_github_edit_link` | Edit Links → Show GitHub proposal link |
| `docspress_github_edit_label` | Edit Links → GitHub label |
| `docspress_github_edit_repository_url` | Edit Links → Repository URL |
| `docspress_github_edit_ref` | Edit Links → Branch or tag |

## Blog and post cards

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_show_post_meta` | Insert or remove the post-meta Group |
| `docspress_show_post_date` | Insert or remove Post Date |
| `docspress_show_post_author` | Insert or remove Post Author Name |
| `docspress_show_featured_images` | Insert or remove Post Featured Image |
| `docspress_show_post_categories` | Insert or remove Post Terms for categories |
| `docspress_show_post_tags` | Insert or remove Post Terms for tags |

These controls are available separately in Single, Blog Home, Archive, Search, and the Front Page Query, so one layout no longer forces the others to match.

## Discussion

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_comments_on_pages`, `docspress_comments_on_posts` | Insert or remove the Comments template part and use the native per-content Discussion status |
| `docspress_show_comment_count` | Insert, remove, or configure Comments Title |
| `docspress_show_comment_avatars` | Insert or remove Avatar |
| `docspress_comment_avatar_size` | Avatar → Image size |
| `docspress_show_comment_dates` | Insert or remove Comment Date |
| `docspress_discussion_title` | Edit or replace the Comments Title block |
| `docspress_comment_form_title` | Add an editable Heading immediately before Post Comments Form |
| `docspress_comments_closed_message` | Add an editable Paragraph in the Comments template part |

Core comment status, registration, moderation, threading, paging, and order remain WordPress settings rather than theme settings.

## Footer

| Retired Customizer setting | Site Editor destination |
| --- | --- |
| `docspress_show_footer` | Insert or remove the Footer template part |
| `docspress_footer_text` | Edit the Footer Paragraph |
| `docspress_footer_link_label` | Edit or add a Footer Navigation link label |
| `docspress_footer_link_url` | Edit or add a Footer Navigation link URL |

## Current Site Editor contract

- No Customizer PHP or preview/control JavaScript is loaded.
- All theme templates are block HTML files.
- Header, Comments, and Footer remain native registered template parts; Comments has its own labeled template-part area.
- All DocsPress shell blocks use API version 3, expose native design supports, and render their instance attributes on the live site.
- The default 770px article, 266px sidebar, 226px table of contents, 14px radius, 17px reading size, and Nunito Sans typography match the reference DocsPress site.
- The Front Page template, Page template, and all three Global Style families are editable without PHP changes.

The legacy setting names above are migration references, not active configuration. After translating them, continue with [Customize the theme in the Site Editor](../guides/customize-theme.md).
