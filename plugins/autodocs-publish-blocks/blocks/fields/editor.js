( function ( blocks, shared ) {
	'use strict';

	const { Button, Fragment, InspectorControls, PanelBody, RichText, SelectControl, TextControl, TextareaControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const defaults = window.autodocsPublishFieldsDefaults || [];
	const types = [ 'string', 'number', 'boolean', 'object', 'array', 'enum', 'url', 'date', 'any' ].map( ( type ) => ( { label: type, value: type } ) );
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M5 6h14M5 12h14M5 18h14M8 4v4M15 10v4M11 16v4' } )
	);

	function updateField( attributes, setAttributes, index, patch ) {
		const fields = attributes.fields.slice();
		fields[ index ] = { ...fields[ index ], ...patch };
		setAttributes( { fields } );
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/fields', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Fields / Schema', 'autodocs-publish-blocks' ),
		description: __( 'Document typed API parameters, configuration keys, environment variables, or CLI options.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'schema', 'autodocs-publish-blocks' ), __( 'parameters', 'autodocs-publish-blocks' ), __( 'configuration', 'autodocs-publish-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Configuration fields' },
			description: { type: 'string', default: 'Typed options, defaults, and constraints in one scannable reference.' },
			fields: { type: 'array', default: defaults },
			searchable: { type: 'boolean', default: true },
			compact: { type: 'boolean', default: false }
		},
		supports: designSupports,
		edit: function FieldsEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( {
				className: `autodocs-publish-fields autodocs-publish-fields--editor ${ attributes.compact ? 'is-compact ' : '' }${ presetClass }`,
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
						{ title: __( 'Reference display', 'autodocs-publish-blocks' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Show field filter', 'autodocs-publish-blocks' ),
							checked: attributes.searchable,
							onChange: ( searchable ) => setAttributes( { searchable } )
						} ),
						el( ToggleControl, {
							label: __( 'Compact rows', 'autodocs-publish-blocks' ),
							checked: attributes.compact,
							onChange: ( compact ) => setAttributes( { compact } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Fields', 'autodocs-publish-blocks' ), initialOpen: false },
						attributes.fields.map( ( field, index ) => el(
							'div',
							{ className: 'autodocs-publish-fields__field-editor', key: `field-${ index }` },
							el( TextControl, {
								label: __( 'Name', 'autodocs-publish-blocks' ),
								value: field.name || '',
								onChange: ( name ) => updateField( attributes, setAttributes, index, { name } )
							} ),
							el( SelectControl, {
								label: __( 'Type', 'autodocs-publish-blocks' ),
								value: field.type || 'string',
								options: types,
								onChange: ( type ) => updateField( attributes, setAttributes, index, { type } )
							} ),
							el( TextareaControl, {
								label: __( 'Description', 'autodocs-publish-blocks' ),
								value: field.description || '',
								onChange: ( description ) => updateField( attributes, setAttributes, index, { description } )
							} ),
							el( TextControl, {
								label: __( 'Default value', 'autodocs-publish-blocks' ),
								value: field.defaultValue || '',
								onChange: ( defaultValue ) => updateField( attributes, setAttributes, index, { defaultValue } )
							} ),
							el( TextControl, {
								label: __( 'Allowed values', 'autodocs-publish-blocks' ),
								help: __( 'Separate values with commas.', 'autodocs-publish-blocks' ),
								value: field.values || '',
								onChange: ( values ) => updateField( attributes, setAttributes, index, { values } )
							} ),
							el( ToggleControl, {
								label: __( 'Required', 'autodocs-publish-blocks' ),
								checked: Boolean( field.required ),
								onChange: ( required ) => updateField( attributes, setAttributes, index, { required } )
							} ),
							el( ToggleControl, {
								label: __( 'Deprecated', 'autodocs-publish-blocks' ),
								checked: Boolean( field.deprecated ),
								onChange: ( deprecated ) => updateField( attributes, setAttributes, index, { deprecated } )
							} ),
							el(
								'div',
								{ className: 'autodocs-publish-fields__field-actions' },
								el( Button, {
									variant: 'tertiary',
									disabled: index === 0,
									onClick: () => {
										const fields = attributes.fields.slice();
										[ fields[ index - 1 ], fields[ index ] ] = [ fields[ index ], fields[ index - 1 ] ];
										setAttributes( { fields } );
									}
								}, __( 'Move up', 'autodocs-publish-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									disabled: index === attributes.fields.length - 1,
									onClick: () => {
										const fields = attributes.fields.slice();
										[ fields[ index ], fields[ index + 1 ] ] = [ fields[ index + 1 ], fields[ index ] ];
										setAttributes( { fields } );
									}
								}, __( 'Move down', 'autodocs-publish-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									isDestructive: true,
									onClick: () => setAttributes( { fields: attributes.fields.filter( ( _, itemIndex ) => itemIndex !== index ) } )
								}, __( 'Remove', 'autodocs-publish-blocks' ) )
							)
						) ),
						attributes.fields.length < 40 && el( Button, {
							variant: 'secondary',
							onClick: () => setAttributes( { fields: [ ...attributes.fields, { name: 'field', type: 'string', required: false, defaultValue: '', description: '', values: '', deprecated: false } ] } )
						}, __( 'Add field', 'autodocs-publish-blocks' ) )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'autodocs-publish-fields__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'autodocs-publish-fields__eyebrow' }, __( 'Reference', 'autodocs-publish-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								className: 'autodocs-publish-fields__title',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Reference title…', 'autodocs-publish-blocks' )
							} ),
							el( RichText, {
								tagName: 'div',
								className: 'autodocs-publish-fields__description',
								value: attributes.description,
								onChange: ( description ) => setAttributes( { description } ),
								allowedFormats: [ 'core/bold', 'core/italic', 'core/code' ],
								placeholder: __( 'What these fields configure…', 'autodocs-publish-blocks' )
							} )
						),
						el( 'span', { className: 'autodocs-publish-fields__count' }, `${ attributes.fields.length } ${ __( 'fields', 'autodocs-publish-blocks' ) }` )
					),
					el(
						'dl',
						{ className: 'autodocs-publish-fields__list' },
						attributes.fields.slice( 0, 8 ).map( ( field, index ) => el(
							'div',
							{ className: `autodocs-publish-fields__item${ field.deprecated ? ' is-deprecated' : '' }`, key: `preview-field-${ index }` },
							el(
								'dt',
								{ className: 'autodocs-publish-fields__term' },
								el( 'code', null, field.name || __( 'field', 'autodocs-publish-blocks' ) ),
								el( 'span', { className: 'autodocs-publish-fields__type' }, field.type || 'string' ),
								field.required && el( 'span', { className: 'autodocs-publish-fields__badge is-required' }, __( 'required', 'autodocs-publish-blocks' ) ),
								field.deprecated && el( 'span', { className: 'autodocs-publish-fields__badge is-deprecated' }, __( 'deprecated', 'autodocs-publish-blocks' ) )
							),
							el(
								'dd',
								{ className: 'autodocs-publish-fields__definition' },
								el( 'p', { className: 'autodocs-publish-fields__copy' }, field.description || __( 'Describe this field in the block sidebar.', 'autodocs-publish-blocks' ) ),
								( field.defaultValue || field.values ) && el(
									'div',
									{ className: 'autodocs-publish-fields__metadata' },
									field.defaultValue && el( 'span', null, el( 'b', null, __( 'Default', 'autodocs-publish-blocks' ) ), el( 'code', null, field.defaultValue ) ),
									field.values && el( 'span', null, el( 'b', null, __( 'Values', 'autodocs-publish-blocks' ) ), el( 'code', null, field.values ) )
								)
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
