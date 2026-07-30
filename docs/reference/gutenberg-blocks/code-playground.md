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

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "Live Code Playground attributes",
    "description": "Source, preview, and sandbox behavior accepted by \u003ccode\u003edocspress/code-playground\u003c/code\u003e.",
    "fields": [
      {
        "name": "title",
        "type": "string",
        "required": false,
        "defaultValue": "Live example",
        "description": "\u003cp\u003ePlain-text playground heading.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "html",
        "type": "string",
        "required": false,
        "defaultValue": "Demo button",
        "description": "\u003cp\u003eHTML placed in the sandboxed document body.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "css",
        "type": "string",
        "required": false,
        "defaultValue": "Demo button styles",
        "description": "\u003cp\u003eCSS placed in an inline style element.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "javascript",
        "type": "string",
        "required": false,
        "defaultValue": "Demo click handler",
        "description": "\u003cp\u003eJavaScript evaluated inside the sandbox.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "height",
        "type": "number",
        "required": false,
        "defaultValue": "320",
        "description": "\u003cp\u003ePreview height in pixels, clamped to 180–720.\u003c/p\u003e",
        "values": "180–720",
        "deprecated": false
      },
      {
        "name": "autoRun",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eRuns the authored source when the page loads.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "showConsole",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eShows relayed console messages and errors.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "allowNetwork",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eRelaxes the sandbox document policy to allow network requests.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### Live Code Playground attributes

Source, preview, and sandbox behavior accepted by <code>docspress/code-playground</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `title` | string | No | Live example | <p>Plain-text playground heading.</p> |
| `html` | string | No | Demo button | <p>HTML placed in the sandboxed document body.</p> |
| `css` | string | No | Demo button styles | <p>CSS placed in an inline style element.</p> |
| `javascript` | string | No | Demo click handler | <p>JavaScript evaluated inside the sandbox.</p> |
| `height` | number | No | 320 | <p>Preview height in pixels, clamped to 180–720.</p> |
| `autoRun` | boolean | No | true | <p>Runs the authored source when the page loads.</p> |
| `showConsole` | boolean | No | true | <p>Shows relayed console messages and errors.</p> |
| `allowNetwork` | boolean | No | false | <p>Relaxes the sandbox document policy to allow network requests.</p> |
<!-- /docspress:block -->

The block supports `wide` alignment in addition to the [shared design controls](index.md#add-and-edit-a-block).

## Creative examples

### Interactive publish button

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-playground",
  "attrs": {
    "title": "Interactive button",
    "html": "\u003cbutton class=\"demo\"\u003ePublish\u003c/button\u003e",
    "css": ".demo { padding: .75rem 1rem; background: #3858e9; color: white; }",
    "javascript": "document.querySelector( '.demo' ).addEventListener( 'click', () =\u003e console.log( 'Published' ) );",
    "height": 320,
    "autoRun": true,
    "showConsole": true,
    "allowNetwork": false
  }
}
-->
#### Interactive button

**HTML**

```html
<button class="demo">Publish</button>
```

**CSS**

```css
.demo { padding: .75rem 1rem; background: #3858e9; color: white; }
```

**JavaScript**

```javascript
document.querySelector( '.demo' ).addEventListener( 'click', () => console.log( 'Published' ) );
```
<!-- /docspress:block -->

### Theme-token mixer

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-playground",
  "attrs": {
    "title": "Mix a documentation accent",
    "html": "\u003clabel\u003eAccent \u003cinput class=\"accent\" type=\"color\" value=\"#3858e9\"\u003e\u003c/label\u003e\n\u003carticle class=\"card\"\u003e\u003cstrong\u003ePreview card\u003c/strong\u003e\u003cp\u003eChange the control to test a theme token.\u003c/p\u003e\u003c/article\u003e",
    "css": ":root { \u002d\u002daccent: #3858e9; }\nbody { font: 16px/1.5 system-ui; padding: 1rem; }\nlabel { display: flex; gap: .75rem; align-items: center; }\n.card { margin-top: 1rem; padding: 1rem; border: 3px solid var(\u002d\u002daccent); border-radius: .6rem; }\n.card strong { color: var(\u002d\u002daccent); }",
    "javascript": "const picker = document.querySelector( '.accent' );\npicker.addEventListener( 'input', () =\u003e {\n  document.documentElement.style.setProperty( '\u002d\u002daccent', picker.value );\n} );",
    "height": 300,
    "autoRun": true,
    "showConsole": false,
    "allowNetwork": false
  }
}
-->
#### Mix a documentation accent

**HTML**

```html
<label>Accent <input class="accent" type="color" value="#3858e9"></label>
<article class="card"><strong>Preview card</strong><p>Change the control to test a theme token.</p></article>
```

**CSS**

```css
:root { --accent: #3858e9; }
body { font: 16px/1.5 system-ui; padding: 1rem; }
label { display: flex; gap: .75rem; align-items: center; }
.card { margin-top: 1rem; padding: 1rem; border: 3px solid var(--accent); border-radius: .6rem; }
.card strong { color: var(--accent); }
```

**JavaScript**

```javascript
const picker = document.querySelector( '.accent' );
picker.addEventListener( 'input', () => {
  document.documentElement.style.setProperty( '--accent', picker.value );
} );
```
<!-- /docspress:block -->

### Accessible disclosure lab

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/code-playground",
  "attrs": {
    "title": "Build a keyboard-friendly disclosure",
    "html": "\u003cbutton class=\"toggle\" aria-expanded=\"false\" aria-controls=\"answer\"\u003eShow answer\u003c/button\u003e\n\u003cdiv id=\"answer\" hidden\u003eThe publishing dry run reports proposed changes without writing Pages.\u003c/div\u003e",
    "css": "body { font: 16px/1.5 system-ui; padding: 1rem; }\nbutton { padding: .65rem .9rem; }\n#answer { margin-top: 1rem; padding: 1rem; background: #f0f4ff; }",
    "javascript": "const button = document.querySelector( '.toggle' );\nconst answer = document.querySelector( '#answer' );\nbutton.addEventListener( 'click', () =\u003e {\n  const expanded = button.getAttribute( 'aria-expanded' ) === 'true';\n  button.setAttribute( 'aria-expanded', String( ! expanded ) );\n  button.textContent = expanded ? 'Show answer' : 'Hide answer';\n  answer.hidden = expanded;\n  console.log( expanded ? 'Collapsed' : 'Expanded' );\n} );",
    "height": 300,
    "autoRun": true,
    "showConsole": true,
    "allowNetwork": false
  }
}
-->
#### Build a keyboard-friendly disclosure

**HTML**

```html
<button class="toggle" aria-expanded="false" aria-controls="answer">Show answer</button>
<div id="answer" hidden>The publishing dry run reports proposed changes without writing Pages.</div>
```

**CSS**

```css
body { font: 16px/1.5 system-ui; padding: 1rem; }
button { padding: .65rem .9rem; }
#answer { margin-top: 1rem; padding: 1rem; background: #f0f4ff; }
```

**JavaScript**

```javascript
const button = document.querySelector( '.toggle' );
const answer = document.querySelector( '#answer' );
button.addEventListener( 'click', () => {
  const expanded = button.getAttribute( 'aria-expanded' ) === 'true';
  button.setAttribute( 'aria-expanded', String( ! expanded ) );
  button.textContent = expanded ? 'Show answer' : 'Hide answer';
  answer.hidden = expanded;
  console.log( expanded ? 'Collapsed' : 'Expanded' );
} );
```
<!-- /docspress:block -->

## Sandbox and output

The result iframe uses `sandbox="allow-scripts"` without same-origin access and sends no referrer. A Content Security Policy blocks network access unless the author enables `allowNetwork`. Run rebuilds the sandbox, Reset restores the authored source, and console calls and errors are relayed only from the active result. Console history is bounded to 100 lines.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "warning",
    "title": "Treat examples as public code",
    "content": "\u003cp\u003eThe iframe isolates the result from the parent page, but readers can inspect every source field. Never embed credentials, private endpoints, or sensitive data.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!WARNING]
>
> **Treat examples as public code**
>
> The iframe isolates the result from the parent page, but readers can inspect every source field. Never embed credentials, private endpoints, or sensitive data.
<!-- /docspress:block -->

Keep examples focused on one behavior, supply visible labels and keyboard interaction in the demo itself, and verify both the initial result and the Reset state.
