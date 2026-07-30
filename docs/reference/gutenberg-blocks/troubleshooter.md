---
title: Interactive Troubleshooter
sidebar_position: 150
---

Use `docspress/troubleshooter` for a short branching support, diagnosis, or onboarding flow. Readers answer yes-or-no questions, move back through their choices, and arrive at a semantic outcome.

## When to use it

Choose Interactive Troubleshooter when the correct next step depends on a few observable conditions. Use [Audience Paths](audience-paths.md) for independent destinations and [Flow](flow.md) for one linear procedure. If the tree needs more than 12 questions, split the problem or move it into a dedicated support application.

## Design the routes

Give every question and outcome a stable unique ID. Set `yesNext` and `noNext` to another question ID or an outcome ID. Start with the condition that divides readers most usefully, and make every answer label describe the choice when “Yes” and “No” would be ambiguous.

DocsPress sanitizes IDs and adds suffixes to duplicates. An invalid `startId` falls back to the first question. An invalid destination shows an authoring warning instead of an empty panel, but it still leaves the reader without an outcome, so test every route.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Interactive Troubleshooter attributes",
    "description": "Frame, routes, and outcomes accepted by \u003ccode\u003edocspress/troubleshooter\u003c/code\u003e.",
    "fields": [
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Find the next step",
        "description": "\u003cp\u003ePlain-text troubleshooter heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "intro",
        "type": "string",
        "required": false,
        "defaultValue": "Starter introduction",
        "description": "\u003cp\u003eFormatted explanation before the first question.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "startId",
        "type": "string",
        "required": false,
        "defaultValue": "First question ID",
        "description": "\u003cp\u003eID of the first question. Invalid values fall back to the first normalized question.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "questions",
        "type": "array",
        "required": true,
        "defaultValue": "Starter questions",
        "description": "\u003cp\u003eUp to 12 branching question objects.\u003c/p\u003e",
        "values": "1–12 items",
        "deprecated": false
      },
      {
        "name": "outcomes",
        "type": "array",
        "required": true,
        "defaultValue": "Starter outcomes",
        "description": "\u003cp\u003eUp to 12 terminal outcome objects.\u003c/p\u003e",
        "values": "1–12 items",
        "deprecated": false
      },
      {
        "name": "showProgress",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows the reader’s progress through the current path.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Interactive Troubleshooter attributes

Frame, routes, and outcomes accepted by <code>docspress/troubleshooter</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `title` | string | No | Find the next step | <p>Plain-text troubleshooter heading.</p> |
| `intro` | string | No | Starter introduction | <p>Formatted explanation before the first question.</p> |
| `startId` | string | No | First question ID | <p>ID of the first question. Invalid values fall back to the first normalized question.</p> |
| `questions` | array | Yes | Starter questions | <p>Up to 12 branching question objects.</p> |
| `outcomes` | array | Yes | Starter outcomes | <p>Up to 12 terminal outcome objects.</p> |
| `showProgress` | boolean | No | true | <p>Shows the reader’s progress through the current path.</p> |
<!-- /docspress:block -->

Each question contains `id`, `question`, `yesLabel`, `yesNext`, `noLabel`, and `noNext`. Each outcome contains `id`, `status`, `title`, and formatted `content`; `status` accepts `success`, `neutral`, `warning`, or `error`.

## Creative examples

### Find the next documentation workflow

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/troubleshooter",
  "attrs": {
    "title": "Find the next step",
    "intro": "Answer two quick questions to get the right workflow.",
    "startId": "source",
    "questions": [
      {
        "id": "source",
        "question": "Do you already have Markdown documentation?",
        "yesLabel": "Yes",
        "yesNext": "connected",
        "noLabel": "No",
        "noNext": "generate"
      },
      {
        "id": "connected",
        "question": "Is the repository connected to WordPress?",
        "yesLabel": "Yes",
        "yesNext": "sync",
        "noLabel": "No",
        "noNext": "install"
      }
    ],
    "outcomes": [
      {
        "id": "install",
        "status": "warning",
        "title": "Connect WordPress",
        "content": "\u003cp\u003eRun the installer and verify the publishing target.\u003c/p\u003e"
      },
      {
        "id": "sync",
        "status": "success",
        "title": "Publish the docs",
        "content": "\u003cp\u003eRun a draft sync and review the rendered Pages.\u003c/p\u003e"
      },
      {
        "id": "generate",
        "status": "neutral",
        "title": "Generate a starter",
        "content": "\u003cp\u003eCreate source-grounded Markdown documentation first.\u003c/p\u003e"
      }
    ],
    "showProgress": true
  }
}
-->
## Find the next step

Answer two quick questions to get the right workflow.

- **Do you already have Markdown documentation?** — Yes / No
- **Is the repository connected to WordPress?** — Yes / No

### Connect WordPress

Run the installer and verify the publishing target.

### Publish the docs

Run a draft sync and review the rendered Pages.

### Generate a starter

Create source-grounded Markdown documentation first.
<!-- /docspress:block -->

### Diagnose a stale preview

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/troubleshooter",
  "attrs": {
    "title": "Why does the preview look stale?",
    "intro": "Use observable states to choose the next safe check.",
    "startId": "source-current",
    "questions": [
      {
        "id": "source-current",
        "question": "Does the local Markdown contain the expected change?",
        "yesLabel": "Yes, source is current",
        "yesNext": "sync-complete",
        "noLabel": "No, source is stale",
        "noNext": "update-source"
      },
      {
        "id": "sync-complete",
        "question": "Did the latest synchronization finish without conflicts?",
        "yesLabel": "Yes, sync completed",
        "yesNext": "cache",
        "noLabel": "No, it failed or conflicted",
        "noNext": "inspect-sync"
      },
      {
        "id": "cache",
        "question": "Does a cache-bypassed request show the new content?",
        "yesLabel": "Yes, origin is current",
        "yesNext": "wait-edge",
        "noLabel": "No, origin is stale",
        "noNext": "inspect-page"
      }
    ],
    "outcomes": [
      {
        "id": "update-source",
        "status": "neutral",
        "title": "Update the source first",
        "content": "\u003cp\u003eMake the reviewed change in Markdown, then preview the generated Page.\u003c/p\u003e"
      },
      {
        "id": "inspect-sync",
        "status": "warning",
        "title": "Resolve synchronization",
        "content": "\u003cp\u003eRead the conflict or error detail before attempting another publish.\u003c/p\u003e"
      },
      {
        "id": "wait-edge",
        "status": "success",
        "title": "The publish succeeded",
        "content": "\u003cp\u003eThe origin is current. Allow the edge cache to refresh and avoid duplicate writes.\u003c/p\u003e"
      },
      {
        "id": "inspect-page",
        "status": "error",
        "title": "Inspect the managed Page",
        "content": "\u003cp\u003eConfirm the expected Page ID, source key, and rendered block content.\u003c/p\u003e"
      }
    ],
    "showProgress": true
  }
}
-->
## Why does the preview look stale?

Use observable states to choose the next safe check.

- **Does the local Markdown contain the expected change?** — Yes, source is current / No, source is stale
- **Did the latest synchronization finish without conflicts?** — Yes, sync completed / No, it failed or conflicted
- **Does a cache-bypassed request show the new content?** — Yes, origin is current / No, origin is stale

### Update the source first

Make the reviewed change in Markdown, then preview the generated Page.

### Resolve synchronization

Read the conflict or error detail before attempting another publish.

### The publish succeeded

The origin is current. Allow the edge cache to refresh and avoid duplicate writes.

### Inspect the managed Page

Confirm the expected Page ID, source key, and rendered block content.
<!-- /docspress:block -->

### Choose a code-example block

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/troubleshooter",
  "attrs": {
    "title": "Which code block should I use?",
    "intro": "Answer two questions to choose the smallest useful component.",
    "startId": "runnable",
    "questions": [
      {
        "id": "runnable",
        "question": "Should readers execute or edit the example in the page?",
        "yesLabel": "Yes, make it interactive",
        "yesNext": "http",
        "noLabel": "No, display and copy are enough",
        "noNext": "alternatives"
      },
      {
        "id": "http",
        "question": "Is the example an HTTP request?",
        "yesLabel": "Yes, it is an API exchange",
        "yesNext": "api",
        "noLabel": "No, it is browser code",
        "noNext": "playground"
      },
      {
        "id": "alternatives",
        "question": "Are there equivalent versions of the same example?",
        "yesLabel": "Yes, show alternatives",
        "yesNext": "tabs",
        "noLabel": "No, show one source",
        "noNext": "colorful"
      }
    ],
    "outcomes": [
      {
        "id": "api",
        "status": "success",
        "title": "Use API Request / Response",
        "content": "\u003cp\u003eShow the method, endpoint, headers, body, and guarded runner together.\u003c/p\u003e"
      },
      {
        "id": "playground",
        "status": "success",
        "title": "Use Live Code Playground",
        "content": "\u003cp\u003eKeep the HTML, CSS, and JavaScript self-contained and sandboxed.\u003c/p\u003e"
      },
      {
        "id": "tabs",
        "status": "neutral",
        "title": "Use Code Tabs",
        "content": "\u003cp\u003eGive every tab the same task and expected result.\u003c/p\u003e"
      },
      {
        "id": "colorful",
        "status": "neutral",
        "title": "Use Colorful Code",
        "content": "\u003cp\u003eHighlight or annotate only the lines the explanation depends on.\u003c/p\u003e"
      }
    ],
    "showProgress": false
  }
}
-->
## Which code block should I use?

Answer two questions to choose the smallest useful component.

- **Should readers execute or edit the example in the page?** — Yes, make it interactive / No, display and copy are enough
- **Is the example an HTTP request?** — Yes, it is an API exchange / No, it is browser code
- **Are there equivalent versions of the same example?** — Yes, show alternatives / No, show one source

### Use API Request / Response

Show the method, endpoint, headers, body, and guarded runner together.

### Use Live Code Playground

Keep the HTML, CSS, and JavaScript self-contained and sandboxed.

### Use Code Tabs

Give every tab the same task and expected result.

### Use Colorful Code

Highlight or annotate only the lines the explanation depends on.
<!-- /docspress:block -->

## Published behavior and accessibility

The active question or outcome is the only visible panel. Back and Start over preserve a clear recovery path, focus moves to the new heading, and progress changes use a live announcement. Controls are real buttons and the authored content remains in the Page source.

Walk every possible route before publishing, including Back and Start over. Phrase questions around facts the reader can know, and make every outcome name the next action.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "Test the complete decision tree",
    "content": "\u003cp\u003eEvery yes and no destination must resolve to a question or outcome ID. Check all routes after renaming an ID or reordering the flow.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!WARNING]
>
> **Test the complete decision tree**
>
> Every yes and no destination must resolve to a question or outcome ID. Check all routes after renaming an ID or reordering the flow.
<!-- /docspress:block -->
