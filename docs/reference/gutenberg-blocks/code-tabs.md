---
title: Code Tabs
sidebar_position: 40
---

Use `docspress/code-tabs` for equivalent examples readers can switch between, such as package managers, programming languages, operating systems, or API clients.

## When to use it

Choose Code Tabs only when every tab accomplishes the same task. Use [Colorful Code](colorful-code.md) when examples build on one another or need highlights, diffs, or annotations. Separate sequential examples with headings instead of hiding steps in tabs.

## Edit the block

Add, remove, and reorder tabs in Gutenberg. Give each tab a short unique label, select its language, and optionally set a filename. Keep the same conceptual scope and expected result across tabs.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Code Tabs attributes",
    "description": "Alternatives and presentation accepted by \u003ccode\u003edocspress/code-tabs\u003c/code\u003e.",
    "fields": [
      {
        "name": "tabs",
        "type": "array",
        "required": true,
        "defaultValue": "Two starter tabs",
        "description": "\u003cp\u003eUp to eight equivalent source examples.\u003c/p\u003e",
        "values": "1–8 items",
        "deprecated": false
      },
      {
        "name": "showLineNumbers",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows one-based line numbers in every panel.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "caption",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional formatted caption for the complete tab set.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Code Tabs attributes

Alternatives and presentation accepted by <code>docspress/code-tabs</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `tabs` | array | Yes | Two starter tabs | <p>Up to eight equivalent source examples.</p> |
| `showLineNumbers` | boolean | No | true | <p>Shows one-based line numbers in every panel.</p> |
| `caption` | string | No |  | <p>Optional formatted caption for the complete tab set.</p> |
<!-- /docspress:block -->

Each `tabs` item accepts:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Tab object",
    "description": "Fields for one equivalent source example.",
    "fields": [
      {
        "name": "label",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eShort, unique tab label.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "language",
        "type": "enum",
        "required": false,
        "defaultValue": "plaintext",
        "description": "\u003cp\u003eSyntax language. Invalid values normalize to plaintext.\u003c/p\u003e",
        "values": "bash, css, html, javascript, json, jsx, markdown, php, plaintext, python, shell, sql, tsx, typescript, yaml",
        "deprecated": false
      },
      {
        "name": "filename",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eOptional display filename.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "code",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003ePlain source text.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": false,
    "compact": true
  }
}
-->
#### Tab object

Fields for one equivalent source example.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `label` | string | Yes |  | <p>Short, unique tab label.</p> |
| `language` | enum | No | plaintext | <p>Syntax language. Invalid values normalize to plaintext.</p> |
| `filename` | string | No |  | <p>Optional display filename.</p> |
| `code` | string | Yes |  | <p>Plain source text.</p> |
<!-- /docspress:block -->

Invalid languages fall back to `plaintext`. Only the first eight valid tabs render. An empty tab array renders no block.

## Creative examples

### Package-manager alternatives

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-tabs",
  "attrs": {
    "tabs": [
      {
        "label": "npm",
        "language": "bash",
        "filename": "Terminal",
        "code": "npm install docspress"
      },
      {
        "label": "pnpm",
        "language": "bash",
        "filename": "Terminal",
        "code": "pnpm add docspress"
      }
    ],
    "showLineNumbers": false,
    "caption": "Equivalent package-manager commands."
  }
}
-->
#### npm — Terminal

```bash
npm install docspress
```

#### pnpm — Terminal

```bash
pnpm add docspress
```

_Equivalent package-manager commands._
<!-- /docspress:block -->

### Fetch one documentation Page

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-tabs",
  "attrs": {
    "tabs": [
      {
        "label": "cURL",
        "language": "bash",
        "filename": "Terminal",
        "code": "curl https://example.test/wp-json/wp/v2/pages/42"
      },
      {
        "label": "JavaScript",
        "language": "javascript",
        "filename": "fetch-page.js",
        "code": "const response = await fetch( 'https://example.test/wp-json/wp/v2/pages/42' );\nconst page = await response.json();"
      },
      {
        "label": "Python",
        "language": "python",
        "filename": "fetch_page.py",
        "code": "import requests\n\npage = requests.get(\n    'https://example.test/wp-json/wp/v2/pages/42',\n    timeout=10,\n).json()"
      }
    ],
    "showLineNumbers": true,
    "caption": "The same read-only request in three clients, using a non-production hostname."
  }
}
-->
#### cURL — Terminal

```bash
curl https://example.test/wp-json/wp/v2/pages/42
```

#### JavaScript — fetch-page.js

```javascript
const response = await fetch( 'https://example.test/wp-json/wp/v2/pages/42' );
const page = await response.json();
```

#### Python — fetch\_page.py

```python
import requests

page = requests.get(
    'https://example.test/wp-json/wp/v2/pages/42',
    timeout=10,
).json()
```

_The same read-only request in three clients, using a non-production hostname._
<!-- /docspress:block -->

### Express one configuration in three formats

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-tabs",
  "attrs": {
    "tabs": [
      {
        "label": "JSON",
        "language": "json",
        "filename": "docspress.json",
        "code": "{\n  \"docsDir\": \"docs\",\n  \"status\": \"draft\"\n}"
      },
      {
        "label": "YAML",
        "language": "yaml",
        "filename": "docspress.yml",
        "code": "docsDir: docs\nstatus: draft"
      },
      {
        "label": "PHP",
        "language": "php",
        "filename": "docspress.php",
        "code": "\u003c?php\nreturn [\n    'docsDir' =\u003e 'docs',\n    'status' =\u003e 'draft',\n];"
      }
    ],
    "showLineNumbers": true,
    "caption": "Equivalent configuration values in formats used by different toolchains."
  }
}
-->
#### JSON — docspress.json

```json
{
  "docsDir": "docs",
  "status": "draft"
}
```

#### YAML — docspress.yml

```yaml
docsDir: docs
status: draft
```

#### PHP — docspress.php

```php
<?php
return [
    'docsDir' => 'docs',
    'status' => 'draft',
];
```

_Equivalent configuration values in formats used by different toolchains._
<!-- /docspress:block -->

## Published behavior and accessibility

The block uses a semantic tab list and tab panels. Readers can move between tabs with the keyboard, and only the active panel participates in the normal tab order. Copy operates on the active example and announces its result.

Put the most broadly useful choice first. Keep tab labels compact, avoid more than four choices when possible, and confirm that every example remains functionally equivalent after updates.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "note",
    "title": "Tabs are alternatives, not steps",
    "content": "\u003cp\u003eEvery tab should solve the same task. If readers must use more than one panel, present the examples sequentially instead.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!NOTE]
>
> **Tabs are alternatives, not steps**
>
> Every tab should solve the same task. If readers must use more than one panel, present the examples sequentially instead.
<!-- /docspress:block -->
