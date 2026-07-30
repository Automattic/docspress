---
title: Prompt
sidebar_position: 140
---

Use `docspress/prompt` for a reusable AI prompt together with its intended model label, interaction mode, reasoning preference, context, and caption.

## When to use it

Choose Prompt when readers should copy and adapt an AI instruction. Use [Colorful Code](colorful-code.md) for source code and [Terminal Session](terminal-session.md) for commands. Treat prompts as maintained examples: verify their referenced skills, files, and expected outcome.

## Context syntax

`context` is a comma-separated list of up to 12 items. DocsPress classifies each item by prefix:

- `$skill-name` for an installed skill.
- `@mention` for an agent or repository mention.
- `#image` for an image reference.
- `https://…` for a URL.
- Any other value as a file or directory.

Invoke a skill as `$skill-name`. Do not tell an agent to read a `SKILL.md` path; the installed skill is the portable interface.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Prompt attributes",
    "description": "Instruction metadata and context accepted by \u003ccode\u003edocspress/prompt\u003c/code\u003e.",
    "fields": [
      {
        "name": "prompt",
        "type": "string",
        "required": true,
        "defaultValue": "DocsPress setup review prompt",
        "description": "\u003cp\u003eThe complete copyable instruction.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "model",
        "type": "string",
        "required": false,
        "defaultValue": "GPT-5",
        "description": "\u003cp\u003eDisplay label for the intended model or agent.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "mode",
        "type": "enum",
        "required": false,
        "defaultValue": "code",
        "description": "\u003cp\u003eInteraction mode shown with the example. Invalid values normalize to chat when rendered.\u003c/p\u003e",
        "values": "chat, code, ask, plan",
        "deprecated": false
      },
      {
        "name": "thinking",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows that deliberate reasoning is expected.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "context",
        "type": "string",
        "required": false,
        "defaultValue": "$docspress-install, @repository, src/sync.js, docs/",
        "description": "\u003cp\u003eComma-separated skill, mention, image, URL, and file references; at most 12 items.\u003c/p\u003e",
        "values": "Up to 12 items",
        "deprecated": false
      },
      {
        "name": "caption",
        "type": "string",
        "required": false,
        "defaultValue": "Prompt example",
        "description": "\u003cp\u003eFormatted explanation below the prompt.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Prompt attributes

Instruction metadata and context accepted by <code>docspress/prompt</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `prompt` | string | Yes | DocsPress setup review prompt | <p>The complete copyable instruction.</p> |
| `model` | string | No | GPT-5 | <p>Display label for the intended model or agent.</p> |
| `mode` | enum | No | code | <p>Interaction mode shown with the example. Invalid values normalize to chat when rendered.</p> |
| `thinking` | boolean | No | true | <p>Shows that deliberate reasoning is expected.</p> |
| `context` | string | No | $docspress-install, @repository, src/sync.js, docs/ | <p>Comma-separated skill, mention, image, URL, and file references; at most 12 items.</p> |
| `caption` | string | No | Prompt example | <p>Formatted explanation below the prompt.</p> |
<!-- /docspress:block -->

## Creative examples

### Find undocumented behavior

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Use $generate-docs-from-source to inspect the public exports and identify undocumented behavior.",
    "model": "Coding agent",
    "mode": "code",
    "thinking": true,
    "context": "$generate-docs-from-source, @repository, src/, test/",
    "caption": "Documentation coverage prompt"
  }
}
-->
#### Documentation coverage prompt

> Use $generate-docs-from-source to inspect the public exports and identify undocumented behavior.

_Model: Coding agent · Mode: code · Thinking: on · Context: $generate-docs-from-source, @repository, src/, test/_
<!-- /docspress:block -->

### Plan an incident-runbook rehearsal

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Plan a 30-minute rehearsal for the documentation publishing incident runbook. Include roles, two injected failures, observable success criteria, and a short retrospective template. Do not change production systems.",
    "model": "Reasoning model",
    "mode": "plan",
    "thinking": true,
    "context": "@operations, docs/runbooks/publishing.md, test/reconcile.test.js",
    "caption": "A bounded planning prompt with an explicit safety constraint."
  }
}
-->
#### A bounded planning prompt with an explicit safety constraint.

> Plan a 30-minute rehearsal for the documentation publishing incident runbook. Include roles, two injected failures, observable success criteria, and a short retrospective template. Do not change production systems.

_Model: Reasoning model · Mode: plan · Thinking: on · Context: @operations, docs/runbooks/publishing.md, test/reconcile.test.js_
<!-- /docspress:block -->

### Review a screenshot for reader friction

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/prompt",
  "attrs": {
    "prompt": "Review the attached documentation screenshot from a first-time reader’s perspective. Identify the three moments with the highest cognitive load, explain the evidence visible in the image, and suggest the smallest copy or layout change for each.",
    "model": "Multimodal model",
    "mode": "ask",
    "thinking": false,
    "context": "#homepage-screenshot, @documentation, docs/index.md",
    "caption": "A visual critique prompt grounded in a named image and source page."
  }
}
-->
#### A visual critique prompt grounded in a named image and source page.

> Review the attached documentation screenshot from a first-time reader’s perspective. Identify the three moments with the highest cognitive load, explain the evidence visible in the image, and suggest the smallest copy or layout change for each.

_Model: Multimodal model · Mode: ask · Thinking: off · Context: #homepage-screenshot, @documentation, docs/index.md_
<!-- /docspress:block -->

## Published behavior and accessibility

Copy copies the prompt itself. Model, mode, reasoning, and context remain visible metadata, while the containing section receives an AI-prompt label for assistive technology.

Write an explicit deliverable and scope, state what evidence the agent should inspect, and never include a secret in prompt text or context.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "tip",
    "title": "Make the prompt verifiable",
    "content": "\u003cp\u003eAsk for concrete checks, files, or outputs. A prompt that defines its evidence and completion condition is easier to reuse safely.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!TIP]
>
> **Make the prompt verifiable**
>
> Ask for concrete checks, files, or outputs. A prompt that defines its evidence and completion condition is easier to reuse safely.
<!-- /docspress:block -->
