import { stripTrailingSlash } from "./utils.js";

export class WordPressClient {
  constructor(options) {
    this.baseUrl = stripTrailingSlash(options.baseUrl || "https://public-api.wordpress.com");
    this.site = options.site;
    this.token = options.token;
    this.fetchImpl = options.fetchImpl || fetch;
    this.taxonomies = options.taxonomies || [];
  }

  restEndpoint(collection) {
    const restCollection = String(collection || "").replace(/^\/+|\/+$/g, "");

    if (this.baseUrl.includes("public-api.wordpress.com")) {
      if (!this.site) {
        throw new Error("wordpress-site is required for WordPress.com API requests.");
      }
      return `${this.baseUrl}/wp/v2/sites/${encodeURIComponent(this.site)}/${restCollection}`;
    }

    return `${this.baseUrl}/wp-json/wp/v2/${restCollection}`;
  }

  pagesEndpoint() {
    return this.restEndpoint("pages");
  }

  async listPages() {
    const pages = [];
    let page = 1;
    let totalPages = 1;

    do {
      const response = await this.request("GET", this.pagesEndpoint(), {
        query: {
          per_page: "100",
          page: String(page),
          context: "edit",
          status: "any"
        }
      });
      pages.push(...response.data);
      totalPages = Number(response.headers.get("x-wp-totalpages") || totalPages || 1);
      page += 1;
    } while (page <= totalPages);

    return pages.map((pageData) => normalizePage(pageData, this.taxonomies));
  }

  async createPage(payload) {
    const response = await this.request("POST", this.pagesEndpoint(), { body: payload });
    return normalizePage(response.data, this.taxonomies);
  }

  async updatePage(id, payload) {
    const response = await this.request("POST", `${this.pagesEndpoint()}/${id}`, { body: payload });
    return normalizePage(response.data, this.taxonomies);
  }

  async deletePage(id, options = {}) {
    const response = await this.request("DELETE", `${this.pagesEndpoint()}/${id}`, {
      query: options.force ? { force: "true" } : {}
    });
    return response.data;
  }

  termsEndpoint(taxonomy) {
    return this.restEndpoint(taxonomy);
  }

  async listTerms(taxonomy) {
    const terms = [];
    let page = 1;
    let totalPages = 1;

    do {
      const response = await this.request("GET", this.termsEndpoint(taxonomy), {
        query: {
          per_page: "100",
          page: String(page),
          context: "edit",
          hide_empty: "false"
        }
      });
      terms.push(...response.data);
      totalPages = Number(response.headers.get("x-wp-totalpages") || totalPages || 1);
      page += 1;
    } while (page <= totalPages);

    return terms.map(normalizeTerm);
  }

  async createTerm(taxonomy, payload) {
    const response = await this.request("POST", this.termsEndpoint(taxonomy), { body: payload });
    return normalizeTerm(response.data);
  }

  async updateTerm(taxonomy, id, payload) {
    const response = await this.request("POST", `${this.termsEndpoint(taxonomy)}/${id}`, { body: payload });
    return normalizeTerm(response.data);
  }

  async getSettings() {
    const response = await this.request("GET", this.restEndpoint("settings"), {
      query: { context: "edit" }
    });
    return response.data || {};
  }

  async updateSettings(payload) {
    const response = await this.request("POST", this.restEndpoint("settings"), { body: payload });
    return response.data || {};
  }

  async request(method, url, options = {}) {
    const requestUrl = new URL(url);
    for (const [key, value] of Object.entries(options.query || {})) {
      requestUrl.searchParams.set(key, value);
    }

    const headers = {
      Accept: "application/json"
    };

    if (this.token) {
      headers.Authorization = `Bearer ${this.token}`;
    }

    const init = {
      method,
      headers
    };

    if (options.body) {
      headers["Content-Type"] = "application/json";
      init.body = JSON.stringify(options.body);
    }

    const response = await this.fetchImpl(requestUrl, init);
    const text = await response.text();
    const data = text ? JSON.parse(text) : null;

    if (!response.ok) {
      const message = formatApiError(data, method, requestUrl, response.status);
      throw new Error(message);
    }

    return {
      data,
      headers: response.headers
    };
  }
}

function formatApiError(data, method, requestUrl, status) {
  const message = data?.message || data?.error || `${method} ${requestUrl} failed with HTTP ${status}`;

  if (String(message).includes("Required scope: `global`")) {
    return `${message} Regenerate WP_ACCESS_TOKEN with the Docspress token helper so it requests the WordPress.com "global" OAuth scope.`;
  }

  return message;
}

export function normalizePage(page, taxonomies = []) {
  const id = page.id ?? page.ID;
  const rawContent = typeof page.content === "string" ? page.content : page.content?.raw ?? page.content?.rendered ?? "";
  const renderedTitle = typeof page.title === "string" ? page.title : page.title?.raw ?? page.title?.rendered ?? "";
  const parent = typeof page.parent === "number" ? page.parent : page.parent?.ID ?? page.parent?.id ?? 0;
  const menuOrder = page.menu_order ?? page.menuOrder ?? 0;
  const terms = {};
  for (const taxonomy of taxonomies) {
    terms[taxonomy] = Array.isArray(page[taxonomy]) ? page[taxonomy].map(Number) : [];
  }

  return {
    id,
    slug: page.slug,
    parent,
    menuOrder,
    title: renderedTitle,
    content: rawContent,
    status: page.status,
    link: page.link ?? page.URL ?? "",
    meta: page.meta && typeof page.meta === "object" ? page.meta : {},
    terms
  };
}

export function normalizeTerm(term) {
  return {
    id: Number(term.id ?? term.ID),
    name: String(term.name || ""),
    slug: String(term.slug || ""),
    count: Number(term.count || 0),
    meta: term.meta && typeof term.meta === "object" ? term.meta : {}
  };
}
