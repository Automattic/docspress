( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
	const { Button, Fragment, InspectorControls, PanelBody, RichText, SelectControl, TextControl, TextareaControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const defaults = window.docspressFieldsDefaults || [];
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

	registerBlockType( 'docspress/fields', {
		apiVersion: 3,
		title: __( 'DocsPress: Fields / Schema', 'docspress-blocks' ),
		description: __( 'Document typed API parameters, configuration keys, environment variables, or CLI options.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'schema', 'docspress-blocks' ), __( 'parameters', 'docspress-blocks' ), __( 'configuration', 'docspress-blocks' ) ],
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
				className: `docspress-fields docspress-fields--editor ${ attributes.compact ? 'is-compact ' : '' }${ presetClass }`,
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
						{ title: __( 'Reference display', 'docspress-blocks' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Show field filter', 'docspress-blocks' ),
							checked: attributes.searchable,
							onChange: ( searchable ) => setAttributes( { searchable } )
						} ),
						el( ToggleControl, {
							label: __( 'Compact rows', 'docspress-blocks' ),
							checked: attributes.compact,
							onChange: ( compact ) => setAttributes( { compact } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Fields', 'docspress-blocks' ), initialOpen: false },
						attributes.fields.map( ( field, index ) => el(
							'div',
							{ className: 'docspress-fields__field-editor', key: `field-${ index }` },
							el( TextControl, {
								label: __( 'Name', 'docspress-blocks' ),
								value: field.name || '',
								onChange: ( name ) => updateField( attributes, setAttributes, index, { name } )
							} ),
							el( SelectControl, {
								label: __( 'Type', 'docspress-blocks' ),
								value: field.type || 'string',
								options: types,
								onChange: ( type ) => updateField( attributes, setAttributes, index, { type } )
							} ),
							el( TextareaControl, {
								label: __( 'Description', 'docspress-blocks' ),
								value: field.description || '',
								onChange: ( description ) => updateField( attributes, setAttributes, index, { description } )
							} ),
							el( TextControl, {
								label: __( 'Default value', 'docspress-blocks' ),
								value: field.defaultValue || '',
								onChange: ( defaultValue ) => updateField( attributes, setAttributes, index, { defaultValue } )
							} ),
							el( TextControl, {
								label: __( 'Allowed values', 'docspress-blocks' ),
								help: __( 'Separate values with commas.', 'docspress-blocks' ),
								value: field.values || '',
								onChange: ( values ) => updateField( attributes, setAttributes, index, { values } )
							} ),
							el( ToggleControl, {
								label: __( 'Required', 'docspress-blocks' ),
								checked: Boolean( field.required ),
								onChange: ( required ) => updateField( attributes, setAttributes, index, { required } )
							} ),
							el( ToggleControl, {
								label: __( 'Deprecated', 'docspress-blocks' ),
								checked: Boolean( field.deprecated ),
								onChange: ( deprecated ) => updateField( attributes, setAttributes, index, { deprecated } )
							} ),
							el(
								'div',
								{ className: 'docspress-fields__field-actions' },
								el( Button, {
									variant: 'tertiary',
									disabled: index === 0,
									onClick: () => {
										const fields = attributes.fields.slice();
										[ fields[ index - 1 ], fields[ index ] ] = [ fields[ index ], fields[ index - 1 ] ];
										setAttributes( { fields } );
									}
								}, __( 'Move up', 'docspress-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									disabled: index === attributes.fields.length - 1,
									onClick: () => {
										const fields = attributes.fields.slice();
										[ fields[ index ], fields[ index + 1 ] ] = [ fields[ index + 1 ], fields[ index ] ];
										setAttributes( { fields } );
									}
								}, __( 'Move down', 'docspress-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									isDestructive: true,
									onClick: () => setAttributes( { fields: attributes.fields.filter( ( _, itemIndex ) => itemIndex !== index ) } )
								}, __( 'Remove', 'docspress-blocks' ) )
							)
						) ),
						attributes.fields.length < 40 && el( Button, {
							variant: 'secondary',
							onClick: () => setAttributes( { fields: [ ...attributes.fields, { name: 'field', type: 'string', required: false, defaultValue: '', description: '', values: '', deprecated: false } ] } )
						}, __( 'Add field', 'docspress-blocks' ) )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'docspress-fields__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'docspress-fields__eyebrow' }, __( 'Reference', 'docspress-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								className: 'docspress-fields__title',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Reference title…', 'docspress-blocks' )
							} ),
							el( RichText, {
								tagName: 'div',
								className: 'docspress-fields__description',
								value: attributes.description,
								onChange: ( description ) => setAttributes( { description } ),
								allowedFormats: [ 'core/bold', 'core/italic', 'core/code' ],
								placeholder: __( 'What these fields configure…', 'docspress-blocks' )
							} )
						),
						el( 'span', { className: 'docspress-fields__count' }, `${ attributes.fields.length } ${ __( 'fields', 'docspress-blocks' ) }` )
					),
					el(
						'dl',
						{ className: 'docspress-fields__list' },
						attributes.fields.slice( 0, 8 ).map( ( field, index ) => el(
							'div',
							{ className: `docspress-fields__item${ field.deprecated ? ' is-deprecated' : '' }`, key: `preview-field-${ index }` },
							el(
								'dt',
								{ className: 'docspress-fields__term' },
								el( 'code', null, field.name || __( 'field', 'docspress-blocks' ) ),
								el( 'span', { className: 'docspress-fields__type' }, field.type || 'string' ),
								field.required && el( 'span', { className: 'docspress-fields__badge is-required' }, __( 'required', 'docspress-blocks' ) ),
								field.deprecated && el( 'span', { className: 'docspress-fields__badge is-deprecated' }, __( 'deprecated', 'docspress-blocks' ) )
							),
							el(
								'dd',
								{ className: 'docspress-fields__definition' },
								el( 'p', { className: 'docspress-fields__copy' }, field.description || __( 'Describe this field in the block sidebar.', 'docspress-blocks' ) ),
								( field.defaultValue || field.values ) && el(
									'div',
									{ className: 'docspress-fields__metadata' },
									field.defaultValue && el( 'span', null, el( 'b', null, __( 'Default', 'docspress-blocks' ) ), el( 'code', null, field.defaultValue ) ),
									field.values && el( 'span', null, el( 'b', null, __( 'Values', 'docspress-blocks' ) ), el( 'code', null, field.values ) )
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
} )( window.wp.blocks, window.docspressBlocksEditor );
