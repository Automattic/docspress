( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const TextareaControl = components.TextareaControl;
	const ToggleControl = components.ToggleControl;
	const __ = i18n.__;

	blocks.registerBlockType( 'docspress/version-notice', {
		title: __( 'Version Notice', 'docspress-blocks' ),
		description: __( 'Warn readers when they are viewing historical documentation.', 'docspress-blocks' ),
		category: 'theme',
		icon: 'warning',
		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const message = attributes.message.replaceAll( '{current}', 'v1' ).replaceAll( '{latest}', 'v3' );
			const blockProps = useBlockProps( { className: 'docspress-version-notice', role: 'status' } );
			return el( element.Fragment, null,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Historical version notice', 'docspress-blocks' ), initialOpen: true },
						el( TextareaControl, {
							label: __( 'Message', 'docspress-blocks' ),
							help: __( 'Use {current} and {latest} placeholders.', 'docspress-blocks' ),
							value: attributes.message,
							onChange: function ( value ) { setAttributes( { message: value } ); },
						} ),
						el( TextControl, { label: __( 'Latest link label', 'docspress-blocks' ), value: attributes.latestLinkLabel, onChange: function ( value ) { setAttributes( { latestLinkLabel: value } ); } } ),
						el( ToggleControl, { label: __( 'Show icon', 'docspress-blocks' ), checked: attributes.showIcon, onChange: function ( value ) { setAttributes( { showIcon: value } ); } } ),
						el( ToggleControl, { label: __( 'Allow visitors to dismiss', 'docspress-blocks' ), checked: attributes.dismissible, onChange: function ( value ) { setAttributes( { dismissible: value } ); } } )
					)
				),
				el( 'aside', blockProps,
					attributes.showIcon && el( 'span', { className: 'docspress-version-notice__icon', 'aria-hidden': true }, 'i' ),
					el( 'div', { className: 'docspress-version-notice__content' },
						el( 'p', null, message ),
						el( 'a', null, attributes.latestLinkLabel, ' →' )
					),
					attributes.dismissible && el( 'button', { type: 'button', className: 'docspress-version-notice__dismiss', disabled: true, 'aria-label': __( 'Dismiss version notice', 'docspress-blocks' ) }, '×' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
