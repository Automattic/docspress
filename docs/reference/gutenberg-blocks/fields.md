---
title: Fields / Schema
sidebar_position: 90
---

Use `docspress/fields` for structured reference data: API parameters, object properties, configuration keys, environment variables, command options, or response fields.

## When to use it

Choose Fields when every item follows the same schema and readers need to scan names, types, requirements, defaults, values, and descriptions. Use a normal table for a small comparison with unrelated columns. Use [File Tree](file-tree.md) for hierarchy.

## Edit the block

Set the title and formatted description, then add up to 40 fields. Keep names exact and select the closest semantic type. Enable search for longer references; the client-side filter appears only when more than three fields render. Enable Compact layout when descriptions are short.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Fields block attributes",
    "description": "Reference content and display behavior accepted by \u003ccode\u003edocspress/fields\u003c/code\u003e.",
    "fields": [
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Configuration fields",
        "description": "\u003cp\u003ePlain-text reference heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "description",
        "type": "string",
        "required": false,
        "defaultValue": "Typed options, defaults, and constraints in one scannable reference.",
        "description": "\u003cp\u003eFormatted reference introduction.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "fields",
        "type": "array",
        "required": true,
        "defaultValue": "Starter fields",
        "description": "\u003cp\u003eUp to 40 field objects. Items with blank names are discarded.\u003c/p\u003e",
        "values": "1–40 items",
        "deprecated": false
      },
      {
        "name": "searchable",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eAdds a client-side filter when more than three fields render.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "compact",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eReduces row spacing.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Fields block attributes

Reference content and display behavior accepted by <code>docspress/fields</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `title` | string | No | Configuration fields | <p>Plain-text reference heading.</p> |
| `description` | string | No | Typed options, defaults, and constraints in one scannable reference. | <p>Formatted reference introduction.</p> |
| `fields` | array | Yes | Starter fields | <p>Up to 40 field objects. Items with blank names are discarded.</p> |
| `searchable` | boolean | No | true | <p>Adds a client-side filter when more than three fields render.</p> |
| `compact` | boolean | No | false | <p>Reduces row spacing.</p> |
<!-- /docspress:block -->

Each item in `fields` accepts:

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Field object",
    "description": "Schema for one documented field.",
    "fields": [
      {
        "name": "name",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eExact parameter, property, variable, or option name.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "type",
        "type": "enum",
        "required": false,
        "defaultValue": "string",
        "description": "\u003cp\u003eSemantic value type.\u003c/p\u003e",
        "values": "string, number, boolean, object, array, enum, url, date, any",
        "deprecated": false
      },
      {
        "name": "required",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eMarks the field as required.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "defaultValue",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003ePlain-text default.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "description",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eFormatted explanation and constraints.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "values",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eComma-separated allowed or representative values.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "deprecated",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eMarks an option readers should stop using.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Field object

Schema for one documented field.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `name` | string | Yes |  | <p>Exact parameter, property, variable, or option name.</p> |
| `type` | enum | No | string | <p>Semantic value type.</p> |
| `required` | boolean | No | false | <p>Marks the field as required.</p> |
| `defaultValue` | string | No |  | <p>Plain-text default.</p> |
| `description` | string | No |  | <p>Formatted explanation and constraints.</p> |
| `values` | string | No |  | <p>Comma-separated allowed or representative values.</p> |
| `deprecated` | boolean | No | false | <p>Marks an option readers should stop using.</p> |
<!-- /docspress:block -->

## Creative examples

### Publishing command options

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Publish options",
    "description": "Typed values accepted by the publishing command.",
    "fields": [
      {
        "name": "site",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "WordPress site domain or numeric site ID.",
        "values": "",
        "deprecated": false
      },
      {
        "name": "status",
        "type": "enum",
        "required": false,
        "defaultValue": "draft",
        "description": "Publication status for synchronized Pages.",
        "values": "draft, publish, private",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": false
  }
}
-->
#### Publish options

Typed values accepted by the publishing command.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `site` | string | Yes |  | WordPress site domain or numeric site ID. |
| `status` | enum | No | draft | Publication status for synchronized Pages. |
<!-- /docspress:block -->

### Webhook event envelope

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Webhook event",
    "description": "Properties delivered when a documentation Page changes.",
    "fields": [
      {
        "name": "id",
        "type": "string",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eStable event identifier used for idempotency.\u003c/p\u003e",
        "values": "evt_…",
        "deprecated": false
      },
      {
        "name": "type",
        "type": "enum",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eEvent that produced the delivery.\u003c/p\u003e",
        "values": "page.created, page.updated, page.trashed",
        "deprecated": false
      },
      {
        "name": "createdAt",
        "type": "date",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eISO 8601 creation time.\u003c/p\u003e",
        "values": "2026-07-27T10:30:00Z",
        "deprecated": false
      },
      {
        "name": "data",
        "type": "object",
        "required": true,
        "defaultValue": "",
        "description": "\u003cp\u003eEvent-specific Page payload.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": false
  }
}
-->
#### Webhook event

Properties delivered when a documentation Page changes.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `id` | string | Yes |  | <p>Stable event identifier used for idempotency.</p> |
| `type` | enum | Yes |  | <p>Event that produced the delivery.</p> |
| `createdAt` | date | Yes |  | <p>ISO 8601 creation time.</p> |
| `data` | object | Yes |  | <p>Event-specific Page payload.</p> |
<!-- /docspress:block -->

### Configuration migration

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Navigation settings",
    "description": "Current settings beside one retired option and its replacement.",
    "fields": [
      {
        "name": "sidebarDepth",
        "type": "number",
        "required": false,
        "defaultValue": "4",
        "description": "\u003cp\u003eMaximum visible documentation depth.\u003c/p\u003e",
        "values": "1–8",
        "deprecated": false
      },
      {
        "name": "showSearch",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows the documentation filter above the tree.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "legacyMenuId",
        "type": "number",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eRetired menu identifier. Use \u003ccode\u003enavigationSource\u003c/code\u003e instead.\u003c/p\u003e",
        "values": "",
        "deprecated": true
      },
      {
        "name": "navigationSource",
        "type": "enum",
        "required": false,
        "defaultValue": "hierarchy",
        "description": "\u003cp\u003eSource used to build documentation navigation.\u003c/p\u003e",
        "values": "hierarchy, menu",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Navigation settings

Current settings beside one retired option and its replacement.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `sidebarDepth` | number | No | 4 | <p>Maximum visible documentation depth.</p> |
| `showSearch` | boolean | No | true | <p>Shows the documentation filter above the tree.</p> |
| `legacyMenuId` | number | No |  | <p>Retired menu identifier. Use <code>navigationSource</code> instead.</p> |
| `navigationSource` | enum | No | hierarchy | <p>Source used to build documentation navigation.</p> |
<!-- /docspress:block -->

## Published behavior and accessibility

Fields renders a semantic description list with a name and explanation for every entry. Search updates the visible count and empty state without changing source content.

Use exact casing for names, distinguish “optional” from “has a default,” and put constraints beside the field they govern. Mark deprecated fields and name the replacement in their description.
