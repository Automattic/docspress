---
title: Hero
sidebar_position: 10
---

Use `docspress/hero` for the primary introduction on a documentation landing Page. It combines an eyebrow, heading, summary, up to two actions, and optional media without requiring a custom template.

## When to use it

Choose Hero when a Page needs one dominant message and clear next steps. Use [Audience Paths](audience-paths.md) instead when the main task is choosing among several journeys. Avoid repeating Hero inside ordinary reference articles; a normal heading and introduction are usually clearer.

## Edit the block

Edit the eyebrow, title, description, and action labels in the canvas. In the Block sidebar:

1. Set each action URL and whether it opens a new tab.
2. Choose an image from the Media Library or supply an external URL and alternative text.
3. Select the split or editorial composition, media side, media width, image scale, height, tone, and text alignment.
4. Optionally add decorative grid, orbit, or backdrop text.
5. Prefer the theme-native colors; use the native Text control or add explicit panel, visual, or accent colors only when the design requires them.

An action appears only when both its label and URL are present. Remove the image for a text-only hero. Transparent artwork remains unframed.

## Attributes

<!-- wp:docspress/fields {"title":"Hero attributes","description":"Content, actions, media, and presentation accepted by <code>docspress/hero</code>.","fields":[{"name":"eyebrow","type":"string","required":false,"defaultValue":"Documentation, publishing, and community","description":"<p>Formatted overline text.</p>","values":"","deprecated":false},{"name":"title","type":"string","required":false,"defaultValue":"Docs that stay connected to your GitHub repo","description":"<p>Formatted primary heading.</p>","values":"","deprecated":false},{"name":"description","type":"string","required":false,"defaultValue":"Introductory summary","description":"<p>Formatted supporting copy.</p>","values":"","deprecated":false},{"name":"primaryLabel / primaryUrl","type":"string","required":false,"defaultValue":"Browse documentation / empty","description":"<p>Primary action. Both values are required for the action to appear.</p>","values":"","deprecated":false},{"name":"primaryNewTab","type":"boolean","required":false,"defaultValue":"false","description":"<p>Opens the primary destination in a new tab.</p>","values":"true, false","deprecated":false},{"name":"secondaryLabel / secondaryUrl","type":"string","required":false,"defaultValue":"Latest updates / empty","description":"<p>Secondary action. Both values are required for the action to appear.</p>","values":"","deprecated":false},{"name":"secondaryNewTab","type":"boolean","required":false,"defaultValue":"false","description":"<p>Opens the secondary destination in a new tab.</p>","values":"true, false","deprecated":false},{"name":"mediaId","type":"number","required":false,"defaultValue":"0","description":"<p>WordPress Media Library attachment ID.</p>","values":"","deprecated":false},{"name":"mediaUrl / mediaAlt","type":"string","required":false,"defaultValue":"","description":"<p>External image URL and its plain-text alternative.</p>","values":"","deprecated":false},{"name":"visualLabel","type":"string","required":false,"defaultValue":"","description":"<p>Decorative backdrop text for the editorial layout.</p>","values":"","deprecated":false},{"name":"layout","type":"enum","required":false,"defaultValue":"split","description":"<p>Overall composition.</p>","values":"split, editorial","deprecated":false},{"name":"mediaPosition","type":"enum","required":false,"defaultValue":"right","description":"<p>Side used by the visual column.</p>","values":"left, right","deprecated":false},{"name":"mediaWidth","type":"number","required":false,"defaultValue":"44","description":"<p>Visual-column percentage, clamped to 34–58.</p>","values":"34–58","deprecated":false},{"name":"imageScale","type":"number","required":false,"defaultValue":"100","description":"<p>Image scale percentage, clamped to 60–120.</p>","values":"60–120","deprecated":false},{"name":"height","type":"enum","required":false,"defaultValue":"standard","description":"<p>Vertical size preset.</p>","values":"compact, standard, tall","deprecated":false},{"name":"tone","type":"enum","required":false,"defaultValue":"theme","description":"<p>Color treatment.</p>","values":"theme, midnight, paper, brand","deprecated":false},{"name":"textAlign","type":"enum","required":false,"defaultValue":"left","description":"<p>Content alignment.</p>","values":"left, center","deprecated":false},{"name":"showGrid / showOrbit","type":"boolean","required":false,"defaultValue":"false","description":"<p>Opt-in decorative effects.</p>","values":"true, false","deprecated":false},{"name":"panelColor / visualColor / accentColor","type":"string","required":false,"defaultValue":"","description":"<p>Optional hexadecimal color overrides. Use the native Text control in Styles for typography color.</p>","values":"#RRGGBB","deprecated":false}],"searchable":true,"compact":true} /-->

The block supports `wide` and `full` alignment in addition to the [shared design controls](index.md#add-and-edit-a-block).

## Creative examples

### Repository launch

<!-- wp:docspress/hero {"eyebrow":"Documentation, publishing, and community","title":"Docs that stay connected to your GitHub repo","description":"Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.","primaryLabel":"Choose your path","primaryUrl":"#choose-your-path","secondaryLabel":"Latest updates","secondaryUrl":"/#latest-updates","mediaUrl":"http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp","mediaAlt":"The GitHub Octocat and WordPress Wapuu celebrating their documentation workflow together.","mediaPosition":"right","mediaWidth":44,"imageScale":100,"height":"standard","tone":"theme","textAlign":"left","layout":"split"} /-->

### Midnight release portal

<!-- wp:docspress/hero {"eyebrow":"Version 4.0 · Upgrade guide","title":"Ship the upgrade without surprises","description":"Review the breaking changes, rehearse the migration, and keep a verified rollback path close at hand.","primaryLabel":"Review the upgrade","primaryUrl":"#upgrade","secondaryLabel":"Open the migration checklist","secondaryUrl":"#migration-checklist","mediaUrl":"http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp","mediaAlt":"The GitHub Octocat and WordPress Wapuu working together.","mediaPosition":"right","mediaWidth":38,"imageScale":76,"height":"standard","tone":"midnight","textAlign":"left","showGrid":false,"showOrbit":false,"layout":"split"} /-->

### Text-only incident handbook

<!-- wp:docspress/hero {"eyebrow":"Incident response","title":"Calm steps for noisy moments","description":"Start with impact, protect evidence, and follow the recovery path your team has already rehearsed.","primaryLabel":"Open the first-response checklist","primaryUrl":"#first-response","secondaryLabel":"Contact the incident lead","secondaryUrl":"#escalation","layout":"split","mediaPosition":"right","mediaWidth":40,"imageScale":100,"height":"compact","tone":"paper","textAlign":"center","showGrid":false,"showOrbit":false} /-->

## Published behavior and accessibility

The hero is a semantic section. Linked actions remain native links and work without JavaScript; new-tab actions receive safe relationship attributes. Media Library images use WordPress responsive image output, while URL images are escaped and receive the supplied alternative text. The decorative `visualLabel` is hidden from assistive technology.

Keep the title specific, limit the description to one short paragraph, and make action labels describe their destination. Use meaningful alternative text for informative media and an empty alternative for purely decorative art.

<!-- wp:docspress/callout {"tone":"tip","title":"Let the theme lead","content":"<p>Start with the theme tone and Global Styles. Add per-block colors only when the hero needs a deliberate campaign or brand treatment.</p>","collapsible":false,"open":true} /-->
