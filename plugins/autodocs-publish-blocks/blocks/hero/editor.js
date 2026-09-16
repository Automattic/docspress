( function ( blocks, shared ) {
	'use strict';

	const {
		Button,
		Fragment,
		InspectorControls,
		MediaUpload,
		MediaUploadCheck,
		PanelBody,
		PanelColorSettings,
		RangeControl,
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
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'rect', { x: 3, y: 5, width: 18, height: 14, rx: 2 } ),
		el( 'path', { d: 'M7 10h6M7 14h4m5-5 2 2-2 2' } )
	);

	function mediaPicker( attributes, setAttributes, compact ) {
		return el(
			MediaUploadCheck,
			null,
			el( MediaUpload, {
				allowedTypes: [ 'image' ],
				value: attributes.mediaId,
				onSelect: ( media ) => setAttributes( {
					mediaId: media.id || 0,
					mediaUrl: media.url || '',
					mediaAlt: media.alt || attributes.mediaAlt
				} ),
				render: ( { open } ) => el(
					Button,
					{
						onClick: open,
						variant: compact ? 'secondary' : 'primary',
						className: compact ? '' : 'autodocs-publish-hero__media-placeholder'
					},
					attributes.mediaUrl ? __( 'Replace image', 'autodocs-publish-blocks' ) : __( 'Choose hero image', 'autodocs-publish-blocks' )
				)
			} )
		);
	}

	function syncDiagramPreview( mascotImage ) {
		return el(
			'div',
			{
				className: 'autodocs-publish-hero-diagram',
				'aria-label': __( 'GitHub Markdown flows through Autodocs Publish to WordPress Pages and machine-readable documentation. WordPress edits return to GitHub as reviewable pull requests.', 'autodocs-publish-blocks' )
			},
			el(
				'div',
				{ className: 'autodocs-publish-hero-diagram__pipeline' },
				el(
					'section',
					{ className: 'autodocs-publish-hero-diagram__source', 'aria-label': __( 'GitHub Markdown source', 'autodocs-publish-blocks' ) },
					el(
						'div',
						{ className: 'autodocs-publish-hero-diagram__bar' },
						el( 'span', { className: 'autodocs-publish-hero-diagram__dot' } ),
						el( 'strong', null, 'github.com/repo' )
					),
					el( 'div', { className: 'autodocs-publish-hero-diagram__folder' }, 'docs/' ),
					el( 'span', { className: 'autodocs-publish-hero-diagram__file' }, 'index.md' ),
					el( 'span', { className: 'autodocs-publish-hero-diagram__file' }, 'quickstart.md' ),
					el( 'span', { className: 'autodocs-publish-hero-diagram__file' }, 'api.md' )
				),
				el(
					'div',
					{ className: 'autodocs-publish-hero-diagram__hub', 'aria-label': 'Autodocs Publish' },
					el(
						'div',
						{ className: 'autodocs-publish-hero-diagram__mascot' },
						mascotImage || el( 'span', { className: 'autodocs-publish-hero-diagram__fallback', 'aria-hidden': true }, 'DP' )
					),
					el( 'strong', null, 'Autodocs Publish' ),
					el( 'span', null, __( 'keeps one source', 'autodocs-publish-blocks' ) )
				),
				el(
					'div',
					{ className: 'autodocs-publish-hero-diagram__outputs', 'aria-label': __( 'Published documentation surfaces', 'autodocs-publish-blocks' ) },
					el(
						'section',
						{ className: 'autodocs-publish-hero-diagram__surface autodocs-publish-hero-diagram__surface--wordpress' },
						el( 'span', { className: 'autodocs-publish-hero-diagram__surface-label' }, __( 'For readers', 'autodocs-publish-blocks' ) ),
						el( 'strong', null, __( 'WordPress Pages', 'autodocs-publish-blocks' ) ),
						el( 'span', null, __( 'Native Gutenberg', 'autodocs-publish-blocks' ) )
					),
					el(
						'section',
						{ className: 'autodocs-publish-hero-diagram__surface autodocs-publish-hero-diagram__surface--agents' },
						el( 'span', { className: 'autodocs-publish-hero-diagram__surface-label' }, __( 'For agents', 'autodocs-publish-blocks' ) ),
						el( 'strong', null, '/llms.txt' ),
						el( 'span', null, __( 'Exact page.md routes', 'autodocs-publish-blocks' ) )
					)
				)
			),
			el(
				'div',
				{ className: 'autodocs-publish-hero-diagram__return' },
				el( 'span', { 'aria-hidden': true }, '↑' ),
				el( 'strong', null, __( 'Reviewable pull request', 'autodocs-publish-blocks' ) ),
				el( 'span', null, __( 'WordPress edits return to GitHub', 'autodocs-publish-blocks' ) )
			)
		);
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/hero', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Hero', 'autodocs-publish-blocks' ),
		description: __( 'A fully configurable homepage hero with actions, media, layout controls, and responsive presentation.', 'autodocs-publish-blocks' ),
		category: 'design',
		icon,
		keywords: [ __( 'homepage', 'autodocs-publish-blocks' ), __( 'banner', 'autodocs-publish-blocks' ), __( 'landing', 'autodocs-publish-blocks' ) ],
		attributes: {
			eyebrow: { type: 'string', default: 'Documentation, publishing, and community' },
			title: { type: 'string', default: 'Docs that stay connected to your GitHub repo' },
			description: { type: 'string', default: 'Write beside your code. Publish a WordPress experience that guides every reader to the docs written for them.' },
			primaryLabel: { type: 'string', default: 'Browse documentation' },
			primaryUrl: { type: 'string', default: '' },
			primaryNewTab: { type: 'boolean', default: false },
			secondaryLabel: { type: 'string', default: 'Latest updates' },
			secondaryUrl: { type: 'string', default: '' },
			secondaryNewTab: { type: 'boolean', default: false },
			mediaId: { type: 'number', default: 0 },
			mediaUrl: { type: 'string', default: '' },
			mediaAlt: { type: 'string', default: '' },
			visualLabel: { type: 'string', default: '' },
			visualVariant: { type: 'string', default: 'image' },
			layout: { type: 'string', default: 'split' },
			mediaPosition: { type: 'string', default: 'right' },
			mediaWidth: { type: 'number', default: 44 },
			imageScale: { type: 'number', default: 100 },
			height: { type: 'string', default: 'standard' },
			tone: { type: 'string', default: 'theme' },
			textAlign: { type: 'string', default: 'left' },
			showGrid: { type: 'boolean', default: false },
			showOrbit: { type: 'boolean', default: false },
			panelColor: { type: 'string', default: '' },
			visualColor: { type: 'string', default: '' },
			accentColor: { type: 'string', default: '' }
		},
		supports: { ...designSupports, align: [ 'wide', 'full' ] },
		edit: function HeroEdit( { attributes, setAttributes } ) {
			const visualVariant = attributes.visualVariant || 'image';
			const hasVisual = visualVariant === 'sync-diagram' || Boolean( attributes.mediaUrl );
			const classes = [
				'autodocs-publish-hero',
				`autodocs-publish-hero--${ attributes.tone }`,
				`autodocs-publish-hero--visual-${ visualVariant }`,
				`autodocs-publish-hero--layout-${ attributes.layout }`,
				`autodocs-publish-hero--media-${ attributes.mediaPosition }`,
				`autodocs-publish-hero--height-${ attributes.height }`,
				`autodocs-publish-hero--align-${ attributes.textAlign }`,
				attributes.showGrid ? '' : 'autodocs-publish-hero--no-grid',
				attributes.showOrbit ? '' : 'autodocs-publish-hero--no-orbit',
				hasVisual ? '' : 'autodocs-publish-hero--no-visual',
				attributes.panelColor ? 'autodocs-publish-hero--has-panel-color' : '',
				attributes.visualColor ? 'autodocs-publish-hero--has-visual-color' : '',
				attributes.accentColor ? 'autodocs-publish-hero--has-accent-color' : '',
				'autodocs-publish-hero--editor',
				presetClass
			].filter( Boolean ).join( ' ' );
			const styles = {
				...themeStyle,
				'--adpb-hero-media-width': `${ attributes.mediaWidth }%`,
				'--adpb-hero-image-scale': `${ attributes.imageScale }%`
			};

			if ( attributes.panelColor ) styles[ '--adpb-hero-panel' ] = attributes.panelColor;
			if ( attributes.visualColor ) styles[ '--adpb-hero-visual' ] = attributes.visualColor;
			if ( attributes.accentColor ) styles[ '--adpb-hero-accent' ] = attributes.accentColor;

			const blockProps = useBlockProps( { className: classes, style: styles } );
			const mascotImage = attributes.mediaUrl
				? el( 'img', {
					className: 'autodocs-publish-hero__image',
					src: attributes.mediaUrl,
					alt: attributes.mediaAlt || ''
				} )
				: null;
			const image = mascotImage
				? el(
					'figure',
					{ className: 'autodocs-publish-hero__media' },
					mascotImage
				)
				: mediaPicker( attributes, setAttributes, false );
			const visualContent = visualVariant === 'sync-diagram'
				? syncDiagramPreview( mascotImage )
				: image;
			const visual = hasVisual || ! attributes.mediaUrl
				? el(
					'div',
					{ className: 'autodocs-publish-hero__visual' },
					attributes.visualLabel && el(
						'span',
						{ className: 'autodocs-publish-hero__visual-label', 'aria-hidden': true },
						attributes.visualLabel
					),
					visualContent
				)
				: null;

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
							label: __( 'Composition', 'autodocs-publish-blocks' ),
							value: attributes.layout,
							options: [
								{ label: __( 'Split panel', 'autodocs-publish-blocks' ), value: 'split' },
								{ label: __( 'Editorial spotlight', 'autodocs-publish-blocks' ), value: 'editorial' }
							],
							onChange: ( layout ) => setAttributes( { layout } )
						} ),
						el( SelectControl, {
							label: __( 'Color style', 'autodocs-publish-blocks' ),
							value: attributes.tone,
							options: [
								{ label: __( 'Theme', 'autodocs-publish-blocks' ), value: 'theme' },
								{ label: __( 'Midnight', 'autodocs-publish-blocks' ), value: 'midnight' },
								{ label: __( 'Paper', 'autodocs-publish-blocks' ), value: 'paper' },
								{ label: __( 'Brand color', 'autodocs-publish-blocks' ), value: 'brand' }
							],
							onChange: ( tone ) => setAttributes( { tone } )
						} ),
						el( SelectControl, {
							label: __( 'Image side', 'autodocs-publish-blocks' ),
							value: attributes.mediaPosition,
							options: [
								{ label: __( 'Right', 'autodocs-publish-blocks' ), value: 'right' },
								{ label: __( 'Left', 'autodocs-publish-blocks' ), value: 'left' }
							],
							onChange: ( mediaPosition ) => setAttributes( { mediaPosition } )
						} ),
						el( SelectControl, {
							label: __( 'Hero height', 'autodocs-publish-blocks' ),
							value: attributes.height,
							options: [
								{ label: __( 'Compact', 'autodocs-publish-blocks' ), value: 'compact' },
								{ label: __( 'Standard', 'autodocs-publish-blocks' ), value: 'standard' },
								{ label: __( 'Tall', 'autodocs-publish-blocks' ), value: 'tall' }
							],
							onChange: ( height ) => setAttributes( { height } )
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
						el( RangeControl, {
							label: __( 'Visual width', 'autodocs-publish-blocks' ),
							value: attributes.mediaWidth,
							min: 34,
							max: 58,
							step: 1,
							onChange: ( mediaWidth ) => setAttributes( { mediaWidth } )
						} ),
						el( ToggleControl, {
							label: __( 'Show grid', 'autodocs-publish-blocks' ),
							checked: attributes.showGrid,
							onChange: ( showGrid ) => setAttributes( { showGrid } )
						} ),
						el( ToggleControl, {
							label: __( 'Show orbit', 'autodocs-publish-blocks' ),
							checked: attributes.showOrbit,
							onChange: ( showOrbit ) => setAttributes( { showOrbit } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Visual', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Visual type', 'autodocs-publish-blocks' ),
							value: visualVariant,
							options: [
								{ label: __( 'Image', 'autodocs-publish-blocks' ), value: 'image' },
								{ label: __( 'GitHub sync diagram', 'autodocs-publish-blocks' ), value: 'sync-diagram' }
							],
							onChange: ( visualVariant ) => setAttributes( { visualVariant } )
						} ),
						mediaPicker( attributes, setAttributes, true ),
						attributes.mediaUrl && el( Button, {
							onClick: () => setAttributes( { mediaId: 0, mediaUrl: '', mediaAlt: '' } ),
							variant: 'tertiary',
							isDestructive: true
						}, __( 'Remove image', 'autodocs-publish-blocks' ) ),
						el( TextControl, {
							label: __( 'External image URL', 'autodocs-publish-blocks' ),
							help: __( 'Choose a Media Library image above, or paste an image URL.', 'autodocs-publish-blocks' ),
							value: attributes.mediaUrl,
							type: 'url',
							onChange: ( mediaUrl ) => setAttributes( { mediaId: 0, mediaUrl } )
						} ),
						el( TextControl, {
							label: __( 'Alternative text', 'autodocs-publish-blocks' ),
							value: attributes.mediaAlt,
							onChange: ( mediaAlt ) => setAttributes( { mediaAlt } )
						} ),
						el( TextControl, {
							label: __( 'Backdrop label', 'autodocs-publish-blocks' ),
							help: __( 'Optional decorative text behind the image in the editorial composition.', 'autodocs-publish-blocks' ),
							value: attributes.visualLabel,
							onChange: ( visualLabel ) => setAttributes( { visualLabel } )
						} ),
						el( RangeControl, {
							label: __( 'Image scale', 'autodocs-publish-blocks' ),
							value: attributes.imageScale,
							min: 60,
							max: 120,
							step: 5,
							onChange: ( imageScale ) => setAttributes( { imageScale } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Primary action', 'autodocs-publish-blocks' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Label', 'autodocs-publish-blocks' ),
							value: attributes.primaryLabel,
							onChange: ( primaryLabel ) => setAttributes( { primaryLabel } )
						} ),
						el( TextControl, {
							label: __( 'URL', 'autodocs-publish-blocks' ),
							value: attributes.primaryUrl,
							type: 'url',
							onChange: ( primaryUrl ) => setAttributes( { primaryUrl } )
						} ),
						el( ToggleControl, {
							label: __( 'Open in a new tab', 'autodocs-publish-blocks' ),
							checked: attributes.primaryNewTab,
							onChange: ( primaryNewTab ) => setAttributes( { primaryNewTab } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Secondary action', 'autodocs-publish-blocks' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Label', 'autodocs-publish-blocks' ),
							value: attributes.secondaryLabel,
							onChange: ( secondaryLabel ) => setAttributes( { secondaryLabel } )
						} ),
						el( TextControl, {
							label: __( 'URL', 'autodocs-publish-blocks' ),
							value: attributes.secondaryUrl,
							type: 'url',
							onChange: ( secondaryUrl ) => setAttributes( { secondaryUrl } )
						} ),
						el( ToggleControl, {
							label: __( 'Open in a new tab', 'autodocs-publish-blocks' ),
							checked: attributes.secondaryNewTab,
							onChange: ( secondaryNewTab ) => setAttributes( { secondaryNewTab } )
						} )
					),
					el(
						PanelColorSettings,
						{
							title: __( 'Custom colors', 'autodocs-publish-blocks' ),
							initialOpen: false,
							colorSettings: [
								{ value: attributes.panelColor, onChange: ( panelColor ) => setAttributes( { panelColor: panelColor || '' } ), label: __( 'Text panel', 'autodocs-publish-blocks' ) },
								{ value: attributes.visualColor, onChange: ( visualColor ) => setAttributes( { visualColor: visualColor || '' } ), label: __( 'Visual panel', 'autodocs-publish-blocks' ) },
								{ value: attributes.accentColor, onChange: ( accentColor ) => setAttributes( { accentColor: accentColor || '' } ), label: __( 'Accent', 'autodocs-publish-blocks' ) }
							]
						}
					)
				),
				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: 'autodocs-publish-hero__copy' },
						el( RichText, {
							tagName: 'p',
							className: 'autodocs-publish-hero__eyebrow',
							value: attributes.eyebrow,
							onChange: ( eyebrow ) => setAttributes( { eyebrow } ),
							allowedFormats: [],
							placeholder: __( 'Hero eyebrow…', 'autodocs-publish-blocks' )
						} ),
						el( RichText, {
							tagName: 'h1',
							className: 'autodocs-publish-hero__title',
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
							allowedFormats: [],
							placeholder: __( 'Hero title…', 'autodocs-publish-blocks' )
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'autodocs-publish-hero__description',
							value: attributes.description,
							onChange: ( description ) => setAttributes( { description } ),
							placeholder: __( 'Explain the value of this site…', 'autodocs-publish-blocks' )
						} ),
						( attributes.primaryLabel || attributes.secondaryLabel ) && el(
							'div',
							{ className: 'autodocs-publish-hero__actions' },
							attributes.primaryLabel && el( RichText, {
								tagName: 'span',
								className: 'autodocs-publish-hero__button autodocs-publish-hero__button--primary wp-element-button',
								value: attributes.primaryLabel,
								onChange: ( primaryLabel ) => setAttributes( { primaryLabel } ),
								allowedFormats: [],
								'aria-label': __( 'Primary action label', 'autodocs-publish-blocks' ),
								placeholder: __( 'Primary action…', 'autodocs-publish-blocks' )
							} ),
							attributes.secondaryLabel && el( RichText, {
								tagName: 'span',
								className: 'autodocs-publish-hero__button autodocs-publish-hero__button--secondary wp-element-button',
								value: attributes.secondaryLabel,
								onChange: ( secondaryLabel ) => setAttributes( { secondaryLabel } ),
								allowedFormats: [],
								'aria-label': __( 'Secondary action label', 'autodocs-publish-blocks' ),
								placeholder: __( 'Secondary action…', 'autodocs-publish-blocks' )
							} )
						)
					),
					visual
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
