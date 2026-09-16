( function () {
	'use strict';

	function enhance( root ) {
		if ( root.dataset.autodocsPublishTroubleshooterReady === 'true' ) return;

		const startId = root.dataset.startId;
		const questions = new Map(
			Array.from( root.querySelectorAll( '[data-autodocs-publish-troubleshooter-question]' ) )
				.map( ( item ) => [ item.dataset.autodocsPublishTroubleshooterQuestion, item ] )
		);
		const outcomes = new Map(
			Array.from( root.querySelectorAll( '[data-autodocs-publish-troubleshooter-outcome]' ) )
				.map( ( item ) => [ item.dataset.autodocsPublishTroubleshooterOutcome, item ] )
		);
		const invalid = root.querySelector( '[data-autodocs-publish-troubleshooter-invalid]' );
		const back = root.querySelector( '[data-autodocs-publish-troubleshooter-back]' );
		const restart = root.querySelector( '[data-autodocs-publish-troubleshooter-restart]' );
		const progress = root.querySelector( '[data-autodocs-publish-troubleshooter-progress]' );
		const progressBar = root.querySelector( '[data-autodocs-publish-troubleshooter-progress-bar]' );
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
			const next = event.target.closest( '[data-autodocs-publish-troubleshooter-next]' );
			if ( next && root.contains( next ) ) {
				history.push( currentId );
				show( next.dataset.autodocsPublishTroubleshooterNext, true );
				return;
			}

			if ( event.target.closest( '[data-autodocs-publish-troubleshooter-back]' ) && history.length ) {
				show( history.pop(), true );
				return;
			}

			if ( event.target.closest( '[data-autodocs-publish-troubleshooter-restart]' ) ) {
				history.splice( 0, history.length );
				show( startId, true );
			}
		} );

		show( startId, false );
		root.dataset.autodocsPublishTroubleshooterReady = 'true';
	}

	function init() {
		document.querySelectorAll( '[data-autodocs-publish-troubleshooter]' ).forEach( enhance );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
