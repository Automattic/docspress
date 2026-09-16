( function ( blockEditor, components, element, hooks, i18n ) {
	'use strict';

	const { InspectorControls, MediaUpload, MediaUploadCheck, PanelColorSettings, PlainText, RichText, useBlockProps } = blockEditor;
	const { Button, ButtonGroup, PanelBody, RangeControl, SelectControl, TextareaControl, TextControl, ToggleControl } = components;
	const { Fragment, createElement: el, useState } = element;
	const { __ } = i18n;
	const designPreset = window.autodocsPublishBlocksSettings && window.autodocsPublishBlocksSettings.preset
		? window.autodocsPublishBlocksSettings.preset.replace( /[^a-z0-9-]/g, '' )
		: 'custom';
	const presetClass = `autodocs-publish-blocks--preset-${ designPreset }`;
	const themeStyle = window.autodocsPublishBlocksSettings && window.autodocsPublishBlocksSettings.tokens
		? window.autodocsPublishBlocksSettings.tokens
		: {};
	const designSupports = {
		anchor: true,
		html: false,
		className: true,
		color: {
			background: true,
			gradients: true,
			link: true,
			text: true
		},
		spacing: {
			blockGap: true,
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

	const languages = [
		{ label: 'Bash', value: 'bash' },
		{ label: 'CSS', value: 'css' },
		{ label: 'HTML', value: 'html' },
		{ label: 'JavaScript', value: 'javascript' },
		{ label: 'JSON', value: 'json' },
		{ label: 'JSX', value: 'jsx' },
		{ label: 'Markdown', value: 'markdown' },
		{ label: 'PHP', value: 'php' },
		{ label: __( 'Plain text', 'autodocs-publish-blocks' ), value: 'plaintext' },
		{ label: 'Python', value: 'python' },
		{ label: 'Shell', value: 'shell' },
		{ label: 'SQL', value: 'sql' },
		{ label: 'TSX', value: 'tsx' },
		{ label: 'TypeScript', value: 'typescript' },
		{ label: 'YAML', value: 'yaml' }
	];

	function registerBlockTypeWithLegacyAlias( blocks, name, settings ) {
		const registered = blocks.registerBlockType( name, settings );
		const prefix = 'autodocs-publish/';
		if ( name.indexOf( prefix ) !== 0 ) {
			return registered;
		}

		const legacyName = `docspress/${ name.slice( prefix.length ) }`;
		if ( ! blocks.getBlockType( legacyName ) ) {
			blocks.registerBlockType( legacyName, {
				...settings,
				supports: {
					...( settings.supports || {} ),
					inserter: false
				}
			} );
		}

		return registered;
	}

	hooks.addFilter(
		'blocks.getBlockDefaultClassName',
		'autodocs-publish/legacy-block-class',
		( className, blockName ) => blockName.indexOf( 'docspress/' ) === 0
			? `wp-block-autodocs-publish-${ blockName.slice( 'docspress/'.length ) }`
			: className
	);

	function CodeSettings( { attributes, setAttributes, includeHighlights = true } ) {
		return el(
			PanelBody,
			{ title: __( 'Code display', 'autodocs-publish-blocks' ), initialOpen: true },
			el( ToggleControl, {
				label: __( 'Show line numbers', 'autodocs-publish-blocks' ),
				checked: attributes.showLineNumbers,
				onChange: ( showLineNumbers ) => setAttributes( { showLineNumbers } )
			} ),
			includeHighlights && el( TextControl, {
				label: __( 'Highlighted lines', 'autodocs-publish-blocks' ),
				help: __( 'Use commas and ranges, for example 2,4-6.', 'autodocs-publish-blocks' ),
				value: attributes.highlightedLines,
				onChange: ( highlightedLines ) => setAttributes( { highlightedLines } )
			} ),
			el( TextControl, {
				label: __( 'Caption', 'autodocs-publish-blocks' ),
				value: attributes.caption,
				onChange: ( caption ) => setAttributes( { caption } )
			} )
		);
	}

	window.autodocsPublishBlocksEditor = {
		Button,
		ButtonGroup,
		CodeSettings,
		Fragment,
		InspectorControls,
		MediaUpload,
		MediaUploadCheck,
		PanelBody,
		PanelColorSettings,
		PlainText,
		RangeControl,
		RichText,
		SelectControl,
		TextareaControl,
		TextControl,
		ToggleControl,
		__,
		designSupports,
		el,
		languages,
		presetClass,
		registerBlockTypeWithLegacyAlias,
		themeStyle,
		useBlockProps,
		useState
	};
} )( window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.hooks, window.wp.i18n );
