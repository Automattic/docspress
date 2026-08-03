import fs from "node:fs/promises";
import path from "node:path";
import matter from "gray-matter";
import { toPosixPath } from "./utils.js";

const SIDEBAR_ID_PATTERN = /^[a-z0-9][a-z0-9_-]*$/;
const ROUTE_SEGMENT_PATTERN = /^[a-z0-9][a-z0-9-]*$/;
const SIDEBARS_FILE_EXTENSIONS = new Set([".json", ".yaml", ".yml"]);

export async function readSidebarsRegistry(options = {}) {
  const cwd = options.cwd || process.cwd();
  const sidebarsFile = toPosixPath(options.sidebarsFile || "");
  if (!sidebarsFile) {
    return null;
  }

  const extension = path.extname(sidebarsFile).toLowerCase();
  if (!SIDEBARS_FILE_EXTENSIONS.has(extension)) {
    throw new Error(`Docspress sidebars file must use .json, .yaml, or .yml: ${sidebarsFile}`);
  }

  const absolutePath = path.resolve(cwd, sidebarsFile);
  const relativePath = path.relative(cwd, absolutePath);
  if (!relativePath || relativePath.startsWith("..") || path.isAbsolute(relativePath)) {
    throw new Error(`The Docspress sidebars file must stay inside the checked-out repository: ${sidebarsFile}`);
  }

  const [realCwd, realFile] = await Promise.all([
    fs.realpath(cwd),
    fs.realpath(absolutePath)
  ]);
  const realRelativePath = path.relative(realCwd, realFile);
  if (!realRelativePath || realRelativePath.startsWith("..") || path.isAbsolute(realRelativePath)) {
    throw new Error(`The Docspress sidebars file must stay inside the checked-out repository: ${sidebarsFile}`);
  }

  const source = await fs.readFile(realFile, "utf8");
  let data;
  try {
    data = extension === ".json"
      ? JSON.parse(source)
      : matter.engines.yaml.parse(source);
  } catch (error) {
    throw new Error(`Could not parse Docspress sidebars file ${sidebarsFile}: ${error.message}`);
  }

  if (!data || typeof data !== "object" || Array.isArray(data)) {
    throw new Error(`Docspress sidebars file must contain an object: ${sidebarsFile}`);
  }
  if (data.version !== undefined && data.version !== 1) {
    throw new Error(`Docspress sidebars file has an unsupported version: ${data.version}`);
  }
  if (!data.sidebars || typeof data.sidebars !== "object" || Array.isArray(data.sidebars)) {
    throw new Error(`Docspress sidebars file must contain a sidebars object: ${sidebarsFile}`);
  }

  const entries = Object.entries(data.sidebars).map(([rawId, configuredRoot], order) => {
    const id = normalizeSidebarId(rawId);
    const root = normalizeSidebarRoot(configuredRoot, id);
    return { id, root, order };
  });
  if (entries.length === 0) {
    throw new Error(`Docspress sidebars file must configure at least one sidebar: ${sidebarsFile}`);
  }

  if (!data.default) {
    throw new Error("Docspress sidebars default must reference a configured sidebar: (missing)");
  }
  const defaultId = normalizeSidebarId(data.default);
  const defaultSidebar = entries.find(({ id }) => id === defaultId);
  if (!defaultSidebar) {
    throw new Error(`Docspress sidebars default must reference a configured sidebar: ${data.default || "(missing)"}`);
  }
  if (defaultSidebar.root !== "") {
    throw new Error(`The default Docspress sidebar (${defaultId}) must use the root '.'.`);
  }

  const roots = new Map();
  for (const entry of entries) {
    const existing = roots.get(entry.root);
    if (existing) {
      throw new Error(`Docspress sidebars ${existing} and ${entry.id} use the same root: ${entry.root || "."}`);
    }
    roots.set(entry.root, entry.id);
  }

  return {
    file: sidebarsFile,
    default: defaultId,
    entries
  };
}

export function applySidebarsRegistry(byRoute, registry) {
  if (!registry) {
    return byRoute;
  }

  for (const sidebar of registry.entries) {
    if (sidebar.root && !byRoute.has(sidebar.root)) {
      throw new Error(`Docspress sidebar ${sidebar.id} references a missing route: ${sidebar.root}`);
    }
  }

  const ordered = [...registry.entries].sort((left, right) => (
    routeDepth(right.root) - routeDepth(left.root) || left.order - right.order
  ));

  for (const page of byRoute.values()) {
    const sidebar = ordered.find(({ root }) => routeContains(root, page.routeKey));
    if (!sidebar) {
      throw new Error(`Docspress could not assign route ${page.routeKey || "."} to a sidebar.`);
    }
    page.sidebarId = sidebar.id;
    page.sidebarRoot = page.routeKey === sidebar.root;
  }

  return byRoute;
}

function normalizeSidebarId(value) {
  const id = String(value || "").trim();
  if (!SIDEBAR_ID_PATTERN.test(id)) {
    throw new Error(`Invalid Docspress sidebar id: ${id || "(missing)"}`);
  }
  return id;
}

function normalizeSidebarRoot(value, id) {
  if (typeof value !== "string") {
    throw new Error(`Docspress sidebar ${id} must map to a route string.`);
  }

  const raw = value.trim().replace(/\\/g, "/");
  if (raw === ".") {
    return "";
  }
  if (!raw || raw.startsWith("/") || raw.includes(":") || raw.includes("?") || raw.includes("#")) {
    throw new Error(`Docspress sidebar ${id} has an invalid root: ${value || "(empty)"}`);
  }

  const segments = raw.replace(/\/+$/, "").split("/");
  if (segments.some((segment) => !ROUTE_SEGMENT_PATTERN.test(segment))) {
    throw new Error(`Docspress sidebar ${id} has an invalid root: ${value}`);
  }
  return segments.join("/");
}

function routeContains(root, route) {
  return root === "" || route === root || route.startsWith(`${root}/`);
}

function routeDepth(route) {
  return route ? route.split("/").length : 0;
}
