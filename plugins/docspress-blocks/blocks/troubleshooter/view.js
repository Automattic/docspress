( function () {
	'use strict';

	function enhance( root ) {
		if ( root.dataset.docspressTroubleshooterReady === 'true' ) return;

		const startId = root.dataset.startId;
		const questions = new Map(
			Array.from( root.querySelectorAll( '[data-docspress-troubleshooter-question]' ) )
				.map( ( item ) => [ item.dataset.docspressTroubleshooterQuestion, item ] )
		);
		const outcomes = new Map(
			Array.from( root.querySelectorAll( '[data-docspress-troubleshooter-outcome]' ) )
				.map( ( item ) => [ item.dataset.docspressTroubleshooterOutcome, item ] )
		);
		const invalid = root.querySelector( '[data-docspress-troubleshooter-invalid]' );
		const back = root.querySelector( '[data-docspress-troubleshooter-back]' );
		const restart = root.querySelector( '[data-docspress-troubleshooter-restart]' );
		const progress = root.querySelector( '[data-docspress-troubleshooter-progress]' );
		const progressBar = root.querySelector( '[data-docspress-troubleshooter-progress-bar]' );
		const panels = [ ...questions.values(), ...outcomes.values(), invalid ].filter( Boolean );
		const history = [];
		let currentId = startId;

		function hidePanels() {
			panels.forEach( ( panel ) => {
				panel.hidden = true;
			} );
		}

		function updateProgress( kind ) {
			const step = history.length + 1;
			const estimatedTotal = Math.max( step + ( kind === 'question' ? 1 : 0 ), 2 );
			const value = kind === 'outcome' ? 100 : Math.min( 88, Math.round( ( step / estimatedTotal ) * 100 ) );

			if ( progress ) {
				progress.textContent = kind === 'outcome'
					? 'Recommendation ready'
					: `Question ${ step }`;
			}
			if ( progressBar ) {
				progressBar.style.width = `${ value }%`;
			}
		}

		function show( id, shouldFocus ) {
			const panel = questions.get( id ) || outcomes.get( id ) || invalid;
			const kind = questions.has( id ) ? 'question' : 'outcome';

			hidePanels();
			panel.hidden = false;
			currentId = id;
			back.disabled = history.length === 0;
			updateProgress( kind );

			if ( shouldFocus ) {
				const heading = panel.querySelector( 'h4' );
				if ( heading ) heading.focus( { preventScroll: true } );
			}
		}

		root.addEventListener( 'click', ( event ) => {
			const next = event.target.closest( '[data-docspress-troubleshooter-next]' );
			if ( next && root.contains( next ) ) {
				history.push( currentId );
				show( next.dataset.docspressTroubleshooterNext, true );
				return;
			}

			if ( event.target.closest( '[data-docspress-troubleshooter-back]' ) && history.length ) {
				show( history.pop(), true );
				return;
			}

			if ( event.target.closest( '[data-docspress-troubleshooter-restart]' ) ) {
				history.splice( 0, history.length );
				show( startId, true );
			}
		} );

		show( startId, false );
		root.dataset.docspressTroubleshooterReady = 'true';
	}

	function init() {
		document.querySelectorAll( '[data-docspress-troubleshooter]' ).forEach( enhance );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
