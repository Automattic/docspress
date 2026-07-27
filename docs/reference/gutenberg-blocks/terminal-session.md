---
title: Terminal Session
sidebar_position: 100
---

Use `docspress/terminal-session` for one copyable command with optional read-only output. It looks like a terminal transcript but never executes code.

## When to use it

Choose Terminal Session when the command and its expected output belong together. Use [Colorful Code](colorful-code.md) for scripts or configuration files and [Code Tabs](code-tabs.md) for equivalent commands. Use [API Request / Response](api-request.md) for an HTTP exchange.

## Attributes

<!-- wp:docspress/fields {"title":"Terminal Session attributes","description":"Terminal labels, command, and output accepted by <code>docspress/terminal-session</code>.","fields":[{"name":"title","type":"string","required":false,"defaultValue":"Terminal","description":"<p>Plain-text session heading.</p>","values":"","deprecated":false},{"name":"shell","type":"string","required":false,"defaultValue":"bash","description":"<p>Shell or runtime label.</p>","values":"bash, zsh, PowerShell, node","deprecated":false},{"name":"prompt","type":"string","required":false,"defaultValue":"$","description":"<p>Prompt displayed before the command.</p>","values":"","deprecated":false},{"name":"command","type":"string","required":true,"defaultValue":"npx docspress publish ./docs","description":"<p>Plain command copied by the reader.</p>","values":"","deprecated":false},{"name":"output","type":"string","required":false,"defaultValue":"Example publish result","description":"<p>Optional read-only expected output.</p>","values":"","deprecated":false}],"searchable":false,"compact":true} /-->

## Creative examples

### Run the package checks

<!-- wp:docspress/terminal-session {"title":"Run the package checks","shell":"bash","prompt":"$","command":"npm run package","output":"Tests passed\nBundle rebuilt"} /-->

### Preview a safe reconciliation

<!-- wp:docspress/terminal-session {"title":"Preview WordPress changes","shell":"bash","prompt":"$","command":"npx docspress publish ./docs --dry-run","output":"Found 42 desired pages\nWould create 0\nWould update 3\nWould trash 1\nConflicts 0"} /-->

### Inspect one response in PowerShell

<!-- wp:docspress/terminal-session {"title":"Read the public API index","shell":"PowerShell","prompt":"PS>","command":"Invoke-RestMethod https://example.test/wp-json/ | Select-Object name, url","output":"name       url\n----       ---\nDocsPress  https://example.test"} /-->

## Published behavior and accessibility

Copy copies only `command`, never the prompt or output. The output has an accessible command-output label, and long lines remain scrollable. No shell process or browser evaluation is attached to the block.

Show only the output needed to recognize success. Do not include volatile timestamps, personal paths, secrets, or pages of logs.

<!-- wp:docspress/callout {"tone":"note","title":"This block does not run commands","content":"<p>Terminal Session is a documentation surface. Readers copy its command and run it in their own trusted terminal.</p>","collapsible":false,"open":true} /-->
