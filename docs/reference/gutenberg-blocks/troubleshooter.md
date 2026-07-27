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

<!-- wp:docspress/fields {"title":"Interactive Troubleshooter attributes","description":"Frame, routes, and outcomes accepted by <code>docspress/troubleshooter</code>.","fields":[{"name":"title","type":"string","required":false,"defaultValue":"Find the next step","description":"<p>Plain-text troubleshooter heading.</p>","values":"","deprecated":false},{"name":"intro","type":"string","required":false,"defaultValue":"Starter introduction","description":"<p>Formatted explanation before the first question.</p>","values":"","deprecated":false},{"name":"startId","type":"string","required":false,"defaultValue":"First question ID","description":"<p>ID of the first question. Invalid values fall back to the first normalized question.</p>","values":"","deprecated":false},{"name":"questions","type":"array","required":true,"defaultValue":"Starter questions","description":"<p>Up to 12 branching question objects.</p>","values":"1–12 items","deprecated":false},{"name":"outcomes","type":"array","required":true,"defaultValue":"Starter outcomes","description":"<p>Up to 12 terminal outcome objects.</p>","values":"1–12 items","deprecated":false},{"name":"showProgress","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows the reader’s progress through the current path.</p>","values":"true, false","deprecated":false}],"searchable":true,"compact":true} /-->

Each question contains `id`, `question`, `yesLabel`, `yesNext`, `noLabel`, and `noNext`. Each outcome contains `id`, `status`, `title`, and formatted `content`; `status` accepts `success`, `neutral`, `warning`, or `error`.

## Creative examples

### Find the next documentation workflow

<!-- wp:docspress/troubleshooter {"title":"Find the next step","intro":"Answer two quick questions to get the right workflow.","startId":"source","questions":[{"id":"source","question":"Do you already have Markdown documentation?","yesLabel":"Yes","yesNext":"connected","noLabel":"No","noNext":"generate"},{"id":"connected","question":"Is the repository connected to WordPress?","yesLabel":"Yes","yesNext":"sync","noLabel":"No","noNext":"install"}],"outcomes":[{"id":"install","status":"warning","title":"Connect WordPress","content":"<p>Run the installer and verify the publishing target.</p>"},{"id":"sync","status":"success","title":"Publish the docs","content":"<p>Run a draft sync and review the rendered Pages.</p>"},{"id":"generate","status":"neutral","title":"Generate a starter","content":"<p>Create source-grounded Markdown documentation first.</p>"}],"showProgress":true} /-->

### Diagnose a stale preview

<!-- wp:docspress/troubleshooter {"title":"Why does the preview look stale?","intro":"Use observable states to choose the next safe check.","startId":"source-current","questions":[{"id":"source-current","question":"Does the local Markdown contain the expected change?","yesLabel":"Yes, source is current","yesNext":"sync-complete","noLabel":"No, source is stale","noNext":"update-source"},{"id":"sync-complete","question":"Did the latest synchronization finish without conflicts?","yesLabel":"Yes, sync completed","yesNext":"cache","noLabel":"No, it failed or conflicted","noNext":"inspect-sync"},{"id":"cache","question":"Does a cache-bypassed request show the new content?","yesLabel":"Yes, origin is current","yesNext":"wait-edge","noLabel":"No, origin is stale","noNext":"inspect-page"}],"outcomes":[{"id":"update-source","status":"neutral","title":"Update the source first","content":"<p>Make the reviewed change in Markdown, then preview the generated Page.</p>"},{"id":"inspect-sync","status":"warning","title":"Resolve synchronization","content":"<p>Read the conflict or error detail before attempting another publish.</p>"},{"id":"wait-edge","status":"success","title":"The publish succeeded","content":"<p>The origin is current. Allow the edge cache to refresh and avoid duplicate writes.</p>"},{"id":"inspect-page","status":"error","title":"Inspect the managed Page","content":"<p>Confirm the expected Page ID, source key, and rendered block content.</p>"}],"showProgress":true} /-->

### Choose a code-example block

<!-- wp:docspress/troubleshooter {"title":"Which code block should I use?","intro":"Answer two questions to choose the smallest useful component.","startId":"runnable","questions":[{"id":"runnable","question":"Should readers execute or edit the example in the page?","yesLabel":"Yes, make it interactive","yesNext":"http","noLabel":"No, display and copy are enough","noNext":"alternatives"},{"id":"http","question":"Is the example an HTTP request?","yesLabel":"Yes, it is an API exchange","yesNext":"api","noLabel":"No, it is browser code","noNext":"playground"},{"id":"alternatives","question":"Are there equivalent versions of the same example?","yesLabel":"Yes, show alternatives","yesNext":"tabs","noLabel":"No, show one source","noNext":"colorful"}],"outcomes":[{"id":"api","status":"success","title":"Use API Request / Response","content":"<p>Show the method, endpoint, headers, body, and guarded runner together.</p>"},{"id":"playground","status":"success","title":"Use Live Code Playground","content":"<p>Keep the HTML, CSS, and JavaScript self-contained and sandboxed.</p>"},{"id":"tabs","status":"neutral","title":"Use Code Tabs","content":"<p>Give every tab the same task and expected result.</p>"},{"id":"colorful","status":"neutral","title":"Use Colorful Code","content":"<p>Highlight or annotate only the lines the explanation depends on.</p>"}],"showProgress":false} /-->

## Published behavior and accessibility

The active question or outcome is the only visible panel. Back and Start over preserve a clear recovery path, focus moves to the new heading, and progress changes use a live announcement. Controls are real buttons and the authored content remains in the Page source.

Walk every possible route before publishing, including Back and Start over. Phrase questions around facts the reader can know, and make every outcome name the next action.

<!-- wp:docspress/callout {"tone":"warning","title":"Test the complete decision tree","content":"<p>Every yes and no destination must resolve to a question or outcome ID. Check all routes after renaming an ID or reordering the flow.</p>","collapsible":false,"open":true} /-->
