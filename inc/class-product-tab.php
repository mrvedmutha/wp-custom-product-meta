<?php
/**
 * WooCommerce product data tab for Eternal Product Meta.
 *
 * Adds a "Product Details" tab to the WooCommerce product data metabox with
 * direct text and textarea inputs — no taxonomy term creation required.
 *
 * @package EternalProductMeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Eternal_Meta_Product_Tab
 *
 * Registers a custom tab inside the WooCommerce product editor (General tab row)
 * and renders grouped text/textarea fields for identity, classification, fragrance,
 * ingredients, how-to-use, and editorial content.
 */
class Eternal_Meta_Product_Tab {

	/**
	 * Constructor.
	 *
	 * Hooks tab registration, panel rendering, and field saving into WooCommerce.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
	}

	/**
	 * Outputs the Product Details tab navigation item.
	 *
	 * @return void
	 */
	public function add_tab(): void {
		echo '<li class="eternal_meta_options"><a href="#eternal_meta_data"><span>'
			. esc_html__( 'Product Details', 'eternal-product-meta' )
			. '</span></a></li>';
	}

	/**
	 * Renders the Product Details panel with all grouped input fields.
	 *
	 * @return void
	 */
	public function render_panel(): void {
		global $post;
		$id = (int) $post->ID;

		echo '<div id="eternal_meta_data" class="panel woocommerce_options_panel hidden">';

		// ── Identity ──────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Identity', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_name_fr',
				'label'       => __( 'French Subtitle', 'eternal-product-meta' ),
				'placeholder' => 'Sérum hydratant',
				'value'       => (string) get_post_meta( $id, 'product_name_fr', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_eyebrow',
				'label' => __( 'Eyebrow Label', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_eyebrow', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_tagline',
				'label' => __( 'Tagline', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_tagline', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_display_tags',
				'label'       => __( 'Display Tags', 'eternal-product-meta' ),
				'placeholder' => 'Hydrating, SPF, Vegan',
				'description' => __( 'Comma-separated pill labels shown on product cards.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_display_tags', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_card_bg',
				'label'       => __( 'Card Background Colour', 'eternal-product-meta' ),
				'placeholder' => '#f5f5f5',
				'description' => __( 'Hex colour for the product card background.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_card_bg', true ),
			)
		);

		echo '</div>';

		// ── Classification ────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Classification', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_type_label',
				'label'       => __( 'Product Type', 'eternal-product-meta' ),
				'placeholder' => 'Serum',
				'description' => __( 'E.g. Serum, Moisturiser, Cleanser, Eye Cream.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_type_label', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_skin_type',
				'label'       => __( 'Skin Type', 'eternal-product-meta' ),
				'placeholder' => 'All skin types',
				'description' => __( 'E.g. Oily, Dry, Combination, Sensitive, All.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_skin_type', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_benefits',
				'label'       => __( 'Benefits', 'eternal-product-meta' ),
				'placeholder' => 'Hydrating, Anti-aging, Brightening',
				'description' => __( 'Key benefits, one per line or comma-separated.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_benefits', true ),
			)
		);

		echo '</div>';

		// ── Fragrance ─────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Fragrance', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_notes_top',
				'label' => __( 'Top Notes', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_notes_top', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_notes_middle',
				'label' => __( 'Middle Notes', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_notes_middle', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_notes_base',
				'label' => __( 'Base Notes', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_notes_base', true ),
			)
		);

		echo '</div>';

		// ── Ingredients ───────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Ingredients', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_inci',
				'label'       => __( 'INCI List', 'eternal-product-meta' ),
				'description' => __( 'Full INCI ingredient list. HTML allowed.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_inci', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'    => 'product_ingredients_disclaimer',
				'label' => __( 'Disclaimer', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_ingredients_disclaimer', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_allergy_info',
				'label' => __( 'Allergy Info', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_allergy_info', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_dosage_instructions',
				'label' => __( 'Dosage Instructions', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_dosage_instructions', true ),
			)
		);

		echo '</div>';

		// ── How to Use ────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'How to Use', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_how_to_use',
				'label'       => __( 'Instructions', 'eternal-product-meta' ),
				'description' => __( 'HTML allowed.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_how_to_use', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_storage_warnings',
				'label'       => __( 'Storage & Warnings', 'eternal-product-meta' ),
				'description' => __( 'HTML allowed.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_storage_warnings', true ),
			)
		);

		echo '</div>';

		// ── Editorial ─────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Editorial', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_editorial_headline',
				'label' => __( 'Headline', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_editorial_headline', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_editorial_body',
				'label'       => __( 'Body Copy', 'eternal-product-meta' ),
				'description' => __( 'HTML allowed.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_editorial_body', true ),
			)
		);

		echo '</div>';

		echo '</div>'; // #eternal_meta_data
	}

	/**
	 * Saves all product detail fields when a product is published or updated.
	 *
	 * WooCommerce verifies the product nonce before firing this hook.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	public function save_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook fires.
		$text_fields = array(
			'product_name_fr',
			'product_eyebrow',
			'product_tagline',
			'product_display_tags',
			'product_card_bg',
			'product_type_label',
			'product_skin_type',
			'product_notes_top',
			'product_notes_middle',
			'product_notes_base',
			'product_allergy_info',
			'product_dosage_instructions',
			'product_editorial_headline',
		);

		foreach ( $text_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				sanitize_text_field( wp_unslash( $_POST[ $field ] ?? '' ) )
			);
		}

		$textarea_fields = array(
			'product_benefits',
			'product_ingredients_disclaimer',
		);

		foreach ( $textarea_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				sanitize_textarea_field( wp_unslash( $_POST[ $field ] ?? '' ) )
			);
		}

		$html_fields = array(
			'product_inci',
			'product_how_to_use',
			'product_storage_warnings',
			'product_editorial_body',
		);

		foreach ( $html_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				wp_kses_post( wp_unslash( $_POST[ $field ] ?? '' ) )
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
