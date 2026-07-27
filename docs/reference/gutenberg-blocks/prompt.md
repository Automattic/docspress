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

<!-- wp:docspress/fields {"title":"Prompt attributes","description":"Instruction metadata and context accepted by <code>docspress/prompt</code>.","fields":[{"name":"prompt","type":"string","required":true,"defaultValue":"DocsPress setup review prompt","description":"<p>The complete copyable instruction.</p>","values":"","deprecated":false},{"name":"model","type":"string","required":false,"defaultValue":"GPT-5","description":"<p>Display label for the intended model or agent.</p>","values":"","deprecated":false},{"name":"mode","type":"enum","required":false,"defaultValue":"code","description":"<p>Interaction mode shown with the example. Invalid values normalize to chat when rendered.</p>","values":"chat, code, ask, plan","deprecated":false},{"name":"thinking","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows that deliberate reasoning is expected.</p>","values":"true, false","deprecated":false},{"name":"context","type":"string","required":false,"defaultValue":"$docspress-install, @repository, src/sync.js, docs/","description":"<p>Comma-separated skill, mention, image, URL, and file references; at most 12 items.</p>","values":"Up to 12 items","deprecated":false},{"name":"caption","type":"string","required":false,"defaultValue":"Prompt example","description":"<p>Formatted explanation below the prompt.</p>","values":"","deprecated":false}],"searchable":true,"compact":true} /-->

## Creative examples

### Find undocumented behavior

<!-- wp:docspress/prompt {"prompt":"Use $generate-docs-from-source to inspect the public exports and identify undocumented behavior.","model":"Coding agent","mode":"code","thinking":true,"context":"$generate-docs-from-source, @repository, src/, test/","caption":"Documentation coverage prompt"} /-->

### Plan an incident-runbook rehearsal

<!-- wp:docspress/prompt {"prompt":"Plan a 30-minute rehearsal for the documentation publishing incident runbook. Include roles, two injected failures, observable success criteria, and a short retrospective template. Do not change production systems.","model":"Reasoning model","mode":"plan","thinking":true,"context":"@operations, docs/runbooks/publishing.md, test/reconcile.test.js","caption":"A bounded planning prompt with an explicit safety constraint."} /-->

### Review a screenshot for reader friction

<!-- wp:docspress/prompt {"prompt":"Review the attached documentation screenshot from a first-time reader’s perspective. Identify the three moments with the highest cognitive load, explain the evidence visible in the image, and suggest the smallest copy or layout change for each.","model":"Multimodal model","mode":"ask","thinking":false,"context":"#homepage-screenshot, @documentation, docs/index.md","caption":"A visual critique prompt grounded in a named image and source page."} /-->

## Published behavior and accessibility

Copy copies the prompt itself. Model, mode, reasoning, and context remain visible metadata, while the containing section receives an AI-prompt label for assistive technology.

Write an explicit deliverable and scope, state what evidence the agent should inspect, and never include a secret in prompt text or context.

<!-- wp:docspress/callout {"tone":"tip","title":"Make the prompt verifiable","content":"<p>Ask for concrete checks, files, or outputs. A prompt that defines its evidence and completion condition is easier to reuse safely.</p>","collapsible":false,"open":true} /-->
