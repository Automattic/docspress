( function () {
	'use strict';

	const unsafeMethods = [ 'POST', 'PUT', 'PATCH', 'DELETE' ];
	const blockedHeaders = [ 'cookie', 'host', 'origin', 'content-length', 'proxy-authorization', 'proxy-connection' ];

	function parseConfig( runner ) {
		const source = runner.querySelector( '.docspress-api__config' );
		try {
			return JSON.parse( source ? source.textContent : '{}' );
		} catch ( error ) {
			return {};
		}
	}

	function parseHeaders( source ) {
		const headers = new Headers();
		String( source || '' ).split( /\r?\n/ ).forEach( function ( line ) {
			const separator = line.indexOf( ':' );
			if ( separator < 1 ) {
				return;
			}
			const name = line.slice( 0, separator ).trim();
			const value = line.slice( separator + 1 ).trim();
			const lowerName = name.toLowerCase();
			if ( ! name || blockedHeaders.includes( lowerName ) || lowerName.startsWith( 'sec-' ) ) {
				return;
			}
			headers.append( name, value );
		} );
		return headers;
	}

	function allowedUrl( endpoint, config ) {
		const base = config.baseUrl || window.location.origin;
		const url = new URL( endpoint, base.endsWith( '/' ) ? base : `${ base }/` );
		const origins = String( config.allowedOrigins || '' )
			.split( ',' )
			.map( function ( origin ) {
				try {
					return new URL( origin.trim() ).origin;
				} catch ( error ) {
					return '';
				}
			} )
			.filter( Boolean );
		if ( url.protocol !== 'https:' && url.protocol !== 'http:' ) {
			throw new Error( 'Only HTTP and HTTPS requests are supported.' );
		}
		if ( url.origin !== window.location.origin && ! origins.includes( url.origin ) ) {
			throw new Error( `Origin not allowed: ${ url.origin }` );
		}
		return url;
	}

	function curlCommand( runner, config ) {
		const endpoint = runner.querySelector( '[data-docspress-api-endpoint-input]' ).value;
		const headers = runner.querySelector( '[data-docspress-api-headers-input]' ).value;
		const body = runner.querySelector( '[data-docspress-api-body-input]' ).value;
		const url = allowedUrl( endpoint, config ).toString();
		const parts = [ `curl --request ${ config.method || 'GET' }`, `'${ url.replace( /'/g, "'\\''" ) }'` ];
		String( headers ).split( /\r?\n/ ).filter( Boolean ).forEach( function ( header ) {
			parts.push( `--header '${ header.replace( /'/g, "'\\''" ) }'` );
		} );
		if ( body && ! [ 'GET', 'HEAD' ].includes( config.method ) ) {
			parts.push( `--data '${ body.replace( /'/g, "'\\''" ) }'` );
		}
		return parts.join( ' \\\n  ' );
	}

	function copyText( text ) {
		if ( navigator.clipboard && window.isSecureContext ) {
			return navigator.clipboard.writeText( text );
		}
		const field = document.createElement( 'textarea' );
		field.value = text;
		field.style.position = 'fixed';
		field.style.opacity = '0';
		document.body.appendChild( field );
		field.select();
		document.execCommand( 'copy' );
		field.remove();
		return Promise.resolve();
	}

	function formatBody( text, contentType ) {
		if ( contentType.includes( 'json' ) || /^[\s]*[\[{]/.test( text ) ) {
			try {
				return { text: JSON.stringify( JSON.parse( text ), null, 2 ), format: 'json' };
			} catch ( error ) {
				// Keep the original response when it looks like JSON but is not valid.
			}
		}
		return { text, format: 'raw' };
	}

	function prepareScrollableResponse( block ) {
		const pre = block.querySelector( '[data-docspress-api-response-body] .docspress-api__payload--response pre' );
		if ( ! pre ) {
			return;
		}
		window.requestAnimationFrame( function () {
			if ( pre.scrollHeight > pre.clientHeight + 1 ) {
				pre.tabIndex = 0;
				pre.setAttribute( 'aria-label', 'Scrollable API response body' );
			} else {
				pre.removeAttribute( 'tabindex' );
				pre.removeAttribute( 'aria-label' );
			}
		} );
	}

	function renderResponse( block, response, body, elapsed ) {
		const responseBody = block.querySelector( '[data-docspress-api-response-body]' );
		const responseHeaders = block.querySelector( '[data-docspress-api-response-headers]' );
		const responseHeaderCode = block.querySelector( '[data-docspress-api-response-header-code]' );
		const label = block.querySelector( '[data-docspress-api-response-label]' );
		const status = block.querySelector( '[data-docspress-api-response-status]' );
		const meta = block.querySelector( '[data-docspress-api-response-meta]' );
		const contentType = response.headers.get( 'content-type' ) || '';
		const formatted = formatBody( body, contentType );
		const bytes = new Blob( [ body ] ).size;
		label.textContent = 'Live response';
		status.textContent = `${ response.status } ${ response.statusText }`.trim();
		meta.textContent = `${ elapsed } ms · ${ bytes.toLocaleString() } B`;
		meta.hidden = false;
		const headerLines = [];
		response.headers.forEach( function ( value, name ) {
			headerLines.push( `${ name }: ${ value }` );
		} );
		responseHeaderCode.textContent = headerLines.sort().join( '\n' );
		responseHeaders.hidden = headerLines.length === 0;
		responseBody.innerHTML = '';
		const section = document.createElement( 'section' );
		section.className = 'docspress-api__payload docspress-api__payload--response';
		section.dataset.docspressApiFormat = formatted.format;
		const heading = document.createElement( 'div' );
		heading.className = 'docspress-api__payload-label';
		heading.innerHTML = `<span>Body</span><span class="docspress-api__format">${ formatted.format.toUpperCase() }</span>`;
		const pre = document.createElement( 'pre' );
		const code = document.createElement( 'code' );
		code.textContent = formatted.text || 'No response body';
		pre.appendChild( code );
		section.append( heading, pre );
		responseBody.appendChild( section );
		if ( window.docspressBlocksView ) {
			window.docspressBlocksView.enhanceApiPayloads( responseBody );
		}
		prepareScrollableResponse( block );
	}

	function setStatus( runner, message, state ) {
		const status = runner.querySelector( '[data-docspress-api-runner-status]' );
		status.textContent = message;
		status.dataset.state = state || '';
	}

	function initializeRunner( runner ) {
		if ( runner.dataset.docspressApiReady ) {
			return;
		}
		runner.dataset.docspressApiReady = 'true';
		const config = parseConfig( runner );
		const block = runner.closest( '.wp-block-docspress-api-request' );
		const runButton = runner.querySelector( '[data-docspress-api-run]' );
		const resetButton = runner.querySelector( '[data-docspress-api-reset]' );
		const copyButton = runner.querySelector( '[data-docspress-api-copy-curl]' );
		const endpointInput = runner.querySelector( '[data-docspress-api-endpoint-input]' );
		const headersInput = runner.querySelector( '[data-docspress-api-headers-input]' );
		const bodyInput = runner.querySelector( '[data-docspress-api-body-input]' );
		const initialResponse = block.querySelector( '[data-docspress-api-response-body]' ).innerHTML;
		const initialStatus = block.querySelector( '[data-docspress-api-response-status]' ).textContent;
		let controller = null;
		let confirmationTimer = null;

		prepareScrollableResponse( block );

		runButton.addEventListener( 'click', async function () {
			const method = String( config.method || 'GET' ).toUpperCase();
			if ( unsafeMethods.includes( method ) && ! config.allowUnsafe ) {
				setStatus( runner, `${ method } is disabled by the author.`, 'error' );
				return;
			}
			if ( unsafeMethods.includes( method ) && runButton.dataset.confirmed !== 'true' ) {
				runButton.dataset.confirmed = 'true';
				runButton.querySelector( 'b' ).textContent = `Confirm ${ method }`;
				setStatus( runner, 'Click again to confirm the mutating request.', 'warning' );
				clearTimeout( confirmationTimer );
				confirmationTimer = setTimeout( function () {
					delete runButton.dataset.confirmed;
					runButton.querySelector( 'b' ).textContent = `Run ${ method }`;
					setStatus( runner, 'Ready', '' );
				}, 6000 );
				return;
			}

			clearTimeout( confirmationTimer );
			delete runButton.dataset.confirmed;
			runButton.querySelector( 'b' ).textContent = 'Cancel';
			runButton.classList.add( 'is-running' );
			setStatus( runner, 'Sending request…', 'running' );
			controller = new AbortController();
			const timeout = setTimeout( function () {
				controller.abort( 'timeout' );
			}, Number( config.timeout ) || 10000 );
			const started = performance.now();

			try {
				const url = allowedUrl( endpointInput.value, config );
				const options = {
					method,
					headers: parseHeaders( headersInput.value ),
					credentials: 'omit',
					mode: url.origin === window.location.origin ? 'same-origin' : 'cors',
					redirect: 'follow',
					signal: controller.signal
				};
				if ( bodyInput.value && ! [ 'GET', 'HEAD' ].includes( method ) ) {
					options.body = bodyInput.value;
				}
				const response = await fetch( url, options );
				const responseText = await response.text();
				const elapsed = Math.max( 1, Math.round( performance.now() - started ) );
				renderResponse( block, response, responseText, elapsed );
				setStatus( runner, response.ok ? 'Request completed.' : 'Request completed with an error status.', response.ok ? 'success' : 'warning' );
			} catch ( error ) {
				const timedOut = controller && controller.signal.aborted;
				setStatus( runner, timedOut ? 'Request timed out or was cancelled.' : error.message, 'error' );
			} finally {
				clearTimeout( timeout );
				controller = null;
				runButton.classList.remove( 'is-running' );
				runButton.querySelector( 'b' ).textContent = `Run ${ method }`;
			}
		} );

		resetButton.addEventListener( 'click', function () {
			if ( controller ) {
				controller.abort( 'reset' );
			}
			endpointInput.value = config.endpoint || '';
			headersInput.value = config.headers || '';
			bodyInput.value = config.body || '';
			block.querySelector( '[data-docspress-api-response-body]' ).innerHTML = initialResponse;
			block.querySelector( '[data-docspress-api-response-label]' ).textContent = 'Example response';
			block.querySelector( '[data-docspress-api-response-status]' ).textContent = initialStatus;
			block.querySelector( '[data-docspress-api-response-meta]' ).hidden = true;
			block.querySelector( '[data-docspress-api-response-headers]' ).hidden = true;
			block.querySelector( '[data-docspress-api-response-header-code]' ).textContent = '';
			setStatus( runner, 'Reset to the documented example.', 'success' );
			if ( window.docspressBlocksView ) {
				window.docspressBlocksView.enhanceApiPayloads( block );
			}
			prepareScrollableResponse( block );
		} );

		copyButton.addEventListener( 'click', function () {
			try {
				copyText( curlCommand( runner, config ) ).then( function () {
					setStatus( runner, 'cURL command copied.', 'success' );
				} );
			} catch ( error ) {
				setStatus( runner, error.message, 'error' );
			}
		} );
	}

	function initialize() {
		document.querySelectorAll( '[data-docspress-api-runner]' ).forEach( initializeRunner );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initialize );
	} else {
		initialize();
	}
} )();
