#!/usr/bin/env node
import fs from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { collectDesiredPages } from "../src/docs.js";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, "..");
const exampleDir = path.join(rootDir, "examples", "stock-wordpress");
const outputPath = path.join(exampleDir, "blueprint.json");

const pages = await collectDesiredPages({
  cwd: exampleDir,
  docsDir: "docs",
  rootSlug: "docs",
  rootTitle: "DocsPress on stock WordPress",
  createH1: false,
  rewriteLinks: true,
  editLink: false,
  status: "publish"
});

for (const page of pages) {
  const customBlock = page.content.match(
    /<!--\s+wp:((?!core\/)[a-z][a-z0-9_-]*\/[^\s]+)[^>]*-->/
  );
  if (customBlock) {
    throw new Error(`Stock WordPress Playground cannot include custom block ${customBlock[1]}.`);
  }
}

const payload = {
  generatedBy: "scripts/build-playground-stock.mjs",
  pages: pages.map((page) => ({
    key: page.key,
    parentKey: page.parentKey,
    slug: page.slug,
    title: page.title,
    content: page.content,
    depth: page.depth
  }))
};
const encodedPayload = Buffer.from(JSON.stringify(payload), "utf8").toString("base64");
const setupCode = `<?php
require_once '/wordpress/wp-load.php';

$generated = json_decode( base64_decode( '${encodedPayload}' ), true );
if ( ! is_array( $generated ) || empty( $generated['pages'] ) ) {
\twp_die( 'The stock WordPress documentation payload is invalid.' );
}

foreach ( array( array( 'post', 'hello-world' ), array( 'page', 'sample-page' ) ) as $starter_content ) {
\t$starter_post = get_page_by_path( $starter_content[1], OBJECT, $starter_content[0] );
\tif ( $starter_post ) {
\t\twp_delete_post( $starter_post->ID, true );
\t}
}

usort(
\t$generated['pages'],
\tstatic function ( $left, $right ) {
\t\t$depth_comparison = (int) $left['depth'] <=> (int) $right['depth'];
\t\treturn $depth_comparison ? $depth_comparison : strcmp( $left['key'], $right['key'] );
\t}
);

$ids_by_key = array();
$order_by_parent = array();

foreach ( $generated['pages'] as $page ) {
\t$parent_key = isset( $page['parentKey'] ) ? (string) $page['parentKey'] : '';
\t$parent_id = $parent_key && isset( $ids_by_key[ $parent_key ] ) ? $ids_by_key[ $parent_key ] : 0;
\tif ( $parent_key && ! $parent_id ) {
\t\twp_die( esc_html( 'Generated documentation parent is unavailable: ' . $parent_key ) );
\t}

\t$content = (string) $page['content'];
\tif ( 'docs' === $page['key'] ) {
\t\trequire_once ABSPATH . 'wp-admin/includes/plugin.php';
\t\t$active_theme = wp_get_theme();
\t\t$theme_state = wp_get_theme( 'docspress' )->exists() ? 'Installed' : 'Not installed';
\t\t$blocks_state = file_exists( WP_PLUGIN_DIR . '/docspress-blocks/docspress-blocks.php' ) ? 'Installed' : 'Not installed';
\t\t$proof = '<figure class="wp-block-table"><table><thead><tr><th>Runtime component</th><th>State</th></tr></thead><tbody>';
\t\t$proof .= '<tr><td>Active theme</td><td>' . esc_html( $active_theme->get( 'Name' ) ) . '</td></tr>';
\t\t$proof .= '<tr><td>DocsPress theme</td><td>' . esc_html( $theme_state ) . '</td></tr>';
\t\t$proof .= '<tr><td>DocsPress Blocks plugin</td><td>' . esc_html( $blocks_state ) . '</td></tr>';
\t\t$proof .= '<tr><td>Published content</td><td>Native core Gutenberg blocks</td></tr>';
\t\t$proof .= '</tbody></table></figure>';
\t\t$content .= "\\n\\n" . get_comment_delimited_block_content( 'core/heading', array( 'level' => 2 ), '<h2 class="wp-block-heading">Runtime proof</h2>' );
\t\t$content .= "\\n\\n" . get_comment_delimited_block_content( 'core/table', array(), $proof );
\t}

\t$order_key = $parent_key ? $parent_key : 'root';
\t$order = isset( $order_by_parent[ $order_key ] ) ? $order_by_parent[ $order_key ] : 0;
\t$existing = get_page_by_path( (string) $page['slug'], OBJECT, 'page' );
\t$post_data = array(
\t\t'ID' => $existing ? $existing->ID : 0,
\t\t'post_type' => 'page',
\t\t'post_status' => 'publish',
\t\t'post_title' => (string) $page['title'],
\t\t'post_name' => (string) $page['slug'],
\t\t'post_parent' => $parent_id,
\t\t'menu_order' => $order * 10,
\t\t'post_content' => $content,
\t);
\t$page_id = wp_insert_post( wp_slash( $post_data ), true );
\tif ( is_wp_error( $page_id ) ) {
\t\twp_die( esc_html( $page_id->get_error_message() ) );
\t}

\t$ids_by_key[ $page['key'] ] = (int) $page_id;
\t$order_by_parent[ $order_key ] = $order + 1;
}

$docs_id = isset( $ids_by_key['docs'] ) ? $ids_by_key['docs'] : 0;
if ( ! $docs_id ) {
\twp_die( 'The generated documentation does not contain the docs root Page.' );
}

update_option( 'blogname', 'DocsPress on stock WordPress' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $docs_id );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules();
`;

const blueprint = {
  $schema: "https://playground.wordpress.net/blueprint-schema.json",
  meta: {
    title: "DocsPress on stock WordPress",
    author: "Automattic",
    description: "Markdown published as native Gutenberg Pages with no DocsPress theme or Blocks plugin installed."
  },
  preferredVersions: {
    php: "8.3",
    wp: "latest"
  },
  constants: {
    WP_ENVIRONMENT_TYPE: "development"
  },
  landingPage: "/",
  login: true,
  steps: [
    {
      step: "runPHP",
      code: setupCode
    }
  ]
};

await fs.writeFile(outputPath, `${JSON.stringify(blueprint, null, 2)}\n`);
console.log(`Generated stock WordPress Playground at ${path.relative(rootDir, outputPath)}.`);
