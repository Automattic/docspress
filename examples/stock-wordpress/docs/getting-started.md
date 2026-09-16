---
title: Getting started
sidebar_position: 2
---

## Publish flow

1. Keep the documentation in the repository.
2. Run Autodocs Publish from GitHub Actions.
3. Review the generated WordPress Pages.

```yaml
- uses: Automattic/autodocs-publish@main
  with:
    docs-dir: docs
    status: draft
    dry-run: true
```

Continue with the [configuration reference](reference/configuration.md).
