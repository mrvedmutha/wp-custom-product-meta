<?php
/**
 * Plugin Name:       Eternal Product Meta
 * Plugin URI:        https://github.com/mrvedmutha/wp-custom-product-meta
 * Description:       Registers custom taxonomies and product meta fields for the Eternal Labs storefront. Provides Gutenberg sidebar panels for content editors — no ACF required.
 * Version:           1.0.0
 * Author:            Eternal Labs
 * Text Domain:       eternal-product-meta
 * Requires at least: 6.4
 * Requires PHP:      8.3
 * WC requires at least: 8.0
 * WC tested up to:   9.0
 *
 * @package EternalProductMeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS).
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

define( 'ETERNAL_META_VERSION', '1.0.0' );
define( 'ETERNAL_META_PATH', plugin_dir_path( __FILE__ ) );
define( 'ETERNAL_META_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialise plugin classes after WooCommerce is loaded.
 *
 * @return void
 */
function eternal_meta_init(): void {
	require_once ETERNAL_META_PATH . 'inc/class-product-tab.php';
	require_once ETERNAL_META_PATH . 'inc/class-meta-registration.php';

	new Eternal_Meta_Product_Tab();
	new Eternal_Meta_Registration();
}
add_action( 'woocommerce_loaded', 'eternal_meta_init' );

/**
 * Enqueue Gutenberg sidebar assets in the block editor.
 *
 * @return void
 */
function eternal_meta_enqueue_editor(): void {
	$asset_file = ETERNAL_META_PATH . 'src/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'eternal-product-meta-editor',
		ETERNAL_META_URL . 'src/build/index.js',
		array_merge(
			$asset['dependencies'],
			array(
				'wp-plugins',
				'wp-edit-post',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-core-data',
				'wp-block-editor',
			)
		),
		$asset['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'eternal_meta_enqueue_editor' );
