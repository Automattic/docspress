( function ( blocks, shared ) {
	'use strict';

	const {
		Button,
		Fragment,
		InspectorControls,
		PanelBody,
		PanelColorSettings,
		RichText,
		SelectControl,
		TextControl,
		ToggleControl,
		__,
		designSupports,
		el,
		presetClass,
		themeStyle,
		useBlockProps
	} = shared;
	const iconSystem = window.autodocsPublishAudiencePathIcons || { fallback: 'compass', icons: {}, aliases: {} };
	const defaultPaths = [
		{
			title: 'I already have Markdown docs',
			description: 'Connect an existing docs folder to WordPress and begin with a safe draft sync.',
			url: '/docs/publish-existing-docs/',
			cta: 'Publish existing docs',
			icon: 'document',
			accent: 'blue',
			newTab: false
		},
		{
			title: 'I need to create docs',
			description: 'Generate source-grounded documentation with AI, review it, then publish it.',
			url: '/docs/create-docs-with-ai/',
			cta: 'Create docs with AI',
			icon: 'sparkles',
			accent: 'gold',
			newTab: false
		}
	];
	const blockIcon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M4 5.5h6.5V12H4V5.5Zm9.5 6.5H20v6.5h-6.5V12ZM10.5 8.75h3m-1.5-1.5 1.5 1.5-1.5 1.5M10.5 15.25h3' } )
	);
	const resolveIcon = ( value ) => {
		const key = String( value || '' ).trim();
		const canonical = key.toLowerCase();

		if ( iconSystem.icons[ canonical ] ) return canonical;
		return iconSystem.aliases[ key ] || iconSystem.aliases[ key.toUpperCase() ] || iconSystem.fallback;
	};
	const renderIcon = ( value, className = 'autodocs-publish-audience-paths__icon-svg' ) => {
		const iconId = resolveIcon( value );
		const definition = iconSystem.icons[ iconId ] || iconSystem.icons[ iconSystem.fallback ];

		if ( ! definition ) return null;

		return el(
			'svg',
			{
				className,
				viewBox: '0 0 24 24',
				fill: 'none',
				stroke: 'currentColor',
				strokeWidth: 1.75,
				strokeLinecap: 'round',
				strokeLinejoin: 'round',
				focusable: false,
				'aria-hidden': true
			},
			...definition.elements.map( ( element, index ) => el( element.tag, { ...element.attrs, key: `${ iconId }-${ index }` } ) )
		);
	};
	const iconOptions = Object.entries( iconSystem.icons )
		.filter( ( [ iconId ] ) => iconId !== 'arrow-up-right' )
		.map( ( [ value, definition ] ) => ( { value, label: definition.label } ) )
		.sort( ( left, right ) => left.label.localeCompare( right.label ) );

	function normalizedPaths( paths ) {
		return Array.isArray( paths ) && paths.length ? paths : defaultPaths;
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/audience-paths', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Audience Paths', 'autodocs-publish-blocks' ),
		description: __( 'Guide different kinds of readers into dedicated documentation roots.', 'autodocs-publish-blocks' ),
		category: 'design',
		icon: blockIcon,
		keywords: [ __( 'audience', 'autodocs-publish-blocks' ), __( 'onboarding', 'autodocs-publish-blocks' ), __( 'docs navigation', 'autodocs-publish-blocks' ) ],
		attributes: {
			eyebrow: { type: 'string', default: 'Choose a starting point' },
			title: { type: 'string', default: 'Where are your docs today?' },
			description: { type: 'string', default: 'Follow the path that matches your repository.' },
			paths: { type: 'array', default: defaultPaths },
			columns: { type: 'number', default: 2 },
			tone: { type: 'string', default: 'theme' },
			textAlign: { type: 'string', default: 'left' },
			compact: { type: 'boolean', default: false },
			showNumbers: { type: 'boolean', default: false },
			showIcons: { type: 'boolean', default: true },
			showLinks: { type: 'boolean', default: true },
			panelColor: { type: 'string', default: '' },
			accentColor: { type: 'string', default: '' }
		},
		supports: { ...designSupports, align: [ 'wide', 'full' ] },
		edit: function AudiencePathsEdit( { attributes, setAttributes } ) {
			const paths = normalizedPaths( attributes.paths );
			const classes = [
				'autodocs-publish-audience-paths',
				`autodocs-publish-audience-paths--${ attributes.tone }`,
				`autodocs-publish-audience-paths--columns-${ attributes.columns }`,
				`autodocs-publish-audience-paths--align-${ attributes.textAlign }`,
				attributes.compact ? 'autodocs-publish-audience-paths--compact' : '',
				attributes.showNumbers ? '' : 'autodocs-publish-audience-paths--no-numbers',
				attributes.showIcons ? '' : 'autodocs-publish-audience-paths--no-icons',
				attributes.showLinks ? '' : 'autodocs-publish-audience-paths--no-links',
				attributes.panelColor ? 'autodocs-publish-audience-paths--has-panel-color' : '',
				attributes.accentColor ? 'autodocs-publish-audience-paths--has-accent-color' : '',
				'autodocs-publish-audience-paths--editor',
				presetClass
			].filter( Boolean ).join( ' ' );
			const styles = { ...themeStyle };

			if ( attributes.panelColor ) styles[ '--adpb-paths-panel' ] = attributes.panelColor;
			if ( attributes.accentColor ) styles[ '--adpb-paths-accent' ] = attributes.accentColor;

			const blockProps = useBlockProps( { className: classes, style: styles } );
			const updatePath = ( index, key, value ) => {
				const nextPaths = paths.map( ( path, pathIndex ) => pathIndex === index ? { ...path, [ key ]: value } : path );
				setAttributes( { paths: nextPaths } );
			};
			const removePath = ( index ) => setAttributes( { paths: paths.filter( ( path, pathIndex ) => pathIndex !== index ) } );
			const addPath = () => {
				const accents = [ 'blue', 'gold', 'coral', 'green' ];
				setAttributes( {
					paths: [
						...paths,
						{
							title: __( 'New audience', 'autodocs-publish-blocks' ),
							description: __( 'Explain what this reader will find in their documentation path.', 'autodocs-publish-blocks' ),
							url: '/docs/',
							cta: __( 'Open this path', 'autodocs-publish-blocks' ),
							icon: 'compass',
							accent: accents[ paths.length % accents.length ],
							newTab: false
						}
					]
				} );
			};

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Layout', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Color style', 'autodocs-publish-blocks' ),
							value: attributes.tone,
							options: [
								{ label: __( 'Theme', 'autodocs-publish-blocks' ), value: 'theme' },
								{ label: __( 'Subtle', 'autodocs-publish-blocks' ), value: 'paper' },
								{ label: __( 'Inverse', 'autodocs-publish-blocks' ), value: 'ink' },
								{ label: __( 'Blueprint', 'autodocs-publish-blocks' ), value: 'blueprint' }
							],
							onChange: ( tone ) => setAttributes( { tone } )
						} ),
						el( SelectControl, {
							label: __( 'Columns', 'autodocs-publish-blocks' ),
							value: String( attributes.columns ),
							options: [
								{ label: '1', value: '1' },
								{ label: '2', value: '2' },
								{ label: '3', value: '3' }
							],
							onChange: ( columns ) => setAttributes( { columns: Number( columns ) } )
						} ),
						el( SelectControl, {
							label: __( 'Text alignment', 'autodocs-publish-blocks' ),
							value: attributes.textAlign,
							options: [
								{ label: __( 'Left', 'autodocs-publish-blocks' ), value: 'left' },
								{ label: __( 'Center', 'autodocs-publish-blocks' ), value: 'center' }
							],
							onChange: ( textAlign ) => setAttributes( { textAlign } )
						} ),
						el( ToggleControl, {
							label: __( 'Compact layout', 'autodocs-publish-blocks' ),
							help: __( 'Reduce spacing and card height for routing inside documentation pages.', 'autodocs-publish-blocks' ),
							checked: attributes.compact,
							onChange: ( compact ) => setAttributes( { compact } )
						} ),
						el( ToggleControl, {
							label: __( 'Show path numbers', 'autodocs-publish-blocks' ),
							checked: attributes.showNumbers,
							onChange: ( showNumbers ) => setAttributes( { showNumbers } )
						} ),
						el( ToggleControl, {
							label: __( 'Show icons', 'autodocs-publish-blocks' ),
							checked: attributes.showIcons,
							onChange: ( showIcons ) => setAttributes( { showIcons } )
						} ),
						el( ToggleControl, {
							label: __( 'Show bottom links', 'autodocs-publish-blocks' ),
							help: __( 'Hide the visible action row. Cards with destinations remain clickable.', 'autodocs-publish-blocks' ),
							checked: attributes.showLinks,
							onChange: ( showLinks ) => setAttributes( { showLinks } )
						} )
					),
					...paths.map( ( path, index ) => el(
						PanelBody,
						{
							key: `path-settings-${ index }`,
							title: path.title || `${ __( 'Path', 'autodocs-publish-blocks' ) } ${ index + 1 }`,
							initialOpen: false
						},
						el( TextControl, {
							label: __( 'Destination URL', 'autodocs-publish-blocks' ),
							value: path.url || '',
							type: 'url',
							onChange: ( url ) => updatePath( index, 'url', url )
						} ),
						el( SelectControl, {
							label: __( 'Icon', 'autodocs-publish-blocks' ),
							help: __( 'Choose a consistent vector icon. Legacy symbols are mapped automatically.', 'autodocs-publish-blocks' ),
							value: resolveIcon( path.icon ),
							options: iconOptions,
							onChange: ( iconValue ) => updatePath( index, 'icon', iconValue )
						} ),
						el( SelectControl, {
							label: __( 'Accent', 'autodocs-publish-blocks' ),
							value: path.accent || 'blue',
							options: [
								{ label: __( 'Blue', 'autodocs-publish-blocks' ), value: 'blue' },
								{ label: __( 'Gold', 'autodocs-publish-blocks' ), value: 'gold' },
								{ label: __( 'Coral', 'autodocs-publish-blocks' ), value: 'coral' },
								{ label: __( 'Green', 'autodocs-publish-blocks' ), value: 'green' }
							],
							onChange: ( accent ) => updatePath( index, 'accent', accent )
						} ),
						el( ToggleControl, {
							label: __( 'Open in a new tab', 'autodocs-publish-blocks' ),
							checked: Boolean( path.newTab ),
							onChange: ( newTab ) => updatePath( index, 'newTab', newTab )
						} ),
						paths.length > 1 && el( Button, {
							onClick: () => removePath( index ),
							variant: 'tertiary',
							isDestructive: true
						}, __( 'Remove path', 'autodocs-publish-blocks' ) )
					) ),
					paths.length < 6 && el(
						PanelBody,
						{ title: __( 'Add an audience', 'autodocs-publish-blocks' ), initialOpen: true },
						el( Button, { onClick: addPath, variant: 'secondary' }, __( 'Add audience path', 'autodocs-publish-blocks' ) )
					),
					el(
						PanelColorSettings,
						{
							title: __( 'Custom colors', 'autodocs-publish-blocks' ),
							initialOpen: false,
							colorSettings: [
								{ value: attributes.panelColor, onChange: ( panelColor ) => setAttributes( { panelColor: panelColor || '' } ), label: __( 'Panel', 'autodocs-publish-blocks' ) },
								{ value: attributes.accentColor, onChange: ( accentColor ) => setAttributes( { accentColor: accentColor || '' } ), label: __( 'Primary accent', 'autodocs-publish-blocks' ) }
							]
						}
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'autodocs-publish-audience-paths__header' },
						el( RichText, {
							tagName: 'p',
							className: 'autodocs-publish-audience-paths__eyebrow',
							value: attributes.eyebrow,
							onChange: ( eyebrow ) => setAttributes( { eyebrow } ),
							allowedFormats: [],
							placeholder: __( 'Choose a starting point…', 'autodocs-publish-blocks' )
						} ),
						el(
							'div',
							{ className: 'autodocs-publish-audience-paths__heading-group' },
							el( RichText, {
								tagName: 'h2',
								className: 'autodocs-publish-audience-paths__title',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Where are your docs today?', 'autodocs-publish-blocks' )
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'autodocs-publish-audience-paths__description',
								value: attributes.description,
								onChange: ( description ) => setAttributes( { description } ),
								allowedFormats: [],
								placeholder: __( 'Explain how these paths differ…', 'autodocs-publish-blocks' )
							} )
						)
					),
					el(
						'div',
						{ className: 'autodocs-publish-audience-paths__grid', role: 'list' },
						...paths.map( ( path, index ) => el(
							'div',
							{
								key: `path-${ index }`,
								className: 'autodocs-publish-audience-paths__item',
								role: 'listitem'
							},
							el(
								'div',
								{ className: `autodocs-publish-audience-paths__card autodocs-publish-audience-paths__card--${ path.accent || 'blue' }` },
								el( 'span', { className: 'autodocs-publish-audience-paths__number', 'aria-hidden': true }, String( index + 1 ).padStart( 2, '0' ) ),
								attributes.showIcons && el(
									'span',
									{ className: 'autodocs-publish-audience-paths__icon', 'aria-hidden': true },
									renderIcon( path.icon )
								),
								el(
									'div',
									{ className: 'autodocs-publish-audience-paths__card-copy' },
									el( RichText, {
										tagName: 'h3',
										className: 'autodocs-publish-audience-paths__card-title',
										value: path.title || '',
										onChange: ( title ) => updatePath( index, 'title', title ),
										allowedFormats: [],
										placeholder: __( 'Audience name…', 'autodocs-publish-blocks' )
									} ),
									el( RichText, {
										tagName: 'p',
										className: 'autodocs-publish-audience-paths__card-description',
										value: path.description || '',
										onChange: ( description ) => updatePath( index, 'description', description ),
										allowedFormats: [],
										placeholder: __( 'What will this reader find?', 'autodocs-publish-blocks' )
									} )
								),
								attributes.showLinks && el(
									'span',
									{ className: 'autodocs-publish-audience-paths__cta' },
									el( RichText, {
										tagName: 'span',
										className: 'autodocs-publish-audience-paths__cta-label',
										value: path.cta || '',
										onChange: ( cta ) => updatePath( index, 'cta', cta ),
										allowedFormats: [],
										placeholder: __( 'Open this path…', 'autodocs-publish-blocks' )
									} ),
									el(
										'span',
										{ className: 'autodocs-publish-audience-paths__cta-icon', 'aria-hidden': true },
										renderIcon( 'arrow-up-right', 'autodocs-publish-audience-paths__cta-svg' )
									)
								),
								el( 'code', { className: 'autodocs-publish-audience-paths__route' }, path.url || __( 'Add destination URL', 'autodocs-publish-blocks' ) )
							)
						) )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
