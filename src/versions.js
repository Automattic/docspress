import fs from "node:fs/promises";
import path from "node:path";
import { slugify, toPosixPath } from "./utils.js";

const SOURCE_TYPES = new Set(["root", "directory", "suffix", "manifest"]);

export async function readVersionsRegistry(options = {}) {
  const cwd = options.cwd || process.cwd();
  const versionsFile = toPosixPath(options.versionsFile || "");
  if (!versionsFile) {
    return null;
  }

  const absolutePath = path.resolve(cwd, versionsFile);
  const relativePath = path.relative(cwd, absolutePath);
  if (!relativePath || relativePath.startsWith("..") || path.isAbsolute(relativePath)) {
    throw new Error(`The Docspress versions file must stay inside the checked-out repository: ${versionsFile}`);
  }

  const data = JSON.parse(await fs.readFile(absolutePath, "utf8"));
  if (!data || typeof data !== "object" || Array.isArray(data)) {
    throw new Error(`Docspress versions file must contain an object: ${versionsFile}`);
  }
  if (!Array.isArray(data.versions) || data.versions.length === 0) {
    throw new Error(`Docspress versions file must contain a non-empty versions array: ${versionsFile}`);
  }

  const versions = data.versions.map((entry, index) => normalizeVersionEntry(entry, index));
  const byId = new Map();
  let rootVersion = null;

  for (const version of versions) {
    if (byId.has(version.id)) {
      throw new Error(`Docspress versions file contains duplicate version id: ${version.id}`);
    }
    byId.set(version.id, version);

    if (version.source.type === "root") {
      if (rootVersion) {
        throw new Error(`Docspress versions file may define only one root source: ${rootVersion.id} and ${version.id}`);
      }
      rootVersion = version;
    }
  }

  const latest = normalizeVersionId(data.latest);
  if (!latest || !byId.has(latest)) {
    throw new Error(`Docspress versions file latest must reference a configured version: ${data.latest || "(missing)"}`);
  }
  if (rootVersion && rootVersion.id !== latest) {
    throw new Error(`The root documentation source must be the configured latest version (${latest}), not ${rootVersion.id}.`);
  }

  for (const [order, version] of versions.entries()) {
    version.order = order;
    version.latest = version.id === latest;
  }

  return {
    file: versionsFile,
    latest,
    versions
  };
}

function normalizeVersionEntry(entry, index) {
  if (!entry || typeof entry !== "object" || Array.isArray(entry)) {
    throw new Error(`Docspress version entry ${index + 1} must be an object.`);
  }

  const rawId = String(entry.id || "").trim();
  const id = normalizeVersionId(rawId);
  if (!id || id !== rawId.toLowerCase()) {
    throw new Error(`Docspress version entry ${index + 1} has an invalid id: ${rawId || "(missing)"}`);
  }

  const label = String(entry.label || rawId).trim();
  if (!label) {
    throw new Error(`Docspress version ${id} must have a non-empty label.`);
  }

  const source = normalizeSource(entry.source, id);
  const redirectsFile = normalizeRepositoryFile(entry.redirects || entry.redirectsFile || "", `redirects for ${id}`);

  return {
    id,
    label,
    source,
    redirectsFile,
    order: index,
    latest: false
  };
}

function normalizeSource(source, versionId) {
  if (!source || typeof source !== "object" || Array.isArray(source)) {
    throw new Error(`Docspress version ${versionId} must define a source object.`);
  }

  const type = String(source.type || "").trim().toLowerCase();
  if (!SOURCE_TYPES.has(type)) {
    throw new Error(`Docspress version ${versionId} has an unsupported source type: ${type || "(missing)"}`);
  }

  if (type === "root") {
    return { type };
  }

  if (type === "directory") {
    const directory = normalizeRelativePath(source.path, `directory source for ${versionId}`);
    if (!directory || directory === ".") {
      throw new Error(`Docspress version ${versionId} directory source must name a directory below docs-dir.`);
    }
    return { type, path: directory };
  }

  if (type === "suffix") {
    const suffix = String(source.suffix || "").trim();
    if (!/^\.[a-z0-9][a-z0-9._-]*$/i.test(suffix)) {
      throw new Error(`Docspress version ${versionId} suffix must look like .v1.`);
    }
    return { type, suffix };
  }

  const manifestPath = normalizeRepositoryFile(source.path, `manifest source for ${versionId}`);
  if (!manifestPath) {
    throw new Error(`Docspress version ${versionId} manifest source must provide a repository-relative path.`);
  }
  return { type, path: manifestPath };
}

function normalizeVersionId(value) {
  const raw = String(value || "").trim().toLowerCase();
  return slugify(raw, "");
}

function normalizeRepositoryFile(value, label) {
  if (!value) {
    return "";
  }
  return normalizeRelativePath(value, label);
}

function normalizeRelativePath(value, label) {
  const raw = toPosixPath(String(value || "").trim());
  if (
    !raw
    || raw.startsWith("/")
    || raw.includes(":")
    || raw.includes("\0")
    || raw.split("/").some((segment) => !segment || segment === "." || segment === "..")
  ) {
    throw new Error(`Invalid repository-relative ${label}: ${raw || "(empty)"}`);
  }
  return raw.replace(/\/+$/, "");
}
