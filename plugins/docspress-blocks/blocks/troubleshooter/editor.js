( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
	const { Button, Fragment, InspectorControls, PanelBody, RichText, SelectControl, TextControl, TextareaControl, ToggleControl, __, designSupports, el, presetClass, themeStyle, useBlockProps } = shared;
	const defaults = window.docspressTroubleshooterDefaults || { questions: [], outcomes: [] };
	const statuses = [
		{ label: __( 'Success', 'docspress-blocks' ), value: 'success' },
		{ label: __( 'Neutral', 'docspress-blocks' ), value: 'neutral' },
		{ label: __( 'Warning', 'docspress-blocks' ), value: 'warning' },
		{ label: __( 'Error', 'docspress-blocks' ), value: 'error' }
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

	registerBlockType( 'docspress/troubleshooter', {
		apiVersion: 3,
		title: __( 'DocsPress: Troubleshooter', 'docspress-blocks' ),
		description: __( 'Guide readers through a short decision tree to the right next step.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'decision tree', 'docspress-blocks' ), __( 'diagnose', 'docspress-blocks' ), __( 'support', 'docspress-blocks' ) ],
		attributes: {
			title: { type: 'string', default: 'Find the next step' },
			intro: { type: 'string', default: 'Answer two quick questions to get the right DocsPress workflow.' },
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
				{ label: __( 'Choose a destination', 'docspress-blocks' ), value: '' },
				...questions.map( ( question, index ) => ( {
					label: `${ __( 'Question', 'docspress-blocks' ) }: ${ question.id || index + 1 }`,
					value: question.id || ''
				} ) ),
				...outcomes.map( ( outcome, index ) => ( {
					label: `${ __( 'Outcome', 'docspress-blocks' ) }: ${ outcome.id || index + 1 }`,
					value: outcome.id || ''
				} ) )
			];
			const startOptions = questions.map( ( question, index ) => ( {
				label: question.question || question.id || `${ __( 'Question', 'docspress-blocks' ) } ${ index + 1 }`,
				value: question.id || ''
			} ) );
			const firstQuestion = questions.find( ( question ) => question.id === attributes.startId ) || questions[0] || {};
			const blockProps = useBlockProps( {
				className: `docspress-troubleshooter docspress-troubleshooter--editor ${ presetClass }`,
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
						{ title: __( 'Troubleshooter settings', 'docspress-blocks' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Starting question', 'docspress-blocks' ),
							value: attributes.startId,
							options: startOptions,
							onChange: ( startId ) => setAttributes( { startId } )
						} ),
						el( ToggleControl, {
							label: __( 'Show progress', 'docspress-blocks' ),
							checked: attributes.showProgress,
							onChange: ( showProgress ) => setAttributes( { showProgress } )
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Questions', 'docspress-blocks' ), initialOpen: false },
						...questions.map( ( question, index ) => el(
							'div',
							{ className: 'docspress-troubleshooter__branch-editor', key: `question-${ index }` },
							el( TextControl, {
								label: __( 'Question ID', 'docspress-blocks' ),
								value: question.id || '',
								onChange: ( id ) => setAttributes( { questions: updateItem( questions, index, { id: normalizeId( id, `question-${ index + 1 }` ) } ) } )
							} ),
							el( TextareaControl, {
								label: __( 'Question', 'docspress-blocks' ),
								value: question.question || '',
								onChange: ( value ) => setAttributes( { questions: updateItem( questions, index, { question: value } ) } )
							} ),
							el( TextControl, {
								label: __( 'First answer label', 'docspress-blocks' ),
								value: question.yesLabel || '',
								onChange: ( yesLabel ) => setAttributes( { questions: updateItem( questions, index, { yesLabel } ) } )
							} ),
							el( SelectControl, {
								label: __( 'First answer destination', 'docspress-blocks' ),
								value: question.yesNext || '',
								options: destinations,
								onChange: ( yesNext ) => setAttributes( { questions: updateItem( questions, index, { yesNext } ) } )
							} ),
							el( TextControl, {
								label: __( 'Second answer label', 'docspress-blocks' ),
								value: question.noLabel || '',
								onChange: ( noLabel ) => setAttributes( { questions: updateItem( questions, index, { noLabel } ) } )
							} ),
							el( SelectControl, {
								label: __( 'Second answer destination', 'docspress-blocks' ),
								value: question.noNext || '',
								options: destinations,
								onChange: ( noNext ) => setAttributes( { questions: updateItem( questions, index, { noNext } ) } )
							} ),
							el(
								'div',
								{ className: 'docspress-troubleshooter__editor-actions' },
								el( Button, {
									variant: 'tertiary',
									disabled: index === 0,
									onClick: () => {
										const next = questions.slice();
										[ next[ index - 1 ], next[ index ] ] = [ next[ index ], next[ index - 1 ] ];
										setAttributes( { questions: next } );
									}
								}, __( 'Move up', 'docspress-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									disabled: index === questions.length - 1,
									onClick: () => {
										const next = questions.slice();
										[ next[ index ], next[ index + 1 ] ] = [ next[ index + 1 ], next[ index ] ];
										setAttributes( { questions: next } );
									}
								}, __( 'Move down', 'docspress-blocks' ) ),
								el( Button, {
									variant: 'tertiary',
									isDestructive: true,
									disabled: questions.length === 1,
									onClick: () => setAttributes( { questions: questions.filter( ( _, itemIndex ) => itemIndex !== index ) } )
								}, __( 'Remove', 'docspress-blocks' ) )
							)
						) ),
						questions.length < 12 && el( Button, {
							variant: 'secondary',
							onClick: () => {
								const id = `question-${ questions.length + 1 }`;
								setAttributes( { questions: [ ...questions, { id, question: 'What should the reader check?', yesLabel: 'Yes', yesNext: '', noLabel: 'No', noNext: '' } ] } );
							}
						}, __( 'Add question', 'docspress-blocks' ) )
					),
					el(
						PanelBody,
						{ title: __( 'Outcomes', 'docspress-blocks' ), initialOpen: false },
						...outcomes.map( ( outcome, index ) => el(
							'div',
							{ className: 'docspress-troubleshooter__branch-editor', key: `outcome-${ index }` },
							el( TextControl, {
								label: __( 'Outcome ID', 'docspress-blocks' ),
								value: outcome.id || '',
								onChange: ( id ) => setAttributes( { outcomes: updateItem( outcomes, index, { id: normalizeId( id, `outcome-${ index + 1 }` ) } ) } )
							} ),
							el( SelectControl, {
								label: __( 'Status', 'docspress-blocks' ),
								value: outcome.status || 'neutral',
								options: statuses,
								onChange: ( status ) => setAttributes( { outcomes: updateItem( outcomes, index, { status } ) } )
							} ),
							el( TextControl, {
								label: __( 'Title', 'docspress-blocks' ),
								value: outcome.title || '',
								onChange: ( title ) => setAttributes( { outcomes: updateItem( outcomes, index, { title } ) } )
							} ),
							el( TextareaControl, {
								label: __( 'Content', 'docspress-blocks' ),
								help: __( 'Basic HTML such as paragraphs, links, and code is supported.', 'docspress-blocks' ),
								value: outcome.content || '',
								onChange: ( content ) => setAttributes( { outcomes: updateItem( outcomes, index, { content } ) } )
							} ),
							el( Button, {
								variant: 'tertiary',
								isDestructive: true,
								disabled: outcomes.length === 1,
								onClick: () => setAttributes( { outcomes: outcomes.filter( ( _, itemIndex ) => itemIndex !== index ) } )
							}, __( 'Remove outcome', 'docspress-blocks' ) )
						) ),
						outcomes.length < 12 && el( Button, {
							variant: 'secondary',
							onClick: () => {
								const id = `outcome-${ outcomes.length + 1 }`;
								setAttributes( { outcomes: [ ...outcomes, { id, status: 'neutral', title: 'Next step', content: '<p>Explain what the reader should do next.</p>' } ] } );
							}
						}, __( 'Add outcome', 'docspress-blocks' ) )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'header',
						{ className: 'docspress-troubleshooter__header' },
						el(
							'div',
							null,
							el( 'span', { className: 'docspress-troubleshooter__eyebrow' }, __( 'Guided diagnosis', 'docspress-blocks' ) ),
							el( RichText, {
								tagName: 'h3',
								value: attributes.title,
								onChange: ( title ) => setAttributes( { title } ),
								allowedFormats: [],
								placeholder: __( 'Troubleshooter title…', 'docspress-blocks' )
							} ),
							el( RichText, {
								tagName: 'div',
								className: 'docspress-troubleshooter__intro',
								value: attributes.intro,
								onChange: ( intro ) => setAttributes( { intro } ),
								placeholder: __( 'Explain what this troubleshooter resolves…', 'docspress-blocks' )
							} )
						),
						el(
							'span',
							{ className: 'docspress-troubleshooter__route', 'aria-hidden': true },
							el( 'span' ), el( 'span' ), el( 'span' )
						)
					),
					attributes.showProgress && el(
						'div',
						{ className: 'docspress-troubleshooter__progress' },
						el( 'span', { className: 'docspress-troubleshooter__progress-bar' }, el( 'i', { style: { width: '34%' } } ) ),
						el( 'span', { className: 'docspress-troubleshooter__progress-label' }, __( 'Question 1', 'docspress-blocks' ) )
					),
					el(
						'div',
						{ className: 'docspress-troubleshooter__stage' },
						el(
							'section',
							{ className: 'docspress-troubleshooter__question' },
							el( 'span', { className: 'docspress-troubleshooter__step' }, __( 'Starting question', 'docspress-blocks' ) ),
							el( 'h4', null, firstQuestion.question || __( 'Add a question in the block sidebar.', 'docspress-blocks' ) ),
							el(
								'div',
								{ className: 'docspress-troubleshooter__choices' },
								el( 'button', { type: 'button', className: 'docspress-troubleshooter__choice is-primary' }, el( 'span', null, firstQuestion.yesLabel || __( 'First answer', 'docspress-blocks' ) ), el( 'b', null, '→' ) ),
								el( 'button', { type: 'button', className: 'docspress-troubleshooter__choice' }, el( 'span', null, firstQuestion.noLabel || __( 'Second answer', 'docspress-blocks' ) ), el( 'b', null, '→' ) )
							),
							el( 'p', { className: 'docspress-troubleshooter__editor-note' }, `${ questions.length } ${ __( 'questions', 'docspress-blocks' ) } · ${ outcomes.length } ${ __( 'outcomes', 'docspress-blocks' ) }` )
						)
					),
					el(
						'footer',
						{ className: 'docspress-troubleshooter__footer' },
						el( 'button', { type: 'button', disabled: true }, __( 'Back', 'docspress-blocks' ) ),
						el( 'button', { type: 'button' }, __( 'Start over', 'docspress-blocks' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.docspressBlocksEditor );
