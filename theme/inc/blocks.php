<?php
/**
 * Site Editor components for the DocsPress block theme.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Native design supports shared by every shell component.
 *
 * @param array<int,string>|false $align Optional alignments.
 * @return array<string,mixed>
 */
function docspress_component_supports( $align = false ) {
	$supports = array(
		'anchor'     => true,
		'className'  => true,
		'html'       => false,
		'color'      => array(
			'background' => true,
			'gradients'  => true,
			'link'       => true,
			'text'       => true,
		),
		'spacing'    => array(
			'margin'  => true,
			'padding' => true,
		),
		'typography' => array(
			'fontFamily'    => true,
			'fontSize'      => true,
			'fontStyle'     => true,
			'fontWeight'    => true,
			'letterSpacing' => true,
			'lineHeight'    => true,
			'textDecoration' => true,
			'textTransform' => true,
		),
		'border'     => array(
			'color'  => true,
			'radius' => true,
			'style'  => true,
			'width'  => true,
		),
		'dimensions' => array(
			'minHeight' => true,
		),
		'position'   => array(
			'sticky' => true,
		),
		'shadow'     => true,
	);
	if ( $align ) {
		$supports['align'] = $align;
	}
	return $supports;
}

/**
 * Return a sanitized component attribute.
 *
 * @param array  $attributes Block attributes.
 * @param string $key        Attribute key.
 * @param mixed  $fallback   Fallback value.
 * @return mixed
 */
function docspress_component_attribute( $attributes, $key, $fallback ) {
	return array_key_exists( $key, $attributes ) ? $attributes[ $key ] : $fallback;
}

/**
 * Return Page objects from a classic menu, retained as an optional docs source.
 *
 * @param string $menu_slug Menu name, slug, or ID.
 * @param int    $max_depth Maximum menu depth.
 * @return WP_Post[]
 */
function docspress_get_menu_pages( $menu_slug, $max_depth = 0 ) {
	$menu = $menu_slug ? wp_get_nav_menu_object( $menu_slug ) : false;
	if ( ! $menu ) {
		return array();
	}

	$items = wp_get_nav_menu_items( $menu->term_id );
	if ( ! $items || is_wp_error( $items ) ) {
		return array();
	}

	$parents = array();
	foreach ( $items as $item ) {
		$parents[ (int) $item->ID ] = (int) $item->menu_item_parent;
	}

	$pages = array();
	foreach ( $items as $item ) {
		if ( 'page' !== $item->object ) {
			continue;
		}

		$depth  = 1;
		$parent = (int) $item->menu_item_parent;
		while ( $parent && isset( $parents[ $parent ] ) ) {
			++$depth;
			$parent = $parents[ $parent ];
		}
		if ( $max_depth && $depth > $max_depth ) {
			continue;
		}

		$page = get_post( (int) $item->object_id );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			$pages[] = $page;
		}
	}
	return $pages;
}

/**
 * Render the documentation navigation block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_docs_navigation( $attributes ) {
	$title       = sanitize_text_field( docspress_component_attribute( $attributes, 'title', __( 'Documentation', 'docspress' ) ) );
	$width       = min( 360, max( 220, absint( docspress_component_attribute( $attributes, 'width', 266 ) ) ) );
	$root_slug   = sanitize_text_field( docspress_component_attribute( $attributes, 'rootSlug', 'docs' ) );
	$source      = docspress_component_attribute( $attributes, 'source', 'pages' );
	$source      = in_array( $source, array( 'pages', 'menu' ), true ) ? $source : 'pages';
	$menu_slug   = sanitize_text_field( docspress_component_attribute( $attributes, 'menuSlug', '' ) );
	$sort        = docspress_component_attribute( $attributes, 'sort', 'menu_order' );
	$sort        = in_array( $sort, array( 'menu_order', 'title', 'newest', 'oldest' ), true ) ? $sort : 'menu_order';
	$show_root   = (bool) docspress_component_attribute( $attributes, 'showRoot', true );
	$max_depth   = min( 8, absint( docspress_component_attribute( $attributes, 'maxDepth', 0 ) ) );
	$show_filter = (bool) docspress_component_attribute( $attributes, 'showFilter', true );
	$placeholder = sanitize_text_field( docspress_component_attribute( $attributes, 'filterPlaceholder', __( 'Filter pages…', 'docspress' ) ) );
	$show_versions = (bool) docspress_component_attribute( $attributes, 'showVersions', true );
	$empty       = sanitize_text_field( docspress_component_attribute( $attributes, 'emptyMessage', __( 'Publish Pages to populate this navigation.', 'docspress' ) ) );
	$show_collapse = (bool) docspress_component_attribute( $attributes, 'showCollapse', true );
	$start_collapsed = $show_collapse && (bool) docspress_component_attribute( $attributes, 'startCollapsed', false );
	$collapse_label = sanitize_text_field( docspress_component_attribute( $attributes, 'collapseLabel', __( 'Collapse sidebar', 'docspress' ) ) );
	$expand_label = sanitize_text_field( docspress_component_attribute( $attributes, 'expandLabel', __( 'Expand sidebar', 'docspress' ) ) );
	$content_id  = wp_unique_id( 'docspress-sidebar-content-' );
	$root_id     = docspress_get_docs_root_id( $root_slug );
	$pages       = 'menu' === $source ? docspress_get_menu_pages( $menu_slug, $max_depth ) : docspress_get_docs_pages( $root_slug, $sort );
	$versions    = docspress_get_versions();
	$wrapper     = get_block_wrapper_attributes(
		array(
			'class'      => 'docs-sidebar' . ( $start_collapsed ? ' is-sidebar-collapsed' : '' ),
			'id'         => 'docs-sidebar',
			'style'      => '--dp-component-width:' . $width . 'px',
			'aria-label' => __( 'Documentation navigation', 'docspress' ),
			'data-sidebar-start-collapsed' => $start_collapsed ? 'true' : 'false',
		)
	);

	ob_start();
	?>
	<aside <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="docs-sidebar-header">
			<?php if ( $title ) : ?><p class="sidebar-eyebrow"><?php echo esc_html( $title ); ?></p><?php endif; ?>
			<?php if ( $show_collapse ) : ?>
				<button
					class="sidebar-collapse-toggle"
					type="button"
					data-sidebar-collapse-toggle
					data-collapse-label="<?php echo esc_attr( $collapse_label ); ?>"
					data-expand-label="<?php echo esc_attr( $expand_label ); ?>"
					aria-controls="<?php echo esc_attr( $content_id ); ?>"
					aria-expanded="<?php echo $start_collapsed ? 'false' : 'true'; ?>"
					aria-label="<?php echo esc_attr( $start_collapsed ? $expand_label : $collapse_label ); ?>"
					title="<?php echo esc_attr( $start_collapsed ? $expand_label : $collapse_label ); ?>"
				>
					<span class="sidebar-collapse-icon" aria-hidden="true"></span>
					<span class="sidebar-collapse-label"><?php echo esc_html( $start_collapsed ? $expand_label : $collapse_label ); ?></span>
				</button>
			<?php endif; ?>
		</div>
		<div class="docs-sidebar-content" id="<?php echo esc_attr( $content_id ); ?>" data-sidebar-content>
			<?php if ( $show_filter ) : ?>
				<div class="sidebar-search">
					<?php echo docspress_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<label class="screen-reader-text" for="docspress-filter"><?php esc_html_e( 'Filter documentation pages', 'docspress' ); ?></label>
					<input id="docspress-filter" type="search" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off" data-docs-filter>
					<button class="sidebar-search-clear" type="button" data-search-clear aria-label="<?php esc_attr_e( 'Clear filter', 'docspress' ); ?>">×</button>
				</div>
			<?php endif; ?>

			<?php if ( $show_versions && $versions['terms'] ) : ?>
				<label class="screen-reader-text" for="docspress-version"><?php esc_html_e( 'Documentation version', 'docspress' ); ?></label>
				<select class="version-select" id="docspress-version" data-version-select>
					<option value=""><?php esc_html_e( 'Choose a version', 'docspress' ); ?></option>
					<?php foreach ( $versions['terms'] as $version ) : ?>
						<?php $version_url = get_term_link( $version ); ?>
						<?php if ( ! is_wp_error( $version_url ) ) : ?>
							<option value="<?php echo esc_url( $version_url ); ?>" <?php selected( $versions['current'], $version->term_id ); ?>><?php echo esc_html( $version->name ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>

			<nav class="docs-nav<?php echo 'menu' === $source ? ' docs-nav-custom' : ''; ?>" data-docs-nav>
				<?php if ( 'menu' === $source && $menu_slug ) : ?>
					<?php
					wp_nav_menu(
						array(
							'menu'        => $menu_slug,
							'container'   => false,
							'items_wrap'  => '<ul>%3$s</ul>',
							'depth'       => $max_depth,
							'fallback_cb' => false,
						)
					);
					?>
				<?php elseif ( $pages ) : ?>
					<?php if ( $root_id && ! $show_root ) : ?>
						<?php docspress_render_page_tree( $pages, $root_id, 0, 1, $max_depth ); ?>
					<?php else : ?>
						<?php docspress_render_page_tree( $pages, 0, $root_id, 1, $max_depth ); ?>
					<?php endif; ?>
				<?php else : ?>
					<p class="docs-nav-empty"><?php echo esc_html( $empty ); ?></p>
				<?php endif; ?>
			</nav>
			<?php if ( $show_filter ) : ?><p class="sidebar-no-results" data-no-results><?php esc_html_e( 'No pages match that filter.', 'docspress' ); ?></p><?php endif; ?>
		</div>
	</aside>
	<button class="drawer-scrim" type="button" data-drawer-close aria-label="<?php esc_attr_e( 'Close documentation menu', 'docspress' ); ?>"></button>
	<?php
	return ob_get_clean();
}

/**
 * Convert Page content to compact searchable text.
 *
 * @param string $content Raw Page content.
 * @return string
 */
function docspress_searchable_text( $content ) {
	$content = preg_replace( '/<!--.*?-->/s', ' ', (string) $content );
	$content = strip_shortcodes( $content );
	$content = html_entity_decode( wp_strip_all_tags( $content, true ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$content = trim( (string) preg_replace( '/\s+/u', ' ', $content ) );
	return function_exists( 'mb_substr' ) ? mb_substr( $content, 0, 12000 ) : substr( $content, 0, 12000 );
}

/**
 * Return the Page hierarchy label used by search results.
 *
 * @param WP_Post $page Page object.
 * @return string
 */
function docspress_search_page_path( $page ) {
	$labels = array();
	foreach ( array_reverse( get_post_ancestors( $page ) ) as $ancestor_id ) {
		$title = wp_strip_all_tags( get_the_title( $ancestor_id ) );
		if ( $title ) {
			$labels[] = $title;
		}
	}
	return implode( ' / ', $labels );
}

/**
 * Build the public command-search index.
 *
 * @param WP_Post[] $pages Searchable Pages.
 * @return array<int,array<string,mixed>>
 */
function docspress_search_index( $pages ) {
	$index = array();
	$seen  = array();
	foreach ( $pages as $page ) {
		if ( ! $page instanceof WP_Post || isset( $seen[ $page->ID ] ) ) {
			continue;
		}
		$seen[ $page->ID ] = true;
		$content = docspress_searchable_text( $page->post_content );
		$excerpt = $page->post_excerpt ? wp_strip_all_tags( $page->post_excerpt ) : wp_trim_words( $content, 24, '…' );
		$index[] = array(
			'id'      => (int) $page->ID,
			'title'   => html_entity_decode( wp_strip_all_tags( get_the_title( $page ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'path'    => docspress_search_page_path( $page ),
			'excerpt' => html_entity_decode( $excerpt, ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'content' => $content,
			'url'     => get_permalink( $page ),
		);
	}
	return apply_filters( 'docspress_search_index', $index );
}

/**
 * Render the command-search trigger and dialog.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_command_search( $attributes ) {
	$label       = sanitize_text_field( docspress_component_attribute( $attributes, 'label', __( 'Search docs', 'docspress' ) ) );
	$placeholder = sanitize_text_field( docspress_component_attribute( $attributes, 'placeholder', __( 'Search documentation…', 'docspress' ) ) );
	$suggested   = sanitize_text_field( docspress_component_attribute( $attributes, 'suggestedLabel', __( 'Suggested pages', 'docspress' ) ) );
	$no_results  = sanitize_text_field( docspress_component_attribute( $attributes, 'noResultsLabel', __( 'No documentation matched that search.', 'docspress' ) ) );
	$limit       = min( 20, max( 3, absint( docspress_component_attribute( $attributes, 'resultsLimit', 8 ) ) ) );
	$root_slug   = sanitize_text_field( docspress_component_attribute( $attributes, 'rootSlug', 'docs' ) );
	$width       = min( 960, max( 420, absint( docspress_component_attribute( $attributes, 'width', 680 ) ) ) );
	$height      = min( 820, max( 320, absint( docspress_component_attribute( $attributes, 'height', 640 ) ) ) );
	$radius      = min( 40, absint( docspress_component_attribute( $attributes, 'radius', 14 ) ) );
	$opacity     = min( 90, max( 0, absint( docspress_component_attribute( $attributes, 'overlayOpacity', 44 ) ) ) );
	$blur        = min( 20, max( 0, absint( docspress_component_attribute( $attributes, 'overlayBlur', 2 ) ) ) );
	$show_paths  = (bool) docspress_component_attribute( $attributes, 'showPaths', true );
	$show_excerpts = (bool) docspress_component_attribute( $attributes, 'showExcerpts', true );
	$show_hints  = (bool) docspress_component_attribute( $attributes, 'showHints', true );
	$dialog_id   = wp_unique_id( 'docspress-search-dialog-' );
	$field_id    = $dialog_id . '-field';
	$results_id  = $dialog_id . '-results';
	$classes     = array( 'docspress-command-search' );
	if ( ! $show_paths ) {
		$classes[] = 'docspress-search-hide-paths';
	}
	if ( ! $show_excerpts ) {
		$classes[] = 'docspress-search-hide-excerpts';
	}
	if ( ! $show_hints ) {
		$classes[] = 'docspress-search-hide-hints';
	}
	$wrapper = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $classes ),
			'style' => '--dp-search-width:' . $width . 'px;--dp-search-height:' . $height . 'px;--dp-search-radius:' . $radius . 'px;--dp-search-overlay-opacity:' . $opacity . '%;--dp-search-overlay-blur:' . $blur . 'px',
		)
	);
	$search_data = array(
		'index'          => docspress_search_index( docspress_get_docs_pages( $root_slug ) ),
		'limit'          => $limit,
		'suggestedLabel' => $suggested,
		'resultsLabel'   => __( 'Search results', 'docspress' ),
		'noResultsLabel' => $no_results,
		'resultSingular' => __( '1 result', 'docspress' ),
		'resultPlural'   => __( '%d results', 'docspress' ),
	);

	ob_start();
	?>
	<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<script type="application/json" data-docspress-search-data><?php echo wp_json_encode( $search_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
		<button class="header-button search-shortcut" type="button" data-docs-search-trigger aria-label="<?php echo esc_attr( $label ); ?>" aria-haspopup="dialog" aria-controls="<?php echo esc_attr( $dialog_id ); ?>" aria-expanded="false">
			<?php echo docspress_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php echo esc_html( $label ); ?></span><kbd data-search-shortcut-hint>⌘ K</kbd>
		</button>
		<dialog class="search-dialog" id="<?php echo esc_attr( $dialog_id ); ?>" data-docs-search-dialog aria-labelledby="<?php echo esc_attr( $field_id . '-label' ); ?>">
			<div class="search-dialog-panel">
				<form class="command-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-command-search-form>
					<div class="command-search-field">
						<?php echo docspress_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<label class="screen-reader-text" id="<?php echo esc_attr( $field_id . '-label' ); ?>" for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Search documentation', 'docspress' ); ?></label>
						<input id="<?php echo esc_attr( $field_id ); ?>" name="s" type="search" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off" spellcheck="false" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $results_id ); ?>" data-docs-command-input>
						<input type="hidden" name="post_type" value="page">
						<button class="command-search-close" type="button" data-docs-search-close aria-label="<?php esc_attr_e( 'Close search', 'docspress' ); ?>"><span aria-hidden="true">×</span></button>
					</div>
					<div class="command-search-body">
						<div class="command-search-status" aria-live="polite" aria-atomic="true" data-command-search-status></div>
						<ul class="command-search-results" id="<?php echo esc_attr( $results_id ); ?>" role="listbox" data-command-search-results></ul>
						<div class="command-search-empty" data-command-search-empty hidden>
							<span class="command-search-empty-icon" aria-hidden="true"><?php echo docspress_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<strong><?php echo esc_html( $no_results ); ?></strong>
							<span><?php esc_html_e( 'Try another term or press Enter for the full WordPress search.', 'docspress' ); ?></span>
						</div>
					</div>
					<?php if ( $show_hints ) : ?>
						<footer class="command-search-footer" aria-hidden="true">
							<span><kbd>↑</kbd><kbd>↓</kbd> <?php esc_html_e( 'to navigate', 'docspress' ); ?></span>
							<span><kbd>↵</kbd> <?php esc_html_e( 'to open', 'docspress' ); ?></span>
							<span><kbd>Esc</kbd> <?php esc_html_e( 'to close', 'docspress' ); ?></span>
						</footer>
					<?php endif; ?>
				</form>
			</div>
		</dialog>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render breadcrumbs for the current Page.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_breadcrumbs( $attributes ) {
	if ( ! is_page() ) {
		return '';
	}

	$show_home = (bool) docspress_component_attribute( $attributes, 'showHome', false );
	$home_label = sanitize_text_field( docspress_component_attribute( $attributes, 'homeLabel', __( 'Home', 'docspress' ) ) );
	$separator = sanitize_text_field( docspress_component_attribute( $attributes, 'separator', '›' ) );
	$ancestors = array_reverse( get_post_ancestors( get_queried_object_id() ) );
	if ( ! $show_home && ! $ancestors ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes( array( 'class' => 'breadcrumbs', 'aria-label' => __( 'Breadcrumbs', 'docspress' ) ) );
	ob_start();
	?>
	<nav <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><ol>
		<?php if ( $show_home ) : ?><li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $home_label ); ?></a><span aria-hidden="true"><?php echo esc_html( $separator ); ?></span></li><?php endif; ?>
		<?php foreach ( $ancestors as $ancestor_id ) : ?>
			<li><a href="<?php echo esc_url( get_permalink( $ancestor_id ) ); ?>"><?php echo esc_html( get_the_title( $ancestor_id ) ); ?></a><span aria-hidden="true"><?php echo esc_html( $separator ); ?></span></li>
		<?php endforeach; ?>
		<li aria-current="page"><?php echo esc_html( get_the_title() ); ?></li>
	</ol></nav>
	<?php
	return ob_get_clean();
}

/**
 * Render a table of contents from current Page headings.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_toc( $attributes ) {
	if ( ! is_singular() ) {
		return '';
	}

	$title = sanitize_text_field( docspress_component_attribute( $attributes, 'title', __( 'On this page', 'docspress' ) ) );
	$width = min( 320, max( 180, absint( docspress_component_attribute( $attributes, 'width', 226 ) ) ) );
	$min_level = min( 6, max( 1, absint( docspress_component_attribute( $attributes, 'minLevel', 2 ) ) ) );
	$max_level = min( 6, max( $min_level, absint( docspress_component_attribute( $attributes, 'maxLevel', 3 ) ) ) );
	$content = apply_filters( 'the_content', get_post_field( 'post_content', get_queried_object_id() ) );
	$toc = docspress_prepare_content( $content, $min_level, $max_level )['toc'];
	if ( ! $toc ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes(
		array(
			'class'      => 'docs-toc',
			'style'      => '--dp-component-width:' . $width . 'px',
			'aria-label' => $title,
		)
	);
	ob_start();
	?>
	<aside <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $title ) : ?><p class="toc-title"><?php echo esc_html( $title ); ?></p><?php endif; ?>
		<ul class="toc-list">
			<?php foreach ( $toc as $item ) : ?>
				<li class="toc-level-<?php echo esc_attr( $item['level'] ); ?>"><a href="#<?php echo esc_attr( $item['id'] ); ?>" data-toc-link><?php echo esc_html( $item['title'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</aside>
	<?php
	return ob_get_clean();
}

/**
 * Render an explicit Page excerpt without WordPress's generated fallback.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_page_summary( $attributes ) {
	if ( ! is_singular() ) {
		return '';
	}

	$fallback = sanitize_text_field( docspress_component_attribute( $attributes, 'fallbackText', '' ) );
	$summary  = has_excerpt() ? get_the_excerpt() : $fallback;
	if ( ! $summary ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes( array( 'class' => 'entry-summary' ) );
	return sprintf(
		'<p %1$s>%2$s</p>',
		$wrapper,
		esc_html( $summary )
	);
}

/**
 * Render WordPress and GitHub page actions.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_edit_links( $attributes ) {
	if ( ! is_singular() ) {
		return '';
	}

	$show_wordpress = (bool) docspress_component_attribute( $attributes, 'showWordPress', true );
	$show_github    = (bool) docspress_component_attribute( $attributes, 'showGitHub', true );
	$wp_label       = sanitize_text_field( docspress_component_attribute( $attributes, 'wordpressLabel', __( 'Edit this page in WordPress', 'docspress' ) ) );
	$github_label   = sanitize_text_field( docspress_component_attribute( $attributes, 'githubLabel', __( 'Propose changes on GitHub', 'docspress' ) ) );
	$repository     = esc_url_raw( docspress_component_attribute( $attributes, 'repositoryUrl', 'https://github.com/Automattic/docspress' ) );
	$ref            = sanitize_text_field( docspress_component_attribute( $attributes, 'ref', 'main' ) );
	$post_id        = get_queried_object_id();
	$wp_url         = get_edit_post_link( $post_id, '' );
	$wp_url         = $wp_url ? $wp_url : wp_login_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
	$github_url     = docspress_get_github_edit_url( $post_id, $repository, $ref );
	if ( ! $show_wordpress && ( ! $show_github || ! $github_url ) ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes( array( 'class' => 'page-actions', 'aria-label' => __( 'Page actions', 'docspress' ) ) );
	ob_start();
	?>
	<nav <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $show_wordpress ) : ?>
			<a class="page-action page-action-wordpress wp-element-button" href="<?php echo esc_url( $wp_url ); ?>"><?php echo docspress_icon( 'pencil' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $wp_label ); ?></span></a>
		<?php endif; ?>
		<?php if ( $show_github && $github_url ) : ?>
			<a class="page-action page-action-github wp-element-button" href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo docspress_icon( 'github' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $github_label ); ?></span></a>
		<?php endif; ?>
	</nav>
	<?php
	return ob_get_clean();
}

/**
 * Return adjacent documentation Pages for a component configuration.
 *
 * @param int    $current_id Current Page ID.
 * @param string $root_slug  Documentation root path.
 * @param string $sort       Page order.
 * @param bool   $show_root  Include root Page.
 * @param int    $max_depth  Maximum depth.
 * @return array{previous:?WP_Post,next:?WP_Post}
 */
function docspress_get_adjacent_pages( $current_id, $root_slug = 'docs', $sort = 'menu_order', $show_root = true, $max_depth = 0 ) {
	$root_id = docspress_get_docs_root_id( $root_slug );
	$pages   = docspress_get_docs_pages( $root_slug, $sort );
	$ordered = $root_id && ! $show_root
		? docspress_flatten_page_tree( $pages, 0, $root_id, 1, $max_depth )
		: docspress_flatten_page_tree( $pages, $root_id, 0, 1, $max_depth );
	$ids   = wp_list_pluck( $ordered, 'ID' );
	$index = array_search( $current_id, $ids, true );
	if ( false === $index ) {
		return array( 'previous' => null, 'next' => null );
	}
	return array(
		'previous' => $index > 0 ? $ordered[ $index - 1 ] : null,
		'next'     => $index < count( $ordered ) - 1 ? $ordered[ $index + 1 ] : null,
	);
}

/**
 * Render previous and next documentation cards.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_adjacent_navigation( $attributes ) {
	if ( ! is_page() ) {
		return '';
	}

	$root_slug = sanitize_text_field( docspress_component_attribute( $attributes, 'rootSlug', 'docs' ) );
	$sort = docspress_component_attribute( $attributes, 'sort', 'menu_order' );
	$sort = in_array( $sort, array( 'menu_order', 'title', 'newest', 'oldest' ), true ) ? $sort : 'menu_order';
	$show_root = (bool) docspress_component_attribute( $attributes, 'showRoot', true );
	$max_depth = min( 8, absint( docspress_component_attribute( $attributes, 'maxDepth', 0 ) ) );
	$previous_label = sanitize_text_field( docspress_component_attribute( $attributes, 'previousLabel', __( '← Previous', 'docspress' ) ) );
	$next_label = sanitize_text_field( docspress_component_attribute( $attributes, 'nextLabel', __( 'Next →', 'docspress' ) ) );
	$show_titles = (bool) docspress_component_attribute( $attributes, 'showTitles', true );
	$adjacent = docspress_get_adjacent_pages( get_queried_object_id(), $root_slug, $sort, $show_root, $max_depth );
	if ( ! $adjacent['previous'] && ! $adjacent['next'] ) {
		return '';
	}

	$wrapper = get_block_wrapper_attributes( array( 'class' => 'docs-pagination', 'aria-label' => __( 'Documentation pages', 'docspress' ) ) );
	ob_start();
	?>
	<nav <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $adjacent['previous'] ) : ?>
			<a class="pagination-link pagination-previous" href="<?php echo esc_url( get_permalink( $adjacent['previous'] ) ); ?>"><span class="pagination-direction"><?php echo esc_html( $previous_label ); ?></span><?php if ( $show_titles ) : ?><span class="pagination-title"><?php echo esc_html( get_the_title( $adjacent['previous'] ) ); ?></span><?php endif; ?></a>
		<?php else : ?><span aria-hidden="true"></span><?php endif; ?>
		<?php if ( $adjacent['next'] ) : ?>
			<a class="pagination-link pagination-next" href="<?php echo esc_url( get_permalink( $adjacent['next'] ) ); ?>"><span class="pagination-direction"><?php echo esc_html( $next_label ); ?></span><?php if ( $show_titles ) : ?><span class="pagination-title"><?php echo esc_html( get_the_title( $adjacent['next'] ) ); ?></span><?php endif; ?></a>
		<?php endif; ?>
	</nav>
	<?php
	return ob_get_clean();
}

/**
 * Render the visitor color-mode switch.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_color_toggle( $attributes ) {
	$label = sanitize_text_field( docspress_component_attribute( $attributes, 'label', __( 'Switch color theme', 'docspress' ) ) );
	$show_label = (bool) docspress_component_attribute( $attributes, 'showLabel', false );
	$default_mode = sanitize_key( docspress_component_attribute( $attributes, 'defaultMode', 'light' ) );
	$default_mode = in_array( $default_mode, array( 'light', 'dark', 'system' ), true ) ? $default_mode : 'light';
	$wrapper = get_block_wrapper_attributes( array( 'class' => 'docspress-color-toggle' ) );
	return sprintf(
		'<div %1$s><button class="header-button" type="button" data-theme-toggle data-default-mode="%2$s" aria-label="%3$s">%4$s%5$s</button></div>',
		$wrapper,
		esc_attr( $default_mode ),
		esc_attr( $label ),
		docspress_icon( 'sun' ) . docspress_icon( 'moon' ),
		$show_label ? '<span data-theme-label>' . esc_html( $label ) . '</span>' : ''
	);
}

/**
 * Render the mobile documentation drawer trigger.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function docspress_render_menu_toggle( $attributes ) {
	$label = sanitize_text_field( docspress_component_attribute( $attributes, 'label', __( 'Open documentation menu', 'docspress' ) ) );
	$wrapper = get_block_wrapper_attributes( array( 'class' => 'docspress-menu-toggle' ) );
	return sprintf(
		'<div %1$s><button class="menu-toggle" type="button" data-drawer-toggle aria-expanded="false" aria-controls="docs-sidebar" aria-label="%2$s">%3$s</button></div>',
		$wrapper,
		esc_attr( $label ),
		docspress_icon( 'menu' )
	);
}

/**
 * Register shell components and component-level block styles.
 */
function docspress_register_blocks() {
	$theme = wp_get_theme();
	$editor_script_path = get_theme_file_path( 'assets/js/block-components.js' );
	$editor_script_version = is_readable( $editor_script_path ) ? (string) filemtime( $editor_script_path ) : $theme->get( 'Version' );
	wp_register_script(
		'docspress-theme-blocks-editor',
		get_theme_file_uri( 'assets/js/block-components.js' ),
		array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
		$editor_script_version,
		true
	);

	$blocks = array(
		'docs-navigation' => array(
			'render_callback' => 'docspress_render_docs_navigation',
			'attributes'      => array(
				'title'             => array( 'type' => 'string', 'default' => 'Documentation', 'role' => 'content' ),
				'width'             => array( 'type' => 'number', 'default' => 266 ),
				'rootSlug'          => array( 'type' => 'string', 'default' => 'docs' ),
				'source'            => array( 'type' => 'string', 'default' => 'pages' ),
				'menuSlug'          => array( 'type' => 'string', 'default' => '' ),
				'sort'              => array( 'type' => 'string', 'default' => 'menu_order' ),
				'showRoot'          => array( 'type' => 'boolean', 'default' => true ),
				'maxDepth'          => array( 'type' => 'number', 'default' => 0 ),
				'showFilter'        => array( 'type' => 'boolean', 'default' => true ),
				'filterPlaceholder' => array( 'type' => 'string', 'default' => 'Filter pages…', 'role' => 'content' ),
				'showVersions'      => array( 'type' => 'boolean', 'default' => true ),
				'emptyMessage'      => array( 'type' => 'string', 'default' => 'Publish Pages to populate this navigation.', 'role' => 'content' ),
				'showCollapse'      => array( 'type' => 'boolean', 'default' => true ),
				'startCollapsed'    => array( 'type' => 'boolean', 'default' => false ),
				'collapseLabel'     => array( 'type' => 'string', 'default' => 'Collapse sidebar', 'role' => 'content' ),
				'expandLabel'       => array( 'type' => 'string', 'default' => 'Expand sidebar', 'role' => 'content' ),
			),
			'supports'        => docspress_component_supports(),
		),
		'command-search' => array(
			'render_callback' => 'docspress_render_command_search',
			'attributes'      => array(
				'label'          => array( 'type' => 'string', 'default' => 'Search docs', 'role' => 'content' ),
				'placeholder'    => array( 'type' => 'string', 'default' => 'Search documentation…', 'role' => 'content' ),
				'suggestedLabel' => array( 'type' => 'string', 'default' => 'Suggested pages', 'role' => 'content' ),
				'noResultsLabel' => array( 'type' => 'string', 'default' => 'No documentation matched that search.', 'role' => 'content' ),
				'resultsLimit'   => array( 'type' => 'number', 'default' => 8 ),
				'rootSlug'       => array( 'type' => 'string', 'default' => 'docs' ),
				'width'          => array( 'type' => 'number', 'default' => 680 ),
				'height'         => array( 'type' => 'number', 'default' => 640 ),
				'radius'         => array( 'type' => 'number', 'default' => 14 ),
				'overlayOpacity' => array( 'type' => 'number', 'default' => 44 ),
				'overlayBlur'    => array( 'type' => 'number', 'default' => 2 ),
				'showPaths'      => array( 'type' => 'boolean', 'default' => true ),
				'showExcerpts'   => array( 'type' => 'boolean', 'default' => true ),
				'showHints'      => array( 'type' => 'boolean', 'default' => true ),
			),
			'supports'        => docspress_component_supports(),
		),
		'breadcrumbs' => array(
			'render_callback' => 'docspress_render_breadcrumbs',
			'attributes'      => array(
				'showHome'  => array( 'type' => 'boolean', 'default' => false ),
				'homeLabel' => array( 'type' => 'string', 'default' => 'Home', 'role' => 'content' ),
				'separator' => array( 'type' => 'string', 'default' => '›', 'role' => 'content' ),
			),
			'supports'        => docspress_component_supports(),
		),
		'table-of-contents' => array(
			'render_callback' => 'docspress_render_toc',
			'attributes'      => array(
				'title'    => array( 'type' => 'string', 'default' => 'On this page', 'role' => 'content' ),
				'width'    => array( 'type' => 'number', 'default' => 226 ),
				'minLevel' => array( 'type' => 'number', 'default' => 2 ),
				'maxLevel' => array( 'type' => 'number', 'default' => 3 ),
			),
			'supports'        => docspress_component_supports(),
		),
		'page-summary' => array(
			'render_callback' => 'docspress_render_page_summary',
			'attributes'      => array(
				'fallbackText' => array( 'type' => 'string', 'default' => '', 'role' => 'content' ),
			),
			'supports'        => docspress_component_supports(),
		),
		'edit-links' => array(
			'render_callback' => 'docspress_render_edit_links',
			'attributes'      => array(
				'showWordPress'  => array( 'type' => 'boolean', 'default' => true ),
				'wordpressLabel' => array( 'type' => 'string', 'default' => 'Edit this page in WordPress', 'role' => 'content' ),
				'showGitHub'     => array( 'type' => 'boolean', 'default' => true ),
				'githubLabel'    => array( 'type' => 'string', 'default' => 'Propose changes on GitHub', 'role' => 'content' ),
				'repositoryUrl'  => array( 'type' => 'string', 'default' => 'https://github.com/Automattic/docspress' ),
				'ref'            => array( 'type' => 'string', 'default' => 'main' ),
			),
			'supports'        => docspress_component_supports(),
		),
		'adjacent-navigation' => array(
			'render_callback' => 'docspress_render_adjacent_navigation',
			'attributes'      => array(
				'rootSlug'      => array( 'type' => 'string', 'default' => 'docs' ),
				'sort'          => array( 'type' => 'string', 'default' => 'menu_order' ),
				'showRoot'      => array( 'type' => 'boolean', 'default' => true ),
				'maxDepth'      => array( 'type' => 'number', 'default' => 0 ),
				'previousLabel' => array( 'type' => 'string', 'default' => '← Previous', 'role' => 'content' ),
				'nextLabel'     => array( 'type' => 'string', 'default' => 'Next →', 'role' => 'content' ),
				'showTitles'    => array( 'type' => 'boolean', 'default' => true ),
			),
			'supports'        => docspress_component_supports(),
		),
		'color-mode-toggle' => array(
			'render_callback' => 'docspress_render_color_toggle',
			'attributes'      => array(
				'label'     => array( 'type' => 'string', 'default' => 'Switch color theme', 'role' => 'content' ),
				'showLabel' => array( 'type' => 'boolean', 'default' => false ),
				'defaultMode' => array( 'type' => 'string', 'default' => 'light' ),
			),
			'supports'        => docspress_component_supports(),
		),
		'docs-menu-toggle' => array(
			'render_callback' => 'docspress_render_menu_toggle',
			'attributes'      => array(
				'label' => array( 'type' => 'string', 'default' => 'Open documentation menu', 'role' => 'content' ),
			),
			'supports'        => docspress_component_supports(),
		),
	);

	foreach ( $blocks as $slug => $args ) {
		register_block_type(
			'docspress/' . $slug,
			array_merge(
				array(
					'api_version'   => 3,
					'editor_script' => 'docspress-theme-blocks-editor',
				),
				$args
			)
		);
	}

	register_block_style( 'core/navigation', array( 'name' => 'underline', 'label' => __( 'Underline', 'docspress' ) ) );
	register_block_style( 'core/navigation', array( 'name' => 'framed', 'label' => __( 'Framed', 'docspress' ) ) );
	register_block_style( 'core/button', array( 'name' => 'text-arrow', 'label' => __( 'Text with arrow', 'docspress' ) ) );
	register_block_style( 'core/post-template', array( 'name' => 'doc-cards', 'label' => __( 'Documentation cards', 'docspress' ) ) );
}
add_action( 'init', 'docspress_register_blocks' );

/**
 * Find the Color Mode Toggle default inside a parsed template-part tree.
 *
 * @param array<int,array<string,mixed>> $blocks Parsed blocks.
 * @return string
 */
function docspress_find_color_mode_default( $blocks ) {
	foreach ( $blocks as $block ) {
		if ( 'docspress/color-mode-toggle' === $block['blockName'] ) {
			$mode = isset( $block['attrs']['defaultMode'] ) ? sanitize_key( $block['attrs']['defaultMode'] ) : 'light';
			return in_array( $mode, array( 'light', 'dark', 'system' ), true ) ? $mode : 'light';
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$mode = docspress_find_color_mode_default( $block['innerBlocks'] );
			if ( $mode ) {
				return $mode;
			}
		}
	}

	return '';
}

/**
 * Initialize visitor color mode before paint.
 */
function docspress_color_mode_bootstrap() {
	$default_mode = 'light';
	$header       = get_block_template( get_stylesheet() . '//header', 'wp_template_part' );
	if ( $header && ! empty( $header->content ) ) {
		$configured_mode = docspress_find_color_mode_default( parse_blocks( $header->content ) );
		if ( $configured_mode ) {
			$default_mode = $configured_mode;
		}
	}
	?>
	<script id="docspress-color-mode">try{var d=<?php echo wp_json_encode( $default_mode ); ?>;var t=localStorage.getItem('docspress-color-mode');if(t==='light'||t==='dark'){document.documentElement.dataset.theme=t}else if(d==='light'||d==='dark'){document.documentElement.dataset.theme=d}else if(window.matchMedia('(prefers-color-scheme: dark)').matches){document.documentElement.dataset.theme='dark'}}catch(e){}</script>
	<?php
}
add_action( 'wp_head', 'docspress_color_mode_bootstrap', 1 );
