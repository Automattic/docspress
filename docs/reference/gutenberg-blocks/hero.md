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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Hero attributes",
    "description": "Content, actions, media, and presentation accepted by \u003ccode\u003edocspress/hero\u003c/code\u003e.",
    "fields": [
      {
        "name": "eyebrow",
        "type": "string",
        "required": false,
        "defaultValue": "Documentation, publishing, and community",
        "description": "\u003cp\u003eFormatted overline text.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Docs that stay connected to your GitHub repo",
        "description": "\u003cp\u003eFormatted primary heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "description",
        "type": "string",
        "required": false,
        "defaultValue": "Introductory summary",
        "description": "\u003cp\u003eFormatted supporting copy.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "primaryLabel / primaryUrl",
        "type": "string",
        "required": false,
        "defaultValue": "Browse documentation / empty",
        "description": "\u003cp\u003ePrimary action. Both values are required for the action to appear.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "primaryNewTab",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eOpens the primary destination in a new tab.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "secondaryLabel / secondaryUrl",
        "type": "string",
        "required": false,
        "defaultValue": "Latest updates / empty",
        "description": "\u003cp\u003eSecondary action. Both values are required for the action to appear.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "secondaryNewTab",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eOpens the secondary destination in a new tab.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "mediaId",
        "type": "number",
        "required": false,
        "defaultValue": "0",
        "description": "\u003cp\u003eWordPress Media Library attachment ID.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "mediaUrl / mediaAlt",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eExternal image URL and its plain-text alternative.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "visualLabel",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eDecorative backdrop text for the editorial layout.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "layout",
        "type": "enum",
        "required": false,
        "defaultValue": "split",
        "description": "\u003cp\u003eOverall composition.\u003c/p\u003e",
        "values": "split, editorial",
        "deprecated": false
      },
      {
        "name": "mediaPosition",
        "type": "enum",
        "required": false,
        "defaultValue": "right",
        "description": "\u003cp\u003eSide used by the visual column.\u003c/p\u003e",
        "values": "left, right",
        "deprecated": false
      },
      {
        "name": "mediaWidth",
        "type": "number",
        "required": false,
        "defaultValue": "44",
        "description": "\u003cp\u003eVisual-column percentage, clamped to 34–58.\u003c/p\u003e",
        "values": "34–58",
        "deprecated": false
      },
      {
        "name": "imageScale",
        "type": "number",
        "required": false,
        "defaultValue": "100",
        "description": "\u003cp\u003eImage scale percentage, clamped to 60–120.\u003c/p\u003e",
        "values": "60–120",
        "deprecated": false
      },
      {
        "name": "height",
        "type": "enum",
        "required": false,
        "defaultValue": "standard",
        "description": "\u003cp\u003eVertical size preset.\u003c/p\u003e",
        "values": "compact, standard, tall",
        "deprecated": false
      },
      {
        "name": "tone",
        "type": "enum",
        "required": false,
        "defaultValue": "theme",
        "description": "\u003cp\u003eColor treatment.\u003c/p\u003e",
        "values": "theme, midnight, paper, brand",
        "deprecated": false
      },
      {
        "name": "textAlign",
        "type": "enum",
        "required": false,
        "defaultValue": "left",
        "description": "\u003cp\u003eContent alignment.\u003c/p\u003e",
        "values": "left, center",
        "deprecated": false
      },
      {
        "name": "showGrid / showOrbit",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eOpt-in decorative effects.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "panelColor / visualColor / accentColor",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional hexadecimal color overrides. Use the native Text control in Styles for typography color.\u003c/p\u003e",
        "values": "#RRGGBB",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Hero attributes

Content, actions, media, and presentation accepted by <code>docspress/hero</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `eyebrow` | string | No | Documentation, publishing, and community | <p>Formatted overline text.</p> |
| `title` | string | No | Docs that stay connected to your GitHub repo | <p>Formatted primary heading.</p> |
| `description` | string | No | Introductory summary | <p>Formatted supporting copy.</p> |
| `primaryLabel / primaryUrl` | string | No | Browse documentation / empty | <p>Primary action. Both values are required for the action to appear.</p> |
| `primaryNewTab` | boolean | No | false | <p>Opens the primary destination in a new tab.</p> |
| `secondaryLabel / secondaryUrl` | string | No | Latest updates / empty | <p>Secondary action. Both values are required for the action to appear.</p> |
| `secondaryNewTab` | boolean | No | false | <p>Opens the secondary destination in a new tab.</p> |
| `mediaId` | number | No | 0 | <p>WordPress Media Library attachment ID.</p> |
| `mediaUrl / mediaAlt` | string | No |  | <p>External image URL and its plain-text alternative.</p> |
| `visualLabel` | string | No |  | <p>Decorative backdrop text for the editorial layout.</p> |
| `layout` | enum | No | split | <p>Overall composition.</p> |
| `mediaPosition` | enum | No | right | <p>Side used by the visual column.</p> |
| `mediaWidth` | number | No | 44 | <p>Visual-column percentage, clamped to 34–58.</p> |
| `imageScale` | number | No | 100 | <p>Image scale percentage, clamped to 60–120.</p> |
| `height` | enum | No | standard | <p>Vertical size preset.</p> |
| `tone` | enum | No | theme | <p>Color treatment.</p> |
| `textAlign` | enum | No | left | <p>Content alignment.</p> |
| `showGrid / showOrbit` | boolean | No | false | <p>Opt-in decorative effects.</p> |
| `panelColor / visualColor / accentColor` | string | No |  | <p>Optional hexadecimal color overrides. Use the native Text control in Styles for typography color.</p> |
<!-- /docspress:block -->

The block supports `wide` and `full` alignment in addition to the [shared design controls](index.md#add-and-edit-a-block).

## Creative examples

### Repository launch

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/hero",
  "attrs": {
    "eyebrow": "Documentation, publishing, and community",
    "title": "Docs that stay connected to your GitHub repo",
    "description": "Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.",
    "primaryLabel": "Choose your path",
    "primaryUrl": "#choose-your-path",
    "secondaryLabel": "Latest updates",
    "secondaryUrl": "/#latest-updates",
    "mediaUrl": "http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp",
    "mediaAlt": "The GitHub Octocat and WordPress Wapuu celebrating their documentation workflow together.",
    "mediaPosition": "right",
    "mediaWidth": 44,
    "imageScale": 100,
    "height": "standard",
    "tone": "theme",
    "textAlign": "left",
    "layout": "split"
  }
}
-->
_Documentation, publishing, and community_

## Docs that stay connected to your GitHub repo

Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.

[Choose your path](#choose-your-path) · [Latest updates](/#latest-updates)

![The GitHub Octocat and WordPress Wapuu celebrating their documentation workflow together.](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp)
<!-- /docspress:block -->

### Midnight release portal

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/hero",
  "attrs": {
    "eyebrow": "Version 4.0 · Upgrade guide",
    "title": "Ship the upgrade without surprises",
    "description": "Review the breaking changes, rehearse the migration, and keep a verified rollback path close at hand.",
    "primaryLabel": "Review the upgrade",
    "primaryUrl": "#upgrade",
    "secondaryLabel": "Open the migration checklist",
    "secondaryUrl": "#migration-checklist",
    "mediaUrl": "http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp",
    "mediaAlt": "The GitHub Octocat and WordPress Wapuu working together.",
    "mediaPosition": "right",
    "mediaWidth": 38,
    "imageScale": 76,
    "height": "standard",
    "tone": "midnight",
    "textAlign": "left",
    "showGrid": false,
    "showOrbit": false,
    "layout": "split"
  }
}
-->
_Version 4.0 · Upgrade guide_

## Ship the upgrade without surprises

Review the breaking changes, rehearse the migration, and keep a verified rollback path close at hand.

[Review the upgrade](#upgrade) · [Open the migration checklist](#migration-checklist)

![The GitHub Octocat and WordPress Wapuu working together.](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/homepage-octocat-wapuu.webp)
<!-- /docspress:block -->

### Text-only incident handbook

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/hero",
  "attrs": {
    "eyebrow": "Incident response",
    "title": "Calm steps for noisy moments",
    "description": "Start with impact, protect evidence, and follow the recovery path your team has already rehearsed.",
    "primaryLabel": "Open the first-response checklist",
    "primaryUrl": "#first-response",
    "secondaryLabel": "Contact the incident lead",
    "secondaryUrl": "#escalation",
    "layout": "split",
    "mediaPosition": "right",
    "mediaWidth": 40,
    "imageScale": 100,
    "height": "compact",
    "tone": "paper",
    "textAlign": "center",
    "showGrid": false,
    "showOrbit": false
  }
}
-->
_Incident response_

## Calm steps for noisy moments

Start with impact, protect evidence, and follow the recovery path your team has already rehearsed.

[Open the first-response checklist](#first-response) · [Contact the incident lead](#escalation)
<!-- /docspress:block -->

## Published behavior and accessibility

The hero is a semantic section. Linked actions remain native links and work without JavaScript; new-tab actions receive safe relationship attributes. Media Library images use WordPress responsive image output, while URL images are escaped and receive the supplied alternative text. The decorative `visualLabel` is hidden from assistive technology.

Keep the title specific, limit the description to one short paragraph, and make action labels describe their destination. Use meaningful alternative text for informative media and an empty alternative for purely decorative art.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Let the theme lead",
    "content": "\u003cp\u003eStart with the theme tone and Global Styles. Add per-block colors only when the hero needs a deliberate campaign or brand treatment.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!TIP]
>
> **Let the theme lead**
>
> Start with the theme tone and Global Styles. Add per-block colors only when the hero needs a deliberate campaign or brand treatment.
<!-- /docspress:block -->
