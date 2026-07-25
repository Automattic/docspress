( function ( blocks, shared ) {
	'use strict';

	const { registerBlockType } = blocks;
	const {
		Button,
		Fragment,
		InspectorControls,
		PanelBody,
		RangeControl,
		RichText,
		__,
		designSupports,
		el,
		presetClass,
		themeStyle,
		useBlockProps
	} = shared;
	const defaultSteps = [
		{ title: 'Choose', content: '<p>Select the option that matches your project.</p>' },
		{ title: 'Configure', content: '<p>Set the values required by your environment.</p>' },
		{ title: 'Verify', content: '<p>Run the check and confirm the expected result.</p>' }
	];
	const icon = el(
		'svg',
		{ viewBox: '0 0 24 24', width: 24, height: 24, fill: 'none', stroke: 'currentColor', strokeWidth: 1.8 },
		el( 'path', { d: 'M7 4h10M7 12h10M7 20h10' } ),
		el( 'circle', { cx: 4, cy: 4, r: 1.5 } ),
		el( 'circle', { cx: 4, cy: 12, r: 1.5 } ),
		el( 'circle', { cx: 4, cy: 20, r: 1.5 } )
	);

	function normalizedSteps( steps ) {
		return Array.isArray( steps ) && steps.length ? steps.slice( 0, 20 ) : defaultSteps;
	}

	registerBlockType( 'docspress/flow', {
		apiVersion: 3,
		title: __( 'DocsPress: Flow', 'docspress-blocks' ),
		description: __( 'Present a connected sequence of editable, automatically numbered steps.', 'docspress-blocks' ),
		category: 'text',
		icon,
		keywords: [ __( 'steps', 'docspress-blocks' ), __( 'process', 'docspress-blocks' ), __( 'instructions', 'docspress-blocks' ) ],
		attributes: {
			start: { type: 'number', default: 1 },
			steps: { type: 'array', default: defaultSteps }
		},
		supports: designSupports,
		edit: function FlowEdit( { attributes, setAttributes } ) {
			const steps = normalizedSteps( attributes.steps );
			const start = Math.max( 1, Math.min( 99, Number( attributes.start ) || 1 ) );
			const blockProps = useBlockProps( {
				className: `docspress-flow docspress-flow--editor ${ presetClass }`,
				style: themeStyle
			} );
			const updateStep = ( index, key, value ) => {
				setAttributes( {
					steps: steps.map( ( step, stepIndex ) => stepIndex === index ? { ...step, [ key ]: value } : step )
				} );
			};
			const moveStep = ( index, direction ) => {
				const nextIndex = index + direction;
				if ( nextIndex < 0 || nextIndex >= steps.length ) return;
				const nextSteps = [ ...steps ];
				[ nextSteps[ index ], nextSteps[ nextIndex ] ] = [ nextSteps[ nextIndex ], nextSteps[ index ] ];
				setAttributes( { steps: nextSteps } );
			};
			const removeStep = ( index ) => {
				if ( steps.length > 1 ) {
					setAttributes( { steps: steps.filter( ( step, stepIndex ) => stepIndex !== index ) } );
				}
			};
			const addStep = () => {
				if ( steps.length < 20 ) {
					setAttributes( {
						steps: [
							...steps,
							{
								title: __( 'New step', 'docspress-blocks' ),
								content: '<p>' + __( 'Explain what readers should do.', 'docspress-blocks' ) + '</p>'
							}
						]
					} );
				}
			};

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Flow settings', 'docspress-blocks' ), initialOpen: true },
						el( RangeControl, {
							label: __( 'Starting number', 'docspress-blocks' ),
							help: __( 'Continue numbering when this flow follows an earlier sequence.', 'docspress-blocks' ),
							value: start,
							min: 1,
							max: 99,
							onChange: ( nextStart ) => setAttributes( { start: nextStart } )
						} )
					)
				),
				el(
					'div',
					blockProps,
					el(
						'ol',
						{ className: 'docspress-flow__list', start, role: 'list' },
						...steps.map( ( step, index ) => el(
							'li',
							{ className: 'docspress-flow__step', key: `flow-step-${ index }` },
							el(
								'div',
								{ className: 'docspress-flow__rail', 'aria-hidden': true },
								el( 'span', { className: 'docspress-flow__marker' }, start + index )
							),
							el(
								'div',
								{ className: 'docspress-flow__content' },
								el( RichText, {
									tagName: 'h3',
									className: 'docspress-flow__title',
									value: step.title || '',
									onChange: ( title ) => updateStep( index, 'title', title ),
									allowedFormats: [],
									placeholder: __( 'Step title…', 'docspress-blocks' )
								} ),
								el( RichText, {
									tagName: 'div',
									multiline: 'p',
									className: 'docspress-flow__body',
									value: step.content || '',
									onChange: ( content ) => updateStep( index, 'content', content ),
									placeholder: __( 'Explain this step…', 'docspress-blocks' )
								} ),
								el(
									'div',
									{ className: 'docspress-flow__step-controls' },
									index > 0 && el( Button, { onClick: () => moveStep( index, -1 ), variant: 'tertiary' }, __( 'Move up', 'docspress-blocks' ) ),
									index < steps.length - 1 && el( Button, { onClick: () => moveStep( index, 1 ), variant: 'tertiary' }, __( 'Move down', 'docspress-blocks' ) ),
									steps.length > 1 && el( Button, {
										onClick: () => removeStep( index ),
										variant: 'tertiary',
										isDestructive: true
									}, __( 'Remove', 'docspress-blocks' ) )
								)
							)
						) )
					),
					steps.length < 20 && el(
						'div',
						{ className: 'docspress-flow__add' },
						el( Button, { onClick: addStep, variant: 'secondary' }, __( 'Add step', 'docspress-blocks' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.docspressBlocksEditor );
