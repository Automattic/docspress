---
title: Result
sidebar_position: 120
---

Use `docspress/result` to summarize the outcome of a procedure, validation, deployment, or diagnostic check. It combines semantic status, a heading, formatted explanation, and compact metadata.

## When to use it

Choose Result after readers perform an action and need to recognize the expected state. Use [Callout](callout.md) for advice or risk that exists independently of a procedure. Do not use success styling as decoration when no result has occurred.

## Attributes

<!-- wp:docspress/fields {"title":"Result attributes","description":"Outcome content and status accepted by <code>docspress/result</code>.","fields":[{"name":"status","type":"enum","required":false,"defaultValue":"success","description":"<p>Semantic outcome state.</p>","values":"success, neutral, warning, error","deprecated":false},{"name":"title","type":"string","required":false,"defaultValue":"Deployment completed","description":"<p>Formatted result heading.</p>","values":"","deprecated":false},{"name":"content","type":"string","required":false,"defaultValue":"All documentation pages are up to date.","description":"<p>Formatted explanation of the outcome and next step.</p>","values":"","deprecated":false},{"name":"meta","type":"string","required":false,"defaultValue":"12 pages · 1.8s","description":"<p>Compact plain-text evidence such as a command, count, or duration.</p>","values":"","deprecated":false}],"searchable":false,"compact":true} /-->

## Status guide

| Status | Use it when |
| --- | --- |
| `success` | The intended task completed |
| `neutral` | The check produced information without a pass or fail |
| `warning` | The task completed partially or needs attention |
| `error` | The task failed or cannot continue |

## Creative examples

### Completed package verification

<!-- wp:docspress/result {"status":"success","title":"Package verified","content":"<p>Lint, tests, and the Action bundle completed.</p>","meta":"npm run package"} /-->

### Partial synchronization

<!-- wp:docspress/result {"status":"warning","title":"Three Pages need editorial review","content":"<p>The safe changes were published. Three Pages remain drafts because their source changed in WordPress and GitHub.</p>","meta":"39 published · 3 conflicts"} /-->

### No-op deployment

<!-- wp:docspress/result {"status":"neutral","title":"Everything is already current","content":"<p>The desired Markdown tree matches every managed WordPress Page, so no writes were necessary.</p>","meta":"42 unchanged · 0 conflicts"} /-->

## Published behavior and accessibility

The block renders a semantic result summary. Its status remains identifiable through text and structure rather than relying on icon shape or color alone; the icon is decorative.

Name the completed action in the title, explain what was verified in the content, and use metadata for evidence rather than a second description.
