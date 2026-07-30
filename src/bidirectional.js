import { createReverseChanges } from "./reverse.js";
import { planReconciliation } from "./reconcile.js";
import { syncPages } from "./sync.js";

export async function syncBidirectional(options) {
  const {
    mode,
    desiredPages,
    client,
    githubClient,
    dryRun = false,
    deleteMode = "trash",
    rootSlug = "docs",
    versionsRegistry = null,
    cwd = process.cwd(),
    manifestFile = "",
    createH1 = false,
    githubRepository = "",
    githubRef = "main",
    githubServerUrl = "https://github.com",
    logger = console
  } = options;
  const existingPages = await client.listPages();
  const plan = planReconciliation({ desiredPages, existingPages });
  const wordpressChangeKeys = new Set(plan.wordpressChanges.map(({ desired }) => desired.key));
  let publishPreview = emptyResult(true);

  if (mode === "reconcile") {
    publishPreview = await syncPages({
      desiredPages,
      client,
      existingPages,
      dryRun: true,
      deleteMode,
      rootSlug,
      versionsRegistry,
      githubRepository,
      githubRef,
      githubServerUrl,
      skipUpdateKeys: wordpressChangeKeys,
      logger: { info() {} }
    });
  }

  const conflicts = mergeConflicts(plan.conflicts, publishPreview.conflictDetails);
  if (conflicts.length > 0) {
    return {
      ...emptyResult(dryRun),
      mode,
      conflicts: conflicts.length,
      conflictDetails: conflicts,
      operations: conflicts.map((conflict) => ({ action: "conflict", ...conflict })),
      classifications: plan.classifications,
      proposed: 0,
      proposedFiles: [],
      pullRequest: null
    };
  }

  let effectiveLatest = versionsRegistry?.latest || "";
  if (versionsRegistry) {
    const settings = await client.getSettings();
    const configured = new Set(versionsRegistry.versions.map(({ id }) => id));
    const override = String(settings.docspress_version_override || "");
    if (configured.has(override)) {
      effectiveLatest = override;
    }
  }
  const changes = await createReverseChanges({
    cwd,
    pages: plan.wordpressChanges,
    desiredPages,
    manifestFile,
    createH1,
    effectiveLatest
  });
  const proposedOperations = changes.map((change) => ({
    action: "propose",
    path: change.path,
    version: change.versionId || ""
  }));

  if (dryRun) {
    const preview = mode === "reconcile" ? publishPreview : emptyResult(true);
    return {
      ...preview,
      dryRun: true,
      mode,
      classifications: plan.classifications,
      proposed: changes.length,
      proposedFiles: changes.map((change) => change.path),
      proposedFileDetails: changes.map(proposedFileDetail),
      pullRequest: null,
      operations: [...preview.operations, ...proposedOperations]
    };
  }

  const pullRequest = await githubClient.syncChanges(changes);
  let wordpressResult;
  if (mode === "reconcile") {
    wordpressResult = await syncPages({
      desiredPages,
      client,
      existingPages,
      dryRun: false,
      deleteMode,
      rootSlug,
      versionsRegistry,
      githubRepository,
      githubRef,
      githubServerUrl,
      skipUpdateKeys: wordpressChangeKeys,
      logger
    });
  } else if (plan.refreshPages.length > 0) {
    wordpressResult = await syncPages({
      desiredPages: plan.refreshPages,
      client,
      existingPages,
      dryRun: false,
      deleteMode,
      rootSlug,
      versionsRegistry,
      githubRepository,
      githubRef,
      githubServerUrl,
      allowDeletions: false,
      logger
    });
  } else {
    wordpressResult = emptyResult(false);
  }

  return {
    ...wordpressResult,
    mode,
    classifications: plan.classifications,
    proposed: changes.length,
    proposedFiles: changes.map((change) => change.path),
    proposedFileDetails: changes.map(proposedFileDetail),
    pullRequest,
    operations: [...wordpressResult.operations, ...proposedOperations]
  };
}

function proposedFileDetail(change) {
  return {
    path: change.path,
    version: change.versionId || "",
    logicalRoute: change.logicalRoute || "",
    effectiveLatest: change.effectiveLatest || ""
  };
}

function emptyResult(dryRun) {
  return {
    dryRun,
    created: 0,
    updated: 0,
    deleted: 0,
    unchanged: 0,
    conflicts: 0,
    conflictDetails: [],
    operations: []
  };
}

function mergeConflicts(...groups) {
  const byKeyAndReason = new Map();
  for (const conflict of groups.flat()) {
    byKeyAndReason.set(`${conflict.key}:${conflict.reason}`, conflict);
  }
  return Array.from(byKeyAndReason.values());
}
