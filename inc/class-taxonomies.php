<?php
/**
 * Registers custom taxonomies for the Eternal Product Meta plugin.
 *
 * @package EternalProductMeta
 */

/**
 * Class Eternal_Meta_Taxonomies
 *
 * Handles the registration of all custom taxonomies used by the Eternal Labs
 * storefront: product-type, skin-type, and product-benefit.
 */
class Eternal_Meta_Taxonomies {

	/**
	 * Constructor.
	 *
	 * Hooks the taxonomy registration method to the WordPress init action.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 10 );
	}

	/**
	 * Orchestrates registration of all custom taxonomies.
	 *
	 * Called on the WordPress `init` action. Delegates to three private
	 * registration methods, one for each taxonomy.
	 *
	 * @return void
	 */
	public function register_taxonomies(): void {
		$this->register_product_type();
		$this->register_skin_type();
		$this->register_product_benefit();
	}

	/**
	 * Registers the `product-type` hierarchical taxonomy.
	 *
	 * Attached to the `product` post type. Supports REST API exposure and
	 * displays an admin column in the products list table.
	 *
	 * @return void
	 */
	private function register_product_type(): void {
		$labels = array(
			'name'          => _x( 'Product Types', 'taxonomy general name', 'eternal-product-meta' ),
			'singular_name' => _x( 'Product Type', 'taxonomy singular name', 'eternal-product-meta' ),
			'search_items'  => __( 'Search Product Types', 'eternal-product-meta' ),
			'all_items'     => __( 'All Product Types', 'eternal-product-meta' ),
			'edit_item'     => __( 'Edit Product Type', 'eternal-product-meta' ),
			'update_item'   => __( 'Update Product Type', 'eternal-product-meta' ),
			'add_new_item'  => __( 'Add New Product Type', 'eternal-product-meta' ),
			'new_item_name' => __( 'New Product Type Name', 'eternal-product-meta' ),
			'menu_name'     => __( 'Product Types', 'eternal-product-meta' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'product-type' ),
		);

		register_taxonomy( 'product-type', 'product', $args );
	}

	/**
	 * Registers the `skin-type` hierarchical taxonomy.
	 *
	 * Attached to the `product` post type. Supports REST API exposure and
	 * displays an admin column in the products list table.
	 *
	 * @return void
	 */
	private function register_skin_type(): void {
		$labels = array(
			'name'          => _x( 'Skin Types', 'taxonomy general name', 'eternal-product-meta' ),
			'singular_name' => _x( 'Skin Type', 'taxonomy singular name', 'eternal-product-meta' ),
			'search_items'  => __( 'Search Skin Types', 'eternal-product-meta' ),
			'all_items'     => __( 'All Skin Types', 'eternal-product-meta' ),
			'edit_item'     => __( 'Edit Skin Type', 'eternal-product-meta' ),
			'update_item'   => __( 'Update Skin Type', 'eternal-product-meta' ),
			'add_new_item'  => __( 'Add New Skin Type', 'eternal-product-meta' ),
			'new_item_name' => __( 'New Skin Type Name', 'eternal-product-meta' ),
			'menu_name'     => __( 'Skin Types', 'eternal-product-meta' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'skin-type' ),
		);

		register_taxonomy( 'skin-type', 'product', $args );
	}

	/**
	 * Registers the `product-benefit` hierarchical taxonomy.
	 *
	 * Attached to the `product` post type. Supports REST API exposure and
	 * displays an admin column in the products list table.
	 *
	 * @return void
	 */
	private function register_product_benefit(): void {
		$labels = array(
			'name'          => _x( 'Benefits', 'taxonomy general name', 'eternal-product-meta' ),
			'singular_name' => _x( 'Benefit', 'taxonomy singular name', 'eternal-product-meta' ),
			'search_items'  => __( 'Search Benefits', 'eternal-product-meta' ),
			'all_items'     => __( 'All Benefits', 'eternal-product-meta' ),
			'edit_item'     => __( 'Edit Benefit', 'eternal-product-meta' ),
			'update_item'   => __( 'Update Benefit', 'eternal-product-meta' ),
			'add_new_item'  => __( 'Add New Benefit', 'eternal-product-meta' ),
			'new_item_name' => __( 'New Benefit Name', 'eternal-product-meta' ),
			'menu_name'     => __( 'Benefits', 'eternal-product-meta' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'product-benefit' ),
		);

		register_taxonomy( 'product-benefit', 'product', $args );
	}
}
