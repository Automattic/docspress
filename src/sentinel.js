export const SENTINEL_PREFIX = "docspress:";

const SENTINEL_PATTERN = /<!--\s*docspress:(.*?)\s*-->/s;

export function createSentinel(metadata) {
  return `<!-- ${SENTINEL_PREFIX}${JSON.stringify({
    version: 1,
    ...metadata
  })} -->`;
}

export function prependSentinel(content, metadata) {
  return `${createSentinel(metadata)}\n${content || ""}`;
}

export function readSentinel(content) {
  const match = String(content || "").match(SENTINEL_PATTERN);
  if (!match) {
    return null;
  }

  try {
    const parsed = JSON.parse(match[1]);
    return parsed && parsed.version === 1 ? parsed : null;
  } catch {
    return null;
  }
}

export function stripSentinel(content) {
  return String(content || "").replace(SENTINEL_PATTERN, "").trim();
}
