---
title: Live Code Playground
sidebar_position: 110
---

Use `docspress/code-playground` for a small HTML, CSS, and JavaScript example readers should edit and run in the page. It combines source editors, a sandboxed result, and bounded console output.

## When to use it

Choose Live Code Playground when interaction is essential to the lesson. Use [Colorful Code](colorful-code.md) when readers only need to study or copy source. Keep multi-file applications and build-tool examples in a real repository or hosted development environment.

## Edit the block

Provide self-contained HTML, CSS, and JavaScript. Set a preview height from 180–720 pixels, decide whether the example runs on load, and show the console only when its output teaches something. Leave network access disabled unless the example genuinely depends on a public resource.

## Attributes

<!-- wp:docspress/fields {"title":"Live Code Playground attributes","description":"Source, preview, and sandbox behavior accepted by <code>docspress/code-playground</code>.","fields":[{"name":"title","type":"string","required":false,"defaultValue":"Live example","description":"<p>Plain-text playground heading.</p>","values":"","deprecated":false},{"name":"html","type":"string","required":false,"defaultValue":"Demo button","description":"<p>HTML placed in the sandboxed document body.</p>","values":"","deprecated":false},{"name":"css","type":"string","required":false,"defaultValue":"Demo button styles","description":"<p>CSS placed in an inline style element.</p>","values":"","deprecated":false},{"name":"javascript","type":"string","required":false,"defaultValue":"Demo click handler","description":"<p>JavaScript evaluated inside the sandbox.</p>","values":"","deprecated":false},{"name":"height","type":"number","required":false,"defaultValue":"320","description":"<p>Preview height in pixels, clamped to 180–720.</p>","values":"180–720","deprecated":false},{"name":"autoRun","type":"boolean","required":false,"defaultValue":"true","description":"<p>Runs the authored source when the page loads.</p>","values":"true, false","deprecated":false},{"name":"showConsole","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows relayed console messages and errors.</p>","values":"true, false","deprecated":false},{"name":"allowNetwork","type":"boolean","required":false,"defaultValue":"false","description":"<p>Relaxes the sandbox document policy to allow network requests.</p>","values":"true, false","deprecated":false}],"searchable":true,"compact":true} /-->

The block supports `wide` alignment in addition to the [shared design controls](index.md#add-and-edit-a-block).

## Creative examples

### Interactive publish button

<!-- wp:docspress/code-playground {"title":"Interactive button","html":"<button class=\"demo\">Publish</button>","css":".demo { padding: .75rem 1rem; background: #3858e9; color: white; }","javascript":"document.querySelector( '.demo' ).addEventListener( 'click', () => console.log( 'Published' ) );","height":320,"autoRun":true,"showConsole":true,"allowNetwork":false} /-->

### Theme-token mixer

<!-- wp:docspress/code-playground {"title":"Mix a documentation accent","html":"<label>Accent <input class=\"accent\" type=\"color\" value=\"#3858e9\"></label>\n<article class=\"card\"><strong>Preview card</strong><p>Change the control to test a theme token.</p></article>","css":":root { --accent: #3858e9; }\nbody { font: 16px/1.5 system-ui; padding: 1rem; }\nlabel { display: flex; gap: .75rem; align-items: center; }\n.card { margin-top: 1rem; padding: 1rem; border: 3px solid var(--accent); border-radius: .6rem; }\n.card strong { color: var(--accent); }","javascript":"const picker = document.querySelector( '.accent' );\npicker.addEventListener( 'input', () => {\n  document.documentElement.style.setProperty( '--accent', picker.value );\n} );","height":300,"autoRun":true,"showConsole":false,"allowNetwork":false} /-->

### Accessible disclosure lab

<!-- wp:docspress/code-playground {"title":"Build a keyboard-friendly disclosure","html":"<button class=\"toggle\" aria-expanded=\"false\" aria-controls=\"answer\">Show answer</button>\n<div id=\"answer\" hidden>The publishing dry run reports proposed changes without writing Pages.</div>","css":"body { font: 16px/1.5 system-ui; padding: 1rem; }\nbutton { padding: .65rem .9rem; }\n#answer { margin-top: 1rem; padding: 1rem; background: #f0f4ff; }","javascript":"const button = document.querySelector( '.toggle' );\nconst answer = document.querySelector( '#answer' );\nbutton.addEventListener( 'click', () => {\n  const expanded = button.getAttribute( 'aria-expanded' ) === 'true';\n  button.setAttribute( 'aria-expanded', String( ! expanded ) );\n  button.textContent = expanded ? 'Show answer' : 'Hide answer';\n  answer.hidden = expanded;\n  console.log( expanded ? 'Collapsed' : 'Expanded' );\n} );","height":300,"autoRun":true,"showConsole":true,"allowNetwork":false} /-->

## Sandbox and output

The result iframe uses `sandbox="allow-scripts"` without same-origin access and sends no referrer. A Content Security Policy blocks network access unless the author enables `allowNetwork`. Run rebuilds the sandbox, Reset restores the authored source, and console calls and errors are relayed only from the active result. Console history is bounded to 100 lines.

<!-- wp:docspress/callout {"tone":"warning","title":"Treat examples as public code","content":"<p>The iframe isolates the result from the parent page, but readers can inspect every source field. Never embed credentials, private endpoints, or sensitive data.</p>","collapsible":false,"open":true} /-->

Keep examples focused on one behavior, supply visible labels and keyboard interaction in the demo itself, and verify both the initial result and the Reset state.
