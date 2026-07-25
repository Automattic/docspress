<?php
/**
 * Import a mounted Markdown documentation directory into WordPress Playground.
 *
 * The public Playground command mounts a repository's docs/ directory at
 * /wordpress/docspress-source-docs before this file runs.
 *
 * @package DocsPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DOCSPRESS_LOCAL_DOCS_SOURCE = '/wordpress/docspress-source-docs';
const DOCSPRESS_LOCAL_DOCS_ROOT   = 'docs';

/**
 * Normalize a relative path without allowing it to escape its root.
 *
 * @param string $path Relative path.
 * @return string
 */
function docspress_local_docs_normalize_path( $path ) {
	$segments = array();

	foreach ( explode( '/', str_replace( '\\', '/', $path ) ) as $segment ) {
		if ( '' === $segment || '.' === $segment ) {
			continue;
		}

		if ( '..' === $segment ) {
			array_pop( $segments );
			continue;
		}

		$segments[] = $segment;
	}

	return implode( '/', $segments );
}

/**
 * Convert a filename or slug into a readable fallback title.
 *
 * @param string $value Filename or slug.
 * @return string
 */
function docspress_local_docs_humanize( $value ) {
	$value = preg_replace( '/\.(?:md|markdown)$/i', '', $value );
	$value = str_replace( array( '-', '_' ), ' ', (string) $value );
	$value = preg_replace( '/\s+/', ' ', $value );

	return ucwords( trim( (string) $value ) );
}

/**
 * Parse the small frontmatter subset used by DocsPress.
 *
 * @param string $markdown Markdown source.
 * @return array{meta: array<string, mixed>, body: string}
 */
function docspress_local_docs_frontmatter( $markdown ) {
	$meta = array();

	if ( ! preg_match( '/\A---\R(.*?)\R---(?:\R|\z)/s', $markdown, $match ) ) {
		return array(
			'meta' => $meta,
			'body' => $markdown,
		);
	}

	foreach ( preg_split( '/\R/', $match[1] ) as $line ) {
		if ( ! preg_match( '/^([A-Za-z0-9_-]+)\s*:\s*(.*)$/', $line, $field ) ) {
			continue;
		}

		$key   = strtolower( str_replace( '-', '_', $field[1] ) );
		$value = trim( $field[2] );

		if (
			strlen( $value ) >= 2
			&& (
				( '"' === $value[0] && '"' === substr( $value, -1 ) )
				|| ( "'" === $value[0] && "'" === substr( $value, -1 ) )
			)
		) {
			$value = substr( $value, 1, -1 );
		}

		if ( in_array( strtolower( $value ), array( 'true', 'false' ), true ) ) {
			$value = 'true' === strtolower( $value );
		} elseif ( preg_match( '/^-?\d+$/', $value ) ) {
			$value = (int) $value;
		}

		$meta[ $key ] = $value;
	}

	return array(
		'meta' => $meta,
		'body' => substr( $markdown, strlen( $match[0] ) ),
	);
}

/**
 * Return the route segments represented by a Markdown path.
 *
 * index.md and README.md represent their containing directory.
 *
 * @param string $relative_path Relative Markdown path.
 * @return string[]
 */
function docspress_local_docs_route_segments( $relative_path ) {
	$without_extension = preg_replace( '/\.(?:md|markdown)$/i', '', $relative_path );
	$segments          = explode( '/', docspress_local_docs_normalize_path( $without_extension ) );
	$last              = strtolower( (string) end( $segments ) );

	if ( in_array( $last, array( 'index', 'readme' ), true ) ) {
		array_pop( $segments );
	}

	return array_values(
		array_filter(
			array_map(
				static function ( $segment ) {
					return sanitize_title( $segment );
				},
				$segments
			)
		)
	);
}

/**
 * Turn route segments into a stable Page key.
 *
 * @param string[] $segments Route segments.
 * @return string
 */
function docspress_local_docs_page_key( $segments ) {
	return implode( '/', array_merge( array( DOCSPRESS_LOCAL_DOCS_ROOT ), $segments ) );
}

/**
 * Turn route segments into a root-relative URL.
 *
 * @param string[] $segments Route segments.
 * @return string
 */
function docspress_local_docs_route_url( $segments ) {
	$path = implode( '/', array_merge( array( DOCSPRESS_LOCAL_DOCS_ROOT ), $segments ) );

	return '/' . trailingslashit( $path );
}

/**
 * Discover Markdown files and their WordPress routes.
 *
 * @param string $source_dir Mounted source directory.
 * @return array<int, array<string, mixed>>
 */
function docspress_local_docs_discover( $source_dir ) {
	$documents = array();
	$iterator  = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $source_dir, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file ) {
		if ( ! $file->isFile() || ! preg_match( '/\.(?:md|markdown)$/i', $file->getFilename() ) ) {
			continue;
		}

		$relative = ltrim( str_replace( '\\', '/', substr( $file->getPathname(), strlen( $source_dir ) ) ), '/' );
		$source   = file_get_contents( $file->getPathname() );

		if ( false === $source ) {
			wp_die( esc_html( 'DocsPress could not read ' . $relative . '.' ) );
		}

		$parsed   = docspress_local_docs_frontmatter( $source );
		$segments = docspress_local_docs_route_segments( $relative );
		$title    = isset( $parsed['meta']['title'] ) ? (string) $parsed['meta']['title'] : '';
		$body     = $parsed['body'];

		if ( preg_match( '/^#\s+(.+?)\s*$/m', $body, $heading, PREG_OFFSET_CAPTURE ) ) {
			if ( '' === trim( $title ) ) {
				$title = trim( $heading[1][0] );
			}

			$body = substr_replace( $body, '', $heading[0][1], strlen( $heading[0][0] ) );
		}

		if ( '' === trim( $title ) ) {
			$title = empty( $segments )
				? 'Documentation'
				: docspress_local_docs_humanize( end( $segments ) );
		}

		$documents[] = array(
			'relative'          => $relative,
			'segments'          => $segments,
			'key'               => docspress_local_docs_page_key( $segments ),
			'title'             => wp_strip_all_tags( $title ),
			'body'              => trim( $body ),
			'sidebar_position'  => $parsed['meta']['sidebar_position'] ?? $parsed['meta']['sidebarposition'] ?? null,
			'sidebar_collapsed' => $parsed['meta']['sidebar_collapsed'] ?? $parsed['meta']['sidebarcollapsed'] ?? null,
		);
	}

	usort(
		$documents,
		static function ( $left, $right ) {
			return strnatcasecmp( $left['relative'], $right['relative'] );
		}
	);

	return $documents;
}

/**
 * Build lookup variants for rewriting Markdown links.
 *
 * @param array<int, array<string, mixed>> $documents Discovered documents.
 * @return array<string, string>
 */
function docspress_local_docs_route_map( $documents ) {
	$routes = array();

	foreach ( $documents as $document ) {
		$relative          = docspress_local_docs_normalize_path( $document['relative'] );
		$without_extension = preg_replace( '/\.(?:md|markdown)$/i', '', $relative );
		$route             = docspress_local_docs_route_url( $document['segments'] );
		$routes[ $relative ]          = $route;
		$routes[ $without_extension ] = $route;

		$basename = strtolower( basename( $without_extension ) );
		if ( in_array( $basename, array( 'index', 'readme' ), true ) ) {
			$directory            = docspress_local_docs_normalize_path( dirname( $without_extension ) );
			$routes[ $directory ] = $route;
		}
	}

	return $routes;
}

/**
 * Resolve a local Markdown link to the imported WordPress Page.
 *
 * @param string               $url             Markdown URL.
 * @param string               $source_relative Source Markdown path.
 * @param array<string,string> $route_map       Markdown path to Page URL.
 * @return string
 */
function docspress_local_docs_rewrite_url( $url, $source_relative, $route_map ) {
	$url = trim( html_entity_decode( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

	if (
		'' === $url
		|| '#' === $url[0]
		|| '/' === $url[0]
		|| preg_match( '#^[a-z][a-z0-9+.-]*:#i', $url )
	) {
		return $url;
	}

	$fragment = '';
	$query    = '';

	if ( false !== strpos( $url, '#' ) ) {
		list( $url, $hash ) = explode( '#', $url, 2 );
		$fragment           = '#' . $hash;
	}

	if ( false !== strpos( $url, '?' ) ) {
		list( $url, $query_string ) = explode( '?', $url, 2 );
		$query                      = '?' . $query_string;
	}

	$base      = dirname( $source_relative );
	$candidate = docspress_local_docs_normalize_path(
		( '.' === $base ? '' : $base . '/' ) . rawurldecode( $url )
	);
	$variants  = array(
		$candidate,
		$candidate . '.md',
		$candidate . '.markdown',
		docspress_local_docs_normalize_path( $candidate . '/index.md' ),
		docspress_local_docs_normalize_path( $candidate . '/README.md' ),
	);

	foreach ( $variants as $variant ) {
		if ( isset( $route_map[ $variant ] ) ) {
			return $route_map[ $variant ] . $query . $fragment;
		}
	}

	return $url . $query . $fragment;
}

/**
 * Import a relative Markdown image into the Playground uploads directory.
 *
 * @param string $url             Image URL.
 * @param string $source_relative Source Markdown path.
 * @param string $source_dir      Mounted documentation directory.
 * @return string
 */
function docspress_local_docs_image_url( $url, $source_relative, $source_dir ) {
	if ( preg_match( '#^(?:https?:)?//#i', $url ) || str_starts_with( $url, 'data:' ) ) {
		return $url;
	}

	static $imported = array();

	$base      = dirname( $source_relative );
	$relative  = docspress_local_docs_normalize_path( ( '.' === $base ? '' : $base . '/' ) . rawurldecode( $url ) );
	$candidate = realpath( trailingslashit( $source_dir ) . $relative );
	$root      = realpath( $source_dir );

	if ( ! $candidate || ! $root || ! str_starts_with( $candidate, trailingslashit( $root ) ) || ! is_file( $candidate ) ) {
		return $url;
	}

	if ( isset( $imported[ $candidate ] ) ) {
		return $imported[ $candidate ];
	}

	$upload = wp_upload_bits( basename( $candidate ), null, file_get_contents( $candidate ) );
	if ( ! empty( $upload['error'] ) ) {
		return $url;
	}

	$imported[ $candidate ] = $upload['url'];
	return $upload['url'];
}

/**
 * Split a Markdown destination into URL and optional title.
 *
 * @param string $destination Markdown destination.
 * @return array{url: string, title: string}
 */
function docspress_local_docs_destination( $destination ) {
	$destination = trim( $destination );
	$url         = $destination;
	$title       = '';

	if ( preg_match( '/^(\S+?)(?:\s+["\'](.*?)["\'])$/s', $destination, $match ) ) {
		$url   = $match[1];
		$title = $match[2];
	}

	if ( strlen( $url ) > 1 && '<' === $url[0] && '>' === substr( $url, -1 ) ) {
		$url = substr( $url, 1, -1 );
	}

	return array(
		'url'   => $url,
		'title' => $title,
	);
}

/**
 * Convert inline Markdown while escaping all untrusted HTML.
 *
 * @param string               $text            Inline Markdown.
 * @param string               $source_relative Source Markdown path.
 * @param array<string,string> $route_map       Markdown path to Page URL.
 * @param string               $source_dir      Mounted documentation directory.
 * @return string
 */
function docspress_local_docs_inline( $text, $source_relative, $route_map, $source_dir ) {
	$placeholders = array();
	$stash        = static function ( $html ) use ( &$placeholders ) {
		$token                  = 'DOCSPRESSPLACEHOLDER' . count( $placeholders ) . 'TOKEN';
		$placeholders[ $token ] = $html;
		return $token;
	};

	$text = preg_replace_callback(
		'/`([^`\n]+)`/',
		static function ( $match ) use ( $stash ) {
			return $stash( '<code>' . esc_html( $match[1] ) . '</code>' );
		},
		$text
	);

	$text = preg_replace_callback(
		'/!\[([^\]]*)\]\(([^)\n]+)\)/',
		static function ( $match ) use ( $stash, $source_relative, $source_dir ) {
			$destination = docspress_local_docs_destination( $match[2] );
			$url         = docspress_local_docs_image_url( $destination['url'], $source_relative, $source_dir );
			$title       = '' !== $destination['title'] ? ' title="' . esc_attr( $destination['title'] ) . '"' : '';

			return $stash(
				'<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $match[1] ) . '"' . $title . '>'
			);
		},
		$text
	);

	$text = preg_replace_callback(
		'/\[([^\]]+)\]\(([^)\n]+)\)/',
		static function ( $match ) use ( $stash, $source_relative, $route_map ) {
			$destination = docspress_local_docs_destination( $match[2] );
			$url         = docspress_local_docs_rewrite_url( $destination['url'], $source_relative, $route_map );
			$title       = '' !== $destination['title'] ? ' title="' . esc_attr( $destination['title'] ) . '"' : '';

			return $stash(
				'<a href="' . esc_url( $url ) . '"' . $title . '>' . esc_html( $match[1] ) . '</a>'
			);
		},
		$text
	);

	$html = esc_html( $text );
	$html = preg_replace( '/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html );
	$html = preg_replace( '/__(.+?)__/s', '<strong>$1</strong>', $html );
	$html = preg_replace( '/~~(.+?)~~/s', '<s>$1</s>', $html );
	$html = preg_replace( '/(?<!\*)\*([^*\n]+)\*(?!\*)/', '<em>$1</em>', $html );
	$html = preg_replace( '/(?<!_)_([^_\n]+)_(?!_)/', '<em>$1</em>', $html );

	foreach ( $placeholders as $token => $replacement ) {
		$html = str_replace( $token, $replacement, $html );
	}

	return $html;
}

/**
 * Serialize one Gutenberg block.
 *
 * @param string $name       Block name without the core/ prefix.
 * @param string $inner_html Rendered block HTML.
 * @param array  $attributes Optional block attributes.
 * @return string
 */
function docspress_local_docs_block( $name, $inner_html, $attributes = array() ) {
	return get_comment_delimited_block_content( 'core/' . $name, $attributes, $inner_html );
}

/**
 * Split a Markdown table row into cells.
 *
 * @param string $line Table row.
 * @return string[]
 */
function docspress_local_docs_table_cells( $line ) {
	$line = trim( $line );
	$line = preg_replace( '/^\|/', '', $line );
	$line = preg_replace( '/\|$/', '', $line );

	return array_map( 'trim', preg_split( '/(?<!\\\\)\|/', $line ) );
}

/**
 * Convert Markdown to Gutenberg-compatible block markup.
 *
 * @param string               $markdown        Markdown body.
 * @param string               $source_relative Source Markdown path.
 * @param array<string,string> $route_map       Markdown path to Page URL.
 * @param string               $source_dir      Mounted documentation directory.
 * @return string
 */
function docspress_local_docs_markdown_to_blocks( $markdown, $source_relative, $route_map, $source_dir ) {
	$lines  = preg_split( '/\R/', str_replace( "\t", '    ', $markdown ) );
	$blocks = array();
	$count  = count( $lines );
	$index  = 0;

	while ( $index < $count ) {
		$line    = $lines[ $index ];
		$trimmed = trim( $line );

		if ( '' === $trimmed ) {
			++$index;
			continue;
		}

		if ( preg_match( '/^(```+|~~~+)\s*([A-Za-z0-9_+.-]*)\s*$/', $trimmed, $fence ) ) {
			$marker = substr( $fence[1], 0, 3 );
			$code   = array();
			++$index;

			while ( $index < $count && ! str_starts_with( trim( $lines[ $index ] ), $marker ) ) {
				$code[] = $lines[ $index ];
				++$index;
			}

			if ( $index < $count ) {
				++$index;
			}

			$class    = '' !== $fence[2] ? ' class="language-' . esc_attr( strtolower( $fence[2] ) ) . '"' : '';
			$blocks[] = docspress_local_docs_block(
				'code',
				'<pre class="wp-block-code"><code' . $class . '>' . esc_html( implode( "\n", $code ) ) . '</code></pre>'
			);
			continue;
		}

		if ( str_starts_with( $trimmed, '<!-- wp:' ) ) {
			$raw   = array( $line );
			$depth = substr_count( $line, '<!-- wp:' ) - substr_count( $line, '<!-- /wp:' );

			if ( str_contains( $line, '/-->' ) ) {
				$depth = 0;
			}

			++$index;
			while ( $index < $count && $depth > 0 ) {
				$raw[] = $lines[ $index ];
				$depth += substr_count( $lines[ $index ], '<!-- wp:' );
				$depth -= substr_count( $lines[ $index ], '<!-- /wp:' );
				++$index;
			}

			$blocks[] = implode( "\n", $raw );
			continue;
		}

		if ( preg_match( '/^(#{1,6})\s+(.+?)\s*#*\s*$/', $trimmed, $heading ) ) {
			$level      = strlen( $heading[1] );
			$attributes = 2 === $level ? array() : array( 'level' => $level );
			$blocks[]   = docspress_local_docs_block(
				'heading',
				'<h' . $level . ' class="wp-block-heading">'
				. docspress_local_docs_inline( $heading[2], $source_relative, $route_map, $source_dir )
				. '</h' . $level . '>',
				$attributes
			);
			++$index;
			continue;
		}

		if (
			$index + 1 < $count
			&& str_contains( $line, '|' )
			&& preg_match( '/^\s*\|?\s*:?-{3,}:?\s*(?:\|\s*:?-{3,}:?\s*)+\|?\s*$/', $lines[ $index + 1 ] )
		) {
			$header = docspress_local_docs_table_cells( $line );
			$rows   = array();
			$index += 2;

			while ( $index < $count && '' !== trim( $lines[ $index ] ) && str_contains( $lines[ $index ], '|' ) ) {
				$rows[] = docspress_local_docs_table_cells( $lines[ $index ] );
				++$index;
			}

			$table = '<figure class="wp-block-table"><table><thead><tr>';
			foreach ( $header as $cell ) {
				$table .= '<th>' . docspress_local_docs_inline( $cell, $source_relative, $route_map, $source_dir ) . '</th>';
			}
			$table .= '</tr></thead><tbody>';
			foreach ( $rows as $row ) {
				$table .= '<tr>';
				foreach ( $row as $cell ) {
					$table .= '<td>' . docspress_local_docs_inline( $cell, $source_relative, $route_map, $source_dir ) . '</td>';
				}
				$table .= '</tr>';
			}
			$table     .= '</tbody></table></figure>';
			$blocks[] = docspress_local_docs_block( 'table', $table, array( 'hasFixedLayout' => false ) );
			continue;
		}

		if ( preg_match( '/^\s*([-+*])\s+(.+)$/', $line, $list_match ) || preg_match( '/^\s*(\d+)[.)]\s+(.+)$/', $line, $list_match ) ) {
			$ordered = ctype_digit( (string) $list_match[1] );
			$start   = $ordered ? (int) $list_match[1] : 1;
			$items   = array();

			while ( $index < $count ) {
				$current = $lines[ $index ];
				$matched = $ordered
					? preg_match( '/^\s*(\d+)[.)]\s+(.+)$/', $current, $item )
					: preg_match( '/^\s*[-+*]\s+(.+)$/', $current, $item );

				if ( ! $matched ) {
					break;
				}

				$text    = $ordered ? $item[2] : $item[1];
				$items[] = '<!-- wp:list-item --><li>'
					. docspress_local_docs_inline( $text, $source_relative, $route_map, $source_dir )
					. '</li><!-- /wp:list-item -->';
				++$index;
			}

			$tag        = $ordered ? 'ol' : 'ul';
			$attributes = $ordered ? array( 'ordered' => true ) : array();
			$start_html = $ordered && 1 !== $start ? ' start="' . $start . '"' : '';
			if ( $ordered && 1 !== $start ) {
				$attributes['start'] = $start;
			}

			$blocks[] = docspress_local_docs_block(
				'list',
				'<' . $tag . ' class="wp-block-list"' . $start_html . '>' . implode( '', $items ) . '</' . $tag . '>',
				$attributes
			);
			continue;
		}

		if ( str_starts_with( $trimmed, '>' ) ) {
			$quote = array();

			while ( $index < $count && preg_match( '/^\s*>\s?(.*)$/', $lines[ $index ], $quote_line ) ) {
				$quote[] = $quote_line[1];
				++$index;
			}

			$blocks[] = docspress_local_docs_block(
				'quote',
				'<blockquote class="wp-block-quote"><p>'
				. docspress_local_docs_inline( implode( ' ', $quote ), $source_relative, $route_map, $source_dir )
				. '</p></blockquote>'
			);
			continue;
		}

		if ( preg_match( '/^(?:-{3,}|\*{3,}|_{3,})$/', $trimmed ) ) {
			$blocks[] = '<!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->';
			++$index;
			continue;
		}

		if ( preg_match( '/^!\[([^\]]*)\]\(([^)\n]+)\)\s*$/', $trimmed, $image ) ) {
			$destination = docspress_local_docs_destination( $image[2] );
			$url         = docspress_local_docs_image_url( $destination['url'], $source_relative, $source_dir );
			$caption     = '' !== $destination['title']
				? '<figcaption class="wp-element-caption">' . esc_html( $destination['title'] ) . '</figcaption>'
				: '';
			$blocks[]    = docspress_local_docs_block(
				'image',
				'<figure class="wp-block-image"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $image[1] ) . '">'
				. $caption
				. '</figure>'
			);
			++$index;
			continue;
		}

		if ( str_starts_with( $trimmed, '<' ) ) {
			$html = array();

			while ( $index < $count && '' !== trim( $lines[ $index ] ) ) {
				$html[] = $lines[ $index ];
				++$index;
			}

			$blocks[] = docspress_local_docs_block( 'html', implode( "\n", $html ) );
			continue;
		}

		$paragraph = array( $trimmed );
		++$index;

		while ( $index < $count ) {
			$next = trim( $lines[ $index ] );
			if (
				'' === $next
				|| preg_match( '/^(?:#{1,6}\s+|```|~~~|>\s?|[-+*]\s+|\d+[.)]\s+|<!-- wp:|<)/', $next )
				|| (
					$index + 1 < $count
					&& str_contains( $next, '|' )
					&& preg_match( '/^\s*\|?\s*:?-{3,}:?\s*(?:\|\s*:?-{3,}:?\s*)+\|?\s*$/', $lines[ $index + 1 ] )
				)
			) {
				break;
			}

			$paragraph[] = $next;
			++$index;
		}

		$blocks[] = docspress_local_docs_block(
			'paragraph',
			'<p>' . docspress_local_docs_inline( implode( ' ', $paragraph ), $source_relative, $route_map, $source_dir ) . '</p>'
		);
	}

	return implode( "\n\n", $blocks );
}

/**
 * Insert or update one imported documentation Page.
 *
 * @param array<string,mixed> $page      Page data.
 * @param int                 $parent_id Parent Page ID.
 * @param int                 $order     Menu order.
 * @return int
 */
function docspress_local_docs_upsert_page( $page, $parent_id, $order ) {
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'name'           => $page['slug'],
			'post_parent'    => $parent_id,
			'posts_per_page' => 1,
		)
	);
	$post     = array(
		'post_title'     => sanitize_text_field( $page['title'] ),
		'post_name'      => sanitize_title( $page['slug'] ),
		'post_content'   => wp_slash( $page['content'] ),
		'post_parent'    => $parent_id,
		'menu_order'     => null !== $page['sidebar_position'] ? (int) $page['sidebar_position'] : $order,
		'post_status'    => 'publish',
		'post_type'      => 'page',
		'comment_status' => 'closed',
	);

	if ( $existing ) {
		$post['ID'] = $existing[0]->ID;
		$page_id    = wp_update_post( $post );
	} else {
		$page_id = wp_insert_post( $post );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		wp_die( esc_html( 'DocsPress could not import ' . $page['title'] . '.' ) );
	}

	if ( ! empty( $page['source_path'] ) ) {
		update_post_meta( $page_id, '_docspress_source_path', sanitize_text_field( $page['source_path'] ) );
	}

	if ( null !== $page['sidebar_collapsed'] ) {
		update_post_meta( $page_id, '_docspress_sidebar_collapsed', (bool) $page['sidebar_collapsed'] );
	}

	update_post_meta( $page_id, '_docspress_playground_import', 1 );

	return (int) $page_id;
}

$source_dir = DOCSPRESS_LOCAL_DOCS_SOURCE;
if ( ! is_dir( $source_dir ) ) {
	wp_die(
		'DocsPress could not find the mounted docs directory. Start Playground with '
		. '<code>--mount="$PWD/docs:/wordpress/docspress-source-docs"</code>.'
	);
}

// Preserve valid block JSON containing HTML attributes during the trusted local import.
wp_set_current_user( 1 );

$documents = docspress_local_docs_discover( $source_dir );
if ( empty( $documents ) ) {
	wp_die( 'DocsPress did not find any .md or .markdown files in the mounted docs directory.' );
}

$route_map = docspress_local_docs_route_map( $documents );
$pages     = array();

// Every path segment becomes a Page, even when a directory has no index file.
$pages[ DOCSPRESS_LOCAL_DOCS_ROOT ] = array(
	'key'               => DOCSPRESS_LOCAL_DOCS_ROOT,
	'parent_key'        => null,
	'slug'              => DOCSPRESS_LOCAL_DOCS_ROOT,
	'title'             => 'Documentation',
	'content'           => docspress_local_docs_block(
		'paragraph',
		'<p>Documentation imported from the repository&apos;s <code>docs/</code> directory.</p>'
	),
	'depth'             => 1,
	'source_path'       => null,
	'sidebar_position'  => null,
	'sidebar_collapsed' => null,
);

foreach ( $documents as $document ) {
	$segments = $document['segments'];

	for ( $depth = 1; $depth <= count( $segments ); ++$depth ) {
		$partial = array_slice( $segments, 0, $depth );
		$key     = docspress_local_docs_page_key( $partial );

		if ( isset( $pages[ $key ] ) ) {
			continue;
		}

		$parent_segments = array_slice( $partial, 0, -1 );
		$pages[ $key ]   = array(
			'key'               => $key,
			'parent_key'        => docspress_local_docs_page_key( $parent_segments ),
			'slug'              => end( $partial ),
			'title'             => docspress_local_docs_humanize( end( $partial ) ),
			'content'           => docspress_local_docs_block(
				'paragraph',
				'<p>Choose a documentation page from this section.</p>'
			),
			'depth'             => $depth + 1,
			'source_path'       => null,
			'sidebar_position'  => null,
			'sidebar_collapsed' => null,
		);
	}

	$key = $document['key'];
	if ( ! empty( $pages[ $key ]['source_path'] ) ) {
		wp_die( esc_html( 'Two Markdown files resolve to the same documentation route: ' . $document['relative'] . '.' ) );
	}

	$pages[ $key ] = array(
		'key'               => $key,
		'parent_key'        => empty( $segments )
			? null
			: docspress_local_docs_page_key( array_slice( $segments, 0, -1 ) ),
		'slug'              => empty( $segments ) ? DOCSPRESS_LOCAL_DOCS_ROOT : end( $segments ),
		'title'             => $document['title'],
		'content'           => docspress_local_docs_markdown_to_blocks(
			$document['body'],
			$document['relative'],
			$route_map,
			$source_dir
		),
		'depth'             => count( $segments ) + 1,
		'source_path'       => 'docs/' . $document['relative'],
		'sidebar_position'  => $document['sidebar_position'],
		'sidebar_collapsed' => $document['sidebar_collapsed'],
	);
}

uasort(
	$pages,
	static function ( $left, $right ) {
		$depth = (int) $left['depth'] <=> (int) $right['depth'];
		return 0 !== $depth ? $depth : strnatcasecmp( $left['key'], $right['key'] );
	}
);

foreach ( array( array( 'post', 'hello-world' ), array( 'page', 'sample-page' ) ) as $starter ) {
	$starter_post = get_page_by_path( $starter[1], OBJECT, $starter[0] );
	if ( $starter_post ) {
		wp_delete_post( $starter_post->ID, true );
	}
}

$page_ids = array();
$orders   = array();
foreach ( $pages as $page ) {
	$parent_id  = $page['parent_key'] && isset( $page_ids[ $page['parent_key'] ] )
		? $page_ids[ $page['parent_key'] ]
		: 0;
	$order_key  = $page['parent_key'] ?: '__root__';
	$orders[ $order_key ] = isset( $orders[ $order_key ] ) ? $orders[ $order_key ] + 1 : 0;
	$page_ids[ $page['key'] ] = docspress_local_docs_upsert_page(
		$page,
		$parent_id,
		$orders[ $order_key ]
	);
}

$root_page = $pages[ DOCSPRESS_LOCAL_DOCS_ROOT ];
update_option( 'blogname', sanitize_text_field( $root_page['title'] ) );
update_option( 'blogdescription', 'Markdown documentation converted into native WordPress Pages by DocsPress.' );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( false );
