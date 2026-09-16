( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, PlainText, RichText, SelectControl, TextControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'm12 3 1.5 5 4.5 1.5-4.5 1.5-1.5 5-1.5-5L6 9.5 10.5 8 12 3Z' } ),
		el( 'path', { d: 'm18 15 .7 2.3L21 18l-2.3.7L18 21l-.7-2.3L15 18l2.3-.7L18 15Z' } )
	);
	const modeLabels = { chat: 'Chat', code: 'Code', ask: 'Ask', plan: 'Plan' };

	function contextKind( label ) {
		if ( label.startsWith( '$' ) ) return 'skill';
		if ( label.startsWith( '@' ) ) return 'mention';
		if ( label.startsWith( '#' ) ) return 'image';
		if ( /^https?:\/\//i.test( label ) ) return 'url';
		return 'file';
	}

	function contextItems( value ) {
		return value.split( ',' ).map( ( item ) => item.trim() ).filter( Boolean ).slice( 0, 12 );
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/prompt', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Prompt', 'autodocs-publish-blocks' ),
		description: __( 'Show a copyable AI prompt with model, mode, and context metadata without making it look like source code.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'AI', 'autodocs-publish-blocks' ), __( 'LLM', 'autodocs-publish-blocks' ), __( 'composer', 'autodocs-publish-blocks' ) ],
		attributes: {
			prompt: { type: 'string', default: 'Use $autodocs-publish-install to review this repository\'s documentation setup. Return a short plan before writing code.' },
			model: { type: 'string', default: 'GPT-5' },
			mode: { type: 'string', default: 'code' },
			thinking: { type: 'boolean', default: true },
			context: { type: 'string', default: '$autodocs-publish-install, @repository, src/sync.js, docs/' },
			caption: { type: 'string', default: 'Prompt example' }
		},
		supports: designSupports,
		edit: function PromptEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-prompt autodocs-publish-prompt--editor ${ presetClass }`,
				style: themeStyle,
				'data-mode': attributes.mode,
				'aria-label': __( 'AI prompt example', 'autodocs-publish-blocks' )
			} );
			const chips = contextItems( attributes.context );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Prompt settings', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Model', 'autodocs-publish-blocks' ),
							value: attributes.model,
							onChange: ( model ) => setAttributes( { model } )
						} ),
						el( SelectControl, {
							label: __( 'Mode', 'autodocs-publish-blocks' ),
							value: attributes.mode,
							options: [
								{ label: __( 'Chat', 'autodocs-publish-blocks' ), value: 'chat' },
								{ label: __( 'Code', 'autodocs-publish-blocks' ), value: 'code' },
								{ label: __( 'Ask', 'autodocs-publish-blocks' ), value: 'ask' },
								{ label: __( 'Plan', 'autodocs-publish-blocks' ), value: 'plan' }
							],
							onChange: ( mode ) => setAttributes( { mode } )
						} ),
						el( ToggleControl, {
							label: __( 'Show Thinking indicator', 'autodocs-publish-blocks' ),
							checked: attributes.thinking,
							onChange: ( thinking ) => setAttributes( { thinking } )
						} ),
						el( TextControl, {
							label: __( 'Context', 'autodocs-publish-blocks' ),
							help: __( 'Comma-separated $skill-name references, @mentions, #images, URLs, or file paths.', 'autodocs-publish-blocks' ),
							value: attributes.context,
							onChange: ( context ) => setAttributes( { context } )
						} ),
						el( TextControl, {
							label: __( 'Caption', 'autodocs-publish-blocks' ),
							value: attributes.caption,
							onChange: ( caption ) => setAttributes( { caption } )
						} )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'autodocs-publish-prompt__header' },
						el( 'span', { className: 'autodocs-publish-prompt__mark', 'aria-hidden': true }, '✦' ),
						el( 'span', { className: 'autodocs-publish-prompt__label' }, __( 'Prompt', 'autodocs-publish-blocks' ) ),
						el( 'span', { className: 'autodocs-publish-prompt__model' }, attributes.model || __( 'Model', 'autodocs-publish-blocks' ) ),
						el( 'span', { className: 'autodocs-publish-prompt__mode' }, modeLabels[ attributes.mode ] || attributes.mode ),
						attributes.thinking && el( 'span', { className: 'autodocs-publish-prompt__thinking' }, el( 'span', { 'aria-hidden': true }, '✦' ), __( 'Thinking', 'autodocs-publish-blocks' ) )
					),
					el(
						'div',
						{ className: 'autodocs-publish-prompt__composer' },
						el( PlainText, {
							className: 'autodocs-publish-prompt__text',
							value: attributes.prompt,
							onChange: ( prompt ) => setAttributes( { prompt } ),
							placeholder: __( 'Write the prompt exactly as a reader should use it…', 'autodocs-publish-blocks' ),
							'aria-label': __( 'Prompt text', 'autodocs-publish-blocks' )
						} ),
						chips.length > 0 && el(
							'div',
							{ className: 'autodocs-publish-prompt__context', 'aria-label': __( 'Prompt context', 'autodocs-publish-blocks' ) },
							chips.map( ( chip ) => el( 'span', { key: chip, className: `autodocs-publish-prompt__chip autodocs-publish-prompt__chip--${ contextKind( chip ) }` }, chip ) )
						),
						el(
							'footer',
							{ className: 'autodocs-publish-prompt__footer' },
							el( RichText, {
								tagName: 'span',
								className: 'autodocs-publish-prompt__caption',
								value: attributes.caption,
								onChange: ( caption ) => setAttributes( { caption } ),
								allowedFormats: [],
								placeholder: __( 'Optional caption…', 'autodocs-publish-blocks' )
							} ),
							el( 'span', { className: 'autodocs-publish-prompt__copy autodocs-publish-prompt__copy--preview' }, '⧉ ', __( 'Copy prompt', 'autodocs-publish-blocks' ) )
						)
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
