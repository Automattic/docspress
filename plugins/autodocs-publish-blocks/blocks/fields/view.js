( function () {
	'use strict';

	function initializeFields( block ) {
		const input = block.querySelector( '[data-autodocs-publish-fields-search]' );
		if ( ! input || input.dataset.autodocsPublishReady ) {
			return;
		}
		input.dataset.autodocsPublishReady = 'true';
		const items = Array.from( block.querySelectorAll( '[data-autodocs-publish-fields-item]' ) );
		const count = block.querySelector( '[data-autodocs-publish-fields-count]' );
		const empty = block.querySelector( '[data-autodocs-publish-fields-empty]' );
		input.addEventListener( 'input', function () {
			const query = input.value.trim().toLowerCase();
			let visible = 0;
			items.forEach( function ( item ) {
				const matches = ! query || item.dataset.search.includes( query );
				item.hidden = ! matches;
				if ( matches ) {
					visible += 1;
				}
			} );
			count.textContent = `${ visible } ${ visible === 1 ? 'field' : 'fields' }`;
			empty.hidden = visible !== 0;
		} );
	}

	function initialize() {
		document.querySelectorAll( '[data-autodocs-publish-fields]' ).forEach( initializeFields );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initialize );
	} else {
		initialize();
	}
} )();
