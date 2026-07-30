---
title: Callout
sidebar_position: 50
---

Use `docspress/callout` to separate an important note, tip, warning, danger, or success message from the surrounding lesson.

## When to use it

Choose a callout for information a reader might otherwise miss. Do not place ordinary paragraphs in callouts for decoration. Use [Result](result.md) when the message is specifically the outcome of a procedure or check.

## Edit the block

Edit the title and rich content in the canvas. Select the tone that matches the consequence. Enable **Collapsible** only for optional detail; `open` determines its initial state.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Callout attributes",
    "description": "Message and disclosure behavior accepted by \u003ccode\u003edocspress/callout\u003c/code\u003e.",
    "fields": [
      {
        "name": "tone",
        "type": "enum",
        "required": false,
        "defaultValue": "note",
        "description": "\u003cp\u003eSemantic message severity.\u003c/p\u003e",
        "values": "note, tip, warning, danger, success",
        "deprecated": false
      },
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Good to know",
        "description": "\u003cp\u003eFormatted callout heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "content",
        "type": "string",
        "required": false,
        "defaultValue": "Starter paragraph",
        "description": "\u003cp\u003eFormatted HTML content.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "collapsible",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eUses a native disclosure when enabled.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "open",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eInitial state of a collapsible callout.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Callout attributes

Message and disclosure behavior accepted by <code>docspress/callout</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `tone` | enum | No | note | <p>Semantic message severity.</p> |
| `title` | string | No | Good to know | <p>Formatted callout heading.</p> |
| `content` | string | No | Starter paragraph | <p>Formatted HTML content.</p> |
| `collapsible` | boolean | No | false | <p>Uses a native disclosure when enabled.</p> |
| `open` | boolean | No | true | <p>Initial state of a collapsible callout.</p> |
<!-- /docspress:block -->

## Creative examples

### Open preview tip

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Preview first",
    "content": "\u003cp\u003eUse a dry run before allowing Page writes.\u003c/p\u003e",
    "collapsible": true,
    "open": true
  }
}
-->
> [!TIP]
>
> **Preview first**
>
> Use a dry run before allowing Page writes.
<!-- /docspress:block -->

### Destructive-operation warning

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "danger",
    "title": "Stop before resetting production",
    "content": "\u003cp\u003eA hard reset can remove unreviewed content. Export the current state, name the recovery owner, and verify the exact target before continuing.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!CAUTION]
>
> **Stop before resetting production**
>
> A hard reset can remove unreviewed content. Export the current state, name the recovery owner, and verify the exact target before continuing.
<!-- /docspress:block -->

### Collapsed background detail

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "note",
    "title": "Why the cache refresh takes a minute",
    "content": "\u003cp\u003eEdge nodes refresh independently. The published Page is already authoritative, but a reader in another region may briefly receive the previous copy.\u003c/p\u003e",
    "collapsible": true,
    "open": false
  }
}
-->
> [!NOTE]
>
> **Why the cache refresh takes a minute**
>
> Edge nodes refresh independently. The published Page is already authoritative, but a reader in another region may briefly receive the previous copy.
<!-- /docspress:block -->

## Tone guide

| Tone | Use it for |
| --- | --- |
| `note` | Context or a neutral detail |
| `tip` | A faster, safer, or more effective technique |
| `warning` | A recoverable risk or prerequisite |
| `danger` | Destructive, security-sensitive, or difficult-to-reverse action |
| `success` | Confirmation or a known-good state |

## Published behavior and accessibility

A standard callout renders as an aside. A collapsible callout uses native `details` and `summary`, so it remains operable without custom JavaScript. The tone is conveyed by text and structure as well as color.

Keep the title meaningful outside its surrounding paragraph. Do not hide required steps in a collapsed callout, and reserve danger for consequences that truly warrant interruption.
