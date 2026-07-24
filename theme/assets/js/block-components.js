( function ( blocks, blockEditor, components, element, i18n, serverSideRender ) {
	'use strict';

	const { registerBlockType } = blocks;
	const { BlockControls, InspectorControls, useBlockProps } = blockEditor;
	const { Disabled, PanelBody, RangeControl, SelectControl, TextControl, ToggleControl, ToolbarButton } = components;
	const { Fragment, createElement: el, useEffect, useState } = element;
	const { __ } = i18n;
	const ServerSideRender = serverSideRender.default || serverSideRender;
	const designSupports = {
		anchor: true,
		className: true,
		html: false,
		color: {
			background: true,
			gradients: true,
			link: true,
			text: true
		},
		spacing: {
			margin: true,
			padding: true
		},
		typography: {
			fontFamily: true,
			fontSize: true,
			fontStyle: true,
			fontWeight: true,
			letterSpacing: true,
			lineHeight: true,
			textDecoration: true,
			textTransform: true
		},
		border: {
			color: true,
			radius: true,
			style: true,
			width: true
		},
		dimensions: {
			minHeight: true
		},
		position: {
			sticky: true
		},
		shadow: true
	};
	const icons = {
		navigation: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M4 5h16v2H4V5Zm0 6h11v2H4v-2Zm0 6h7v2H4v-2Z' } ) ),
		search: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'm16.8 15.4 4.4 4.4-1.4 1.4-4.4-4.4a7.5 7.5 0 1 1 1.4-1.4ZM10.5 16a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11Z' } ) ),
		trail: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M4 6h6v2H6v8h4v2H4V6Zm8 5h8v2h-8v-2Zm4-4 5 5-5 5-1.4-1.4 3.6-3.6-3.6-3.6L16 7Z' } ) ),
		toc: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M4 5h3v3H4V5Zm5 0h11v2H9V5ZM4 11h3v3H4v-3Zm5 0h11v2H9v-2ZM4 17h3v3H4v-3Zm5 0h11v2H9v-2Z' } ) ),
		summary: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M4 5h16v2H4V5Zm0 5h16v2H4v-2Zm0 5h11v2H4v-2Zm0 4h8v2H4v-2Z' } ) ),
		edit: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'm5 17.2-.8 3.6 3.6-.8L19 8.8 15.2 5 5 17.2Zm12.3-14 3.5 3.5-1.4 1.4-3.5-3.5 1.4-1.4Z' } ) ),
		pencil: el( 'svg', { viewBox: '0 0 24 24', fill: 'none', 'aria-hidden': 'true' }, el( 'path', { d: 'm14.7 5.3 4 4M5 19l2.1-5.1L16.6 4.4a1.4 1.4 0 0 1 2 0l1 1a1.4 1.4 0 0 1 0 2L10.1 17 5 19Z', stroke: 'currentColor', strokeWidth: '1.7', strokeLinecap: 'round', strokeLinejoin: 'round' } ) ),
		github: el( 'svg', { viewBox: '0 0 24 24', fill: 'currentColor', 'aria-hidden': 'true' }, el( 'path', { d: 'M12 2C6.48 2 2 6.58 2 12.23c0 4.51 2.87 8.34 6.84 9.69.5.1.68-.22.68-.49 0-.24-.01-1.05-.01-1.9-2.78.62-3.37-1.2-3.37-1.2-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.35 1.12 2.92.85.09-.66.35-1.12.64-1.37-2.22-.26-4.55-1.14-4.55-5.07 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.72 0 0 .84-.28 2.75 1.05A9.36 9.36 0 0 1 12 6.92c.85 0 1.69.12 2.49.34 1.91-1.33 2.75-1.05 2.75-1.05.55 1.42.2 2.46.1 2.72.64.72 1.03 1.64 1.03 2.76 0 3.94-2.34 4.8-4.57 5.06.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.8 0 .27.18.59.69.49A10.25 10.25 0 0 0 22 12.23C22 6.58 17.52 2 12 2Z' } ) ),
		adjacent: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'm8.4 5.6 1.4 1.4-4 4H20v2H5.8l4 4-1.4 1.4L2 12l6.4-6.4Z' } ) ),
		mode: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M12 2v2a8 8 0 1 0 8 8h2A10 10 0 1 1 12 2Zm2 0a8 8 0 0 1 8 8h-8V2Z' } ) ),
		menu: el( 'svg', { viewBox: '0 0 24 24' }, el( 'path', { d: 'M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z' } ) )
	};

	function text( label, key, attributes, setAttributes, help ) {
		return el( TextControl, {
			label,
			help,
			value: attributes[ key ],
			onChange: ( value ) => setAttributes( { [ key ]: value } )
		} );
	}

	function toggle( label, key, attributes, setAttributes, help ) {
		return el( ToggleControl, {
			label,
			help,
			checked: attributes[ key ],
			onChange: ( value ) => setAttributes( { [ key ]: value } )
		} );
	}

	function range( label, key, attributes, setAttributes, min, max, step = 1, help ) {
		return el( RangeControl, {
			label,
			help,
			value: attributes[ key ],
			min,
			max,
			step,
			onChange: ( value ) => setAttributes( { [ key ]: value } )
		} );
	}

	function select( label, key, attributes, setAttributes, options ) {
		return el( SelectControl, {
			label,
			value: attributes[ key ],
			options,
			onChange: ( value ) => setAttributes( { [ key ]: value } )
		} );
	}

	function panel( title, controls, initialOpen = true ) {
		return el( PanelBody, { title, initialOpen }, ...controls );
	}

	function CommandSearchEditorPreview( { attributes, isSelected } ) {
		const [ isOpen, setIsOpen ] = useState( false );
		const previewClasses = [
			'docspress-command-search',
			'docspress-command-search-editor-preview'
		];
		const sampleResults = [
			{
				path: __( 'Documentation', 'docspress' ),
				title: __( 'DocsPress documentation', 'docspress' ),
				excerpt: __( 'Keep documentation beside the code that explains it, then publish that Markdown as native WordPress Pages and blocks.', 'docspress' )
			},
			{
				path: __( 'DocsPress documentation / Publish existing docs', 'docspress' ),
				title: __( 'Publish existing docs', 'docspress' ),
				excerpt: __( 'Connect an existing Markdown documentation tree to WordPress through the reviewed DocsPress workflow.', 'docspress' )
			},
			{
				path: __( 'DocsPress documentation / Create docs with AI', 'docspress' ),
				title: __( 'Create docs with AI', 'docspress' ),
				excerpt: __( 'Generate verified documentation from repository evidence, review it, and publish it with DocsPress.', 'docspress' )
			}
		].slice( 0, Math.min( 3, attributes.resultsLimit ) );

		if ( ! attributes.showPaths ) {
			previewClasses.push( 'docspress-search-hide-paths' );
		}
		if ( ! attributes.showExcerpts ) {
			previewClasses.push( 'docspress-search-hide-excerpts' );
		}
		if ( ! attributes.showHints ) {
			previewClasses.push( 'docspress-search-hide-hints' );
		}

		useEffect( () => {
			if ( ! isSelected ) {
				setIsOpen( false );
			}
		}, [ isSelected ] );

		useEffect( () => {
			if ( ! isOpen ) {
				return undefined;
			}

			const closeOnEscape = ( event ) => {
				if ( event.key === 'Escape' ) {
					setIsOpen( false );
				}
			};
			window.addEventListener( 'keydown', closeOnEscape );
			return () => window.removeEventListener( 'keydown', closeOnEscape );
		}, [ isOpen ] );

		const openPreview = ( event ) => {
			event.preventDefault();
			event.stopPropagation();
			setIsOpen( true );
		};
		const closePreview = ( event ) => {
			event.preventDefault();
			event.stopPropagation();
			setIsOpen( false );
		};
		const preventNavigation = ( event ) => event.preventDefault();
		const searchIcon = () => el(
			'svg',
			{ viewBox: '0 0 24 24', 'aria-hidden': 'true' },
			el( 'path', { d: 'm16.8 15.4 4.4 4.4-1.4 1.4-4.4-4.4a7.5 7.5 0 1 1 1.4-1.4ZM10.5 16a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11Z' } )
		);
		const previewStyle = {
			'--dp-search-width': `${ attributes.width }px`,
			'--dp-search-height': `${ attributes.height }px`,
			'--dp-search-radius': `${ attributes.radius }px`,
			'--dp-search-overlay-opacity': `${ attributes.overlayOpacity }%`,
			'--dp-search-overlay-blur': `${ attributes.overlayBlur }px`
		};

		return el(
			Fragment,
			null,
			el(
				BlockControls,
				{ group: 'block' },
				el( ToolbarButton, {
					icon: icons.search,
					label: isOpen
						? __( 'Close search dialog preview', 'docspress' )
						: __( 'Preview search dialog', 'docspress' ),
					isPressed: isOpen,
					onClick: () => setIsOpen( ! isOpen )
				} )
			),
			el(
				'div',
				{ className: previewClasses.join( ' ' ), style: previewStyle },
				el(
					'button',
					{
						className: 'header-button search-shortcut',
						type: 'button',
						'aria-expanded': isOpen,
						onClick: openPreview
					},
					searchIcon(),
					el( 'span', null, attributes.label ),
					el( 'kbd', null, '⌘ K' )
				),
				isOpen && el(
					'div',
					{
						className: 'docspress-command-search-editor-overlay',
						onClick: closePreview
					},
					el(
						'div',
						{
							className: 'search-dialog is-editor-preview',
							role: 'dialog',
							'aria-label': __( 'Search dialog preview', 'docspress' ),
							'aria-modal': 'true',
							onClick: ( event ) => event.stopPropagation()
						},
						el(
							'div',
							{ className: 'search-dialog-panel' },
							el(
								'div',
								{ className: 'command-search' },
								el(
									'div',
									{ className: 'command-search-field' },
									searchIcon(),
									el( 'input', {
										type: 'search',
										placeholder: attributes.placeholder,
										readOnly: true,
										'aria-label': __( 'Search documentation preview', 'docspress' )
									} ),
									el(
										'button',
										{
											className: 'command-search-close',
											type: 'button',
											'aria-label': __( 'Close search dialog preview', 'docspress' ),
											onClick: closePreview
										},
										el( 'span', { 'aria-hidden': 'true' }, '×' )
									)
								),
								el(
									'div',
									{ className: 'command-search-body' },
									el( 'div', { className: 'command-search-status' }, attributes.suggestedLabel ),
									el(
										'ul',
										{ className: 'command-search-results', role: 'listbox' },
										...sampleResults.map( ( result, index ) =>
											el(
												'li',
												{
													className: `command-search-result${ index === 0 ? ' is-active' : '' }`,
													key: result.title,
													role: 'option',
													'aria-selected': index === 0
												},
												el(
													'a',
													{ href: '#', onClick: preventNavigation },
													attributes.showPaths && el( 'span', { className: 'command-search-result-path' }, result.path ),
													el( 'span', { className: 'command-search-result-title' }, result.title ),
													attributes.showExcerpts && el( 'span', { className: 'command-search-result-excerpt' }, result.excerpt ),
													el( 'span', { className: 'command-search-result-arrow', 'aria-hidden': 'true' }, '↵' )
												)
											)
										)
									)
								),
								attributes.showHints && el(
									'footer',
									{ className: 'command-search-footer', 'aria-hidden': 'true' },
									el( 'span', null, el( 'kbd', null, '↑' ), el( 'kbd', null, '↓' ), __( 'to navigate', 'docspress' ) ),
									el( 'span', null, el( 'kbd', null, '↵' ), __( 'to open', 'docspress' ) ),
									el( 'span', null, el( 'kbd', null, 'Esc' ), __( 'to close', 'docspress' ) )
								)
							)
						)
					)
				)
			)
		);
	}

	function registerComponent( slug, config ) {
		registerBlockType( `docspress/${ slug }`, {
			apiVersion: 3,
			title: config.title,
			description: config.description,
			category: 'theme',
			icon: config.icon,
			attributes: config.attributes,
			supports: designSupports,
			edit: function ComponentEdit( { attributes, setAttributes, isSelected } ) {
				const blockProps = useBlockProps( { className: `docspress-component-editor docspress-component-editor--${ slug }` } );
				const preview = config.EditorPreview
					? el( config.EditorPreview, { attributes, isSelected } )
					: config.preview
					? config.preview( attributes, setAttributes )
					: el(
						Disabled,
						null,
						el( ServerSideRender, {
							block: `docspress/${ slug }`,
							attributes,
							EmptyResponsePlaceholder: () => el( 'div', { className: 'docspress-component-placeholder' }, config.empty )
						} )
					);
				return el(
					Fragment,
					null,
					el( InspectorControls, null, ...config.controls( attributes, setAttributes ) ),
					el( 'div', blockProps, preview )
				);
			},
			save: function () {
				return null;
			}
		} );
	}

	registerComponent( 'docs-navigation', {
		title: __( 'DocsPress: Documentation Navigation', 'docspress' ),
		description: __( 'A hierarchy-aware Page tree or selected classic menu with filtering and versions.', 'docspress' ),
		icon: icons.navigation,
		empty: __( 'Documentation navigation preview', 'docspress' ),
		attributes: {
			title: { type: 'string', default: 'Documentation', role: 'content' },
			width: { type: 'number', default: 266 },
			rootSlug: { type: 'string', default: 'docs' },
			source: { type: 'string', default: 'pages' },
			menuSlug: { type: 'string', default: '' },
			sort: { type: 'string', default: 'menu_order' },
			showRoot: { type: 'boolean', default: true },
			maxDepth: { type: 'number', default: 0 },
			showFilter: { type: 'boolean', default: true },
			filterPlaceholder: { type: 'string', default: 'Filter pages…', role: 'content' },
			showVersions: { type: 'boolean', default: true },
			emptyMessage: { type: 'string', default: 'Publish Pages to populate this navigation.', role: 'content' },
			showCollapse: { type: 'boolean', default: true },
			startCollapsed: { type: 'boolean', default: false },
			collapseLabel: { type: 'string', default: 'Collapse sidebar', role: 'content' },
			expandLabel: { type: 'string', default: 'Expand sidebar', role: 'content' }
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Navigation source', 'docspress' ), [
				range( __( 'Sidebar width', 'docspress' ), 'width', attributes, setAttributes, 220, 360, 1 ),
				select( __( 'Source', 'docspress' ), 'source', attributes, setAttributes, [
					{ label: __( 'Automatic Page tree', 'docspress' ), value: 'pages' },
					{ label: __( 'Classic menu', 'docspress' ), value: 'menu' }
				] ),
				text( __( 'Documentation root path', 'docspress' ), 'rootSlug', attributes, setAttributes, __( 'Use a Page path such as docs or developer/docs.', 'docspress' ) ),
				attributes.source === 'menu' && text( __( 'Menu slug, name, or ID', 'docspress' ), 'menuSlug', attributes, setAttributes ),
				select( __( 'Automatic Page order', 'docspress' ), 'sort', attributes, setAttributes, [
					{ label: __( 'Page order, then title', 'docspress' ), value: 'menu_order' },
					{ label: __( 'Title', 'docspress' ), value: 'title' },
					{ label: __( 'Newest first', 'docspress' ), value: 'newest' },
					{ label: __( 'Oldest first', 'docspress' ), value: 'oldest' }
				] ),
				toggle( __( 'Show root Page', 'docspress' ), 'showRoot', attributes, setAttributes ),
				range( __( 'Maximum depth', 'docspress' ), 'maxDepth', attributes, setAttributes, 0, 8, 1, __( 'Zero shows every level.', 'docspress' ) )
			] ),
			panel( __( 'Labels and tools', 'docspress' ), [
				text( __( 'Heading', 'docspress' ), 'title', attributes, setAttributes ),
				toggle( __( 'Show Page filter', 'docspress' ), 'showFilter', attributes, setAttributes ),
				attributes.showFilter && text( __( 'Filter placeholder', 'docspress' ), 'filterPlaceholder', attributes, setAttributes ),
				toggle( __( 'Show version selector', 'docspress' ), 'showVersions', attributes, setAttributes ),
				text( __( 'Empty state', 'docspress' ), 'emptyMessage', attributes, setAttributes )
			], false ),
			panel( __( 'Sidebar collapse button', 'docspress' ), [
				toggle(
					__( 'Show collapse circle', 'docspress' ),
					'showCollapse',
					attributes,
					setAttributes,
					__( 'Display the circular desktop control on the sidebar divider.', 'docspress' )
				),
				attributes.showCollapse && toggle( __( 'Start collapsed on desktop', 'docspress' ), 'startCollapsed', attributes, setAttributes ),
				attributes.showCollapse && text( __( 'Collapse label', 'docspress' ), 'collapseLabel', attributes, setAttributes ),
				attributes.showCollapse && text( __( 'Expand label', 'docspress' ), 'expandLabel', attributes, setAttributes )
			] )
		]
	} );

	registerComponent( 'command-search', {
		title: __( 'DocsPress: Command Search', 'docspress' ),
		description: __( 'A keyboard-accessible documentation search trigger and command dialog.', 'docspress' ),
		icon: icons.search,
		empty: __( 'Search is available on the published site.', 'docspress' ),
		EditorPreview: CommandSearchEditorPreview,
		attributes: {
			label: { type: 'string', default: 'Search docs', role: 'content' },
			placeholder: { type: 'string', default: 'Search documentation…', role: 'content' },
			suggestedLabel: { type: 'string', default: 'Suggested pages', role: 'content' },
			noResultsLabel: { type: 'string', default: 'No documentation matched that search.', role: 'content' },
			resultsLimit: { type: 'number', default: 8 },
			rootSlug: { type: 'string', default: 'docs' },
			width: { type: 'number', default: 680 },
			height: { type: 'number', default: 640 },
			radius: { type: 'number', default: 14 },
			overlayOpacity: { type: 'number', default: 44 },
			overlayBlur: { type: 'number', default: 2 },
			showPaths: { type: 'boolean', default: true },
			showExcerpts: { type: 'boolean', default: true },
			showHints: { type: 'boolean', default: true }
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Search content', 'docspress' ), [
				text( __( 'Trigger label', 'docspress' ), 'label', attributes, setAttributes ),
				text( __( 'Field placeholder', 'docspress' ), 'placeholder', attributes, setAttributes ),
				text( __( 'Suggested results label', 'docspress' ), 'suggestedLabel', attributes, setAttributes ),
				text( __( 'No-results message', 'docspress' ), 'noResultsLabel', attributes, setAttributes ),
				text( __( 'Documentation root path', 'docspress' ), 'rootSlug', attributes, setAttributes ),
				range( __( 'Maximum results', 'docspress' ), 'resultsLimit', attributes, setAttributes, 3, 20 )
			] ),
			panel( __( 'Dialog', 'docspress' ), [
				range(
					__( 'Width', 'docspress' ),
					'width',
					attributes,
					setAttributes,
					420,
					960,
					1,
					__( 'Click the search trigger or use the block toolbar to preview these settings.', 'docspress' )
				),
				range( __( 'Height', 'docspress' ), 'height', attributes, setAttributes, 320, 820 ),
				range( __( 'Corner radius', 'docspress' ), 'radius', attributes, setAttributes, 0, 40 ),
				range( __( 'Backdrop opacity', 'docspress' ), 'overlayOpacity', attributes, setAttributes, 0, 90 ),
				range( __( 'Backdrop blur', 'docspress' ), 'overlayBlur', attributes, setAttributes, 0, 20 ),
				toggle( __( 'Show Page paths', 'docspress' ), 'showPaths', attributes, setAttributes ),
				toggle( __( 'Show excerpts', 'docspress' ), 'showExcerpts', attributes, setAttributes ),
				toggle( __( 'Show keyboard hints', 'docspress' ), 'showHints', attributes, setAttributes )
			], false )
		]
	} );

	registerComponent( 'breadcrumbs', {
		title: __( 'DocsPress: Breadcrumbs', 'docspress' ),
		description: __( 'The current Page trail with optional home link and custom separator.', 'docspress' ),
		icon: icons.trail,
		attributes: {
			showHome: { type: 'boolean', default: false },
			homeLabel: { type: 'string', default: 'Home', role: 'content' },
			separator: { type: 'string', default: '›', role: 'content' }
		},
		preview: ( attributes ) => {
			const item = ( label, isCurrent = false ) => el(
				'li',
				isCurrent ? { 'aria-current': 'page' } : null,
				isCurrent ? label : el( 'a', { 'aria-disabled': 'true' }, label ),
				! isCurrent && el( 'span', { 'aria-hidden': 'true' }, attributes.separator )
			);
			return el(
				'nav',
				{ className: 'breadcrumbs', 'aria-label': __( 'Breadcrumbs preview', 'docspress' ) },
				el(
					'ol',
					null,
					attributes.showHome && item( attributes.homeLabel ),
					item( __( 'DocsPress documentation', 'docspress' ) ),
					item( __( 'Parent page', 'docspress' ) ),
					item( __( 'Current page', 'docspress' ), true )
				)
			);
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Breadcrumbs', 'docspress' ), [
				toggle( __( 'Show home link', 'docspress' ), 'showHome', attributes, setAttributes ),
				attributes.showHome && text( __( 'Home label', 'docspress' ), 'homeLabel', attributes, setAttributes ),
				text(
					__( 'Separator', 'docspress' ),
					'separator',
					attributes,
					setAttributes,
					__( 'Parent and current labels come from Page titles.', 'docspress' )
				)
			] )
		]
	} );

	registerComponent( 'table-of-contents', {
		title: __( 'DocsPress: Table of Contents', 'docspress' ),
		description: __( 'A live list of the current document headings.', 'docspress' ),
		icon: icons.toc,
		attributes: {
			title: { type: 'string', default: 'On this page', role: 'content' },
			width: { type: 'number', default: 226 },
			minLevel: { type: 'number', default: 2 },
			maxLevel: { type: 'number', default: 3 }
		},
		preview: ( attributes ) => {
			const minLevel = Math.min( 6, Math.max( 1, Number( attributes.minLevel ) || 2 ) );
			const maxLevel = Math.min( 6, Math.max( minLevel, Number( attributes.maxLevel ) || 3 ) );
			const nestedLevel = Math.min( maxLevel, minLevel + 1 );
			const deepLevel = Math.min( maxLevel, minLevel + 2 );
			const examples = [
				{ label: __( 'Overview', 'docspress' ), level: minLevel },
				{ label: __( 'Install DocsPress', 'docspress' ), level: minLevel },
				{ label: __( 'Configure publishing', 'docspress' ), level: nestedLevel },
				{ label: __( 'Customize the theme', 'docspress' ), level: deepLevel },
				{ label: __( 'Next steps', 'docspress' ), level: minLevel }
			];

			return el(
				'aside',
				{
					className: 'docs-toc docs-toc-preview',
					'aria-label': attributes.title || __( 'Table of contents preview', 'docspress' )
				},
				attributes.title && el( 'p', { className: 'toc-title' }, attributes.title ),
				el(
					'ul',
					{ className: 'toc-list' },
					...examples.map( ( item, index ) => el(
						'li',
						{ className: `toc-level-${ item.level }`, key: `${ item.level }-${ item.label }` },
						el(
							'a',
							{
								'aria-disabled': 'true',
								className: index === 0 ? 'is-active' : undefined
							},
							item.label
						)
					) )
				)
			);
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Table of contents', 'docspress' ), [
				text( __( 'Heading', 'docspress' ), 'title', attributes, setAttributes ),
				range( __( 'Column width', 'docspress' ), 'width', attributes, setAttributes, 180, 320, 1 ),
				range( __( 'First heading level', 'docspress' ), 'minLevel', attributes, setAttributes, 1, 6 ),
				range( __( 'Last heading level', 'docspress' ), 'maxLevel', attributes, setAttributes, attributes.minLevel, 6 )
			] )
		]
	} );

	registerComponent( 'page-summary', {
		title: __( 'DocsPress: Page Summary', 'docspress' ),
		description: __( 'Show a manually written Page excerpt without generating duplicate content.', 'docspress' ),
		icon: icons.summary,
		attributes: {
			fallbackText: { type: 'string', default: '', role: 'content' }
		},
		preview: ( attributes ) => el(
			'p',
			{ className: 'entry-summary' },
			attributes.fallbackText || __( 'A manually written Page excerpt appears here.', 'docspress' )
		),
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Page summary', 'docspress' ), [
				text(
					__( 'Fallback summary', 'docspress' ),
					'fallbackText',
					attributes,
					setAttributes,
					__( 'Used only when the current Page has no manual excerpt.', 'docspress' )
				)
			] )
		]
	} );

	registerComponent( 'edit-links', {
		title: __( 'DocsPress: Edit Links', 'docspress' ),
		description: __( 'WordPress and source-aware GitHub editing actions.', 'docspress' ),
		icon: icons.edit,
		attributes: {
			showWordPress: { type: 'boolean', default: true },
			wordpressLabel: { type: 'string', default: 'Edit this page in WordPress', role: 'content' },
			showGitHub: { type: 'boolean', default: true },
			githubLabel: { type: 'string', default: 'Propose changes on GitHub', role: 'content' },
			repositoryUrl: { type: 'string', default: 'https://github.com/Automattic/docspress' },
			ref: { type: 'string', default: 'main' }
		},
		preview: ( attributes ) => {
			const actions = [];
			const action = ( className, icon, label ) => el(
				'span',
				{
					className: `page-action ${ className } wp-element-button`,
					'aria-disabled': 'true'
				},
				icon,
				el( 'span', null, label )
			);

			if ( attributes.showWordPress ) {
				actions.push( action( 'page-action-wordpress', icons.pencil, attributes.wordpressLabel ) );
			}
			if ( attributes.showGitHub ) {
				actions.push( action( 'page-action-github', icons.github, attributes.githubLabel ) );
			}

			return el(
				'nav',
				{
					className: 'page-actions page-actions-preview',
					'aria-label': __( 'Page actions preview', 'docspress' )
				},
				...( actions.length
					? actions
					: [ el( 'p', { className: 'docspress-component-placeholder' }, __( 'Enable a WordPress or GitHub action to preview it.', 'docspress' ) ) ] )
			);
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'WordPress action', 'docspress' ), [
				toggle( __( 'Show WordPress edit link', 'docspress' ), 'showWordPress', attributes, setAttributes ),
				attributes.showWordPress && text( __( 'WordPress label', 'docspress' ), 'wordpressLabel', attributes, setAttributes )
			] ),
			panel( __( 'GitHub action', 'docspress' ), [
				toggle( __( 'Show GitHub proposal link', 'docspress' ), 'showGitHub', attributes, setAttributes ),
				attributes.showGitHub && text( __( 'GitHub label', 'docspress' ), 'githubLabel', attributes, setAttributes ),
				attributes.showGitHub && text( __( 'Repository URL', 'docspress' ), 'repositoryUrl', attributes, setAttributes ),
				attributes.showGitHub && text( __( 'Branch or tag', 'docspress' ), 'ref', attributes, setAttributes )
			], false )
		]
	} );

	registerComponent( 'adjacent-navigation', {
		title: __( 'DocsPress: Previous / Next', 'docspress' ),
		description: __( 'Previous and next documentation Pages in navigation order.', 'docspress' ),
		icon: icons.adjacent,
		empty: __( 'Adjacent links appear inside a documentation Page tree.', 'docspress' ),
		attributes: {
			rootSlug: { type: 'string', default: 'docs' },
			sort: { type: 'string', default: 'menu_order' },
			showRoot: { type: 'boolean', default: true },
			maxDepth: { type: 'number', default: 0 },
			previousLabel: { type: 'string', default: '← Previous', role: 'content' },
			nextLabel: { type: 'string', default: 'Next →', role: 'content' },
			showTitles: { type: 'boolean', default: true }
		},
		preview: ( attributes ) => {
			const link = ( className, direction, title ) => el(
				'span',
				{ className: `pagination-link ${ className }`, 'aria-disabled': 'true' },
				el( 'span', { className: 'pagination-direction' }, direction ),
				attributes.showTitles && el( 'span', { className: 'pagination-title' }, title )
			);
			return el(
				'nav',
				{ className: 'docs-pagination docs-pagination-preview', 'aria-label': __( 'Previous and next Page preview', 'docspress' ) },
				link( 'pagination-previous', attributes.previousLabel, __( 'Configure publishing', 'docspress' ) ),
				link( 'pagination-next', attributes.nextLabel, __( 'Customize the theme', 'docspress' ) )
			);
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Page order', 'docspress' ), [
				text( __( 'Documentation root path', 'docspress' ), 'rootSlug', attributes, setAttributes ),
				select( __( 'Order', 'docspress' ), 'sort', attributes, setAttributes, [
					{ label: __( 'Page order, then title', 'docspress' ), value: 'menu_order' },
					{ label: __( 'Title', 'docspress' ), value: 'title' },
					{ label: __( 'Newest first', 'docspress' ), value: 'newest' },
					{ label: __( 'Oldest first', 'docspress' ), value: 'oldest' }
				] ),
				toggle( __( 'Include root Page', 'docspress' ), 'showRoot', attributes, setAttributes ),
				range( __( 'Maximum depth', 'docspress' ), 'maxDepth', attributes, setAttributes, 0, 8 )
			] ),
			panel( __( 'Labels', 'docspress' ), [
				text( __( 'Previous label', 'docspress' ), 'previousLabel', attributes, setAttributes ),
				text( __( 'Next label', 'docspress' ), 'nextLabel', attributes, setAttributes ),
				toggle( __( 'Show Page titles', 'docspress' ), 'showTitles', attributes, setAttributes )
			], false )
		]
	} );

	registerComponent( 'color-mode-toggle', {
		title: __( 'DocsPress: Color Mode Toggle', 'docspress' ),
		description: __( 'Let visitors switch between the active style and its dark palette.', 'docspress' ),
		icon: icons.mode,
		empty: __( 'Color mode toggle', 'docspress' ),
		attributes: {
			label: { type: 'string', default: 'Switch color theme', role: 'content' },
			showLabel: { type: 'boolean', default: false },
			defaultMode: { type: 'string', default: 'light' }
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Color mode toggle', 'docspress' ), [
				text( __( 'Accessible label', 'docspress' ), 'label', attributes, setAttributes ),
				toggle( __( 'Show visible label', 'docspress' ), 'showLabel', attributes, setAttributes ),
				select( __( 'Default mode', 'docspress' ), 'defaultMode', attributes, setAttributes, [
					{ label: __( 'Light', 'docspress' ), value: 'light' },
					{ label: __( 'Dark', 'docspress' ), value: 'dark' },
					{ label: __( 'Follow device', 'docspress' ), value: 'system' }
				] )
			] )
		]
	} );

	registerComponent( 'docs-menu-toggle', {
		title: __( 'DocsPress: Mobile Docs Menu', 'docspress' ),
		description: __( 'Open the documentation sidebar as a mobile drawer.', 'docspress' ),
		icon: icons.menu,
		empty: __( 'Mobile documentation menu toggle', 'docspress' ),
		attributes: {
			label: { type: 'string', default: 'Open documentation menu', role: 'content' }
		},
		controls: ( attributes, setAttributes ) => [
			panel( __( 'Mobile menu', 'docspress' ), [
				text( __( 'Accessible label', 'docspress' ), 'label', attributes, setAttributes )
			] )
		]
	} );

	/*
	 * WordPress's content-only template navigator currently renders the base
	 * "Template Part" block title for every instance, even though the Header,
	 * Comments, and Footer entities have titles in theme.json. Keep the native
	 * blocks and replace only those generic quick-navigation labels.
	 */
	function templatePartBlocks( blockList ) {
		return blockList.reduce( ( found, block ) => {
			if ( block.name === 'core/template-part' ) {
				found.push( block );
			}
			return found.concat( templatePartBlocks( block.innerBlocks || [] ) );
		}, [] );
	}

	function templatePartLabel( slug ) {
		const labels = {
			header: __( 'Header', 'docspress' ),
			comments: __( 'Comments', 'docspress' ),
			footer: __( 'Footer', 'docspress' )
		};
		if ( labels[ slug ] ) {
			return labels[ slug ];
		}
		return String( slug || __( 'Template part', 'docspress' ) )
			.replace( /[-_]+/g, ' ' )
			.replace( /\b\w/g, ( character ) => character.toUpperCase() );
	}

	function updateTemplatePartNavigatorLabels() {
		const data = window.wp.data;
		const editor = data && data.select( 'core/block-editor' );
		if ( ! editor || ! editor.getBlocks ) {
			return;
		}

		const parts = templatePartBlocks( editor.getBlocks() );
		const buttons = Array.from(
			document.querySelectorAll( '.block-editor-block-quick-navigation__item' )
		).filter( ( button ) => button.querySelector( '.components-truncate' )?.textContent === 'Template Part' );
		if ( ! parts.length || buttons.length !== parts.length ) {
			return;
		}

		buttons.forEach( ( button, index ) => {
			const label = templatePartLabel( parts[ index ].attributes.slug );
			const text = button.querySelector( '.components-truncate' );
			if ( text && text.textContent !== label ) {
				text.textContent = label;
				button.setAttribute( 'aria-label', label );
			}
		} );
	}

	let templatePartLabelFrame = 0;
	function queueTemplatePartLabelUpdate() {
		if ( templatePartLabelFrame ) {
			return;
		}
		templatePartLabelFrame = window.requestAnimationFrame( () => {
			templatePartLabelFrame = 0;
			updateTemplatePartNavigatorLabels();
		} );
	}

	if ( window.wp.data ) {
		window.wp.data.subscribe( queueTemplatePartLabelUpdate );
		new MutationObserver( queueTemplatePartLabelUpdate ).observe( document.documentElement, {
			childList: true,
			subtree: true
		} );
		queueTemplatePartLabelUpdate();
	}
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.wp.serverSideRender );
