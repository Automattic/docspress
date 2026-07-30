---
title: Keep documentation synchronized
---

Enable automatic synchronization only after the same pinned workflow succeeds as a dry run and as a real draft write.

## Choose a direction

| Direction | Guide | Mode |
| --- | --- | --- |
| Merged Markdown publishes WordPress Pages | [GitHub to WordPress](github-to-wordpress.md) | `publish`, or `reconcile` in a combined workflow |
| Gutenberg edits open a Markdown pull request | [WordPress to GitHub](wordpress-to-github.md) | `propose`, or `reconcile` in a combined workflow |

Use the combined `reconcile` workflow below when both authoring paths are valid for the same Page tree.

## Stage 1: manual dry run

Use `workflow_dispatch`, `status: draft`, `delete-mode: trash`, and `dry-run: true`. Review the Action summary and confirm that every planned create, update, and delete is expected.

## Stage 2: manual draft write

After explicit approval, change only `dry-run` to `false`. The workflow can now create, update, and trash WordPress Pages, so keep the trigger manual while you inspect:

- the Page hierarchy;
- titles and headings;
- Gutenberg block rendering;
- rewritten internal links;
- exact GitHub source actions;
- unexpected unmanaged conflicts.

## Stage 3: path-scoped synchronization

After the draft lifecycle succeeds, add the default-branch trigger:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": ".github/workflows/sync-docs.yml",
    "code": "on:\n  push:\n    branches: [main]\n    paths:\n      - \"docs/**/*.md\"\n      - \"docs/**/*.markdown\"\n      - \"docs/**/*.json\"\n      - \".github/workflows/sync-docs.yml\"\n  workflow_dispatch:\n\npermissions:\n  contents: read",
    "highlightedLines": "2-8,11-12",
    "showLineNumbers": true,
    "caption": "Only documentation and workflow changes start the ongoing sync."
  }
}
-->
**.github/workflows/sync-docs.yml — Only documentation and workflow changes start the ongoing sync.**

```yaml
on:
  push:
    branches: [main]
    paths:
      - "docs/**/*.md"
      - "docs/**/*.markdown"
      - "docs/**/*.json"
      - ".github/workflows/sync-docs.yml"
  workflow_dispatch:

permissions:
  contents: read
```
<!-- /docspress:block -->

Keep `status: draft` if WordPress remains an editorial review gate. Set `status: publish` only when merged Markdown should update public Pages automatically.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "danger",
    "title": "Automatic sync includes removals",
    "content": "\u003cp\u003eDeleting a managed Markdown file schedules its managed WordPress Page for Trash, or permanent deletion when \u003ccode\u003edelete-mode: force\u003c/code\u003e. Review that policy before enabling the push trigger.\u003c/p\u003e",
    "collapsible": true,
    "open": false
  }
}
-->
> [!CAUTION]
>
> **Automatic sync includes removals**
>
> Deleting a managed Markdown file schedules its managed WordPress Page for Trash, or permanent deletion when `delete-mode: force`. Review that policy before enabling the push trigger.
<!-- /docspress:block -->

## Stage 4: reconcile WordPress edits

After normal publishing is stable, use one workflow for push-based publishing and scheduled WordPress polling:

Before the first reverse-sync run, open [Settings → Actions → General for the repository](https://github.com/Automattic/docspress/settings/actions). Under **Workflow permissions**, enable **Allow GitHub Actions to create and approve pull requests**, then select **Save**. For another repository, open the same settings page under its owner and repository name.

![GitHub Actions workflow permissions with Allow GitHub Actions to create and approve pull requests enabled](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/github-actions/allow-actions-create-pull-requests.png "Enable the pull request setting and save it before running WordPress-to-GitHub synchronization.")

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": ".github/workflows/sync-docs.yml",
    "code": "on:\n  push:\n    branches: [main]\n    paths: [\"docs/**\", \".github/workflows/sync-docs.yml\"]\n  schedule:\n    - cron: \"3/5 * * * *\"\n  workflow_dispatch:\n\npermissions:\n  contents: write\n  pull-requests: write\n\nconcurrency:\n  group: docspress-sync\n  cancel-in-progress: false\n\njobs:\n  sync:\n    # Do not publish a merged WordPress proposal back to WordPress.\n    if: \u003e-\n      github.event_name != 'push' ||\n      !contains(\n        github.event.head_commit.message,\n        format('from {0}/docspress/wordpress-sync', github.repository_owner)\n      )\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@FULL_COMMIT_SHA\n      - uses: Automattic/docspress@FULL_COMMIT_SHA\n        with:\n          mode: reconcile\n          wordpress-site: example.wordpress.com\n          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}\n          docs-dir: docs\n          root-slug: docs\n          status: publish",
    "highlightedLines": "5-6,10-12,20-26,33",
    "showLineNumbers": true,
    "caption": "Pushes publish Markdown; schedules propose WordPress edits; merges from the managed proposal branch are skipped."
  }
}
-->
**.github/workflows/sync-docs.yml — Pushes publish Markdown; schedules propose WordPress edits; merges from the managed proposal branch are skipped.**

```yaml
on:
  push:
    branches: [main]
    paths: ["docs/**", ".github/workflows/sync-docs.yml"]
  schedule:
    - cron: "3/5 * * * *"
  workflow_dispatch:

permissions:
  contents: write
  pull-requests: write

concurrency:
  group: docspress-sync
  cancel-in-progress: false

jobs:
  sync:
    # Do not publish a merged WordPress proposal back to WordPress.
    if: >-
      github.event_name != 'push' ||
      !contains(
        github.event.head_commit.message,
        format('from {0}/docspress/wordpress-sync', github.repository_owner)
      )
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@FULL_COMMIT_SHA
      - uses: Automattic/docspress@FULL_COMMIT_SHA
        with:
          mode: reconcile
          wordpress-site: example.wordpress.com
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          root-slug: docs
          status: publish
```
<!-- /docspress:block -->

DocsPress compares the live Gutenberg tree with the tree generated from the current Markdown, then rewrites only source regions whose blocks changed. Unchanged frontmatter, spacing, code-fence languages, tables, and readable block envelopes stay byte-for-byte intact. Plain core blocks become ordinary Markdown. DocsPress and non-portable core blocks use semantic, lossless envelopes instead of exposing raw `wp:*` comments. If DocsPress cannot map the blocks safely, the run fails instead of regenerating the whole file.

The Action updates one action-owned branch and pull request instead of opening duplicates on every poll.

The job condition skips the `push` event created when GitHub merges the action-owned `docspress/wordpress-sync` branch. Scheduled and manual runs still reconcile normally. If you set a custom `pull-request-branch`, use the same branch name in the condition. As a fallback for workflows without the condition, the Action recognizes its configured branch in GitHub's merge commit and exits successfully without reading or writing WordPress.

While that pull request is open, `reconcile` leaves the WordPress-only Page untouched. After the pull request merges, the next run recognizes that both sides converge and refreshes the Page sentinel; GitHub-only changes to other Pages can continue publishing in the same run.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "Two-sided edits stop before writes",
    "content": "\u003cp\u003eIf GitHub and WordPress both changed the same managed Page since the sentinel baseline, reconcile mode reports a conflict and changes neither system. Resolve one side deliberately, then run the workflow again.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!WARNING]
>
> **Two-sided edits stop before writes**
>
> If GitHub and WordPress both changed the same managed Page since the sentinel baseline, reconcile mode reports a conflict and changes neither system. Resolve one side deliberately, then run the workflow again.
<!-- /docspress:block -->

## Observe each run

The Action exports counters for created, updated, deleted, unchanged, proposed, skipped, and conflict operations plus `summary-json` for downstream jobs.

The [first synchronization walkthrough](../publish-existing-docs/first-sync.md) shows the run overview and generated Sync Summary with tightly cropped screenshots. Use the same counters to review every automatic run.

## Pin and update intentionally

Use full verified commit SHAs for both `actions/checkout` and `Automattic/docspress`. When adopting a newer DocsPress revision:

1. inspect its `action.yml` inputs;
2. review source and bundled `dist/` changes;
3. update the SHA;
4. return to a manual dry run;
5. restore automatic synchronization after verification.
