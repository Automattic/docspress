( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
	const {
		CodeSettings,
		Button,
		Fragment,
		InspectorControls,
		PanelBody,
		PlainText,
		RichText,
		SelectControl,
		TextControl,
		TextareaControl,
		__,
		designSupports,
		el,
		languages,
		presetClass,
		themeStyle,
		useBlockProps
	} = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'm8 9-3 3 3 3M16 9l3 3-3 3M14 5l-4 14' } )
	);

	registerBlockType( 'docspress/colorful-code', {
		apiVersion: 3,
		title: __( 'DocsPress: Colorful Code', 'docspress-blocks' ),
		description: __( 'A polished code sample that follows the active theme preset, with line highlights and one-click copy.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'syntax', 'docspress-blocks' ), __( 'snippet', 'docspress-blocks' ), __( 'developer', 'docspress-blocks' ) ],
		attributes: {
			language: { type: 'string', default: 'javascript' },
			filename: { type: 'string', default: '' },
			code: { type: 'string', default: 'const hello = "DocsPress";\nconsole.log( hello );' },
			highlightedLines: { type: 'string', default: '' },
			showLineNumbers: { type: 'boolean', default: true },
			caption: { type: 'string', default: '' },
			diffMode: { type: 'string', default: 'none' },
			copyMode: { type: 'string', default: 'all' },
			annotations: { type: 'array', default: [] }
		},
		supports: designSupports,
		edit: function ColorfulCodeEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `docspress-code docspress-code--editor ${ presetClass }`,
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
						{ title: __( 'Snippet', 'docspress-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Language', 'docspress-blocks' ),
							value: attributes.language,
							options: languages,
							onChange: ( language ) => setAttributes( { language } )
						} ),
						el( TextControl, {
							label: __( 'Filename or label', 'docspress-blocks' ),
							value: attributes.filename,
							onChange: ( filename ) => setAttributes( { filename } )
						} )
					),
					el( CodeSettings, { attributes, setAttributes } )
					,
					el(
						PanelBody,
						{ title: __( 'Diff and annotations', 'docspress-blocks' ), initialOpen: false },
						el( SelectControl, {
							label: __( 'Code presentation', 'docspress-blocks' ),
							value: attributes.diffMode,
							options: [
								{ label: __( 'Standard code', 'docspress-blocks' ), value: 'none' },
								{ label: __( 'Unified diff', 'docspress-blocks' ), value: 'unified' }
							],
							onChange: ( diffMode ) => setAttributes( { diffMode } )
						} ),
						attributes.diffMode === 'unified' && el( SelectControl, {
							label: __( 'Copy button', 'docspress-blocks' ),
							value: attributes.copyMode,
							options: [
								{ label: __( 'Copy the complete diff', 'docspress-blocks' ), value: 'all' },
								{ label: __( 'Copy the final version', 'docspress-blocks' ), value: 'final' }
							],
							onChange: ( copyMode ) => setAttributes( { copyMode } )
						} ),
						attributes.annotations.map( ( annotation, index ) => el(
							'div',
							{ className: 'docspress-code__annotation-editor', key: `annotation-${ index }` },
							el( TextControl, {
								label: __( 'Line', 'docspress-blocks' ),
								type: 'number',
								min: 1,
								value: annotation.line || 1,
								onChange: ( line ) => {
									const annotations = attributes.annotations.slice();
									annotations[ index ] = { ...annotation, line: Math.max( 1, parseInt( line, 10 ) || 1 ) };
									setAttributes( { annotations } );
								}
							} ),
							el( TextareaControl, {
								label: __( 'Explanation', 'docspress-blocks' ),
								value: annotation.content || '',
								onChange: ( content ) => {
									const annotations = attributes.annotations.slice();
									annotations[ index ] = { ...annotation, content };
									setAttributes( { annotations } );
								}
							} ),
							el( Button, {
								isDestructive: true,
								variant: 'tertiary',
								onClick: () => setAttributes( { annotations: attributes.annotations.filter( ( _, itemIndex ) => itemIndex !== index ) } )
							}, __( 'Remove annotation', 'docspress-blocks' ) )
						) ),
						attributes.annotations.length < 20 && el( Button, {
							variant: 'secondary',
							onClick: () => setAttributes( { annotations: [ ...attributes.annotations, { line: 1, content: '' } ] } )
						}, __( 'Add annotation', 'docspress-blocks' ) )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'docspress-code__surface' + ( attributes.showLineNumbers ? ' has-line-numbers' : '' ) + ( attributes.diffMode === 'unified' ? ' is-diff' : '' ) },
						el(
							'div',
							{ className: 'docspress-code__bar' },
							el( 'span', { className: 'docspress-code__language' }, attributes.language ),
							el( 'span', { className: 'docspress-code__filename' }, attributes.filename || attributes.language )
						),
						el( PlainText, {
							className: 'docspress-code__editor',
							value: attributes.code,
							onChange: ( code ) => setAttributes( { code } ),
							placeholder: __( 'Paste or write code…', 'docspress-blocks' ),
							'aria-label': __( 'Code', 'docspress-blocks' )
						} )
					),
					attributes.annotations.length > 0 && el(
						'ol',
						{ className: 'docspress-code__annotations docspress-code__annotations--editor' },
						attributes.annotations.filter( ( annotation ) => annotation.content ).map( ( annotation, index ) => el(
							'li',
							{ className: 'docspress-code__annotation', key: `preview-${ index }` },
							el( 'span', { className: 'docspress-code__annotation-number' }, String( index + 1 ) ),
							el( 'div', null, el( 'strong', null, `${ __( 'Line', 'docspress-blocks' ) } ${ annotation.line || 1 }` ), el( 'p', null, annotation.content ) )
						) )
					),
					el( RichText, {
						tagName: 'figcaption',
						className: 'docspress-code__caption',
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
