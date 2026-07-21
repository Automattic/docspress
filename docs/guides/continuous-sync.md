---
title: Keep documentation synchronized
---

Enable automatic synchronization only after the same pinned workflow succeeds as a dry run and as a real draft write.

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

<!-- wp:docspress/colorful-code {"language":"yaml","filename":".github/workflows/sync-docs.yml","code":"on:\n  push:\n    branches: [main]\n    paths:\n      - \"docs/**/*.md\"\n      - \"docs/**/*.markdown\"\n      - \"docs/**/*.json\"\n      - \".github/workflows/sync-docs.yml\"\n  workflow_dispatch:\n\npermissions:\n  contents: read","highlightedLines":"2-8,11-12","showLineNumbers":true,"caption":"Only documentation and workflow changes start the ongoing sync."} /-->

Keep `status: draft` if WordPress remains an editorial review gate. Set `status: publish` only when merged Markdown should update public Pages automatically.

<!-- wp:docspress/callout {"tone":"danger","title":"Automatic sync includes removals","content":"<p>Deleting a managed Markdown file schedules its managed WordPress Page for Trash, or permanent deletion when <code>delete-mode: force</code>. Review that policy before enabling the push trigger.</p>","collapsible":true,"open":false} /-->

## Observe each run

The Action exports counters for created, updated, deleted, unchanged, and conflict operations plus `summary-json` for downstream jobs.

Open the workflow run summary before inspecting individual steps. It identifies the triggering commit and branch, reports the overall status and duration, and shows whether the `sync` job completed. This [real DocsPress synchronization run](https://github.com/Automattic/docspress/actions/runs/29798422167) completed successfully in 15 seconds.

![A successful DocsPress workflow run showing the status, duration, and sync job](https://raw.githubusercontent.com/Automattic/docspress/main/theme/assets/images/github-actions/workflow-run-overview.jpg "The tightly cropped run overview keeps the successful status, duration, triggering revision, and sync job graph in view.")

Open the `sync` job when the run fails or when you need to verify which Action revision executed. The completed-step list separates checkout, DocsPress synchronization, post-job cleanup, and final completion.

![The completed sync job showing the checkout, DocsPress, cleanup, and completion steps](https://raw.githubusercontent.com/Automattic/docspress/main/theme/assets/images/github-actions/sync-job-steps.jpg "The job crop removes unrelated GitHub navigation and focuses on the steps that must complete successfully.")

The DocsPress step found 21 desired Pages in this example. It created the new theme-customization guide, updated three existing Pages, and left 17 Pages unchanged. A successful run with no deletion or conflict messages confirmed that the public Page tree matched the repository.

<!-- wp:docspress/result {"status":"success","title":"Example synchronization completed","content":"<p>The run created 1 Page, updated 3 Pages, left 17 unchanged, and reported no deletions or conflicts.</p>","meta":"21 desired Pages · Success in 15 seconds"} /-->

## Pin and update intentionally

Use full verified commit SHAs for both `actions/checkout` and `Automattic/docspress`. When adopting a newer DocsPress revision:

1. inspect its `action.yml` inputs;
2. review source and bundled `dist/` changes;
3. update the SHA;
4. return to a manual dry run;
5. restore automatic synchronization after verification.
