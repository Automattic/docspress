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

<!-- wp:docspress/fields {"title":"Fields block attributes","description":"Reference content and display behavior accepted by <code>docspress/fields</code>.","fields":[{"name":"title","type":"string","required":false,"defaultValue":"Configuration fields","description":"<p>Plain-text reference heading.</p>","values":"","deprecated":false},{"name":"description","type":"string","required":false,"defaultValue":"Typed options, defaults, and constraints in one scannable reference.","description":"<p>Formatted reference introduction.</p>","values":"","deprecated":false},{"name":"fields","type":"array","required":true,"defaultValue":"Starter fields","description":"<p>Up to 40 field objects. Items with blank names are discarded.</p>","values":"1–40 items","deprecated":false},{"name":"searchable","type":"boolean","required":false,"defaultValue":"true","description":"<p>Adds a client-side filter when more than three fields render.</p>","values":"true, false","deprecated":false},{"name":"compact","type":"boolean","required":false,"defaultValue":"false","description":"<p>Reduces row spacing.</p>","values":"true, false","deprecated":false}],"searchable":true,"compact":true} /-->

Each item in `fields` accepts:

<!-- wp:docspress/fields {"title":"Field object","description":"Schema for one documented field.","fields":[{"name":"name","type":"string","required":true,"defaultValue":"","description":"<p>Exact parameter, property, variable, or option name.</p>","values":"","deprecated":false},{"name":"type","type":"enum","required":false,"defaultValue":"string","description":"<p>Semantic value type.</p>","values":"string, number, boolean, object, array, enum, url, date, any","deprecated":false},{"name":"required","type":"boolean","required":false,"defaultValue":"false","description":"<p>Marks the field as required.</p>","values":"true, false","deprecated":false},{"name":"defaultValue","type":"string","required":false,"defaultValue":"","description":"<p>Plain-text default.</p>","values":"","deprecated":false},{"name":"description","type":"string","required":false,"defaultValue":"","description":"<p>Formatted explanation and constraints.</p>","values":"","deprecated":false},{"name":"values","type":"string","required":false,"defaultValue":"","description":"<p>Comma-separated allowed or representative values.</p>","values":"","deprecated":false},{"name":"deprecated","type":"boolean","required":false,"defaultValue":"false","description":"<p>Marks an option readers should stop using.</p>","values":"true, false","deprecated":false}],"searchable":true,"compact":true} /-->

## Creative examples

### Publishing command options

<!-- wp:docspress/fields {"title":"Publish options","description":"Typed values accepted by the publishing command.","fields":[{"name":"site","type":"string","required":true,"defaultValue":"","description":"WordPress site domain or numeric site ID.","values":"","deprecated":false},{"name":"status","type":"enum","required":false,"defaultValue":"draft","description":"Publication status for synchronized Pages.","values":"draft, publish, private","deprecated":false}],"searchable":true,"compact":false} /-->

### Webhook event envelope

<!-- wp:docspress/fields {"title":"Webhook event","description":"Properties delivered when a documentation Page changes.","fields":[{"name":"id","type":"string","required":true,"defaultValue":"","description":"<p>Stable event identifier used for idempotency.</p>","values":"evt_…","deprecated":false},{"name":"type","type":"enum","required":true,"defaultValue":"","description":"<p>Event that produced the delivery.</p>","values":"page.created, page.updated, page.trashed","deprecated":false},{"name":"createdAt","type":"date","required":true,"defaultValue":"","description":"<p>ISO 8601 creation time.</p>","values":"2026-07-27T10:30:00Z","deprecated":false},{"name":"data","type":"object","required":true,"defaultValue":"","description":"<p>Event-specific Page payload.</p>","values":"","deprecated":false}],"searchable":true,"compact":false} /-->

### Configuration migration

<!-- wp:docspress/fields {"title":"Navigation settings","description":"Current settings beside one retired option and its replacement.","fields":[{"name":"sidebarDepth","type":"number","required":false,"defaultValue":"4","description":"<p>Maximum visible documentation depth.</p>","values":"1–8","deprecated":false},{"name":"showSearch","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows the documentation filter above the tree.</p>","values":"true, false","deprecated":false},{"name":"legacyMenuId","type":"number","required":false,"defaultValue":"","description":"<p>Retired menu identifier. Use <code>navigationSource</code> instead.</p>","values":"","deprecated":true},{"name":"navigationSource","type":"enum","required":false,"defaultValue":"hierarchy","description":"<p>Source used to build documentation navigation.</p>","values":"hierarchy, menu","deprecated":false}],"searchable":true,"compact":true} /-->

## Published behavior and accessibility

Fields renders a semantic description list with a name and explanation for every entry. Search updates the visible count and empty state without changing source content.

Use exact casing for names, distinguish “optional” from “has a default,” and put constraints beside the field they govern. Mark deprecated fields and name the replacement in their description.
