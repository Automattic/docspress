( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
	const { Fragment, InspectorControls, PanelBody, RichText, TextareaControl, TextControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M4 5h6l2 2h8v12H4V5Zm4 6h8m-8 4h5' } )
	);

	function parseTree( source ) {
		const nodes = [];
		const path = [];

		String( source || '' ).replace( /\t/g, '  ' ).split( /\r?\n/ ).forEach( ( line ) => {
			const match = line.match( /^(\s*)(.*)$/ );
			const label = match ? match[ 2 ].trim() : '';
			if ( ! label ) return;

			const depth = Math.min( 12, Math.floor( ( match ? match[ 1 ].length : 0 ) / 2 ) );
			while ( path.length > depth ) path.pop();
			const parent = path.length ? path[ path.length - 1 ].children : nodes;
			const node = { label, folder: label.endsWith( '/' ), children: [] };
			parent.push( node );
			path.push( node );
		} );

		return nodes;
	}

	function treeIcon( folder ) {
		return folder
			? el(
				'svg',
				{ viewBox: '0 0 20 20', fill: 'none', stroke: 'currentColor', strokeWidth: 1.5, 'aria-hidden': true },
				el( 'path', { d: 'M2.5 5.5h5l1.5 2h8.5v7.5a1.5 1.5 0 0 1-1.5 1.5H4A1.5 1.5 0 0 1 2.5 15V5.5Z' } ),
				el( 'path', { d: 'M2.5 7.5V5A1.5 1.5 0 0 1 4 3.5h3l2 2h7A1.5 1.5 0 0 1 17.5 7v.5' } )
			)
			: el(
				'svg',
				{ viewBox: '0 0 20 20', fill: 'none', stroke: 'currentColor', strokeWidth: 1.5, 'aria-hidden': true },
				el( 'path', { d: 'M5 2.5h6l4 4V17H5V2.5Z' } ),
				el( 'path', { d: 'M11 2.5v4h4' } )
			);
	}

	function toggleIcon() {
		return el(
			'span',
			{ className: 'docspress-file-tree__toggle', 'aria-hidden': true },
			el(
				'svg',
				{ viewBox: '0 0 12 12', fill: 'none', stroke: 'currentColor', strokeWidth: 1.6 },
				el( 'path', { d: 'm4 2 4 4-4 4' } )
			)
		);
	}

	function renderNodes( nodes, collapsible, open, path = 'root' ) {
		return el(
			'ul',
			{ className: path === 'root' ? 'docspress-file-tree__entries' : 'docspress-file-tree__children' },
			...nodes.map( ( node, index ) => {
				const folder = node.folder || node.children.length > 0;
				const canToggle = folder && node.children.length > 0 && collapsible;
				const entry = [
					canToggle ? toggleIcon() : el( 'span', { className: 'docspress-file-tree__toggle', 'aria-hidden': true } ),
					el( 'span', { className: 'docspress-file-tree__icon', 'aria-hidden': true }, treeIcon( folder ) ),
					el( 'code', null, node.label )
				];
				const children = node.children.length ? renderNodes( node.children, collapsible, open, `${ path }-${ index }` ) : null;

				return el(
					'li',
					{
						className: `docspress-file-tree__item ${ folder ? 'is-folder' : 'is-file' }${ children ? ' has-children' : '' }`,
						key: `${ path }-${ index }-${ node.label }`
					},
					canToggle
						? el(
							'details',
							{ className: 'docspress-file-tree__folder', open: open ? true : undefined },
							el( 'summary', { className: 'docspress-file-tree__entry' }, ...entry ),
							children
						)
						: el(
							Fragment,
							null,
							el( 'div', { className: 'docspress-file-tree__entry' }, ...entry ),
							children
						)
				);
			} )
		);
	}

	registerBlockType( 'docspress/file-tree', {
		apiVersion: 3,
		title: __( 'DocsPress: File Tree', 'docspress-blocks' ),
		description: __( 'Show a repository or project structure with indentation-aware file and folder entries.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'files', 'docspress-blocks' ), __( 'folder', 'docspress-blocks' ), __( 'structure', 'docspress-blocks' ) ],
		attributes: {
			root: { type: 'string', default: 'project/' },
			tree: { type: 'string', default: 'docs/\n  getting-started.md\n  api/\n    endpoints.md\npackage.json' },
			caption: { type: 'string', default: '' },
			collapsible: { type: 'boolean', default: true },
			open: { type: 'boolean', default: true }
		},
		supports: designSupports,
		edit: function FileTreeEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `docspress-file-tree docspress-file-tree--editor ${ presetClass }`,
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
						{ title: __( 'File tree', 'docspress-blocks' ), initialOpen: true },
						el( TextControl, { label: __( 'Root label', 'docspress-blocks' ), value: attributes.root, onChange: ( root ) => setAttributes( { root } ) } ),
						el( TextareaControl, {
							label: __( 'Tree entries', 'docspress-blocks' ),
							help: __( 'Use two spaces per depth and end folder labels with a slash.', 'docspress-blocks' ),
							value: attributes.tree,
							rows: 12,
							onChange: ( tree ) => setAttributes( { tree } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Folder controls', 'docspress-blocks' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Allow readers to collapse folders', 'docspress-blocks' ),
							checked: attributes.collapsible,
							onChange: ( collapsible ) => setAttributes( { collapsible } )
						} ),
						attributes.collapsible && el( ToggleControl, {
							label: __( 'Expand folders by default', 'docspress-blocks' ),
							checked: attributes.open,
							onChange: ( open ) => setAttributes( { open } )
						} )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'docspress-file-tree__bar' },
						el( 'span', { className: 'docspress-file-tree__root-icon', 'aria-hidden': true }, '▱' ),
						el( 'code', null, attributes.root ),
						el( 'span', null, __( 'File tree', 'docspress-blocks' ) )
					),
					renderNodes( parseTree( attributes.tree ), attributes.collapsible, attributes.open ),
					el( RichText, {
						tagName: 'figcaption',
						className: 'docspress-file-tree__caption',
						value: attributes.caption,
						onChange: ( caption ) => setAttributes( { caption } ),
						allowedFormats: [],
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
