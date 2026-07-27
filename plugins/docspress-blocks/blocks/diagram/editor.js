( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
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

	registerBlockType( 'docspress/diagram', {
		apiVersion: 3,
		title: __( 'DocsPress: Diagram', 'docspress-blocks' ),
		description: __( 'A theme-native flow or sequence diagram from a compact relationship list.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'flowchart', 'docspress-blocks' ), __( 'sequence', 'docspress-blocks' ), __( 'architecture', 'docspress-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Publishing flow' },
			type: { type: 'string', default: 'flow' },
			source: { type: 'string', default: 'Markdown -> DocsPress: collect\nDocsPress -> WordPress: publish\nWordPress -> Reader: serve' },
			caption: { type: 'string', default: '' }
		},
		supports: { ...designSupports, align: [ 'wide' ] },
		edit: function DiagramEdit( { attributes, setAttributes } ) {
			const items = relationships( attributes.source );
			const blockProps = useBlockProps( {
				className: `docspress-diagram docspress-diagram--editor is-${ attributes.type } ${ presetClass }`,
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
						{ title: __( 'Diagram', 'docspress-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Diagram type', 'docspress-blocks' ),
							value: attributes.type,
							options: [
								{ label: __( 'Flow', 'docspress-blocks' ), value: 'flow' },
								{ label: __( 'Sequence', 'docspress-blocks' ), value: 'sequence' }
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
						{ className: 'docspress-diagram__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'docspress-diagram__eyebrow' }, attributes.type === 'sequence' ? __( 'Sequence', 'docspress-blocks' ) : __( 'Flow', 'docspress-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Diagram title…', 'docspress-blocks' )
							} )
						),
						el( 'span', null, `${ items.length } ${ __( 'relationships', 'docspress-blocks' ) }` )
					),
					el(
						'div',
						{ className: 'docspress-diagram__editor-source' },
						el( 'label', null, __( 'One relationship per line: Source -> Target: label', 'docspress-blocks' ) ),
						el( PlainText, {
							value: attributes.source,
							onChange: ( source ) => setAttributes( { source } ),
							placeholder: 'Reader -> API: GET /pages',
							'aria-label': __( 'Diagram relationships', 'docspress-blocks' )
						} )
					),
					el(
						'div',
						{ className: 'docspress-diagram__preview' },
						items.map( ( item, index ) => el(
							'div',
							{ className: 'docspress-diagram__preview-edge', key: `edge-${ index }` },
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
						placeholder: __( 'Optional caption…', 'docspress-blocks' )
					} )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.docspressBlocksEditor );
