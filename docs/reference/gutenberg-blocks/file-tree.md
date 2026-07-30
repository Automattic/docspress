---
title: File Tree
sidebar_position: 130
---

Use `docspress/file-tree` to show the relevant part of a repository or directory hierarchy. Two-space indentation defines depth, and a trailing `/` identifies a folder.

## When to use it

Choose File Tree when location and nesting are part of the lesson. Use [Fields / Schema](fields.md) for structured properties and [Diagram](diagram.md) for relationships among systems. Omit files that do not help the current task.

## Write tree source

Use two spaces for each depth:

```text
docs/
  index.md
  guides/
    publishing.md
package.json
```

Tabs normalize to two spaces. Nesting is capped at 12 levels. A label ending in `/` is a folder; other labels are files.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "File Tree attributes",
    "description": "Root, hierarchy, and disclosure behavior accepted by \u003ccode\u003edocspress/file-tree\u003c/code\u003e.",
    "fields": [
      {
        "name": "root",
        "type": "string",
        "required": false,
        "defaultValue": "project/",
        "description": "\u003cp\u003ePlain-text label above the tree.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "tree",
        "type": "string",
        "required": true,
        "defaultValue": "Starter project tree",
        "description": "\u003cp\u003eIndentation-aware hierarchy with two spaces per depth and trailing slashes for folders.\u003c/p\u003e",
        "values": "Maximum depth 12",
        "deprecated": false
      },
      {
        "name": "caption",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional formatted explanation.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "collapsible",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eUses native folder disclosures when a folder has children.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "open",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eInitial state of collapsible folders.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### File Tree attributes

Root, hierarchy, and disclosure behavior accepted by <code>docspress/file-tree</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `root` | string | No | project/ | <p>Plain-text label above the tree.</p> |
| `tree` | string | Yes | Starter project tree | <p>Indentation-aware hierarchy with two spaces per depth and trailing slashes for folders.</p> |
| `caption` | string | No |  | <p>Optional formatted explanation.</p> |
| `collapsible` | boolean | No | true | <p>Uses native folder disclosures when a folder has children.</p> |
| `open` | boolean | No | true | <p>Initial state of collapsible folders.</p> |
<!-- /docspress:block -->

## Creative examples

### Focused documentation tree

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "repository/",
    "tree": "docs/\n  index.md\n  guides/\n    continuous-sync.md\npackage.json",
    "caption": "A relevant source tree.",
    "collapsible": true,
    "open": true
  }
}
-->
#### repository/

```text
docs/
  index.md
  guides/
    continuous-sync.md
package.json
```

_A relevant source tree._
<!-- /docspress:block -->

### Collapsed monorepo map

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "workspace/",
    "tree": "apps/\n  docs-site/\n    package.json\n  admin/\n    package.json\npackages/\n  blocks/\n    src/\n  sync-client/\n    src/\npnpm-workspace.yaml",
    "caption": "Start collapsed when the hierarchy matters more than individual files.",
    "collapsible": true,
    "open": false
  }
}
-->
#### workspace/

```text
apps/
  docs-site/
    package.json
  admin/
    package.json
packages/
  blocks/
    src/
  sync-client/
    src/
pnpm-workspace.yaml
```

_Start collapsed when the hierarchy matters more than individual files._
<!-- /docspress:block -->

### Generated plugin package

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "docspress-blocks/",
    "tree": "blocks/\n  api-request/\n    block.php\n    editor.js\n    style.css\n    view.js\nassets/\n  code.css\nincludes/\n  code-surface.php\ndocspress-blocks.php\nreadme.txt",
    "caption": "A non-collapsible release package keeps every relevant artifact visible.",
    "collapsible": false,
    "open": true
  }
}
-->
#### docspress-blocks/

```text
blocks/
  api-request/
    block.php
    editor.js
    style.css
    view.js
assets/
  code.css
includes/
  code-surface.php
docspress-blocks.php
readme.txt
```

_A non-collapsible release package keeps every relevant artifact visible._
<!-- /docspress:block -->

## Published behavior and accessibility

The tree uses nested semantic lists. Collapsible folders with children use native disclosure controls and honor the authored initial state. The figure receives an accessible file-tree label.

Keep the sample shallow and task-specific. If readers need to copy file contents, follow the tree with [Colorful Code](colorful-code.md) examples rather than placing content in the hierarchy.
