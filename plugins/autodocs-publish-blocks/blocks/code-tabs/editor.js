( function ( blocks, shared ) {
	'use strict';

	const {
		Button,
		ButtonGroup,
		CodeSettings,
		Fragment,
		InspectorControls,
		PanelBody,
		PlainText,
		RichText,
		SelectControl,
		TextControl,
		__,
		designSupports,
		el,
		languages,
		presetClass,
		themeStyle,
		useBlockProps,
		useState
	} = shared;
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M4 5h16v14H4zM4 9h16M8 5v4m4-4v4' } )
	);

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/code-tabs', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Code Tabs', 'autodocs-publish-blocks' ),
		description: __( 'Switchable code examples for languages, package managers, or platforms.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'tabs', 'autodocs-publish-blocks' ), __( 'code', 'autodocs-publish-blocks' ), __( 'examples', 'autodocs-publish-blocks' ) ],
		attributes: {
			tabs: {
				type: 'array',
				default: [
					{ label: 'JavaScript', language: 'javascript', filename: 'example.js', code: 'const docs = await publish();' },
					{ label: 'PHP', language: 'php', filename: 'example.php', code: '$docs = autodocs_publish_publish();' }
				]
			},
			showLineNumbers: { type: 'boolean', default: true },
			caption: { type: 'string', default: '' }
		},
		supports: designSupports,
		edit: function CodeTabsEdit( { attributes, setAttributes } ) {
			const tabs = Array.isArray( attributes.tabs ) && attributes.tabs.length ? attributes.tabs : [ { label: 'Example', language: 'plaintext', filename: '', code: '' } ];
			const [ selected, setSelected ] = useState( 0 );
			const activeIndex = Math.min( selected, tabs.length - 1 );
			const active = tabs[ activeIndex ];
			const blockProps = useBlockProps( {
				className: `autodocs-publish-code-tabs autodocs-publish-code-tabs--editor ${ presetClass }`,
				style: themeStyle
			} );

			function updateTab( key, value ) {
				setAttributes( {
					tabs: tabs.map( ( tab, index ) => index === activeIndex ? { ...tab, [ key ]: value } : tab )
				} );
			}

			function addTab() {
				if ( tabs.length >= 8 ) {
					return;
				}
				const next = [ ...tabs, { label: __( 'New example', 'autodocs-publish-blocks' ), language: 'plaintext', filename: '', code: '' } ];
				setAttributes( { tabs: next } );
				setSelected( next.length - 1 );
			}

			function removeTab() {
				if ( tabs.length <= 1 ) {
					return;
				}
				const next = tabs.filter( ( tab, index ) => index !== activeIndex );
				setAttributes( { tabs: next } );
				setSelected( Math.max( 0, activeIndex - 1 ) );
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Active tab', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Tab label', 'autodocs-publish-blocks' ),
							value: active.label,
							onChange: ( label ) => updateTab( 'label', label )
						} ),
						el( SelectControl, {
							label: __( 'Language', 'autodocs-publish-blocks' ),
							value: active.language,
							options: languages,
							onChange: ( language ) => updateTab( 'language', language )
						} ),
						el( TextControl, {
							label: __( 'Filename or label', 'autodocs-publish-blocks' ),
							value: active.filename,
							onChange: ( filename ) => updateTab( 'filename', filename )
						} ),
						el(
							ButtonGroup,
							null,
							el( Button, { variant: 'secondary', onClick: addTab, disabled: tabs.length >= 8 }, __( 'Add tab', 'autodocs-publish-blocks' ) ),
							el( Button, { variant: 'tertiary', isDestructive: true, onClick: removeTab, disabled: tabs.length <= 1 }, __( 'Remove tab', 'autodocs-publish-blocks' ) )
						)
					),
					el( CodeSettings, { attributes, setAttributes, includeHighlights: false } )
				),
				el(
					'figure',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-code-tabs__surface' },
						el(
							'div',
							{ className: 'autodocs-publish-code-tabs__header' },
							el(
								'div',
								{ className: 'autodocs-publish-code-tabs__list', role: 'tablist' },
								...tabs.map( ( tab, index ) => el(
									'button',
									{
										key: index,
										type: 'button',
										role: 'tab',
										'aria-selected': index === activeIndex,
										className: 'autodocs-publish-code-tabs__tab' + ( index === activeIndex ? ' is-active' : '' ),
										onClick: () => setSelected( index )
									},
									tab.label || __( 'Untitled', 'autodocs-publish-blocks' )
								) )
							),
							el(
								'div',
								{ className: 'autodocs-publish-code-tabs__tools' },
								el( 'span', { className: 'autodocs-publish-code-tabs__filename' }, active.filename || active.language )
							)
						),
						el(
							'div',
							{ className: 'autodocs-publish-code-tabs__panel' },
							el(
								'div',
								{ className: 'autodocs-publish-code__surface' + ( attributes.showLineNumbers ? ' has-line-numbers' : '' ) },
								el( PlainText, {
									className: 'autodocs-publish-code__editor',
									value: active.code,
									onChange: ( code ) => updateTab( 'code', code ),
									placeholder: __( 'Write this tab’s code…', 'autodocs-publish-blocks' ),
									'aria-label': __( 'Code', 'autodocs-publish-blocks' )
								} )
							)
						)
					),
					el( RichText, {
						tagName: 'figcaption',
						className: 'autodocs-publish-code__caption',
						value: attributes.caption,
						onChange: ( caption ) => setAttributes( { caption } ),
						allowedFormats: [],
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
