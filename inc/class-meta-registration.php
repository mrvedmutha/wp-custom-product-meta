<?php
/**
 * Registers custom post meta fields for WooCommerce products.
 *
 * @package EternalProductMeta
 */

/**
 * Class Eternal_Meta_Registration
 *
 * Registers all 18 custom meta fields for the `product` post type.
 * All fields are exposed to the REST API so they can be read and written
 * by the Gutenberg sidebar panels provided by this plugin.
 */
class Eternal_Meta_Registration {

	/**
	 * Constructor.
	 *
	 * Hooks the field registration method to the WordPress init action.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_fields' ), 10 );
	}

	/**
	 * Registers all custom meta fields for the `product` post type.
	 *
	 * Each field is registered with `single => true` and `show_in_rest => true`
	 * to make it available in the Gutenberg block editor via the REST API.
	 *
	 * @return void
	 */
	public function register_fields(): void {

		// 1. French subtitle.
		register_post_meta(
			'product',
			'product_name_fr',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'French subtitle', 'eternal-product-meta' ),
			)
		);

		// 2. PDP eyebrow label.
		register_post_meta(
			'product',
			'product_eyebrow',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'PDP eyebrow label', 'eternal-product-meta' ),
			)
		);

		// 3. One-line tagline.
		register_post_meta(
			'product',
			'product_tagline',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'One-line tagline', 'eternal-product-meta' ),
			)
		);

		// 4. Comma-separated pill labels.
		register_post_meta(
			'product',
			'product_display_tags',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Comma-separated pill labels', 'eternal-product-meta' ),
			)
		);

		// 5. Card background hex colour.
		register_post_meta(
			'product',
			'product_card_bg',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_hex_color',
				'default'           => '#f5f5f5',
				'description'       => __( 'Card background hex colour', 'eternal-product-meta' ),
			)
		);

		// 6. Product type label (replaces product-type taxonomy).
		register_post_meta(
			'product',
			'product_type_label',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Product type, e.g. Serum, Moisturiser', 'eternal-product-meta' ),
			)
		);

		// 7. Skin type label (replaces skin-type taxonomy).
		register_post_meta(
			'product',
			'product_skin_type',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Skin type, e.g. Oily, Dry, All', 'eternal-product-meta' ),
			)
		);

		// 8. Benefits (replaces product-benefit taxonomy).
		register_post_meta(
			'product',
			'product_benefits',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Key benefits, comma-separated', 'eternal-product-meta' ),
			)
		);

		// 9. Top fragrance notes.
		register_post_meta(
			'product',
			'product_notes_top',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 7. Middle fragrance notes.
		register_post_meta(
			'product',
			'product_notes_middle',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 8. Base fragrance notes.
		register_post_meta(
			'product',
			'product_notes_base',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 9. INCI ingredient list (HTML allowed).
		register_post_meta(
			'product',
			'product_inci',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		// 10. Ingredients disclaimer.
		register_post_meta(
			'product',
			'product_ingredients_disclaimer',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);

		// 11. Allergy information.
		register_post_meta(
			'product',
			'product_allergy_info',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 12. How to use (HTML allowed).
		register_post_meta(
			'product',
			'product_how_to_use',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		// 13. Storage & warnings (HTML allowed).
		register_post_meta(
			'product',
			'product_storage_warnings',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		// 14. Dosage instructions.
		register_post_meta(
			'product',
			'product_dosage_instructions',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 15. Ingredient cards (JSON-encoded array).
		register_post_meta(
			'product',
			'product_ingredient_cards',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( string $value ): string {
					$decoded = json_decode( $value, true );

					if ( ! is_array( $decoded ) ) {
						return '[]';
					}

					$encoded = wp_json_encode( $decoded );

					return false !== $encoded ? $encoded : '[]';
				},
			)
		);

		// 16. Editorial headline.
		register_post_meta(
			'product',
			'product_editorial_headline',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// 17. Editorial body copy (HTML allowed).
		register_post_meta(
			'product',
			'product_editorial_body',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		// 18. Editorial image attachment ID.
		register_post_meta(
			'product',
			'product_editorial_image_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
			)
		);

		// 19. Key ingredients section title.
		register_post_meta(
			'product',
			'product_ingredients_title',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Key ingredients section title', 'eternal-product-meta' ),
			)
		);

		// 20. Key ingredients (JSON-encoded array of 3 × {image_id, image_url, name, description}).
		register_post_meta(
			'product',
			'product_key_ingredients',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( string $value ): string {
					$decoded = json_decode( $value, true );

					if ( ! is_array( $decoded ) ) {
						return '[]';
					}

					$encoded = wp_json_encode( $decoded );

					return false !== $encoded ? $encoded : '[]';
				},
				'description'       => __( 'Key ingredients — JSON array of 3 entries with image, name, and description', 'eternal-product-meta' ),
			)
		);

		// 20. Product features (JSON-encoded array of {image_id, image_url, heading, body}).
		register_post_meta(
			'product',
			'product_features',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( string $value ): string {
					$decoded = json_decode( $value, true );

					if ( ! is_array( $decoded ) ) {
						return '[]';
					}

					$encoded = wp_json_encode( $decoded );

					return false !== $encoded ? $encoded : '[]';
				},
				'description'       => __( 'Product features — JSON array of image, heading, and body per feature', 'eternal-product-meta' ),
			)
		);

		// 20. Benefits — paragraph text.
		register_post_meta(
			'product',
			'product_benefits_text',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Benefits as paragraph text', 'eternal-product-meta' ),
			)
		);

		// 21. Benefits — bullet points (one per line).
		register_post_meta(
			'product',
			'product_benefits_bullets',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Benefits as bullet points, one per line', 'eternal-product-meta' ),
			)
		);

		// 22. Product caption (single-line caption shown on the product).
		register_post_meta(
			'product',
			'product_caption',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Product caption', 'eternal-product-meta' ),
			)
		);

		// 21. French text (single-line French product descriptor).
		register_post_meta(
			'product',
			'product_french_text',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'French product descriptor text', 'eternal-product-meta' ),
			)
		);

		// 21. Additional product tags (comma-separated pill labels for the product card).
		register_post_meta(
			'product',
			'product_additional_tags',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Comma-separated pill tags shown on the product card alongside amount + unit', 'eternal-product-meta' ),
			)
		);

		// 21. Buy Box — numeric amount (e.g. 100, 30).
		register_post_meta(
			'product',
			'product_buy_box_amount',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Buy box contains: numeric amount', 'eternal-product-meta' ),
			)
		);

		// 20. Buy Box — unit label (e.g. ml, capsules, g).
		register_post_meta(
			'product',
			'product_buy_box_unit',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'description'       => __( 'Buy box contains: unit of measurement', 'eternal-product-meta' ),
			)
		);

		// 21. Buy Box — consumer-facing ingredients list.
		register_post_meta(
			'product',
			'product_buy_box_ingredients',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Buy box ingredients list', 'eternal-product-meta' ),
			)
		);

		// 22. Buy Box — cautionary / warning text.
		register_post_meta(
			'product',
			'product_buy_box_caution',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Buy box cautionary text', 'eternal-product-meta' ),
			)
		);

		// 23. Buy Box — how to apply instructions.
		register_post_meta(
			'product',
			'product_buy_box_how_to_apply',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Buy box how to apply instructions', 'eternal-product-meta' ),
			)
		);

		// 24. Buy Box — bonus tip / aromatic experience note.
		register_post_meta(
			'product',
			'product_buy_box_bonus_tip',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'description'       => __( 'Buy box bonus tip', 'eternal-product-meta' ),
			)
		);

		// 25. Product video assets (JSON-encoded array of { url } entries).
		register_post_meta(
			'product',
			'product_video_assets',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => '[]',
				'sanitize_callback' => static function ( string $value ): string {
					$decoded = json_decode( $value, true );

					if ( ! is_array( $decoded ) ) {
						return '[]';
					}

					$sanitized = array_map(
						static function ( $item ): array {
							$url = isset( $item['url'] ) && is_string( $item['url'] ) ? $item['url'] : '';
							// Relative paths (/wp-content/…) are preserved as-is; absolute URLs are sanitised.
							$sanitized_url = str_starts_with( $url, '/' )
								? sanitize_text_field( $url )
								: esc_url_raw( $url );
							return array( 'url' => $sanitized_url );
						},
						$decoded
					);

					$encoded = wp_json_encode( $sanitized );

					return false !== $encoded ? $encoded : '[]';
				},
				'description'       => __( 'Product video assets — JSON array of { url } entries (YouTube, Vimeo, or local /wp-content path)', 'eternal-product-meta' ),
			)
		);
	}
}
