( function ( blocks, shared ) {
	'use strict';

	const { Fragment, InspectorControls, PanelBody, PlainText, TextControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'rect', { x: 3, y: 4, width: 18, height: 16, rx: 2 } ),
		el( 'path', { d: 'm7 9 3 3-3 3m6 0h4' } )
	);

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/terminal-session', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Terminal Session', 'autodocs-publish-blocks' ),
		description: __( 'A copyable command with realistic terminal output, without pretending the output is editable code.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'command', 'autodocs-publish-blocks' ), __( 'shell', 'autodocs-publish-blocks' ), __( 'CLI', 'autodocs-publish-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Terminal' },
			shell: { type: 'string', default: 'bash' },
			prompt: { type: 'string', default: '$' },
			command: { type: 'string', default: 'npx autodocs-publish publish ./docs' },
			output: { type: 'string', default: '✓ Read 12 documents\n✓ Published 12 WordPress pages' }
		},
		supports: designSupports,
		edit: function TerminalSessionEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-terminal autodocs-publish-terminal--editor ${ presetClass }`,
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
						{ title: __( 'Terminal', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextControl, { label: __( 'Title', 'autodocs-publish-blocks' ), value: attributes.title, onChange: ( title ) => setAttributes( { title } ) } ),
						el( TextControl, { label: __( 'Shell label', 'autodocs-publish-blocks' ), value: attributes.shell, onChange: ( shell ) => setAttributes( { shell } ) } ),
						el( TextControl, { label: __( 'Prompt', 'autodocs-publish-blocks' ), value: attributes.prompt, onChange: ( prompt ) => setAttributes( { prompt } ) } )
					)
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-terminal__bar' },
						el( 'span', { className: 'autodocs-publish-terminal__icon', 'aria-hidden': true }, '›_' ),
						el( 'span', { className: 'autodocs-publish-terminal__title' }, attributes.title ),
						el( 'span', { className: 'autodocs-publish-terminal__shell' }, attributes.shell )
					),
					el(
						'div',
						{ className: 'autodocs-publish-terminal__body' },
						el(
							'div',
							{ className: 'autodocs-publish-terminal__command' },
							el( 'span', { className: 'autodocs-publish-terminal__prompt', 'aria-hidden': true }, attributes.prompt ),
							el( PlainText, {
								value: attributes.command,
								onChange: ( command ) => setAttributes( { command } ),
								placeholder: __( 'Command…', 'autodocs-publish-blocks' ),
								'aria-label': __( 'Command', 'autodocs-publish-blocks' )
							} )
						),
						el( PlainText, {
							className: 'autodocs-publish-terminal__output',
							value: attributes.output,
							onChange: ( output ) => setAttributes( { output } ),
							placeholder: __( 'Command output…', 'autodocs-publish-blocks' ),
							'aria-label': __( 'Command output', 'autodocs-publish-blocks' )
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
