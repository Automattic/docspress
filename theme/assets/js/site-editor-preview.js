( function () {
	'use strict';

	const context = window.docspressSiteEditorPreview;
	if ( ! context || ! context.postId || ! context.postType ) {
		return;
	}

	let redirecting = false;

	function applyPreviewContext() {
		if ( redirecting ) {
			return;
		}

		const url = new URL( window.location.href );
		if ( url.searchParams.get( 'p' ) !== '/styles' ) {
			return;
		}

		const hasEntityContext =
			url.searchParams.has( 'postId' ) ||
			url.searchParams.has( 'postType' );
		const isLegacyPagePreview =
			url.searchParams.get( 'postType' ) === 'page';

		if ( hasEntityContext && ! isLegacyPagePreview ) {
			return;
		}

		redirecting = true;
		url.searchParams.set( 'postType', context.postType );
		url.searchParams.set( 'postId', String( context.postId ) );
		window.location.replace( url.toString() );
	}

	function schedulePreviewContext() {
		window.setTimeout( applyPreviewContext, 0 );
	}

	for ( const method of [ 'pushState', 'replaceState' ] ) {
		const original = window.history[ method ];
		window.history[ method ] = function () {
			const result = original.apply( this, arguments );
			schedulePreviewContext();
			return result;
		};
	}

	window.addEventListener( 'popstate', schedulePreviewContext );
	applyPreviewContext();
}() );

( function ( compose, element, hooks ) {
	'use strict';

	const context = window.docspressSiteEditorPreview;
	if (
		! context ||
		! context.archivePostId ||
		! compose ||
		! element ||
		! hooks
	) {
		return;
	}

	const { createHigherOrderComponent } = compose;
	const { createElement } = element;
	const { addFilter } = hooks;

	function isArchiveTemplateEditor() {
		const url = new URL( window.location.href );
		return (
			url.searchParams.get( 'p' ) ===
			`/wp_template/${ context.archivePostId }`
		);
	}

	/*
	 * The generic Archive template has no category, tag, author, or date
	 * context in the Site Editor. Core therefore leaves an inherited Query
	 * block waiting indefinitely. Give only the editor preview a normal posts
	 * query; the attributes saved in archive.html remain inherited so every
	 * live archive still receives the correct request context.
	 */
	const withArchiveQueryPreview = createHigherOrderComponent(
		( BlockEdit ) =>
			function ArchiveQueryPreview( props ) {
				const query = props.attributes?.query;

				if (
					props.name !== 'core/query' ||
					! isArchiveTemplateEditor() ||
					! query?.inherit
				) {
					return createElement( BlockEdit, props );
				}

				return createElement( BlockEdit, {
					...props,
					attributes: {
						...props.attributes,
						query: {
							...query,
							inherit: false,
						},
					},
				} );
			},
		'withArchiveQueryPreview'
	);

	addFilter(
		'editor.BlockEdit',
		'docspress/archive-query-preview',
		withArchiveQueryPreview
	);
} )( window.wp.compose, window.wp.element, window.wp.hooks );
