( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, PlainText, RangeControl, SelectControl, TextControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M4 7h16M4 17h16M8 4 4 7l4 3m8 4 4 3-4 3' } )
	);

	function PayloadEditor( { label, value, placeholder, onChange, modifier, format } ) {
		const formatLabel = format === 'headers' ? __( 'Key: value', 'autodocs-publish-blocks' ) : format.toUpperCase();
		return el(
			'section',
			{ className: `autodocs-publish-api__payload autodocs-publish-api__payload--${ modifier }`, 'data-autodocs-publish-api-format': format },
			el(
				'div',
				{ className: 'autodocs-publish-api__payload-label' },
				el( 'span', null, label ),
				el( 'span', { className: 'autodocs-publish-api__format' }, formatLabel )
			),
			el( PlainText, { value, onChange, placeholder, 'aria-label': label } )
		);
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/api-request', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: API Request / Response', 'autodocs-publish-blocks' ),
		description: __( 'Document an endpoint, request headers and body, and the exact response in one structured example.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'REST', 'autodocs-publish-blocks' ), __( 'endpoint', 'autodocs-publish-blocks' ), __( 'response', 'autodocs-publish-blocks' ) ],
		attributes: {
			method: { type: 'string', default: 'GET' },
			endpoint: { type: 'string', default: '/wp-json/wp/v2/pages' },
			headers: { type: 'string', default: 'Accept: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN' },
			requestBody: { type: 'string', default: '' },
			requestBodyFormat: { type: 'string', default: 'json' },
			responseStatus: { type: 'string', default: '200 OK' },
			responseBody: { type: 'string', default: '{\n  "id": 42,\n  "slug": "getting-started"\n}' },
			responseBodyFormat: { type: 'string', default: 'json' },
			runnable: { type: 'boolean', default: false },
			editable: { type: 'boolean', default: true },
			allowUnsafe: { type: 'boolean', default: false },
			baseUrl: { type: 'string', default: '' },
			allowedOrigins: { type: 'string', default: '' },
			timeout: { type: 'number', default: 10000 }
		},
		supports: designSupports,
		edit: function ApiRequestEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-api autodocs-publish-api--editor ${ presetClass }`,
				style: themeStyle,
				'data-method': attributes.method.toLowerCase()
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'API exchange', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Method', 'autodocs-publish-blocks' ),
							value: attributes.method,
							options: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ].map( ( method ) => ( { label: method, value: method } ) ),
							onChange: ( method ) => setAttributes( { method } )
						} ),
						el( TextControl, {
							label: __( 'Endpoint', 'autodocs-publish-blocks' ),
							value: attributes.endpoint,
							onChange: ( endpoint ) => setAttributes( { endpoint } )
						} ),
						el( TextControl, {
							label: __( 'Response status', 'autodocs-publish-blocks' ),
							value: attributes.responseStatus,
							onChange: ( responseStatus ) => setAttributes( { responseStatus } )
						} ),
						el( SelectControl, {
							label: __( 'Request body display', 'autodocs-publish-blocks' ),
							value: attributes.requestBodyFormat,
							options: [
								{ label: __( 'JSON — syntax highlighted', 'autodocs-publish-blocks' ), value: 'json' },
								{ label: __( 'Raw — unformatted text', 'autodocs-publish-blocks' ), value: 'raw' }
							],
							onChange: ( requestBodyFormat ) => setAttributes( { requestBodyFormat } )
						} ),
						el( SelectControl, {
							label: __( 'Response body display', 'autodocs-publish-blocks' ),
							value: attributes.responseBodyFormat,
							options: [
								{ label: __( 'JSON — syntax highlighted', 'autodocs-publish-blocks' ), value: 'json' },
								{ label: __( 'Raw — unformatted text', 'autodocs-publish-blocks' ), value: 'raw' }
							],
							onChange: ( responseBodyFormat ) => setAttributes( { responseBodyFormat } )
						} ),
						el( ToggleControl, {
							label: __( 'Runnable request', 'autodocs-publish-blocks' ),
							help: __( 'Let readers execute this request directly from their browser.', 'autodocs-publish-blocks' ),
							checked: attributes.runnable,
							onChange: ( runnable ) => setAttributes( { runnable } )
						} )
					),
					attributes.runnable && el(
						PanelBody,
						{ title: __( 'Runner safety', 'autodocs-publish-blocks' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Readers can edit inputs', 'autodocs-publish-blocks' ),
							checked: attributes.editable,
							onChange: ( editable ) => setAttributes( { editable } )
						} ),
						el( ToggleControl, {
							label: __( 'Allow mutating methods', 'autodocs-publish-blocks' ),
							help: __( 'POST, PUT, PATCH, and DELETE still require a second reader confirmation.', 'autodocs-publish-blocks' ),
							checked: attributes.allowUnsafe,
							onChange: ( allowUnsafe ) => setAttributes( { allowUnsafe } )
						} ),
						el( TextControl, {
							label: __( 'Base URL', 'autodocs-publish-blocks' ),
							help: __( 'Leave empty for the current site. Cross-origin hosts must also be allowlisted.', 'autodocs-publish-blocks' ),
							value: attributes.baseUrl,
							onChange: ( baseUrl ) => setAttributes( { baseUrl } )
						} ),
						el( TextControl, {
							label: __( 'Allowed origins', 'autodocs-publish-blocks' ),
							help: __( 'Comma-separated exact origins, for example https://api.example.com.', 'autodocs-publish-blocks' ),
							value: attributes.allowedOrigins,
							onChange: ( allowedOrigins ) => setAttributes( { allowedOrigins } )
						} ),
						el( RangeControl, {
							label: __( 'Timeout in milliseconds', 'autodocs-publish-blocks' ),
							value: attributes.timeout,
							min: 1000,
							max: 30000,
							step: 1000,
							onChange: ( timeout ) => setAttributes( { timeout } )
						} )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-api__request-line' },
						el( 'span', { className: 'autodocs-publish-api__eyebrow' }, __( 'Request', 'autodocs-publish-blocks' ) ),
						el( 'span', { className: 'autodocs-publish-api__method' }, attributes.method ),
						el( 'code', { className: 'autodocs-publish-api__endpoint' }, attributes.endpoint || __( 'Endpoint…', 'autodocs-publish-blocks' ) )
					),
					el( PayloadEditor, {
						label: __( 'Headers', 'autodocs-publish-blocks' ),
						value: attributes.headers,
						placeholder: 'Accept: application/json',
						modifier: 'headers',
						format: 'headers',
						onChange: ( headers ) => setAttributes( { headers } )
					} ),
					el( PayloadEditor, {
						label: __( 'Request body', 'autodocs-publish-blocks' ),
						value: attributes.requestBody,
						placeholder: __( 'Optional request body…', 'autodocs-publish-blocks' ),
						modifier: 'request',
						format: attributes.requestBodyFormat,
						onChange: ( requestBody ) => setAttributes( { requestBody } )
					} ),
					attributes.runnable && el(
						'div',
						{ className: 'autodocs-publish-api__runner autodocs-publish-api__runner--preview' },
						el(
							'div',
							{ className: 'autodocs-publish-api__runner-heading' },
							el( 'div', null, el( 'span', { className: 'autodocs-publish-api__eyebrow' }, __( 'Try it', 'autodocs-publish-blocks' ) ), el( 'strong', null, __( 'Run this request', 'autodocs-publish-blocks' ) ) ),
							el( 'span', { className: 'autodocs-publish-api__runner-policy' }, __( 'Credentials omitted', 'autodocs-publish-blocks' ) )
						),
						el(
							'div',
							{ className: 'autodocs-publish-api__runner-actions' },
							el( 'button', { className: 'autodocs-publish-api__run', type: 'button', disabled: true }, el( 'span', { className: 'autodocs-publish-api__run-dot', 'aria-hidden': true } ), el( 'b', null, `${ __( 'Run', 'autodocs-publish-blocks' ) } ${ attributes.method }` ) ),
							el( 'span', { className: 'autodocs-publish-api__runner-status' }, __( 'Interactive on the published page', 'autodocs-publish-blocks' ) )
						)
					),
					el(
						'section',
						{ className: 'autodocs-publish-api__response' },
						el(
							'div',
							{ className: 'autodocs-publish-api__response-line' },
							el( 'span', { className: 'autodocs-publish-api__eyebrow' }, __( 'Response', 'autodocs-publish-blocks' ) ),
							el( 'span', { className: 'autodocs-publish-api__status' }, attributes.responseStatus )
						),
						el( PayloadEditor, {
							label: __( 'Body', 'autodocs-publish-blocks' ),
							value: attributes.responseBody,
							placeholder: '{\n  "ok": true\n}',
							modifier: 'response',
							format: attributes.responseBodyFormat,
							onChange: ( responseBody ) => setAttributes( { responseBody } )
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
