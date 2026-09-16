( function ( blocks, blockEditor, components, element, i18n, shared ) {
	'use strict';

	const el = element.createElement;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const ToggleControl = components.ToggleControl;
	const SelectControl = components.SelectControl;
	const __ = i18n.__;

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/version-switcher', {
		title: __( 'Version Switcher', 'autodocs-publish-blocks' ),
		description: __( 'Let readers move between API documentation versions.', 'autodocs-publish-blocks' ),
		category: 'theme',
		icon: 'randomize',
		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( {
				className: `autodocs-publish-version-switcher autodocs-publish-version-switcher--${ attributes.presentation }`,
			} );
			const preview = attributes.presentation === 'links'
				? el( 'ul', { className: 'autodocs-publish-version-switcher__links' },
					el( 'li', null, el( 'a', { 'aria-current': 'page' }, 'v3 ', attributes.showLatestBadge && el( 'span', { className: 'autodocs-publish-version-switcher__badge' }, __( 'Latest', 'autodocs-publish-blocks' ) ) ) ),
					el( 'li', null, el( 'a', null, 'v2' ) ),
					el( 'li', null, el( 'a', null, 'v1' ) )
				)
				: el( 'span', { className: 'autodocs-publish-version-switcher__control' },
					el( 'select', { value: 'v3', disabled: true }, el( 'option', null, attributes.showLatestBadge ? 'v3 — Latest' : 'v3' ) ),
					el( 'span', { className: 'autodocs-publish-version-switcher__chevron', 'aria-hidden': true } )
				);

			return el( element.Fragment, null,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Version switcher', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextControl, { label: __( 'Label', 'autodocs-publish-blocks' ), value: attributes.label, onChange: function ( value ) { setAttributes( { label: value } ); } } ),
						el( ToggleControl, { label: __( 'Show label', 'autodocs-publish-blocks' ), checked: attributes.showLabel, onChange: function ( value ) { setAttributes( { showLabel: value } ); } } ),
						el( SelectControl, {
							label: __( 'Presentation', 'autodocs-publish-blocks' ),
							value: attributes.presentation,
							options: [
								{ label: __( 'Select menu', 'autodocs-publish-blocks' ), value: 'select' },
								{ label: __( 'Link list', 'autodocs-publish-blocks' ), value: 'links' },
							],
							onChange: function ( value ) { setAttributes( { presentation: value } ); },
						} ),
						el( ToggleControl, { label: __( 'Show latest badge', 'autodocs-publish-blocks' ), checked: attributes.showLatestBadge, onChange: function ( value ) { setAttributes( { showLatestBadge: value } ); } } ),
						el( ToggleControl, { label: __( 'Hide when only one version exists', 'autodocs-publish-blocks' ), checked: attributes.hideSingle, onChange: function ( value ) { setAttributes( { hideSingle: value } ); } } ),
						el( TextControl, { label: __( 'Missing page wording', 'autodocs-publish-blocks' ), value: attributes.unavailableLabel, onChange: function ( value ) { setAttributes( { unavailableLabel: value } ); } } )
					)
				),
				el( 'div', blockProps,
					el( 'span', { className: attributes.showLabel ? 'autodocs-publish-version-switcher__label' : 'autodocs-publish-version-switcher__label screen-reader-text' }, attributes.label ),
					preview
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.autodocsPublishBlocksEditor );
