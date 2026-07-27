( function () {
	'use strict';

	const playgrounds = new Map();

	function parseConfig( block ) {
		try {
			return JSON.parse( block.querySelector( '.docspress-playground__config' ).textContent );
		} catch ( error ) {
			return {};
		}
	}

	function closingTagSafe( source, tag ) {
		return String( source || '' ).replace( new RegExp( `<\\/${ tag }`, 'gi' ), `<\\/${ tag.slice( 0, 1 ) }${ tag.slice( 1 ) }` );
	}

	function serializeLogValue( value ) {
		if ( typeof value === 'string' ) {
			return value;
		}
		try {
			return JSON.stringify( value, null, 2 );
		} catch ( error ) {
			return String( value );
		}
	}

	function documentSource( block, config ) {
		const html = block.querySelector( '[data-docspress-playground-html]' ).value;
		const css = closingTagSafe( block.querySelector( '[data-docspress-playground-css]' ).value, 'style' );
		const javascript = closingTagSafe( block.querySelector( '[data-docspress-playground-js]' ).value, 'script' );
		const csp = config.allowNetwork
			? "default-src 'none'; img-src data: blob: https: http:; font-src data: https: http:; style-src 'unsafe-inline' https: http:; script-src 'unsafe-inline' https: http:; connect-src https: http:;"
			: "default-src 'none'; img-src data: blob:; font-src data:; style-src 'unsafe-inline'; script-src 'unsafe-inline'; connect-src 'none';";
		const bridge = `
			(() => {
				const send = (level, values) => parent.postMessage({
					type: 'docspress-playground-console',
					id: ${ JSON.stringify( config.id ) },
					level,
					values
				}, '*');
				[ 'log', 'info', 'warn', 'error' ].forEach((level) => {
					const original = console[level];
					console[level] = (...values) => {
						send(level, values.map((value) => {
							if (typeof value === 'string') return value;
							try { return JSON.stringify(value, null, 2); } catch (error) { return String(value); }
						}));
						original.apply(console, values);
					};
				});
				window.addEventListener('error', (event) => send('error', [ event.message ]));
				window.addEventListener('unhandledrejection', (event) => send('error', [ String(event.reason) ]));
			})();
		`;
		return `<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Security-Policy" content="${ csp }">
<style>
:root { color-scheme: light dark; font-family: ui-sans-serif, system-ui, sans-serif; }
* { box-sizing: border-box; }
body { min-height: 100vh; margin: 0; padding: 1.25rem; background: Canvas; color: CanvasText; }
${ css }
</style>
</head>
<body>
${ html }
<script>${ bridge }<\/script>
<script>${ javascript }<\/script>
</body>
</html>`;
	}

	function appendConsole( block, level, values ) {
		const output = block.querySelector( '[data-docspress-playground-console]' );
		if ( ! output ) {
			return;
		}
		if ( output.querySelector( '.is-muted' ) ) {
			output.innerHTML = '';
		}
		if ( output.children.length >= 100 ) {
			output.firstElementChild.remove();
		}
		const line = document.createElement( 'span' );
		line.className = `is-${ level }`;
		line.textContent = values.map( serializeLogValue ).join( ' ' );
		output.appendChild( line );
	}

	function run( block, config ) {
		const frame = block.querySelector( '[data-docspress-playground-frame]' );
		const status = block.querySelector( '[data-docspress-playground-status]' );
		const output = block.querySelector( '[data-docspress-playground-console]' );
		if ( output ) {
			output.innerHTML = '<span class="is-muted">Running…</span>';
		}
		status.textContent = 'Running…';
		frame.srcdoc = documentSource( block, config );
		frame.addEventListener( 'load', function loaded() {
			status.textContent = 'Rendered';
			frame.removeEventListener( 'load', loaded );
		} );
	}

	function initializePlayground( block ) {
		if ( block.dataset.docspressReady ) {
			return;
		}
		block.dataset.docspressReady = 'true';
		const config = parseConfig( block );
		const initial = {
			html: config.html || '',
			css: config.css || '',
			javascript: config.javascript || ''
		};
		playgrounds.set( config.id, { block, frame: block.querySelector( '[data-docspress-playground-frame]' ) } );
		block.querySelector( '[data-docspress-playground-run]' ).addEventListener( 'click', function () {
			run( block, config );
		} );
		block.querySelector( '[data-docspress-playground-reset]' ).addEventListener( 'click', function () {
			block.querySelector( '[data-docspress-playground-html]' ).value = initial.html;
			block.querySelector( '[data-docspress-playground-css]' ).value = initial.css;
			block.querySelector( '[data-docspress-playground-js]' ).value = initial.javascript;
			run( block, config );
		} );
		const clear = block.querySelector( '[data-docspress-playground-clear]' );
		if ( clear ) {
			clear.addEventListener( 'click', function () {
				block.querySelector( '[data-docspress-playground-console]' ).innerHTML = '<span class="is-muted">Console cleared.</span>';
			} );
		}
		if ( config.autoRun ) {
			run( block, config );
		}
	}

	window.addEventListener( 'message', function ( event ) {
		const data = event.data;
		if ( ! data || data.type !== 'docspress-playground-console' || ! playgrounds.has( data.id ) ) {
			return;
		}
		const playground = playgrounds.get( data.id );
		if ( event.source !== playground.frame.contentWindow ) {
			return;
		}
		appendConsole( playground.block, data.level || 'log', Array.isArray( data.values ) ? data.values : [] );
	} );

	function initialize() {
		document.querySelectorAll( '[data-docspress-playground]' ).forEach( initializePlayground );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initialize );
	} else {
		initialize();
	}
} )();
