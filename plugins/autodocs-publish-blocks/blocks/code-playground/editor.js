( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, PlainText, RangeControl, TextControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'rect', { x: 3, y: 4, width: 18, height: 16, rx: 2 } ),
		el( 'path', { d: 'M3 9h18M8 13l-2 2 2 2m4-4 2 2-2 2m5-5v6' } )
	);

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/code-playground', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Live Code Playground', 'autodocs-publish-blocks' ),
		description: __( 'An editable HTML, CSS, and JavaScript sandbox with a live result and console.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'sandbox', 'autodocs-publish-blocks' ), __( 'live code', 'autodocs-publish-blocks' ), __( 'preview', 'autodocs-publish-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Live example' },
			html: { type: 'string', default: '<button class="demo-button">Publish docs</button>' },
			css: { type: 'string', default: '.demo-button {\n  padding: 0.75rem 1rem;\n  border: 0;\n  border-radius: 0.4rem;\n  background: #3858e9;\n  color: white;\n  font: inherit;\n}' },
			javascript: { type: 'string', default: "document.querySelector( '.demo-button' ).addEventListener( 'click', () => {\n  console.log( 'Documentation published' );\n} );" },
			height: { type: 'number', default: 320 },
			autoRun: { type: 'boolean', default: true },
			showConsole: { type: 'boolean', default: true },
			allowNetwork: { type: 'boolean', default: false }
		},
		supports: { ...designSupports, align: [ 'wide' ] },
		edit: function CodePlaygroundEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-playground autodocs-publish-playground--editor ${ presetClass }`,
				style: { ...themeStyle, '--autodocs-publish-playground-height': `${ attributes.height }px` }
			} );
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Playground', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Title', 'autodocs-publish-blocks' ),
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } )
						} ),
						el( RangeControl, {
							label: __( 'Result height', 'autodocs-publish-blocks' ),
							value: attributes.height,
							min: 180,
							max: 720,
							step: 20,
							onChange: ( height ) => setAttributes( { height } )
						} ),
						el( ToggleControl, {
							label: __( 'Run automatically', 'autodocs-publish-blocks' ),
							checked: attributes.autoRun,
							onChange: ( autoRun ) => setAttributes( { autoRun } )
						} ),
						el( ToggleControl, {
							label: __( 'Show console', 'autodocs-publish-blocks' ),
							checked: attributes.showConsole,
							onChange: ( showConsole ) => setAttributes( { showConsole } )
						} ),
						el( ToggleControl, {
							label: __( 'Allow network requests', 'autodocs-publish-blocks' ),
							help: __( 'Off by default. The preview otherwise blocks fetch, images, fonts, and external scripts.', 'autodocs-publish-blocks' ),
							checked: attributes.allowNetwork,
							onChange: ( allowNetwork ) => setAttributes( { allowNetwork } )
						} )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-playground__bar' },
						el(
							'div',
							{ className: 'autodocs-publish-playground__identity' },
							el( 'span', { className: 'autodocs-publish-playground__lights', 'aria-hidden': true }, el( 'i' ), el( 'i' ), el( 'i' ) ),
							el( 'div', null, el( 'span', { className: 'autodocs-publish-playground__eyebrow' }, __( 'Browser sandbox', 'autodocs-publish-blocks' ) ), el( 'strong', null, attributes.title || __( 'Live example', 'autodocs-publish-blocks' ) ) )
						),
						el( 'span', { className: 'autodocs-publish-playground__policy' }, attributes.allowNetwork ? __( 'Network enabled', 'autodocs-publish-blocks' ) : __( 'Network blocked', 'autodocs-publish-blocks' ) )
					),
					el(
						'div',
						{ className: 'autodocs-publish-playground__editors autodocs-publish-playground__editors--editor' },
						[
							[ 'HTML', 'html', attributes.html ],
							[ 'CSS', 'css', attributes.css ],
							[ 'JavaScript', 'javascript', attributes.javascript ]
						].map( ( item ) => el(
							'label',
							{ key: item[ 1 ] },
							el( 'span', null, item[ 0 ] ),
							el( PlainText, {
								value: item[ 2 ],
								onChange: ( value ) => setAttributes( { [ item[ 1 ] ]: value } ),
								'aria-label': item[ 0 ]
							} )
						) )
					),
					el(
						'div',
						{ className: 'autodocs-publish-playground__editor-notice' },
						el( 'strong', null, __( 'Result preview', 'autodocs-publish-blocks' ) ),
						el( 'span', null, __( 'Run the sandbox on the published page to see the live result and console.', 'autodocs-publish-blocks' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
