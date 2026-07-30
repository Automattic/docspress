---
title: Terminal Session
sidebar_position: 100
---

Use `docspress/terminal-session` for one copyable command with optional read-only output. It looks like a terminal transcript but never executes code.

## When to use it

Choose Terminal Session when the command and its expected output belong together. Use [Colorful Code](colorful-code.md) for scripts or configuration files and [Code Tabs](code-tabs.md) for equivalent commands. Use [API Request / Response](api-request.md) for an HTTP exchange.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Terminal Session attributes",
    "description": "Terminal labels, command, and output accepted by \u003ccode\u003edocspress/terminal-session\u003c/code\u003e.",
    "fields": [
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Terminal",
        "description": "\u003cp\u003ePlain-text session heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "shell",
        "type": "string",
        "required": false,
        "defaultValue": "bash",
        "description": "\u003cp\u003eShell or runtime label.\u003c/p\u003e",
        "values": "bash, zsh, PowerShell, node",
        "deprecated": false
      },
      {
        "name": "prompt",
        "type": "string",
        "required": false,
        "defaultValue": "$",
        "description": "\u003cp\u003ePrompt displayed before the command.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "command",
        "type": "string",
        "required": true,
        "defaultValue": "npx docspress publish ./docs",
        "description": "\u003cp\u003ePlain command copied by the reader.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "output",
        "type": "string",
        "required": false,
        "defaultValue": "Example publish result",
        "description": "\u003cp\u003eOptional read-only expected output.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Terminal Session attributes

Terminal labels, command, and output accepted by <code>docspress/terminal-session</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `title` | string | No | Terminal | <p>Plain-text session heading.</p> |
| `shell` | string | No | bash | <p>Shell or runtime label.</p> |
| `prompt` | string | No | $ | <p>Prompt displayed before the command.</p> |
| `command` | string | Yes | npx docspress publish ./docs | <p>Plain command copied by the reader.</p> |
| `output` | string | No | Example publish result | <p>Optional read-only expected output.</p> |
<!-- /docspress:block -->

## Creative examples

### Run the package checks

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Run the package checks",
    "shell": "bash",
    "prompt": "$",
    "command": "npm run package",
    "output": "Tests passed\nBundle rebuilt"
  }
}
-->
#### Run the package checks

```bash
$ npm run package
```

**Output**

```text
Tests passed
Bundle rebuilt
```
<!-- /docspress:block -->

### Preview a safe reconciliation

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Preview WordPress changes",
    "shell": "bash",
    "prompt": "$",
    "command": "npx docspress publish ./docs \u002d\u002ddry-run",
    "output": "Found 42 desired pages\nWould create 0\nWould update 3\nWould trash 1\nConflicts 0"
  }
}
-->
#### Preview WordPress changes

```bash
$ npx docspress publish ./docs --dry-run
```

**Output**

```text
Found 42 desired pages
Would create 0
Would update 3
Would trash 1
Conflicts 0
```
<!-- /docspress:block -->

### Inspect one response in PowerShell

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Read the public API index",
    "shell": "PowerShell",
    "prompt": "PS\u003e",
    "command": "Invoke-RestMethod https://example.test/wp-json/ | Select-Object name, url",
    "output": "name       url\n\u002d\u002d\u002d\u002d       \u002d\u002d-\nDocsPress  https://example.test"
  }
}
-->
#### Read the public API index

```PowerShell
PS> Invoke-RestMethod https://example.test/wp-json/ | Select-Object name, url
```

**Output**

```text
name       url
----       ---
DocsPress  https://example.test
```
<!-- /docspress:block -->

## Published behavior and accessibility

Copy copies only `command`, never the prompt or output. The output has an accessible command-output label, and long lines remain scrollable. No shell process or browser evaluation is attached to the block.

Show only the output needed to recognize success. Do not include volatile timestamps, personal paths, secrets, or pages of logs.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "note",
    "title": "This block does not run commands",
    "content": "\u003cp\u003eTerminal Session is a documentation surface. Readers copy its command and run it in their own trusted terminal.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!NOTE]
>
> **This block does not run commands**
>
> Terminal Session is a documentation surface. Readers copy its command and run it in their own trusted terminal.
<!-- /docspress:block -->
