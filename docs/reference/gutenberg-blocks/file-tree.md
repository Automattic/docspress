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

<!-- wp:docspress/fields {"title":"File Tree attributes","description":"Root, hierarchy, and disclosure behavior accepted by <code>docspress/file-tree</code>.","fields":[{"name":"root","type":"string","required":false,"defaultValue":"project/","description":"<p>Plain-text label above the tree.</p>","values":"","deprecated":false},{"name":"tree","type":"string","required":true,"defaultValue":"Starter project tree","description":"<p>Indentation-aware hierarchy with two spaces per depth and trailing slashes for folders.</p>","values":"Maximum depth 12","deprecated":false},{"name":"caption","type":"string","required":false,"defaultValue":"","description":"<p>Optional formatted explanation.</p>","values":"","deprecated":false},{"name":"collapsible","type":"boolean","required":false,"defaultValue":"true","description":"<p>Uses native folder disclosures when a folder has children.</p>","values":"true, false","deprecated":false},{"name":"open","type":"boolean","required":false,"defaultValue":"true","description":"<p>Initial state of collapsible folders.</p>","values":"true, false","deprecated":false}],"searchable":false,"compact":true} /-->

## Creative examples

### Focused documentation tree

<!-- wp:docspress/file-tree {"root":"repository/","tree":"docs/\n  index.md\n  guides/\n    continuous-sync.md\npackage.json","caption":"A relevant source tree.","collapsible":true,"open":true} /-->

### Collapsed monorepo map

<!-- wp:docspress/file-tree {"root":"workspace/","tree":"apps/\n  docs-site/\n    package.json\n  admin/\n    package.json\npackages/\n  blocks/\n    src/\n  sync-client/\n    src/\npnpm-workspace.yaml","caption":"Start collapsed when the hierarchy matters more than individual files.","collapsible":true,"open":false} /-->

### Generated plugin package

<!-- wp:docspress/file-tree {"root":"docspress-blocks/","tree":"blocks/\n  api-request/\n    block.php\n    editor.js\n    style.css\n    view.js\nassets/\n  code.css\nincludes/\n  code-surface.php\ndocspress-blocks.php\nreadme.txt","caption":"A non-collapsible release package keeps every relevant artifact visible.","collapsible":false,"open":true} /-->

## Published behavior and accessibility

The tree uses nested semantic lists. Collapsible folders with children use native disclosure controls and honor the authored initial state. The figure receives an accessible file-tree label.

Keep the sample shallow and task-specific. If readers need to copy file contents, follow the tree with [Colorful Code](colorful-code.md) examples rather than placing content in the hierarchy.
