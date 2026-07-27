---
title: Callout
sidebar_position: 50
---

Use `docspress/callout` to separate an important note, tip, warning, danger, or success message from the surrounding lesson.

## When to use it

Choose a callout for information a reader might otherwise miss. Do not place ordinary paragraphs in callouts for decoration. Use [Result](result.md) when the message is specifically the outcome of a procedure or check.

## Edit the block

Edit the title and rich content in the canvas. Select the tone that matches the consequence. Enable **Collapsible** only for optional detail; `open` determines its initial state.

## Attributes

<!-- wp:docspress/fields {"title":"Callout attributes","description":"Message and disclosure behavior accepted by <code>docspress/callout</code>.","fields":[{"name":"tone","type":"enum","required":false,"defaultValue":"note","description":"<p>Semantic message severity.</p>","values":"note, tip, warning, danger, success","deprecated":false},{"name":"title","type":"string","required":false,"defaultValue":"Good to know","description":"<p>Formatted callout heading.</p>","values":"","deprecated":false},{"name":"content","type":"string","required":false,"defaultValue":"Starter paragraph","description":"<p>Formatted HTML content.</p>","values":"","deprecated":false},{"name":"collapsible","type":"boolean","required":false,"defaultValue":"false","description":"<p>Uses a native disclosure when enabled.</p>","values":"true, false","deprecated":false},{"name":"open","type":"boolean","required":false,"defaultValue":"true","description":"<p>Initial state of a collapsible callout.</p>","values":"true, false","deprecated":false}],"searchable":false,"compact":true} /-->

## Creative examples

### Open preview tip

<!-- wp:docspress/callout {"tone":"tip","title":"Preview first","content":"<p>Use a dry run before allowing Page writes.</p>","collapsible":true,"open":true} /-->

### Destructive-operation warning

<!-- wp:docspress/callout {"tone":"danger","title":"Stop before resetting production","content":"<p>A hard reset can remove unreviewed content. Export the current state, name the recovery owner, and verify the exact target before continuing.</p>","collapsible":false} /-->

### Collapsed background detail

<!-- wp:docspress/callout {"tone":"note","title":"Why the cache refresh takes a minute","content":"<p>Edge nodes refresh independently. The published Page is already authoritative, but a reader in another region may briefly receive the previous copy.</p>","collapsible":true,"open":false} /-->

## Tone guide

| Tone | Use it for |
| --- | --- |
| `note` | Context or a neutral detail |
| `tip` | A faster, safer, or more effective technique |
| `warning` | A recoverable risk or prerequisite |
| `danger` | Destructive, security-sensitive, or difficult-to-reverse action |
| `success` | Confirmation or a known-good state |

## Published behavior and accessibility

A standard callout renders as an aside. A collapsible callout uses native `details` and `summary`, so it remains operable without custom JavaScript. The tone is conveyed by text and structure as well as color.

Keep the title meaningful outside its surrounding paragraph. Do not hide required steps in a collapsed callout, and reserve danger for consequences that truly warrant interruption.
