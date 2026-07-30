---
title: Reference
sidebar_position: 50
sidebar_collapsed: true
---

Use these pages when configuring the Action, diagnosing reconciliation, installing the presentation layer, or emitting custom blocks.

## Reference pages

- [GitHub Action inputs and outputs](action-inputs.md)
- [Token helper CLI](cli.md)
- [Synchronization and REST API](sync-and-rest-api.md)
- [DocsPress WordPress theme](theme.md)
- [DocsPress Gutenberg blocks](gutenberg-blocks/index.md)
- [Kitchen Sink](kitchen-sink.md)

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/file-tree",
  "attrs": {
    "root": "Automattic/docspress/",
    "tree": "action.yml\nbin/\n  docspress.mjs\nscripts/\n  create-wordpress-token.mjs\nsrc/\n  docs.js\n  markdown.js\n  sync.js\n  wordpress.js\nplugins/\n  docspress-blocks/\ntheme/",
    "caption": "The public contract is split between Action metadata, the token CLI, synchronization source, blocks plugin, and theme."
  }
}
-->
#### Automattic/docspress/

```text
action.yml
bin/
  docspress.mjs
scripts/
  create-wordpress-token.mjs
src/
  docs.js
  markdown.js
  sync.js
  wordpress.js
plugins/
  docspress-blocks/
theme/
```

_The public contract is split between Action metadata, the token CLI, synchronization source, blocks plugin, and theme._
<!-- /docspress:block -->
