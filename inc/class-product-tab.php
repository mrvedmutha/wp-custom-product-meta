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
		add_action( 'wp_ajax_eternal_save_product_details', array( $this, 'ajax_save' ) );
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

		// ── Buy Box ───────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Contains', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_buy_box_amount',
				'label'       => __( 'Amount', 'eternal-product-meta' ),
				'placeholder' => '100',
				'type'        => 'number',
				'value'       => (string) get_post_meta( $id, 'product_buy_box_amount', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_buy_box_unit',
				'label'       => __( 'Unit', 'eternal-product-meta' ),
				'placeholder' => 'ml, capsules, g…',
				'value'       => (string) get_post_meta( $id, 'product_buy_box_unit', true ),
			)
		);

		echo '</div>';

		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Ingredients &amp; Safety', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_textarea_input(
			array(
				'id'    => 'product_buy_box_ingredients',
				'label' => __( 'Ingredients', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_buy_box_ingredients', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'    => 'product_buy_box_caution',
				'label' => __( 'Cautionary Text', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_buy_box_caution', true ),
			)
		);

		echo '</div>';

		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'How to Apply', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_textarea_input(
			array(
				'id'    => 'product_buy_box_how_to_apply',
				'label' => __( 'How to Use', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_buy_box_how_to_apply', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_buy_box_bonus_tip',
				'label'       => __( 'Bonus Tip', 'eternal-product-meta' ),
				'placeholder' => 'FOR AN AROMATIC EXPERIENCE: Start your body routine…',
				'value'       => (string) get_post_meta( $id, 'product_buy_box_bonus_tip', true ),
			)
		);

		echo '</div>';

		// ── Benefits ─────────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Benefits', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_benefits_text',
				'label'       => __( 'Benefits Text', 'eternal-product-meta' ),
				'description' => __( 'For paragraph-style benefit content. Leave blank if using bullets below.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_benefits_text', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => 'product_benefits_bullets',
				'label'       => __( 'Benefits Bullets', 'eternal-product-meta' ),
				'description' => __( 'One benefit per line — each line becomes a bullet point. Leave blank if using text above.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_benefits_bullets', true ),
			)
		);

		echo '</div>';

		// ── Product Caption ──────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Product Caption', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_caption',
				'label' => __( 'Caption', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_caption', true ),
			)
		);

		echo '</div>';

		// ── French Text ──────────────────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'French Text', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'    => 'product_french_text',
				'label' => __( 'French Text', 'eternal-product-meta' ),
				'value' => (string) get_post_meta( $id, 'product_french_text', true ),
			)
		);

		echo '</div>';

		// ── Additional Product Tags ───────────────────────────────────────────
		echo '<div class="options_group">';
		echo '<p class="epm-section-heading"><strong>' . esc_html__( 'Additional Product Tags', 'eternal-product-meta' ) . '</strong></p>';

		woocommerce_wp_text_input(
			array(
				'id'          => 'product_additional_tags',
				'label'       => __( 'Tags', 'eternal-product-meta' ),
				'placeholder' => 'Fragrance Free, Vegan, SPF 30…',
				'description' => __( 'Comma-separated. Shown as pill tags on the product card alongside the amount + unit.', 'eternal-product-meta' ),
				'desc_tip'    => true,
				'value'       => (string) get_post_meta( $id, 'product_additional_tags', true ),
			)
		);

		echo '</div>';

		// ── Save button ──────────────────────────────────────────────────────
		echo '<div style="padding:12px 12px 16px;">';
		echo '<button type="button" class="button button-primary epm-save-btn" '
			. 'data-action="eternal_save_product_details" '
			. 'data-panel="eternal_meta_data" '
			. 'data-label="' . esc_attr__( 'Save Product Details', 'eternal-product-meta' ) . '" '
			. 'data-nonce="' . esc_attr( wp_create_nonce( 'eternal_meta_save' ) ) . '" '
			. 'data-post-id="' . esc_attr( (string) $id ) . '">'
			. esc_html__( 'Save Product Details', 'eternal-product-meta' )
			. '</button>'
			. '<span class="epm-save-msg" style="margin-left:10px;font-style:italic;display:none;"></span>';
		echo '</div>';

		echo '</div>'; // #eternal_meta_data

		$this->render_scripts();
	}

	/**
	 * Outputs the shared save-button JavaScript.
	 * Uses a guard so it only initialises once even if multiple tabs render it.
	 *
	 * @return void
	 */
	private function render_scripts(): void {
		?>
		<script type="text/javascript">
		(function ($) {
			'use strict';

			if ( window.epmSaveInitialized ) {
				return;
			}
			window.epmSaveInitialized = true;

			$(document).on('click', '.epm-save-btn', function () {
				var $btn    = $(this);
				var $msg    = $btn.siblings('.epm-save-msg');
				var panelId = $btn.data('panel');
				var label   = $btn.data('label');
				var extra   = $.param({
					action  : $btn.data('action'),
					nonce   : $btn.data('nonce'),
					post_id : $btn.data('post-id'),
				});
				var fields  = $('#' + panelId).find(':input').serialize();

				$btn.prop('disabled', true).text('<?php echo esc_js( __( 'Saving…', 'eternal-product-meta' ) ); ?>');
				$msg.hide();

				$.post(ajaxurl, fields + '&' + extra, function (response) {
					$btn.prop('disabled', false).text(label);
					if ( response.success ) {
						$msg.text('<?php echo esc_js( __( '✓ Saved', 'eternal-product-meta' ) ); ?>').css('color', '#46b450').show();
						setTimeout(function () { $msg.fadeOut(); }, 3000);
					} else {
						$msg.text('✗ ' + ( response.data.message || '<?php echo esc_js( __( 'Error saving', 'eternal-product-meta' ) ); ?>' )).css('color', '#d63638').show();
					}
				}).fail(function () {
					$btn.prop('disabled', false).text(label);
					$msg.text('<?php echo esc_js( __( '✗ Connection error', 'eternal-product-meta' ) ); ?>').css('color', '#d63638').show();
				});
			});

		}(jQuery));
		</script>
		<?php
	}

	/**
	 * Saves all product detail fields when a product is published or updated.
	 *
	 * WooCommerce verifies the product nonce before firing this hook.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	/**
	 * AJAX handler for the "Save Product Details" button.
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
	 * Saves all product detail fields when a product is published or updated.
	 *
	 * WooCommerce verifies the product nonce before firing this hook.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	public function save_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook fires.

		// Buy Box + card tags — text fields.
		$text_fields = array(
			'product_buy_box_amount',
			'product_buy_box_unit',
			'product_additional_tags',
			'product_french_text',
			'product_caption',
		);

		foreach ( $text_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				sanitize_text_field( wp_unslash( $_POST[ $field ] ?? '' ) )
			);
		}

		// Buy Box + benefits — textarea fields.
		$textarea_fields = array(
			'product_buy_box_ingredients',
			'product_buy_box_caution',
			'product_buy_box_how_to_apply',
			'product_buy_box_bonus_tip',
			'product_benefits_text',
			'product_benefits_bullets',
		);

		foreach ( $textarea_fields as $field ) {
			update_post_meta(
				$post_id,
				$field,
				sanitize_textarea_field( wp_unslash( $_POST[ $field ] ?? '' ) )
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
