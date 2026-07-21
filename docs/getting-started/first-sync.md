---
title: Run your first synchronization
---

Follow these steps once to connect a repository to WordPress safely. The first run only calculates changes; it does not write any Pages.

## 1. Add the WordPress secret

Create a repository secret named `WP_ACCESS_TOKEN`. The [authentication guide](./authentication.md) explains how to generate the token without exposing it.

<!-- wp:docspress/terminal-session {"title":"Add the repository secret","shell":"bash","prompt":"$","command":"gh secret set WP_ACCESS_TOKEN --repo OWNER/REPOSITORY","output":"✓ Set Actions secret WP_ACCESS_TOKEN"} /-->

## 2. Add a safe manual workflow

Create `.github/workflows/sync-docs.yml` with a manual trigger, draft status, recoverable deletion policy, and dry-run mode:

<!-- wp:docspress/colorful-code {"language":"yaml","filename":".github/workflows/sync-docs.yml","code":"name: Sync DocsPress documentation\n\non:\n  workflow_dispatch:\n\npermissions:\n  contents: read\n\njobs:\n  sync:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@11d5960a326750d5838078e36cf38b85af677262\n      - uses: Automattic/docspress@fc23da3d8575b18bbc81a81bd8c6b915dbdfcdd9\n        with:\n          wordpress-site: example.wordpress.com\n          wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}\n          docs-dir: docs\n          root-slug: docs\n          root-title: Docs\n          create-h1: false\n          rewrite-links: true\n          edit-link: false\n          status: draft\n          delete-mode: trash\n          dry-run: true","highlightedLines":"3-4,13-14,24-26","showLineNumbers":true,"caption":"The workflow starts manually and cannot change WordPress while dry-run is true."} /-->

Replace `example.wordpress.com` with the site domain. Keep both Actions pinned to reviewed commit SHAs.

## 3. Start the workflow

Run the workflow from a trusted terminal:

<!-- wp:docspress/terminal-session {"title":"Run DocsPress","shell":"bash","prompt":"$","command":"gh workflow run sync-docs.yml --repo OWNER/REPOSITORY\ngh run watch --repo OWNER/REPOSITORY --exit-status","output":"✓ sync completed successfully"} /-->

You can also open **Actions → Sync DocsPress documentation → Run workflow** in GitHub.

## 4. Confirm the run succeeded

The run overview should report **Success** and show a completed `sync` job. The screenshot below is cropped from a [real authenticated DocsPress run](https://github.com/Automattic/docspress/actions/runs/29799038783), so the important state remains readable.

![The GitHub Actions run overview cropped to the successful status, duration, revision, and sync job](https://raw.githubusercontent.com/Automattic/docspress/main/theme/assets/images/github-actions/workflow-run-overview.jpg "Confirm Success, the expected revision, and a completed sync job before continuing.")

This production example performed a real synchronization. Your first run remains safe because its workflow uses `status: draft` and `dry-run: true`.

## 5. Read the Sync Summary

Scroll to **Docspress Sync Summary** on the same run overview. This is the quickest way to decide whether the planned synchronization is correct.

![The DocsPress Sync Summary cropped to the created, updated, deleted, unchanged, and conflict counters](https://raw.githubusercontent.com/Automattic/docspress/main/theme/assets/images/github-actions/sync-summary.jpg "Review all five counters before allowing WordPress writes.")

| Counter | What to verify |
| --- | --- |
| Created | Every new Page is expected. |
| Updated | Only intentionally changed Pages appear. |
| Deleted | No unexpected Page is scheduled for Trash. |
| Unchanged | Existing matching Pages need no work. |
| Conflicts | This must be zero before continuing. |

Stop if you see a conflict, unexpected deletion, authentication error, or the wrong Page hierarchy. Correct the source or workflow and run the dry run again.

## 6. Approve the first draft write

After the summary is correct, obtain approval for WordPress Page creation, updates, and Trash operations. Change only:

```yaml
dry-run: false
```

Keep `status: draft`, dispatch the workflow again, and inspect the generated WordPress Pages. Check the hierarchy, Gutenberg blocks, and rewritten links before publishing anything.

<!-- wp:docspress/result {"status":"success","title":"First draft tree verified","content":"<p>The repository hierarchy, Gutenberg content, rewritten links, and managed-page boundaries are ready for editorial review.</p>","meta":"next: continuous sync"} /-->

## 7. Enable ongoing synchronization

Do not add a push trigger or switch to `status: publish` until the draft tree is approved. Then follow the [continuous synchronization guide](../guides/continuous-sync.md).
