---
title: Run your first synchronization
---

Follow these steps once to connect an existing Markdown tree to WordPress safely. The starting workflow is ready for both synchronization directions, but the first run only calculates changes; it does not write Pages, branches, or pull requests.

## 1. Add the WordPress secret

Create a repository secret named `WP_ACCESS_TOKEN`. The [authentication guide](./authentication.md) explains how to generate the token without exposing it. The same token reads editor content for WordPress-to-GitHub proposals and writes Pages for GitHub-to-WordPress publication.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Add the repository secret",
    "shell": "bash",
    "prompt": "$",
    "command": "gh secret set WP_ACCESS_TOKEN \u002d\u002drepo OWNER/REPOSITORY",
    "output": "✓ Set Actions secret WP_ACCESS_TOKEN"
  }
}
-->
#### Add the repository secret

```bash
$ gh secret set WP_ACCESS_TOKEN --repo OWNER/REPOSITORY
```

**Output**

```text
✓ Set Actions secret WP_ACCESS_TOKEN
```
<!-- /docspress:block -->

## 2. Allow pull request creation

Open [Settings → Actions → General for the repository](https://github.com/Automattic/docspress/settings/actions). Under **Workflow permissions**, enable **Allow GitHub Actions to create and approve pull requests**, then select **Save**. For another repository, use the equivalent page under its owner and repository name.

![GitHub Actions workflow permissions with Allow GitHub Actions to create and approve pull requests enabled](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/github-actions/allow-actions-create-pull-requests.png "Enable the pull request setting and save it before running WordPress-to-GitHub synchronization.")

The workflow's `GITHUB_TOKEN` creates the proposal branch and pull request. You do not need another stored GitHub token.

## 3. Add a safe two-way workflow

Create `.github/workflows/sync-docs.yml` with a manual trigger, `mode: reconcile`, draft status, a recoverable deletion policy, and dry-run mode:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/colorful-code",
  "attrs": {
    "language": "yaml",
    "filename": ".github/workflows/sync-docs.yml",
    "code": "name: Sync DocsPress documentation\n\non:\n  workflow_dispatch:\n\npermissions:\n  contents: write\n  pull-requests: write\n\nconcurrency:\n  group: docspress-sync\n  cancel-in-progress: false\n\njobs:\n  sync:\n    # Skip a push created by merging the managed WordPress proposal.\n    if: \u003e-\n      github.event_name != 'push' ||\n      !contains(\n        github.event.head_commit.message,\n        format('from {0}/docspress/wordpress-sync', github.repository_owner)\n      )\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@11d5960a326750d5838078e36cf38b85af677262\n      - uses: Automattic/docspress@14d318924a81fb95ce4d3aaa9c3b547bf76b7768\n        with:\n          mode: reconcile\n          wordpress-site: example.wordpress.com\n          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}\n          docs-dir: docs\n          root-slug: docs\n          root-title: Docs\n          create-h1: false\n          rewrite-links: true\n          edit-link: false\n          pull-request-branch: docspress/wordpress-sync\n          status: draft\n          delete-mode: trash\n          dry-run: true",
    "highlightedLines": "3-4,6-8,17-22,25-28,37-40",
    "showLineNumbers": true,
    "caption": "The workflow understands both directions, but starts manually and cannot change either system while dry-run is true."
  }
}
-->
**.github/workflows/sync-docs.yml — The workflow understands both directions, but starts manually and cannot change either system while dry-run is true.**

```yaml
name: Sync DocsPress documentation

on:
  workflow_dispatch:

permissions:
  contents: write
  pull-requests: write

concurrency:
  group: docspress-sync
  cancel-in-progress: false

jobs:
  sync:
    # Skip a push created by merging the managed WordPress proposal.
    if: >-
      github.event_name != 'push' ||
      !contains(
        github.event.head_commit.message,
        format('from {0}/docspress/wordpress-sync', github.repository_owner)
      )
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@11d5960a326750d5838078e36cf38b85af677262
      - uses: Automattic/docspress@14d318924a81fb95ce4d3aaa9c3b547bf76b7768
        with:
          mode: reconcile
          wordpress-site: example.wordpress.com
          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}
          docs-dir: docs
          root-slug: docs
          root-title: Docs
          create-h1: false
          rewrite-links: true
          edit-link: false
          pull-request-branch: docspress/wordpress-sync
          status: draft
          delete-mode: trash
          dry-run: true
```
<!-- /docspress:block -->

Replace `example.wordpress.com` with the site domain. Keep both Actions pinned to reviewed commit SHAs.

`mode: reconcile` chooses the safe operation for each managed Page:

| Detected state | Planned operation |
| --- | --- |
| Only Markdown changed | Update the corresponding WordPress Page. |
| Only Gutenberg content changed | Update Markdown on a managed branch and open a pull request. |
| Both sides changed differently | Report a conflict and write neither version. |
| Both sides already match | Leave the Page unchanged or refresh its synchronization marker. |

## 4. Start the workflow

Run the workflow from a trusted terminal:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/terminal-session",
  "attrs": {
    "title": "Run DocsPress",
    "shell": "bash",
    "prompt": "$",
    "command": "gh workflow run sync-docs.yml \u002d\u002drepo OWNER/REPOSITORY\ngh run watch \u002d\u002drepo OWNER/REPOSITORY \u002d\u002dexit-status",
    "output": "✓ sync completed successfully"
  }
}
-->
#### Run DocsPress

```bash
$ gh workflow run sync-docs.yml --repo OWNER/REPOSITORY
$ gh run watch --repo OWNER/REPOSITORY --exit-status
```

**Output**

```text
✓ sync completed successfully
```
<!-- /docspress:block -->

You can also open **Actions → Sync DocsPress documentation → Run workflow** in GitHub.

## 5. Confirm the run succeeded

The run overview should report **Success** and show a completed `sync` job. The screenshot below is cropped from a [real authenticated DocsPress run](https://github.com/Automattic/docspress/actions/runs/29799038783), so the important state remains readable.

![The GitHub Actions run overview cropped to the successful status, duration, revision, and sync job](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/github-actions/workflow-run-overview.jpg "Confirm Success, the expected revision, and a completed sync job before continuing.")

This production example performed a real synchronization. Your first run remains safe because its workflow uses `status: draft` and `dry-run: true`.

## 6. Read the Sync Summary

Scroll to **Docspress Dry Run Summary** on the same run overview. This is the quickest way to decide whether the planned synchronization is correct.

![The DocsPress Sync Summary cropped to the created, updated, deleted, unchanged, and conflict counters](http://fkadocs.atomicsites.blog/wp-content/themes/docspress/assets/images/github-actions/sync-summary.jpg "Review every counter before allowing WordPress or GitHub writes.")

| Counter | What to verify |
| --- | --- |
| Created | Every new Page is expected. |
| Updated | Only intentionally changed Pages appear. |
| Deleted | No unexpected Page is scheduled for Trash. |
| Unchanged | Existing matching Pages need no work. |
| Proposed files | Only expected WordPress editor changes would enter a pull request. |
| Conflicts | This must be zero before continuing. |

Stop if you see a conflict, unexpected deletion, authentication error, or the wrong Page hierarchy. Correct the source or workflow and run the dry run again.

## 7. Approve the first draft write

After the summary is correct, obtain approval for WordPress Page creation, updates, Trash operations, and GitHub proposal branches. Change only:

```yaml
dry-run: false
```

Keep `status: draft`, dispatch the workflow again, and inspect the generated WordPress Pages. Check the hierarchy, Gutenberg blocks, rewritten links, and any proposed Markdown pull request before publishing or merging anything.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/result",
  "attrs": {
    "status": "success",
    "title": "First two-way draft verified",
    "content": "\u003cp\u003eThe Page hierarchy, Gutenberg content, Markdown proposals, rewritten links, and managed boundaries are ready for editorial review.\u003c/p\u003e",
    "meta": "next: choose automatic triggers"
  }
}
-->
> [!TIP]
>
> **First two-way draft verified**
>
> The Page hierarchy, Gutenberg content, Markdown proposals, rewritten links, and managed boundaries are ready for editorial review.
>
> _next: choose automatic triggers_
<!-- /docspress:block -->

## 8. Enable the directions you need

Do not add automatic triggers or switch to `status: publish` until the draft lifecycle is approved.

- [GitHub to WordPress](../guides/github-to-wordpress.md) explains push-based publication from merged Markdown.
- [WordPress to GitHub](../guides/wordpress-to-github.md) explains scheduled Gutenberg polling and rolling pull requests.
- [Keep documentation synchronized](../guides/continuous-sync.md) combines both directions in one `reconcile` workflow.
