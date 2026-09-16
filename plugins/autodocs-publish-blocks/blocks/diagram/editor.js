( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, PlainText, RichText, SelectControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'rect', { x: 3, y: 4, width: 6, height: 5, rx: 1 } ),
		el( 'rect', { x: 15, y: 15, width: 6, height: 5, rx: 1 } ),
		el( 'path', { d: 'M9 6.5h5a3 3 0 0 1 3 3V15m-3-2 3 2 3-2' } )
	);

	function relationships( source ) {
		return String( source || '' ).split( /\r?\n/ ).map( ( line ) => {
			const match = line.match( /^(.+?)\s*(?:-->|->)\s*(.+?)(?:\s*:\s*(.+))?$/ );
			return match ? { from: match[ 1 ].trim(), to: match[ 2 ].trim(), label: ( match[ 3 ] || '' ).trim() } : null;
		} ).filter( Boolean ).slice( 0, 8 );
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/diagram', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Diagram', 'autodocs-publish-blocks' ),
		description: __( 'A theme-native flow or sequence diagram from a compact relationship list.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'flowchart', 'autodocs-publish-blocks' ), __( 'sequence', 'autodocs-publish-blocks' ), __( 'architecture', 'autodocs-publish-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Publishing flow' },
			type: { type: 'string', default: 'flow' },
			source: { type: 'string', default: 'Markdown -> Autodocs Publish: collect\nAutodocs Publish -> WordPress: publish\nWordPress -> Reader: serve' },
			caption: { type: 'string', default: '' }
		},
		supports: { ...designSupports, align: [ 'wide' ] },
		edit: function DiagramEdit( { attributes, setAttributes } ) {
			const items = relationships( attributes.source );
			const blockProps = useBlockProps( {
				className: `autodocs-publish-diagram autodocs-publish-diagram--editor is-${ attributes.type } ${ presetClass }`,
				style: themeStyle
			} );
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Diagram', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Diagram type', 'autodocs-publish-blocks' ),
							value: attributes.type,
							options: [
								{ label: __( 'Flow', 'autodocs-publish-blocks' ), value: 'flow' },
								{ label: __( 'Sequence', 'autodocs-publish-blocks' ), value: 'sequence' }
							],
							onChange: ( type ) => setAttributes( { type } )
						} )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-diagram__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'autodocs-publish-diagram__eyebrow' }, attributes.type === 'sequence' ? __( 'Sequence', 'autodocs-publish-blocks' ) : __( 'Flow', 'autodocs-publish-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Diagram title…', 'autodocs-publish-blocks' )
							} )
						),
						el( 'span', null, `${ items.length } ${ __( 'relationships', 'autodocs-publish-blocks' ) }` )
					),
					el(
						'div',
						{ className: 'autodocs-publish-diagram__editor-source' },
						el( 'label', null, __( 'One relationship per line: Source -> Target: label', 'autodocs-publish-blocks' ) ),
						el( PlainText, {
							value: attributes.source,
							onChange: ( source ) => setAttributes( { source } ),
							placeholder: 'Reader -> API: GET /pages',
							'aria-label': __( 'Diagram relationships', 'autodocs-publish-blocks' )
						} )
					),
					el(
						'div',
						{ className: 'autodocs-publish-diagram__preview' },
						items.map( ( item, index ) => el(
							'div',
							{ className: 'autodocs-publish-diagram__preview-edge', key: `edge-${ index }` },
							el( 'span', null, item.from ),
							el( 'i', { 'aria-hidden': true }, '→' ),
							el( 'span', null, item.to ),
							item.label && el( 'b', null, item.label )
						) )
					),
					el( RichText, {
						tagName: 'figcaption',
						value: attributes.caption,
						onChange: ( caption ) => setAttributes( { caption } ),
						allowedFormats: [ 'core/bold', 'core/italic', 'core/code' ],
						placeholder: __( 'Optional caption…', 'autodocs-publish-blocks' )
					} )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
