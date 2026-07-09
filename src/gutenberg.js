import { escapeAttribute, escapeHtml } from "./utils.js";

const VOID_BLOCKS = new Set(["core/more", "core/nextpage"]);

export function serializeBlock(name, attrs, html) {
  const serializedAttrs = attrs && Object.keys(attrs).length > 0 ? ` ${JSON.stringify(attrs)}` : "";

  if (VOID_BLOCKS.has(name)) {
    return `<!-- wp:${name.replace(/^core\//, "")}${serializedAttrs} /-->`;
  }

  return `<!-- wp:${name.replace(/^core\//, "")}${serializedAttrs} -->\n${html}\n<!-- /wp:${name.replace(/^core\//, "")} -->`;
}

export function paragraphBlock(html) {
  return serializeBlock("core/paragraph", null, `<p>${html}</p>`);
}

export function headingBlock(level, html) {
  const safeLevel = Math.min(Math.max(Number(level) || 2, 1), 6);
  const attrs = safeLevel === 2 ? null : { level: safeLevel };
  return serializeBlock("core/heading", attrs, `<h${safeLevel}>${html}</h${safeLevel}>`);
}

export function listBlock(html, ordered = false) {
  const tag = ordered ? "ol" : "ul";
  const attrs = ordered ? { ordered: true } : null;
  return serializeBlock("core/list", attrs, `<${tag}>${html}</${tag}>`);
}

export function quoteBlock(html) {
  return serializeBlock("core/quote", null, `<blockquote class="wp-block-quote">${html}</blockquote>`);
}

export function codeBlock(value, lang) {
  const className = lang ? ` class="language-${escapeAttribute(lang)}"` : "";
  return serializeBlock("core/code", null, `<pre class="wp-block-code"><code${className}>${escapeHtml(value)}</code></pre>`);
}

export function preformattedBlock(value) {
  return serializeBlock("core/preformatted", null, `<pre class="wp-block-preformatted">${escapeHtml(value)}</pre>`);
}

export function separatorBlock() {
  return serializeBlock("core/separator", null, '<hr class="wp-block-separator has-alpha-channel-opacity"/>');
}

export function htmlBlock(value) {
  return serializeBlock("core/html", null, String(value || ""));
}

export function imageBlock(node) {
  const url = node.url || "";
  const alt = node.alt || "";
  const title = node.title || "";
  const attrs = { url, alt };
  const caption = title ? `<figcaption class="wp-element-caption">${escapeHtml(title)}</figcaption>` : "";
  return serializeBlock(
    "core/image",
    attrs,
    `<figure class="wp-block-image"><img src="${escapeAttribute(url)}" alt="${escapeAttribute(alt)}"/>${caption}</figure>`
  );
}

export function tableBlock(html) {
  return serializeBlock("core/table", null, `<figure class="wp-block-table"><table>${html}</table></figure>`);
}
