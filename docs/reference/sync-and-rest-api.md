---
title: Synchronization and REST API
---

Autodocs Publish builds a desired Page model from Markdown, lists existing WordPress Pages, and reconciles only the Pages carrying a valid management sentinel.

## WordPress endpoints

For WordPress.com, the Pages collection is:

```text
https://public-api.wordpress.com/wp/v2/sites/{site}/pages
```

For self-hosted WordPress, it is:

```text
{wordpress-url}/wp-json/wp/v2/pages
```

Listing requests use `context=edit`, `status=any`, and pages of 100 records until `x-wp-totalpages` is exhausted.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/api-request",
  "attrs": {
    "method": "POST",
    "endpoint": "/wp-json/wp/v2/pages",
    "headers": "Accept: application/json\nContent-Type: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN",
    "requestBody": "{\n  \"title\": \"Getting started\",\n  \"slug\": \"getting-started\",\n  \"status\": \"draft\",\n  \"parent\": 42,\n  \"content\": \"\u003c!\u002d\u002d autodocs-publish:{...} \u002d\u002d\u003e\\n\u003c!\u002d\u002d wp:paragraph \u002d\u002d\u003e...\"\n}",
    "requestBodyFormat": "json",
    "responseStatus": "201 Created",
    "responseBody": "{\n  \"id\": 43,\n  \"slug\": \"getting-started\",\n  \"status\": \"draft\",\n  \"parent\": 42\n}",
    "responseBodyFormat": "json"
  }
}
-->
<details>
<summary><strong>Request:</strong> <code>POST /wp-json/wp/v2/pages</code></summary>

**Headers**

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer $WP_ACCESS_TOKEN
```

**Body**

```json
{
  "title": "Getting started",
  "slug": "getting-started",
  "status": "draft",
  "parent": 42,
  "content": "<!-- autodocs-publish:{...} -->\n<!-- wp:paragraph -->..."
}
```

</details>

<details>
<summary><strong>Response:</strong> <code>201 Created</code></summary>

**Body**

```json
{
  "id": 43,
  "slug": "getting-started",
  "status": "draft",
  "parent": 42
}
```

</details>
<!-- /autodocs-publish:block -->

Updates use `POST /pages/{id}`. Deletions use `DELETE /pages/{id}` and add `force=true` only for `delete-mode: force`.

## Management sentinel

Every generated Page starts with a hidden comment containing version, Page key, source path, and content hash:

```html
<!-- autodocs-publish:{"version":1,"key":"docs/getting-started","source":"docs/getting-started.md","hash":"…"} -->
```

The hash covers the Page key, source, title, slug, parent key, status, and converted body. A content, route, hierarchy, source, or status change therefore schedules an update.

In `propose` and `reconcile` modes, the same hash acts as a common ancestor. Autodocs Publish computes the current GitHub and live WordPress states against that ancestor before it performs any write.

## Reconciliation order

Desired Pages are sorted by depth and key so parents are available before children. Existing Pages are indexed by their full parent path and by sentinel key.

| Condition | Operation |
| --- | --- |
| No Page at the desired path | Create |
| Managed Page with changed hash or parent | Update |
| Managed Page with matching hash and parent | Unchanged |
| Unmanaged Page already using the path | Conflict; do not write |
| Managed Page below `root-slug` absent from desired docs | Trash or permanently delete |
| Desired child whose parent is unavailable | Conflict |

For bidirectional runs, a GitHub-only change is published, a WordPress-only title or content change becomes a pull request, and matching current states refresh the sentinel after that pull request merges. If both current states differ from the sentinel and from each other, the run fails before writes. WordPress-created or deleted Pages and WordPress slug, parent, or status changes are intentionally outside reverse-sync scope.

<!-- autodocs-publish:block
{
  "version": 1,
  "name": "autodocs-publish/callout",
  "attrs": {
    "tone": "success",
    "title": "Manual Pages are protected",
    "content": "\u003cp\u003eAn unmanaged Page collision fails the Action instead of overwriting content that was created outside Autodocs Publish.\u003c/p\u003e",
    "collapsible": false
  }
}
-->
> [!TIP]
>
> **Manual Pages are protected**
>
> An unmanaged Page collision fails the Action instead of overwriting content that was created outside Autodocs Publish.
<!-- /autodocs-publish:block -->
## Dry-run behavior

Dry-run performs discovery, conversion, Page listing, comparison, conflict detection, deletion planning, and reverse Markdown generation. It assigns synthetic parent IDs for planned creates but does not call WordPress or GitHub write endpoints.

## API errors

Autodocs Publish surfaces the WordPress error message. When WordPress.com reports that `global` scope is required, the error adds a hint to regenerate `WP_ACCESS_TOKEN` with the token helper.
