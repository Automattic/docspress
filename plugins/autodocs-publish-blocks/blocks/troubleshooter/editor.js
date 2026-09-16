( function ( blocks, shared ) {
	'use strict';

	const { Button, Fragment, InspectorControls, PanelBody, RichText, SelectControl, TextControl, TextareaControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const defaults = window.autodocsPublishTroubleshooterDefaults || { questions: [], outcomes: [] };
	const statuses = [
		{ label: __( 'Success', 'autodocs-publish-blocks' ), value: 'success' },
		{ label: __( 'Neutral', 'autodocs-publish-blocks' ), value: 'neutral' },
		{ label: __( 'Warning', 'autodocs-publish-blocks' ), value: 'warning' },
		{ label: __( 'Error', 'autodocs-publish-blocks' ), value: 'error' }
	];
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M5 5h5v5H5zM14 14h5v5h-5zM10 7.5h3a3 3 0 0 1 3 3V14M7.5 10v9h3' } )
	);

	function normalizeId( value, fallback ) {
		const id = String( value || '' ).toLowerCase().trim().replace( /[^a-z0-9_-]+/g, '-' ).replace( /^-+|-+$/g, '' );
		return id || fallback;
	}

	function updateItem( items, index, patch ) {
		return items.map( ( item, itemIndex ) => itemIndex === index ? { ...item, ...patch } : item );
	}

	shared.registerBlockTypeWithLegacyAlias( blocks, 'autodocs-publish/troubleshooter', {
		apiVersion: 3,
		title: __( 'Autodocs Publish: Troubleshooter', 'autodocs-publish-blocks' ),
		description: __( 'Guide readers through a short decision tree to the right next step.', 'autodocs-publish-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'decision tree', 'autodocs-publish-blocks' ), __( 'diagnose', 'autodocs-publish-blocks' ), __( 'support', 'autodocs-publish-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Find the next step' },
			intro: { type: 'string', default: 'Answer two quick questions to get the right Autodocs Publish workflow.' },
			startId: { type: 'string', default: 'source' },
			questions: { type: 'array', default: defaults.questions || [] },
			outcomes: { type: 'array', default: defaults.outcomes || [] },
			showProgress: { type: 'boolean', default: true }
		},
		supports: { ...designSupports, align: [ 'wide' ] },
		edit: function TroubleshooterEdit( { attributes, setAttributes } ) {
			const questions = Array.isArray( attributes.questions ) ? attributes.questions : [];
			const outcomes = Array.isArray( attributes.outcomes ) ? attributes.outcomes : [];
			const destinations = [
				{ label: __( 'Choose a destination', 'autodocs-publish-blocks' ), value: '' },
				...questions.map( ( question, index ) => ( {
					label: `${ __( 'Question', 'autodocs-publish-blocks' ) }: ${ question.id || index + 1 }`,
					value: question.id || ''
				} ) ),
				...outcomes.map( ( outcome, index ) => ( {
					label: `${ __( 'Outcome', 'autodocs-publish-blocks' ) }: ${ outcome.id || index + 1 }`,
					value: outcome.id || ''
				} ) )
			];
			const startOptions = questions.map( ( question, index ) => ( {
				label: question.question || question.id || `${ __( 'Question', 'autodocs-publish-blocks' ) } ${ index + 1 }`,
				value: question.id || ''
			} ) );
			const firstQuestion = questions.find( ( question ) => question.id === attributes.startId ) || questions[0] || {};
			const blockProps = useBlockProps( {
				className: `autodocs-publish-troubleshooter autodocs-publish-troubleshooter--editor ${ presetClass }`,
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
						{ title: __( 'Troubleshooter settings', 'autodocs-publish-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Starting question', 'autodocs-publish-blocks' ),
							value: attributes.startId,
							options: startOptions,
							onChange: ( startId ) => setAttributes( { startId } )
						} ),
						el( ToggleControl, {
							label: __( 'Show progress', 'autodocs-publish-blocks' ),
							checked: attributes.showProgress,
							onChange: ( showProgress ) => setAttributes( { showProgress } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Questions', 'autodocs-publish-blocks' ), initialOpen: false },
						...questions.map( ( question, index ) => el(
							'div',
							{ className: 'autodocs-publish-troubleshooter__branch-editor', key: `question-${ index }` },
							el( TextControl, {
								label: __( 'Question ID', 'autodocs-publish-blocks' ),
								value: question.id || '',
								onChange: ( id ) => setAttributes( { questions: updateItem( questions, index, { id: normalizeId( id, `question-${ index + 1 }` ) } ) } )
							} ),
							el( TextareaControl, {
								label: __( 'Question', 'autodocs-publish-blocks' ),
								value: question.question || '',
								onChange: ( value ) => setAttributes( { questions: updateItem( questions, index, { question: value } ) } )
							} ),
							el( TextControl, {
								label: __( 'First answer label', 'autodocs-publish-blocks' ),
								value: question.yesLabel || '',
								onChange: ( yesLabel ) => setAttributes( { questions: updateItem( questions, index, { yesLabel } ) } )
							} ),
							el( SelectControl, {
								label: __( 'First answer destination', 'autodocs-publish-blocks' ),
								value: question.yesNext || '',
								options: destinations,
								onChange: ( yesNext ) => setAttributes( { questions: updateItem( questions, index, { yesNext } ) } )
							} ),
							el( TextControl, {
								label: __( 'Second answer label', 'autodocs-publish-blocks' ),
								value: question.noLabel || '',
								onChange: ( noLabel ) => setAttributes( { questions: updateItem( questions, index, { noLabel } ) } )
							} ),
							el( SelectControl, {
								label: __( 'Second answer destination', 'autodocs-publish-blocks' ),
								value: question.noNext || '',
								options: destinations,
								onChange: ( noNext ) => setAttributes( { questions: updateItem( questions, index, { noNext } ) } )
							} ),
							el(
								'div',
								{ className: 'autodocs-publish-troubleshooter__editor-actions' },
								el( Button, {
									variant: 'tertiary',
									disabled: index === 0,
									onClick: () => {
										const next = questions.slice();
										[ next[ index - 1 ], next[ index ] ] = [ next[ index ], next[ index - 1 ] ];
										setAttributes( { questions: next } );
									}
								}, __( 'Move up', 'autodocs-publish-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									disabled: index === questions.length - 1,
									onClick: () => {
										const next = questions.slice();
										[ next[ index ], next[ index + 1 ] ] = [ next[ index + 1 ], next[ index ] ];
										setAttributes( { questions: next } );
									}
								}, __( 'Move down', 'autodocs-publish-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									isDestructive: true,
									disabled: questions.length === 1,
									onClick: () => setAttributes( { questions: questions.filter( ( _, itemIndex ) => itemIndex !== index ) } )
								}, __( 'Remove', 'autodocs-publish-blocks' ) )
							)
						) ),
						questions.length < 12 && el( Button, {
							variant: 'secondary',
							onClick: () => {
								const id = `question-${ questions.length + 1 }`;
								setAttributes( { questions: [ ...questions, { id, question: 'What should the reader check?', yesLabel: 'Yes', yesNext: '', noLabel: 'No', noNext: '' } ] } );
							}
						}, __( 'Add question', 'autodocs-publish-blocks' ) )
					),
					el(
						PanelBody,
						{ title: __( 'Outcomes', 'autodocs-publish-blocks' ), initialOpen: false },
						...outcomes.map( ( outcome, index ) => el(
							'div',
							{ className: 'autodocs-publish-troubleshooter__branch-editor', key: `outcome-${ index }` },
							el( TextControl, {
								label: __( 'Outcome ID', 'autodocs-publish-blocks' ),
								value: outcome.id || '',
								onChange: ( id ) => setAttributes( { outcomes: updateItem( outcomes, index, { id: normalizeId( id, `outcome-${ index + 1 }` ) } ) } )
							} ),
							el( SelectControl, {
								label: __( 'Status', 'autodocs-publish-blocks' ),
								value: outcome.status || 'neutral',
								options: statuses,
								onChange: ( status ) => setAttributes( { outcomes: updateItem( outcomes, index, { status } ) } )
							} ),
							el( TextControl, {
								label: __( 'Title', 'autodocs-publish-blocks' ),
								value: outcome.title || '',
								onChange: ( title ) => setAttributes( { outcomes: updateItem( outcomes, index, { title } ) } )
							} ),
							el( TextareaControl, {
								label: __( 'Content', 'autodocs-publish-blocks' ),
								help: __( 'Basic HTML such as paragraphs, links, and code is supported.', 'autodocs-publish-blocks' ),
								value: outcome.content || '',
								onChange: ( content ) => setAttributes( { outcomes: updateItem( outcomes, index, { content } ) } )
							} ),
							el( Button, {
								variant: 'tertiary',
								isDestructive: true,
								disabled: outcomes.length === 1,
								onClick: () => setAttributes( { outcomes: outcomes.filter( ( _, itemIndex ) => itemIndex !== index ) } )
							}, __( 'Remove outcome', 'autodocs-publish-blocks' ) )
						) ),
						outcomes.length < 12 && el( Button, {
							variant: 'secondary',
							onClick: () => {
								const id = `outcome-${ outcomes.length + 1 }`;
								setAttributes( { outcomes: [ ...outcomes, { id, status: 'neutral', title: 'Next step', content: '<p>Explain what the reader should do next.</p>' } ] } );
							}
						}, __( 'Add outcome', 'autodocs-publish-blocks' ) )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'autodocs-publish-troubleshooter__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'autodocs-publish-troubleshooter__eyebrow' }, __( 'Guided diagnosis', 'autodocs-publish-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Troubleshooter title…', 'autodocs-publish-blocks' )
							} ),
							el( RichText, {
								tagName: 'div',
								className: 'autodocs-publish-troubleshooter__intro',
								value: attributes.intro,
								onChange: ( intro ) => setAttributes( { intro } ),
								placeholder: __( 'Explain what this troubleshooter resolves…', 'autodocs-publish-blocks' )
							} )
						),
						el(
							'span',
							{ className: 'autodocs-publish-troubleshooter__route', 'aria-hidden': true },
							el( 'span' ), el( 'span' ), el( 'span' )
						)
					),
					attributes.showProgress && el(
						'div',
						{ className: 'autodocs-publish-troubleshooter__progress' },
						el( 'span', { className: 'autodocs-publish-troubleshooter__progress-bar' }, el( 'i', { style: { width: '34%' } } ) ),
						el( 'span', { className: 'autodocs-publish-troubleshooter__progress-label' }, __( 'Question 1', 'autodocs-publish-blocks' ) )
					),
					el(
						'div',
						{ className: 'autodocs-publish-troubleshooter__stage' },
						el(
							'section',
							{ className: 'autodocs-publish-troubleshooter__question' },
							el( 'span', { className: 'autodocs-publish-troubleshooter__step' }, __( 'Starting question', 'autodocs-publish-blocks' ) ),
							el( 'h4', null, firstQuestion.question || __( 'Add a question in the block sidebar.', 'autodocs-publish-blocks' ) ),
							el(
								'div',
								{ className: 'autodocs-publish-troubleshooter__choices' },
								el( 'button', { type: 'button', className: 'autodocs-publish-troubleshooter__choice is-primary' }, el( 'span', null, firstQuestion.yesLabel || __( 'First answer', 'autodocs-publish-blocks' ) ), el( 'b', null, '→' ) ),
								el( 'button', { type: 'button', className: 'autodocs-publish-troubleshooter__choice' }, el( 'span', null, firstQuestion.noLabel || __( 'Second answer', 'autodocs-publish-blocks' ) ), el( 'b', null, '→' ) )
							),
							el( 'p', { className: 'autodocs-publish-troubleshooter__editor-note' }, `${ questions.length } ${ __( 'questions', 'autodocs-publish-blocks' ) } · ${ outcomes.length } ${ __( 'outcomes', 'autodocs-publish-blocks' ) }` )
						)
					),
					el(
						'footer',
						{ className: 'autodocs-publish-troubleshooter__footer' },
						el( 'button', { type: 'button', disabled: true }, __( 'Back', 'autodocs-publish-blocks' ) ),
						el( 'button', { type: 'button' }, __( 'Start over', 'autodocs-publish-blocks' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.autodocsPublishBlocksEditor );
