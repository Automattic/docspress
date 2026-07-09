import { describe, expect, it } from "vitest";
import { WordPressClient } from "../src/wordpress.js";

function jsonResponse(data, init = {}) {
  return {
    ok: init.ok ?? true,
    status: init.status ?? 200,
    headers: {
      get(name) {
        return init.headers?.[name.toLowerCase()] || init.headers?.[name] || null;
      }
    },
    async text() {
      return JSON.stringify(data);
    }
  };
}

describe("WordPressClient", () => {
  it("uses the WordPress.com pages endpoint and bearer token", async () => {
    const calls = [];
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "token",
      fetchImpl: async (url, init) => {
        calls.push({ url: String(url), init });
        return jsonResponse([], { headers: { "x-wp-totalpages": "1" } });
      }
    });

    await client.listPages();

    expect(calls[0].url).toContain("https://public-api.wordpress.com/wp/v2/sites/fkadev.blog/pages");
    expect(calls[0].url).toContain("context=edit");
    expect(calls[0].init.headers.Authorization).toBe("Bearer token");
  });

  it("paginates page listing", async () => {
    const calls = [];
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "token",
      fetchImpl: async (url, init) => {
        calls.push({ url: String(url), init });
        const page = new URL(String(url)).searchParams.get("page");
        return jsonResponse([{ id: Number(page), slug: `page-${page}`, parent: 0, content: { raw: "" }, title: { raw: "" } }], {
          headers: { "x-wp-totalpages": "2" }
        });
      }
    });

    const pages = await client.listPages();

    expect(pages.map((page) => page.id)).toEqual([1, 2]);
    expect(calls).toHaveLength(2);
  });

  it("normalizes configured taxonomy fields on pages", async () => {
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "token",
      taxonomies: ["docspress_version"],
      fetchImpl: async () => jsonResponse([
        { id: 1, slug: "v1", parent: 0, content: { raw: "" }, title: { raw: "" }, docspress_version: [12, "13"] }
      ], {
        headers: { "x-wp-totalpages": "1" }
      })
    });

    const pages = await client.listPages();

    expect(pages[0].terms.docspress_version).toEqual([12, 13]);
  });

  it("lists and creates terms through the configured taxonomy endpoint", async () => {
    const calls = [];
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "token",
      fetchImpl: async (url, init) => {
        calls.push({ url: String(url), init });
        if (init.method === "GET") {
          return jsonResponse([], { headers: { "x-wp-totalpages": "1" } });
        }
        return jsonResponse({ id: 12, slug: "v1", name: "v1" });
      }
    });

    const term = await client.ensureTerm("docspress_version", { slug: "v1", name: "v1" });

    expect(term).toEqual({ id: 12, slug: "v1", name: "v1" });
    expect(calls[0].url).toContain("/wp/v2/sites/fkadev.blog/docspress_version");
    expect(calls[0].url).toContain("hide_empty=false");
    expect(calls[1].init.method).toBe("POST");
    expect(JSON.parse(calls[1].init.body)).toEqual({ slug: "v1", name: "v1" });
  });

  it("raises WordPress API errors", async () => {
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "bad-token",
      fetchImpl: async () => jsonResponse({ message: "Invalid token" }, { ok: false, status: 401 })
    });

    await expect(client.listPages()).rejects.toThrow("Invalid token");
  });

  it("adds a useful hint for WordPress.com global scope failures", async () => {
    const client = new WordPressClient({
      baseUrl: "https://public-api.wordpress.com",
      site: "fkadev.blog",
      token: "narrow-token",
      fetchImpl: async () => jsonResponse(
        { message: "That API call is not allowed for this account. Required scope: `global`. Granted scope(s): `posts,media`." },
        { ok: false, status: 403 }
      )
    });

    await expect(client.listPages()).rejects.toThrow(/Regenerate WP_ACCESS_TOKEN/);
  });
});
