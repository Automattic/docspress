---
title: Flow
sidebar_position: 60
---

Use `docspress/flow` for a connected, automatically numbered procedure. It gives a multi-step task more visual continuity than a normal ordered list while preserving semantic list markup.

## When to use it

Choose Flow when readers must perform the steps in order. Use ordinary bullets for independent choices, [Audience Paths](audience-paths.md) for branching destinations, and [Interactive Troubleshooter](troubleshooter.md) for a decision tree.

## Edit the block

Add, remove, and reorder steps in Gutenberg. Give each step a concise action title and put the explanation, commands, or expected state in its formatted content. Set `start` when this Flow continues numbering from an earlier sequence.

## Attributes

<!-- wp:docspress/fields {"title":"Flow attributes","description":"Numbering and steps accepted by <code>docspress/flow</code>.","fields":[{"name":"start","type":"number","required":false,"defaultValue":"1","description":"<p>First visible step number, clamped to 1–99.</p>","values":"1–99","deprecated":false},{"name":"steps","type":"array","required":true,"defaultValue":"Three starter steps","description":"<p>One to 20 ordered step objects.</p>","values":"1–20 items","deprecated":false}],"searchable":false,"compact":true} /-->

Each step has a formatted `title` and formatted `content`. Empty or invalid step data falls back to the three starter steps so the block never publishes as an unexplained empty shell.

## Creative examples

### Configure and verify

<!-- wp:docspress/flow {"start":1,"steps":[{"title":"Choose","content":"<p>Select the option that matches your project.</p>"},{"title":"Configure","content":"<p>Set the values required by your environment.</p>"},{"title":"Verify","content":"<p>Run the check and confirm the expected result.</p>"}]} /-->

### Continue a release checklist

<!-- wp:docspress/flow {"start":4,"steps":[{"title":"Freeze content edits","content":"<p>Tell editors when the release window begins and where urgent corrections should go.</p>"},{"title":"Publish the migration","content":"<p>Apply the reviewed change to the staged target before production.</p>"},{"title":"Observe the first readers","content":"<p>Watch error rate, search exits, and support messages for fifteen minutes.</p>"}]} /-->

### Restore service during an incident

<!-- wp:docspress/flow {"start":1,"steps":[{"title":"Name the impact","content":"<p>Record which readers, routes, and regions are affected.</p>"},{"title":"Protect evidence","content":"<p>Capture the failing request and relevant logs before changing the system.</p>"},{"title":"Reduce the blast radius","content":"<p>Disable the smallest unsafe surface or route traffic to the known-good version.</p>"},{"title":"Confirm recovery","content":"<p>Repeat the original failing journey and document the observed result.</p>"}]} /-->

## Published behavior and accessibility

The block renders as an ordered list with an explicit starting value. Numbers are part of the procedure’s semantics, not decorative text, and remain meaningful without CSS or JavaScript.

Start each title with an imperative verb. Keep one reader action per step, and place the observable success condition at the end of the step.

<!-- wp:docspress/callout {"tone":"tip","title":"Show the verification step","content":"<p>A procedure is easier to trust when its last step tells readers exactly how to confirm that the task succeeded.</p>","collapsible":false,"open":true} /-->
