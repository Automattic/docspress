import { sha256, stableJson } from "./utils.js";

const BLOCK_DELIMITER_PATTERN = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?=([^}]+|}+(?=})|(?!}\s+\/?-->)[^])*)\5|[^]*?)}\s+)?(\/)?-->/g;

export function pageState(page) {
  return {
    key: page.key,
    sourcePath: page.sourcePath,
    title: page.title,
    slug: page.slug,
    parentKey: page.parentKey || null,
    status: page.status,
    body: normalizeGutenbergSerialization(page.body || "")
  };
}

export function hashPageState(page) {
  return sha256(stableJson(pageState(page)));
}

export function normalizeGutenbergSerialization(value) {
  return String(value || "").replace(
    BLOCK_DELIMITER_PATTERN,
    (delimiter, _closer, _namespace, _name, attributes) => {
      if (!attributes) {
        return delimiter;
      }

      const normalized = attributes.replace(
        /(^|[^\\])\\u005c/gi,
        (_escape, prefix) => `${prefix}\\\\`
      );
      return normalized === attributes ? delimiter : delimiter.replace(attributes, normalized);
    }
  );
}
