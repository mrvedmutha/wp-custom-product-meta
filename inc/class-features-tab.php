<?php
/**
 * WooCommerce product data tab for Product Features.
 *
 * Adds a dedicated "Product Features" tab to the WooCommerce product
 * data metabox with a dynamic repeater (add / remove rows) backed by
 * the WordPress media library for image selection.
 *
 * @package EternalProductMeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Eternal_Meta_Features_Tab
 */
class Eternal_Meta_Features_Tab {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		add_action( 'wp_ajax_eternal_save_product_features', array( $this, 'ajax_save' ) );
	}

	/**
	 * Ensure the WordPress media library scripts are available on product edit screens.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
	}

	/**
	 * Outputs the Product Features tab navigation item.
	 *
	 * @return void
	 */
	public function add_tab(): void {
		echo '<li class="eternal_features_options"><a href="#eternal_features_data"><span>'
			. esc_html__( 'Product Features', 'eternal-product-meta' )
			. '</span></a></li>';
	}

	/**
	 * Renders the Product Features panel.
	 *
	 * @return void
	 */
	public function render_panel(): void {
		global $post;
		$id = (int) $post->ID;

		$features = array();
		$raw      = get_post_meta( $id, 'product_features', true );

		if ( $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$features = $decoded;
			}
		}

		echo '<div id="eternal_features_data" class="panel woocommerce_options_panel hidden">';
		echo '<div class="options_group">';
		echo '<div id="epm-features-container">';

		foreach ( $features as $feature ) {
			$this->render_feature_row( $feature );
		}

		echo '</div>'; // #epm-features-container

		echo '<p style="padding: 12px 12px 0;">';
		echo '<button type="button" id="epm-add-feature" class="button">'
			. esc_html__( '+ Add Feature', 'eternal-product-meta' )
			. '</button>';
		echo '</p>';

		echo '</div>'; // .options_group
		// ── Save button ──────────────────────────────────────────────────────
		echo '<div style="padding:12px 12px 16px;border-top:1px solid #eee;margin-top:4px;">';
		echo '<button type="button" class="button button-primary epm-save-btn" '
			. 'data-action="eternal_save_product_features" '
			. 'data-panel="eternal_features_data" '
			. 'data-label="' . esc_attr__( 'Save Product Features', 'eternal-product-meta' ) . '" '
			. 'data-nonce="' . esc_attr( wp_create_nonce( 'eternal_meta_save' ) ) . '" '
			. 'data-post-id="' . esc_attr( (string) $id ) . '">'
			. esc_html__( 'Save Product Features', 'eternal-product-meta' )
			. '</button>'
			. '<span class="epm-save-msg" style="margin-left:10px;font-style:italic;display:none;"></span>';
		echo '</div>';

		echo '</div>'; // #eternal_features_data

		// Hidden JS template — rendered once, cloned by JS on "Add Feature".
		echo '<script type="text/template" id="epm-feature-template">';
		$this->render_feature_row(
			array(
				'image_id'  => 0,
				'image_url' => '',
				'heading'   => '',
				'body'      => '',
			)
		);
		echo '</script>';

		$this->render_scripts();
	}

	/**
	 * Outputs the HTML for a single feature row.
	 *
	 * @param array $feature Associative array with image_id, image_url, heading, body.
	 * @return void
	 */
	private function render_feature_row( array $feature ): void {
		$image_id  = (int) ( $feature['image_id'] ?? 0 );
		$image_url = esc_url( $feature['image_url'] ?? '' );
		$heading   = esc_attr( $feature['heading'] ?? '' );
		$body      = esc_textarea( $feature['body'] ?? '' );
		?>
		<div class="epm-feature-row" style="border:1px solid #ddd;margin:12px;padding:12px;border-radius:4px;">

			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
				<strong style="font-size:13px;">
					<?php esc_html_e( 'Feature', 'eternal-product-meta' ); ?>
					<span class="epm-feature-number"></span>
				</strong>
				<button type="button" class="button epm-remove-feature" style="color:#d63638;border-color:#d63638;">
					<?php esc_html_e( 'Remove', 'eternal-product-meta' ); ?>
				</button>
			</div>

			<!-- Image -->
			<div style="margin-bottom:12px;">
				<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
					<?php esc_html_e( 'Feature Image', 'eternal-product-meta' ); ?>
				</p>
				<img
					class="epm-image-preview"
					src="<?php echo esc_url( $image_url ); ?>"
					style="max-width:100%;height:150px;object-fit:cover;display:<?php echo esc_attr( $image_url ? 'block' : 'none' ); ?>;margin-bottom:6px;border-radius:3px;"
				/>
				<input type="hidden" class="epm-image-id"  name="product_feature_image_id[]"  value="<?php echo esc_attr( (string) $image_id ); ?>" />
				<input type="hidden" class="epm-image-url" name="product_feature_image_url[]" value="<?php echo esc_attr( $image_url ); ?>" />
				<button type="button" class="button epm-upload-image">
					<?php echo esc_html( $image_url ? __( 'Change Image', 'eternal-product-meta' ) : __( 'Select Image', 'eternal-product-meta' ) ); ?>
				</button>
				<button type="button" class="button epm-remove-image" style="color:#d63638;<?php echo esc_attr( $image_url ? '' : 'display:none;' ); ?>">
					<?php esc_html_e( 'Remove Image', 'eternal-product-meta' ); ?>
				</button>
			</div>

			<!-- Heading -->
			<div style="margin-bottom:12px;">
				<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
					<?php esc_html_e( 'Heading', 'eternal-product-meta' ); ?>
				</p>
				<input
					type="text"
					name="product_feature_heading[]"
					value="<?php echo esc_attr( $heading ); ?>"
					placeholder="<?php esc_attr_e( 'e.g. Sensual Hydration. Polished Skin.', 'eternal-product-meta' ); ?>"
					style="width:100%;"
				/>
			</div>

			<!-- Body -->
			<div>
				<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
					<?php esc_html_e( 'Body', 'eternal-product-meta' ); ?>
				</p>
				<textarea
					name="product_feature_body[]"
					rows="5"
					style="width:100%;"
					placeholder="<?php esc_attr_e( 'Write your feature description. Press Enter twice for a new paragraph.', 'eternal-product-meta' ); ?>"
				><?php echo esc_textarea( $body ); ?></textarea>
				<p class="description" style="margin-top:4px;">
					<?php esc_html_e( 'Press Enter twice between paragraphs — they will display as separate paragraphs on the site.', 'eternal-product-meta' ); ?>
				</p>
			</div>

		</div><!-- .epm-feature-row -->
		<?php
	}

	/**
	 * Outputs the inline JavaScript for the repeater and media picker.
	 *
	 * @return void
	 */
	private function render_scripts(): void {
		?>
		<script type="text/javascript">
		(function ($) {
			'use strict';

			// Keep feature numbers accurate.
			function renumberRows() {
				$('#epm-features-container .epm-feature-row').each(function (i) {
					$(this).find('.epm-feature-number').text(' ' + (i + 1));
				});
			}

			renumberRows();

			// Add feature row.
			$('#epm-add-feature').on('click', function () {
				var tpl = $('#epm-feature-template').html();
				$('#epm-features-container').append(tpl);
				renumberRows();
			});

			// Remove feature row.
			$(document).on('click', '.epm-remove-feature', function () {
				$(this).closest('.epm-feature-row').remove();
				renumberRows();
			});

			// Open media library picker.
			$(document).on('click', '.epm-upload-image', function (e) {
				e.preventDefault();
				var $row  = $(this).closest('.epm-feature-row');
				var frame = wp.media({
					title    : '<?php echo esc_js( __( 'Select Feature Image', 'eternal-product-meta' ) ); ?>',
					button   : { text: '<?php echo esc_js( __( 'Use this image', 'eternal-product-meta' ) ); ?>' },
					multiple : false,
				});
				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					$row.find('.epm-image-id').val(attachment.id);
					$row.find('.epm-image-url').val(attachment.url);
					$row.find('.epm-image-preview').attr('src', attachment.url).show();
					$row.find('.epm-upload-image').text('<?php echo esc_js( __( 'Change Image', 'eternal-product-meta' ) ); ?>');
					$row.find('.epm-remove-image').show();
				});
				frame.open();
			});

			// Remove image from row.
			$(document).on('click', '.epm-remove-image', function () {
				var $row = $(this).closest('.epm-feature-row');
				$row.find('.epm-image-id').val(0);
				$row.find('.epm-image-url').val('');
				$row.find('.epm-image-preview').attr('src', '').hide();
				$row.find('.epm-upload-image').text('<?php echo esc_js( __( 'Select Image', 'eternal-product-meta' ) ); ?>');
				$(this).hide();
			});

		}(jQuery));
		</script>
		<?php
	}

	/**
	 * Saves all product feature rows when the product is published or updated.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	/**
	 * AJAX handler for the "Save Product Features" button.
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
	 * Saves all product feature rows when the product is published or updated.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	public function save_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook fires.
		$image_ids  = array_map( 'absint', wp_unslash( $_POST['product_feature_image_id'] ?? array() ) );
		$image_urls = array_map( 'esc_url_raw', wp_unslash( $_POST['product_feature_image_url'] ?? array() ) );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below with sanitize_text_field.
		$raw_headings = wp_unslash( $_POST['product_feature_heading'] ?? array() );
		$headings     = array_map( 'sanitize_text_field', $raw_headings );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below with sanitize_textarea_field.
		$raw_bodies = wp_unslash( $_POST['product_feature_body'] ?? array() );
		$bodies     = array_map( 'sanitize_textarea_field', $raw_bodies );

		$features = array();
		$count    = count( $image_ids );

		for ( $i = 0; $i < $count; $i++ ) {
			$features[] = array(
				'image_id'  => $image_ids[ $i ] ?? 0,
				'image_url' => $image_urls[ $i ] ?? '',
				'heading'   => $headings[ $i ] ?? '',
				'body'      => $bodies[ $i ] ?? '',
			);
		}

		$encoded = wp_json_encode( $features );
		if ( false === $encoded ) {
			$encoded = '[]';
		}
		update_post_meta( $post_id, 'product_features', $encoded );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
