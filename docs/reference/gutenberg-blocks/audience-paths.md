---
title: Audience Paths
sidebar_position: 20
---

Use `docspress/audience-paths` to route readers into distinct documentation branches. Each path is a complete, keyboard-accessible card with its own title, summary, accent, and destination. Icons and visible bottom actions are optional for the whole block.

## When to use it

Choose Audience Paths when readers arrive with different goals, roles, or starting states. Good examples include “I already have docs” versus “I need to create docs,” or separate administrator, developer, and contributor journeys. Use ordinary links when the choices are secondary or differ only slightly.

## Edit the block

Edit the section eyebrow, title, description, and card copy in the canvas. Use the sidebar to set each destination, icon, accent, and new-tab behavior. You can add up to six paths, select one to three columns, center or left-align text, show path numbers, hide icons, hide bottom links, reduce spacing with **Compact layout**, or choose a theme, paper, ink, or blueprint tone.

Use the spacious layout for a landing-page decision. Use Compact layout for a router inside an article. Point paths to normal WordPress Page roots so their child Pages form predictable sidebar branches.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Audience Paths attributes",
    "description": "Section content and layout accepted by \u003ccode\u003edocspress/audience-paths\u003c/code\u003e.",
    "fields": [
      {
        "name": "eyebrow",
        "type": "string",
        "required": false,
        "defaultValue": "Choose a starting point",
        "description": "\u003cp\u003ePlain-text overline.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Where are your docs today?",
        "description": "\u003cp\u003eSection heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "description",
        "type": "string",
        "required": false,
        "defaultValue": "Follow the path that matches your repository.",
        "description": "\u003cp\u003eSection summary.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "paths",
        "type": "array",
        "required": true,
        "defaultValue": "Two starter paths",
        "description": "\u003cp\u003eOne to six path objects.\u003c/p\u003e",
        "values": "1–6 items",
        "deprecated": false
      },
      {
        "name": "columns",
        "type": "number",
        "required": false,
        "defaultValue": "2",
        "description": "\u003cp\u003eResponsive column target.\u003c/p\u003e",
        "values": "1, 2, 3",
        "deprecated": false
      },
      {
        "name": "tone",
        "type": "enum",
        "required": false,
        "defaultValue": "theme",
        "description": "\u003cp\u003ePanel treatment.\u003c/p\u003e",
        "values": "theme, paper, ink, blueprint",
        "deprecated": false
      },
      {
        "name": "textAlign",
        "type": "enum",
        "required": false,
        "defaultValue": "left",
        "description": "\u003cp\u003eCard content alignment.\u003c/p\u003e",
        "values": "left, center",
        "deprecated": false
      },
      {
        "name": "compact",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eReduces panel spacing, card height, and type scale.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "showNumbers",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eShows each path number.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "showIcons",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows the decorative icon badge on every path.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "showLinks",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows the bottom action row. Hiding it does not disable the card destination.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "panelColor / accentColor",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional hexadecimal color overrides.\u003c/p\u003e",
        "values": "#RRGGBB",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Audience Paths attributes

Section content and layout accepted by <code>docspress/audience-paths</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `eyebrow` | string | No | Choose a starting point | <p>Plain-text overline.</p> |
| `title` | string | No | Where are your docs today? | <p>Section heading.</p> |
| `description` | string | No | Follow the path that matches your repository. | <p>Section summary.</p> |
| `paths` | array | Yes | Two starter paths | <p>One to six path objects.</p> |
| `columns` | number | No | 2 | <p>Responsive column target.</p> |
| `tone` | enum | No | theme | <p>Panel treatment.</p> |
| `textAlign` | enum | No | left | <p>Card content alignment.</p> |
| `compact` | boolean | No | false | <p>Reduces panel spacing, card height, and type scale.</p> |
| `showNumbers` | boolean | No | false | <p>Shows each path number.</p> |
| `showIcons` | boolean | No | true | <p>Shows the decorative icon badge on every path.</p> |
| `showLinks` | boolean | No | true | <p>Shows the bottom action row. Hiding it does not disable the card destination.</p> |
| `panelColor / accentColor` | string | No |  | <p>Optional hexadecimal color overrides.</p> |
<!-- /docspress:block -->

Each `paths` item accepts:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Path object",
    "description": "Fields for one audience destination.",
    "fields": [
      {
        "name": "title",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eCard heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "description",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eShort plain-text explanation.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "url",
        "type": "url",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eDestination. An empty URL renders a non-link card.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "cta",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eAction label.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "icon",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eSemantic icon ID selected in the editor.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "accent",
        "type": "enum",
        "required": false,
        "defaultValue": "blue",
        "description": "\u003cp\u003eCard accent.\u003c/p\u003e",
        "values": "blue, gold, coral, green",
        "deprecated": false
      },
      {
        "name": "newTab",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eOpens the destination in a new tab.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Path object

Fields for one audience destination.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `title` | string | Yes |  | <p>Card heading.</p> |
| `description` | string | No |  | <p>Short plain-text explanation.</p> |
| `url` | url | No |  | <p>Destination. An empty URL renders a non-link card.</p> |
| `cta` | string | No |  | <p>Action label.</p> |
| `icon` | string | No |  | <p>Semantic icon ID selected in the editor.</p> |
| `accent` | enum | No | blue | <p>Card accent.</p> |
| `newTab` | boolean | No | false | <p>Opens the destination in a new tab.</p> |
<!-- /docspress:block -->

The icon selector includes documentation, code, site, AI, API, terminal, testing, troubleshooting, repository, security, operations, and other common documentation concepts. Legacy icon abbreviations remain compatible.

## Creative examples

### Choose by documentation state

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "anchor": "choose-your-path",
    "align": "wide",
    "compact": false,
    "eyebrow": "Choose a starting point",
    "title": "Where are your docs today?",
    "description": "Follow the path that matches your repository.",
    "paths": [
      {
        "title": "I already have Markdown docs",
        "description": "Connect an existing docs folder to WordPress and begin with a safe draft sync.",
        "url": "/docs/publish-existing-docs/",
        "cta": "Publish existing docs",
        "icon": "document",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "I need to create docs",
        "description": "Generate source-grounded documentation with AI, review it, then publish it.",
        "url": "/docs/create-docs-with-ai/",
        "cta": "Create docs with AI",
        "icon": "sparkles",
        "accent": "gold",
        "newTab": false
      }
    ],
    "columns": 2,
    "tone": "theme",
    "textAlign": "left",
    "showNumbers": false
  }
}
-->
_Choose a starting point_

## Where are your docs today?

Follow the path that matches your repository.

### I already have Markdown docs

Connect an existing docs folder to WordPress and begin with a safe draft sync.

[Publish existing docs](/docs/publish-existing-docs/)

### I need to create docs

Generate source-grounded documentation with AI, review it, then publish it.

[Create docs with AI](/docs/create-docs-with-ai/)
<!-- /docspress:block -->

### Choose by role

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "eyebrow": "Pick your workspace",
    "title": "What are you responsible for?",
    "description": "Start with the controls, examples, and checks that match your role.",
    "paths": [
      {
        "title": "I build integrations",
        "description": "Learn endpoints, authentication, webhooks, and safe retry behavior.",
        "url": "#developers",
        "cta": "Open developer docs",
        "icon": "code",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "I run the site",
        "description": "Configure publishing, navigation, search, and editorial access.",
        "url": "#administrators",
        "cta": "Open administrator docs",
        "icon": "site",
        "accent": "gold",
        "newTab": false
      },
      {
        "title": "I contribute fixes",
        "description": "Set up the repository, run checks, and prepare a reviewable change.",
        "url": "#contributors",
        "cta": "Open contributor docs",
        "icon": "contribute",
        "accent": "green",
        "newTab": false
      }
    ],
    "columns": 3,
    "tone": "ink",
    "textAlign": "center",
    "compact": false,
    "showNumbers": true
  }
}
-->
_Pick your workspace_

## What are you responsible for?

Start with the controls, examples, and checks that match your role.

### I build integrations

Learn endpoints, authentication, webhooks, and safe retry behavior.

[Open developer docs](#developers)

### I run the site

Configure publishing, navigation, search, and editorial access.

[Open administrator docs](#administrators)

### I contribute fixes

Set up the repository, run checks, and prepare a reviewable change.

[Open contributor docs](#contributors)
<!-- /docspress:block -->

### Compact task router

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "eyebrow": "Jump to a task",
    "title": "What do you need right now?",
    "description": "Use this compact router inside a longer operations guide.",
    "paths": [
      {
        "title": "Call the API",
        "description": "Build and inspect a request.",
        "url": "#api",
        "cta": "API examples",
        "icon": "api",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "Run a command",
        "description": "Copy a verified terminal workflow.",
        "url": "#terminal",
        "cta": "Terminal steps",
        "icon": "terminal",
        "accent": "gold",
        "newTab": false
      },
      {
        "title": "Trace a failure",
        "description": "Follow symptoms to the next check.",
        "url": "#diagnose",
        "cta": "Troubleshoot",
        "icon": "bug",
        "accent": "coral",
        "newTab": false
      },
      {
        "title": "Tune performance",
        "description": "Measure first, then change one variable.",
        "url": "#performance",
        "cta": "Performance guide",
        "icon": "performance",
        "accent": "green",
        "newTab": false
      }
    ],
    "columns": 2,
    "tone": "blueprint",
    "textAlign": "left",
    "compact": true,
    "showNumbers": false
  }
}
-->
_Jump to a task_

## What do you need right now?

Use this compact router inside a longer operations guide.

### Call the API

Build and inspect a request.

[API examples](#api)

### Run a command

Copy a verified terminal workflow.

[Terminal steps](#terminal)

### Trace a failure

Follow symptoms to the next check.

[Troubleshoot](#diagnose)

### Tune performance

Measure first, then change one variable.

[Performance guide](#performance)
<!-- /docspress:block -->

### Text-only paths without icons

Use a single compact column when the wording should carry the decision and decorative icon badges would add noise.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "eyebrow": "Choose a reading path",
    "title": "How do you want to learn?",
    "description": "Start with an explanation or move directly into a guided task.",
    "paths": [
      {
        "title": "Understand the model",
        "description": "Learn the concepts, boundaries, and vocabulary before changing a project.",
        "url": "#concepts",
        "cta": "Read the concepts",
        "icon": "document",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "Build the first version",
        "description": "Follow a small end-to-end example and verify the result as you go.",
        "url": "#tutorial",
        "cta": "Start the tutorial",
        "icon": "terminal",
        "accent": "green",
        "newTab": false
      }
    ],
    "columns": 1,
    "tone": "paper",
    "textAlign": "left",
    "compact": true,
    "showNumbers": false,
    "showIcons": false,
    "showLinks": true
  }
}
-->
_Choose a reading path_

## How do you want to learn?

Start with an explanation or move directly into a guided task.

### Understand the model

Learn the concepts, boundaries, and vocabulary before changing a project.

[Read the concepts](#concepts)

### Build the first version

Follow a small end-to-end example and verify the result as you go.

[Start the tutorial](#tutorial)
<!-- /docspress:block -->

### Linked cards without bottom actions

Hide the repeated action row when concise three-column cards already make their destinations clear. The full card remains clickable.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/audience-paths",
  "attrs": {
    "eyebrow": "Browse the reference",
    "title": "Choose an area",
    "description": "Each complete card opens its matching reference section.",
    "paths": [
      {
        "title": "Configuration",
        "description": "Review supported settings and defaults.",
        "url": "#configuration",
        "cta": "Open configuration",
        "icon": "settings",
        "accent": "blue",
        "newTab": false
      },
      {
        "title": "Authentication",
        "description": "Choose credentials and protect secrets.",
        "url": "#authentication",
        "cta": "Open authentication",
        "icon": "security",
        "accent": "gold",
        "newTab": false
      },
      {
        "title": "Troubleshooting",
        "description": "Match symptoms to the next diagnostic check.",
        "url": "#troubleshooting",
        "cta": "Open troubleshooting",
        "icon": "bug",
        "accent": "coral",
        "newTab": false
      }
    ],
    "columns": 3,
    "tone": "theme",
    "textAlign": "left",
    "compact": true,
    "showNumbers": false,
    "showIcons": true,
    "showLinks": false
  }
}
-->
_Browse the reference_

## Choose an area

Each complete card opens its matching reference section.

### Configuration

Review supported settings and defaults.

[Open configuration](#configuration)

### Authentication

Choose credentials and protect secrets.

[Open authentication](#authentication)

### Troubleshooting

Match symptoms to the next diagnostic check.

[Open troubleshooting](#troubleshooting)
<!-- /docspress:block -->

## Published behavior and accessibility

A destination card is one native link rather than nested links, so it works without JavaScript and has a predictable keyboard focus target. Turning off **Show bottom links** removes only the visible action row; the card remains linked and the readable Markdown fallback keeps its destination. Turning off **Show icons** removes decorative badges without removing information. New-tab destinations receive safe relationship attributes. Empty URLs intentionally render static cards.

Keep the paths mutually exclusive enough that a reader can choose quickly. Start each title with the reader’s state or goal, keep descriptions parallel, and use consistent action labels.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Make the decision obvious",
    "content": "\u003cp\u003eReaders should understand the difference between paths from the titles alone. If two cards lead to nearly the same workflow, use ordinary links inside one path instead.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!TIP]
>
> **Make the decision obvious**
>
> Readers should understand the difference between paths from the titles alone. If two cards lead to nearly the same workflow, use ordinary links inside one path instead.
<!-- /docspress:block -->
