---
title: Getting started
sidebar_position: 2
---

## Publish flow

1. Keep the documentation in the repository.
2. Run DocsPress from GitHub Actions.
3. Review the generated WordPress Pages.

```yaml
- uses: Automattic/docspress@main
  with:
    docs-dir: docs
    status: draft
    dry-run: true
```

Continue with the [configuration reference](reference/configuration.md).
