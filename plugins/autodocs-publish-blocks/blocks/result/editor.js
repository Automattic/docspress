( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, RichText, SelectControl, TextControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'circle', { cx: 12, cy: 12, r: 9 } ),
		el( 'path', { d: 'm8 12 2.5 2.5L16 9' } )
	);

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/result', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Result', 'autodocs-publish-blocks' ),
		description: __( 'A compact, scannable outcome for commands, checks, builds, and verification steps.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'output', 'autodocs-publish-blocks' ), __( 'status', 'autodocs-publish-blocks' ), __( 'verification', 'autodocs-publish-blocks' ) ],
		attributes: {
			status: { type: 'string', default: 'success' },
			title: { type: 'string', default: 'Deployment completed' },
			content: { type: 'string', default: '<p>All documentation pages are up to date.</p>' },
			meta: { type: 'string', default: '12 pages · 1.8s' }
		},
		supports: designSupports,
		edit: function ResultEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-result autodocs-publish-result--${ attributes.status } autodocs-publish-result--editor ${ presetClass }`,
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
						{ title: __( 'Result', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Status', 'autodocs-publish-blocks' ),
							value: attributes.status,
							options: [
								{ label: __( 'Success', 'autodocs-publish-blocks' ), value: 'success' },
								{ label: __( 'Neutral', 'autodocs-publish-blocks' ), value: 'neutral' },
								{ label: __( 'Warning', 'autodocs-publish-blocks' ), value: 'warning' },
								{ label: __( 'Error', 'autodocs-publish-blocks' ), value: 'error' }
							],
							onChange: ( status ) => setAttributes( { status } )
						} ),
						el( TextControl, { label: __( 'Metadata', 'autodocs-publish-blocks' ), value: attributes.meta, onChange: ( meta ) => setAttributes( { meta } ) } )
					)
				),
				el(
					'section',
					blockProps,
					el( 'span', { className: 'autodocs-publish-result__icon', 'aria-hidden': true }, attributes.status === 'success' ? '✓' : attributes.status === 'error' ? '×' : attributes.status === 'warning' ? '!' : '–' ),
					el(
						'div',
						{ className: 'autodocs-publish-result__body' },
						el( 'div', { className: 'autodocs-publish-result__kicker' }, attributes.status ),
						el( RichText, {
							tagName: 'div',
							className: 'autodocs-publish-result__title',
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
							allowedFormats: [],
							placeholder: __( 'Result title…', 'autodocs-publish-blocks' )
						} ),
						el( RichText, {
							tagName: 'div',
							multiline: 'p',
							className: 'autodocs-publish-result__content',
							value: attributes.content,
							onChange: ( content ) => setAttributes( { content } ),
							placeholder: __( 'What happened…', 'autodocs-publish-blocks' )
						} )
					),
					el( 'div', { className: 'autodocs-publish-result__meta' }, attributes.meta )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
