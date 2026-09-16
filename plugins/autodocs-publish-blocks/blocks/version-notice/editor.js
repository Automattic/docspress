( function ( blocks, blockEditor, components, element, i18n, shared ) {
	'use strict';

	const el = element.createElement;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const TextareaControl = components.TextareaControl;
	const ToggleControl = components.ToggleControl;
	const __ = i18n.__;

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/version-notice', {
		title: __( 'Version Notice', 'autodocs-publish-blocks' ),
		description: __( 'Warn readers when they are viewing historical documentation.', 'autodocs-publish-blocks' ),
		category: 'theme',
		icon: 'warning',
		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const message = attributes.message.replaceAll( '{current}', 'v1' ).replaceAll( '{latest}', 'v3' );
			const blockProps = useBlockProps( { className: 'autodocs-publish-version-notice', role: 'status' } );
			return el( element.Fragment, null,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Historical version notice', 'autodocs-publish-blocks' ), initialOpen: true },
						el( TextareaControl, {
							label: __( 'Message', 'autodocs-publish-blocks' ),
							help: __( 'Use {current} and {latest} placeholders.', 'autodocs-publish-blocks' ),
							value: attributes.message,
							onChange: function ( value ) { setAttributes( { message: value } ); },
						} ),
						el( TextControl, { label: __( 'Latest link label', 'autodocs-publish-blocks' ), value: attributes.latestLinkLabel, onChange: function ( value ) { setAttributes( { latestLinkLabel: value } ); } } ),
						el( ToggleControl, { label: __( 'Show icon', 'autodocs-publish-blocks' ), checked: attributes.showIcon, onChange: function ( value ) { setAttributes( { showIcon: value } ); } } ),
						el( ToggleControl, { label: __( 'Allow visitors to dismiss', 'autodocs-publish-blocks' ), checked: attributes.dismissible, onChange: function ( value ) { setAttributes( { dismissible: value } ); } } )
					)
				),
				el( 'aside', blockProps,
					attributes.showIcon && el( 'span', { className: 'autodocs-publish-version-notice__icon', 'aria-hidden': true }, 'i' ),
					el( 'div', { className: 'autodocs-publish-version-notice__content' },
						el( 'p', null, message ),
						el( 'a', null, attributes.latestLinkLabel, ' →' )
					),
					attributes.dismissible && el( 'button', { type: 'button', className: 'autodocs-publish-version-notice__dismiss', disabled: true, 'aria-label': __( 'Dismiss version notice', 'autodocs-publish-blocks' ) }, '×' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.autodocsPublishBlocksEditor );
