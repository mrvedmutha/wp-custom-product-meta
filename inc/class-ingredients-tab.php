<?php
/**
 * WooCommerce product data tab for Key Ingredients.
 *
 * Adds a dedicated "Key Ingredients" tab to the WooCommerce product
 * data metabox with exactly 3 fixed, collapsible ingredient entries.
 * Each entry has an image (media library), a name, and a description.
 *
 * @package EternalProductMeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Eternal_Meta_Ingredients_Tab
 */
class Eternal_Meta_Ingredients_Tab {

	/**
	 * Total number of ingredient slots — always fixed at 3.
	 */
	const INGREDIENT_COUNT = 3;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		add_action( 'wp_ajax_eternal_save_key_ingredients', array( $this, 'ajax_save' ) );
	}

	/**
	 * Outputs the Key Ingredients tab navigation item.
	 *
	 * @return void
	 */
	public function add_tab(): void {
		echo '<li class="eternal_ingredients_options"><a href="#eternal_ingredients_data"><span>'
			. esc_html__( 'Key Ingredients', 'eternal-product-meta' )
			. '</span></a></li>';
	}

	/**
	 * Renders the Key Ingredients panel with 3 fixed accordion entries.
	 *
	 * @return void
	 */
	public function render_panel(): void {
		global $post;
		$id = (int) $post->ID;

		$ingredients = array();
		$raw         = get_post_meta( $id, 'product_key_ingredients', true );

		if ( $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$ingredients = $decoded;
			}
		}

		// Always ensure exactly 3 slots.
		for ( $i = 0; $i < self::INGREDIENT_COUNT; $i++ ) {
			if ( ! isset( $ingredients[ $i ] ) ) {
				$ingredients[ $i ] = array(
					'image_id'    => 0,
					'image_url'   => '',
					'name'        => '',
					'description' => '',
				);
			}
		}

		echo '<div id="eternal_ingredients_data" class="panel woocommerce_options_panel hidden">';

		// ── Section Title ─────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Section Title', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_ingredients_title',
				'label'       => __( 'Title', 'eternal-product-meta' ),
				'placeholder' => 'Inside the Nourishing Science',
				'value'       => (string) get_post_meta( $id, 'product_ingredients_title', true ),
			)
		);

		echo '</div>';

		echo '<div class="options_group">';

		for ( $i = 0; $i < self::INGREDIENT_COUNT; $i++ ) {
			$this->render_ingredient_accordion( $i + 1, $ingredients[ $i ] );
		}

		// ── Save button ──────────────────────────────────────────────────────
		echo '<div style="padding:12px 12px 16px;">';
		echo '<button type="button" class="button button-primary epm-save-btn" '
			. 'data-action="eternal_save_key_ingredients" '
			. 'data-panel="eternal_ingredients_data" '
			. 'data-label="' . esc_attr__( 'Save Key Ingredients', 'eternal-product-meta' ) . '" '
			. 'data-nonce="' . esc_attr( wp_create_nonce( 'eternal_meta_save' ) ) . '" '
			. 'data-post-id="' . esc_attr( (string) $id ) . '">'
			. esc_html__( 'Save Key Ingredients', 'eternal-product-meta' )
			. '</button>'
			. '<span class="epm-save-msg" style="margin-left:10px;font-style:italic;display:none;"></span>';
		echo '</div>';

		echo '</div>';
		echo '</div>';

		$this->render_scripts();
	}

	/**
	 * Renders a single collapsible accordion for one ingredient.
	 *
	 * @param int   $number     1-based position number.
	 * @param array $ingredient Data array with image_id, image_url, name, description.
	 * @return void
	 */
	private function render_ingredient_accordion( int $number, array $ingredient ): void {
		$image_id    = (int) ( $ingredient['image_id'] ?? 0 );
		$image_url   = esc_url( $ingredient['image_url'] ?? '' );
		$name        = esc_attr( $ingredient['name'] ?? '' );
		$description = esc_textarea( $ingredient['description'] ?? '' );

		$accordion_id = 'epm-ingredient-body-' . (string) $number;
		?>
		<div class="epm-ingredient-accordion" style="border:1px solid #ddd;margin:12px;border-radius:4px;overflow:hidden;">

			<!-- Accordion header -->
			<div
				class="epm-accordion-header"
				data-target="<?php echo esc_attr( $accordion_id ); ?>"
				style="display:flex;justify-content:space-between;align-items:center;padding:12px;cursor:pointer;background:#fafafa;user-select:none;"
			>
				<strong style="font-size:13px;">
					<?php
					/* translators: %d: ingredient number */
					printf( esc_html__( 'Ingredient %d', 'eternal-product-meta' ), esc_html( (string) $number ) );
					?>
					<?php if ( $ingredient['name'] ) : ?>
						<span style="font-weight:400;color:#757575;margin-left:8px;">— <?php echo esc_html( $ingredient['name'] ); ?></span>
					<?php endif; ?>
				</strong>
				<span class="epm-accordion-arrow" style="font-size:10px;color:#757575;transition:transform 0.2s;">&#9660;</span>
			</div>

			<!-- Accordion body -->
			<div id="<?php echo esc_attr( $accordion_id ); ?>" class="epm-accordion-body" style="padding:12px;display:none;">

				<!-- Image -->
				<div style="margin-bottom:12px;">
					<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
						<?php esc_html_e( 'Ingredient Image', 'eternal-product-meta' ); ?>
					</p>
					<img
						class="epm-image-preview"
						src="<?php echo esc_url( $image_url ); ?>"
						style="max-width:100%;height:150px;object-fit:cover;display:<?php echo esc_attr( $image_url ? 'block' : 'none' ); ?>;margin-bottom:6px;border-radius:3px;"
					/>
					<input type="hidden" class="epm-image-id"  name="product_ingredient_image_id[]"  value="<?php echo esc_attr( (string) $image_id ); ?>" />
					<input type="hidden" class="epm-image-url" name="product_ingredient_image_url[]" value="<?php echo esc_attr( $image_url ); ?>" />
					<button type="button" class="button epm-upload-ingredient-image">
						<?php echo esc_html( $image_url ? __( 'Change Image', 'eternal-product-meta' ) : __( 'Select Image', 'eternal-product-meta' ) ); ?>
					</button>
					<button type="button" class="button epm-remove-ingredient-image" style="color:#d63638;<?php echo esc_attr( $image_url ? '' : 'display:none;' ); ?>">
						<?php esc_html_e( 'Remove Image', 'eternal-product-meta' ); ?>
					</button>
				</div>

				<!-- Name -->
				<div style="margin-bottom:12px;">
					<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
						<?php esc_html_e( 'Ingredient Name', 'eternal-product-meta' ); ?>
					</p>
					<input
						type="text"
						name="product_ingredient_name[]"
						value="<?php echo esc_attr( $name ); ?>"
						placeholder="<?php esc_attr_e( 'e.g. Amaranth Oil', 'eternal-product-meta' ); ?>"
						style="width:100%;"
						class="epm-ingredient-name-input"
					/>
				</div>

				<!-- Description -->
				<div>
					<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
						<?php esc_html_e( 'Description', 'eternal-product-meta' ); ?>
					</p>
					<textarea
						name="product_ingredient_description[]"
						rows="4"
						style="width:100%;"
						placeholder="<?php esc_attr_e( 'A short description of what this ingredient does for the skin.', 'eternal-product-meta' ); ?>"
					><?php echo esc_textarea( $description ); ?></textarea>
				</div>

			</div><!-- .epm-accordion-body -->

		</div><!-- .epm-ingredient-accordion -->
		<?php
	}

	/**
	 * Outputs the inline JavaScript for accordion toggle, media picker,
	 * and live header name preview.
	 *
	 * @return void
	 */
	private function render_scripts(): void {
		?>
		<script type="text/javascript">
		(function ($) {
			'use strict';

			// Accordion toggle.
			$(document).on('click', '.epm-accordion-header', function () {
				var targetId = $(this).data('target');
				var $body    = $('#' + targetId);
				var $arrow   = $(this).find('.epm-accordion-arrow');

				$body.slideToggle(150, function () {
					$arrow.css('transform', $body.is(':visible') ? 'rotate(180deg)' : 'rotate(0deg)');
				});
			});

			// Live-update the header preview as the user types the ingredient name.
			$(document).on('input', '.epm-ingredient-name-input', function () {
				var name    = $(this).val().trim();
				var $header = $(this).closest('.epm-ingredient-accordion').find('.epm-accordion-header strong');
				var $preview = $header.find('span');

				if ( name ) {
					if ( $preview.length ) {
						$preview.text('\u2014 ' + name);
					} else {
						$header.append('<span style="font-weight:400;color:#757575;margin-left:8px;">\u2014 ' + name + '</span>');
					}
				} else {
					$preview.remove();
				}
			});

			// Open media library picker.
			$(document).on('click', '.epm-upload-ingredient-image', function (e) {
				e.preventDefault();
				var $row  = $(this).closest('.epm-accordion-body');
				var frame = wp.media({
					title    : '<?php echo esc_js( __( 'Select Ingredient Image', 'eternal-product-meta' ) ); ?>',
					button   : { text: '<?php echo esc_js( __( 'Use this image', 'eternal-product-meta' ) ); ?>' },
					multiple : false,
				});
				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					$row.find('.epm-image-id').val(attachment.id);
					$row.find('.epm-image-url').val(attachment.url);
					$row.find('.epm-image-preview').attr('src', attachment.url).show();
					$row.find('.epm-upload-ingredient-image').text('<?php echo esc_js( __( 'Change Image', 'eternal-product-meta' ) ); ?>');
					$row.find('.epm-remove-ingredient-image').show();
				});
				frame.open();
			});

			// Remove image.
			$(document).on('click', '.epm-remove-ingredient-image', function () {
				var $row = $(this).closest('.epm-accordion-body');
				$row.find('.epm-image-id').val(0);
				$row.find('.epm-image-url').val('');
				$row.find('.epm-image-preview').attr('src', '').hide();
				$row.find('.epm-upload-ingredient-image').text('<?php echo esc_js( __( 'Select Image', 'eternal-product-meta' ) ); ?>');
				$(this).hide();
			});

		}(jQuery));
		</script>
		<?php
	}

	/**
	 * Saves the 3 ingredient entries when the product is published or updated.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	/**
	 * AJAX handler for the "Save Key Ingredients" button.
	 *
	 * @return void
	 */
	public function ajax_save(): void {
		check_ajax_referer( 'eternal_meta_save', 'nonce' );

		$post_id = absint( wp_unslash( $_POST['post_id'] ?? 0 ) );
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'eternal-product-meta' ) ) );
		}

		$this->save_fields( $post_id );

		wp_send_json_success( array( 'message' => __( 'Saved.', 'eternal-product-meta' ) ) );
	}

	/**
	 * Saves the 3 ingredient entries when the product is published or updated.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	public function save_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook fires.
		update_post_meta(
			$post_id,
			'product_ingredients_title',
			sanitize_text_field( wp_unslash( $_POST['product_ingredients_title'] ?? '' ) )
		);

		$image_ids  = array_map( 'absint', wp_unslash( $_POST['product_ingredient_image_id'] ?? array() ) );
		$image_urls = array_map( 'esc_url_raw', wp_unslash( $_POST['product_ingredient_image_url'] ?? array() ) );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below with sanitize_text_field.
		$raw_names = wp_unslash( $_POST['product_ingredient_name'] ?? array() );
		$names     = array_map( 'sanitize_text_field', $raw_names );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below with sanitize_textarea_field.
		$raw_descriptions = wp_unslash( $_POST['product_ingredient_description'] ?? array() );
		$descriptions     = array_map( 'sanitize_textarea_field', $raw_descriptions );

		$ingredients = array();

		for ( $i = 0; $i < self::INGREDIENT_COUNT; $i++ ) {
			$ingredients[] = array(
				'image_id'    => $image_ids[ $i ] ?? 0,
				'image_url'   => $image_urls[ $i ] ?? '',
				'name'        => $names[ $i ] ?? '',
				'description' => $descriptions[ $i ] ?? '',
			);
		}

		$encoded = wp_json_encode( $ingredients );
		if ( false === $encoded ) {
			$encoded = '[]';
		}
		update_post_meta( $post_id, 'product_key_ingredients', $encoded );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
