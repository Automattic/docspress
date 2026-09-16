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
- [Autodocs Publish WordPress theme](theme.md)
- [Autodocs Publish Gutenberg blocks](gutenberg-blocks/index.md)
- [Kitchen Sink](kitchen-sink.md)

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/file-tree",
  "attrs": {
    "root": "Automattic/autodocs-publish/",
    "tree": "action.yml\nbin/\n  autodocs-publish.mjs\nscripts/\n  create-wordpress-token.mjs\nsrc/\n  docs.js\n  markdown.js\n  sync.js\n  wordpress.js\nplugins/\n  autodocs-publish-blocks/\ntheme/",
    "caption": "The public contract is split between Action metadata, the token CLI, synchronization source, blocks plugin, and theme."
  }
}
-->
#### Automattic/autodocs-publish/

```text
action.yml
bin/
  autodocs-publish.mjs
scripts/
  create-wordpress-token.mjs
src/
  docs.js
  markdown.js
  sync.js
  wordpress.js
plugins/
  autodocs-publish-blocks/
theme/
```

_The public contract is split between Action metadata, the token CLI, synchronization source, blocks plugin, and theme._
<!-- /autodocs-publish:block -->
