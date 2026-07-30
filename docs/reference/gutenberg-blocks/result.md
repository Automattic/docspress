---
title: Result
sidebar_position: 120
---

Use `docspress/result` to summarize the outcome of a procedure, validation, deployment, or diagnostic check. It combines semantic status, a heading, formatted explanation, and compact metadata.

## When to use it

Choose Result after readers perform an action and need to recognize the expected state. Use [Callout](callout.md) for advice or risk that exists independently of a procedure. Do not use success styling as decoration when no result has occurred.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Result attributes",
    "description": "Outcome content and status accepted by \u003ccode\u003edocspress/result\u003c/code\u003e.",
    "fields": [
      {
        "name": "status",
        "type": "enum",
        "required": false,
        "defaultValue": "success",
        "description": "\u003cp\u003eSemantic outcome state.\u003c/p\u003e",
        "values": "success, neutral, warning, error",
        "deprecated": false
      },
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Deployment completed",
        "description": "\u003cp\u003eFormatted result heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "content",
        "type": "string",
        "required": false,
        "defaultValue": "All documentation pages are up to date.",
        "description": "\u003cp\u003eFormatted explanation of the outcome and next step.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "meta",
        "type": "string",
        "required": false,
        "defaultValue": "12 pages · 1.8s",
        "description": "\u003cp\u003eCompact plain-text evidence such as a command, count, or duration.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Result attributes

Outcome content and status accepted by <code>docspress/result</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `status` | enum | No | success | <p>Semantic outcome state.</p> |
| `title` | string | No | Deployment completed | <p>Formatted result heading.</p> |
| `content` | string | No | All documentation pages are up to date. | <p>Formatted explanation of the outcome and next step.</p> |
| `meta` | string | No | 12 pages · 1.8s | <p>Compact plain-text evidence such as a command, count, or duration.</p> |
<!-- /docspress:block -->

## Status guide

| Status | Use it when |
| --- | --- |
| `success` | The intended task completed |
| `neutral` | The check produced information without a pass or fail |
| `warning` | The task completed partially or needs attention |
| `error` | The task failed or cannot continue |

## Creative examples

### Completed package verification

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "Package verified",
    "content": "\u003cp\u003eLint, tests, and the Action bundle completed.\u003c/p\u003e",
    "meta": "npm run package"
  }
}
-->
> [!TIP]
>
> **Package verified**
>
> Lint, tests, and the Action bundle completed.
>
> _npm run package_
<!-- /docspress:block -->

### Partial synchronization

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "warning",
    "title": "Three Pages need editorial review",
    "content": "\u003cp\u003eThe safe changes were published. Three Pages remain drafts because their source changed in WordPress and GitHub.\u003c/p\u003e",
    "meta": "39 published · 3 conflicts"
  }
}
-->
> [!WARNING]
>
> **Three Pages need editorial review**
>
> The safe changes were published. Three Pages remain drafts because their source changed in WordPress and GitHub.
>
> _39 published · 3 conflicts_
<!-- /docspress:block -->

### No-op deployment

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "neutral",
    "title": "Everything is already current",
    "content": "\u003cp\u003eThe desired Markdown tree matches every managed WordPress Page, so no writes were necessary.\u003c/p\u003e",
    "meta": "42 unchanged · 0 conflicts"
  }
}
-->
> [!NOTE]
>
> **Everything is already current**
>
> The desired Markdown tree matches every managed WordPress Page, so no writes were necessary.
>
> _42 unchanged · 0 conflicts_
<!-- /docspress:block -->

## Published behavior and accessibility

The block renders a semantic result summary. Its status remains identifiable through text and structure rather than relying on icon shape or color alone; the icon is decorative.

Name the completed action in the title, explain what was verified in the content, and use metadata for evidence rather than a second description.
