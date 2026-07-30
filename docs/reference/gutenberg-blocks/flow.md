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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Flow attributes",
    "description": "Numbering and steps accepted by \u003ccode\u003edocspress/flow\u003c/code\u003e.",
    "fields": [
      {
        "name": "start",
        "type": "number",
        "required": false,
        "defaultValue": "1",
        "description": "\u003cp\u003eFirst visible step number, clamped to 1–99.\u003c/p\u003e",
        "values": "1–99",
        "deprecated": false
      },
      {
        "name": "steps",
        "type": "array",
        "required": true,
        "defaultValue": "Three starter steps",
        "description": "\u003cp\u003eOne to 20 ordered step objects.\u003c/p\u003e",
        "values": "1–20 items",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Flow attributes

Numbering and steps accepted by <code>docspress/flow</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `start` | number | No | 1 | <p>First visible step number, clamped to 1–99.</p> |
| `steps` | array | Yes | Three starter steps | <p>One to 20 ordered step objects.</p> |
<!-- /docspress:block -->

Each step has a formatted `title` and formatted `content`. Empty or invalid step data falls back to the three starter steps so the block never publishes as an unexplained empty shell.

## Creative examples

### Configure and verify

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/flow",
  "attrs": {
    "start": 1,
    "steps": [
      {
        "title": "Choose",
        "content": "\u003cp\u003eSelect the option that matches your project.\u003c/p\u003e"
      },
      {
        "title": "Configure",
        "content": "\u003cp\u003eSet the values required by your environment.\u003c/p\u003e"
      },
      {
        "title": "Verify",
        "content": "\u003cp\u003eRun the check and confirm the expected result.\u003c/p\u003e"
      }
    ]
  }
}
-->
1. **Choose**

   Select the option that matches your project.

2. **Configure**

   Set the values required by your environment.

3. **Verify**

   Run the check and confirm the expected result.
<!-- /docspress:block -->

### Continue a release checklist

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/flow",
  "attrs": {
    "start": 4,
    "steps": [
      {
        "title": "Freeze content edits",
        "content": "\u003cp\u003eTell editors when the release window begins and where urgent corrections should go.\u003c/p\u003e"
      },
      {
        "title": "Publish the migration",
        "content": "\u003cp\u003eApply the reviewed change to the staged target before production.\u003c/p\u003e"
      },
      {
        "title": "Observe the first readers",
        "content": "\u003cp\u003eWatch error rate, search exits, and support messages for fifteen minutes.\u003c/p\u003e"
      }
    ]
  }
}
-->
4. **Freeze content edits**

   Tell editors when the release window begins and where urgent corrections should go.

5. **Publish the migration**

   Apply the reviewed change to the staged target before production.

6. **Observe the first readers**

   Watch error rate, search exits, and support messages for fifteen minutes.
<!-- /docspress:block -->

### Restore service during an incident

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/flow",
  "attrs": {
    "start": 1,
    "steps": [
      {
        "title": "Name the impact",
        "content": "\u003cp\u003eRecord which readers, routes, and regions are affected.\u003c/p\u003e"
      },
      {
        "title": "Protect evidence",
        "content": "\u003cp\u003eCapture the failing request and relevant logs before changing the system.\u003c/p\u003e"
      },
      {
        "title": "Reduce the blast radius",
        "content": "\u003cp\u003eDisable the smallest unsafe surface or route traffic to the known-good version.\u003c/p\u003e"
      },
      {
        "title": "Confirm recovery",
        "content": "\u003cp\u003eRepeat the original failing journey and document the observed result.\u003c/p\u003e"
      }
    ]
  }
}
-->
1. **Name the impact**

   Record which readers, routes, and regions are affected.

2. **Protect evidence**

   Capture the failing request and relevant logs before changing the system.

3. **Reduce the blast radius**

   Disable the smallest unsafe surface or route traffic to the known-good version.

4. **Confirm recovery**

   Repeat the original failing journey and document the observed result.
<!-- /docspress:block -->

## Published behavior and accessibility

The block renders as an ordered list with an explicit starting value. Numbers are part of the procedure’s semantics, not decorative text, and remain meaningful without CSS or JavaScript.

Start each title with an imperative verb. Keep one reader action per step, and place the observable success condition at the end of the step.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Show the verification step",
    "content": "\u003cp\u003eA procedure is easier to trust when its last step tells readers exactly how to confirm that the task succeeded.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!TIP]
>
> **Show the verification step**
>
> A procedure is easier to trust when its last step tells readers exactly how to confirm that the task succeeded.
<!-- /docspress:block -->
