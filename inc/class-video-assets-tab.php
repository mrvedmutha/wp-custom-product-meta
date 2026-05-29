<?php
/**
 * WooCommerce product data tab for Video Assets.
 *
 * Adds a "Video Assets" tab to the WooCommerce product data metabox.
 * Supports an unlimited number of video URLs — YouTube, Vimeo, or local
 * /wp-content paths. Local videos are picked via the WordPress media
 * library; the host prefix is stripped on selection so only the relative
 * path is stored. Stored as a JSON array in product_video_assets.
 *
 * @package EternalProductMeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Eternal_Meta_Video_Assets_Tab
 *
 * Registers the Video Assets tab and panel inside the WooCommerce
 * product editor. Each entry is a single URL; type is detected
 * client-side (YouTube / Vimeo / Local).
 */
class Eternal_Meta_Video_Assets_Tab {

	/**
	 * Constructor.
	 *
	 * Hooks tab registration, panel rendering, and field saving into WooCommerce.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		add_action( 'wp_ajax_eternal_save_video_assets', array( $this, 'ajax_save' ) );
	}

	/**
	 * Enqueues the WordPress media library on product edit screens.
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
	 * Outputs the Video Assets tab navigation item.
	 *
	 * @return void
	 */
	public function add_tab(): void {
		echo '<li class="eternal_video_assets_options"><a href="#eternal_video_assets_data"><span>'
			. esc_html__( 'Video Assets', 'eternal-product-meta' )
			. '</span></a></li>';
	}

	/**
	 * Renders the Video Assets panel.
	 *
	 * @return void
	 */
	public function render_panel(): void {
		global $post;
		$id = (int) $post->ID;

		$videos = array();
		$raw    = get_post_meta( $id, 'product_video_assets', true );

		if ( $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$videos = $decoded;
			}
		}

		echo '<div id="eternal_video_assets_data" class="panel woocommerce_options_panel hidden">';
		echo '<div class="options_group">';
		echo '<div id="epm-videos-container">';

		foreach ( $videos as $video ) {
			$this->render_video_row( $video );
		}

		echo '</div>'; // #epm-videos-container

		echo '<p style="padding: 12px 12px 0;">';
		echo '<button type="button" id="epm-add-video" class="button">'
			. esc_html__( '+ Add Video', 'eternal-product-meta' )
			. '</button>';
		echo '</p>';
		echo '</div>'; // .options_group

		echo '<div style="padding:12px 12px 16px;border-top:1px solid #eee;margin-top:4px;">';
		echo '<button type="button" class="button button-primary epm-save-btn" '
			. 'data-action="eternal_save_video_assets" '
			. 'data-panel="eternal_video_assets_data" '
			. 'data-label="' . esc_attr__( 'Save Video Assets', 'eternal-product-meta' ) . '" '
			. 'data-nonce="' . esc_attr( wp_create_nonce( 'eternal_meta_save' ) ) . '" '
			. 'data-post-id="' . esc_attr( (string) $id ) . '">'
			. esc_html__( 'Save Video Assets', 'eternal-product-meta' )
			. '</button>'
			. '<span class="epm-save-msg" style="margin-left:10px;font-style:italic;display:none;"></span>';
		echo '</div>';

		echo '</div>'; // #eternal_video_assets_data

		// Hidden JS template — cloned by JS on "Add Video".
		echo '<script type="text/template" id="epm-video-template">';
		$this->render_video_row( array( 'url' => '' ) );
		echo '</script>';

		$this->render_scripts();
	}

	/**
	 * Outputs the HTML for a single video row.
	 *
	 * @param array $video Associative array with 'url' key.
	 * @return void
	 */
	private function render_video_row( array $video ): void {
		$url = esc_attr( $video['url'] ?? '' );
		?>
		<div class="epm-video-row" style="border:1px solid #ddd;margin:12px;padding:12px;border-radius:4px;">

			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
				<strong style="font-size:13px;">
					<?php esc_html_e( 'Video', 'eternal-product-meta' ); ?>
					<span class="epm-video-number"></span>
				</strong>
				<button type="button" class="button epm-remove-video" style="color:#d63638;border-color:#d63638;">
					<?php esc_html_e( 'Remove', 'eternal-product-meta' ); ?>
				</button>
			</div>

			<div>
				<p style="margin:0 0 6px;font-weight:600;font-size:13px;">
					<?php esc_html_e( 'Video URL', 'eternal-product-meta' ); ?>
				</p>
				<input
					type="text"
					name="product_video_url[]"
					class="epm-video-url"
					value="<?php echo esc_attr( $url ); ?>"
					placeholder="<?php esc_attr_e( 'Paste YouTube / Vimeo URL, or pick a local file below', 'eternal-product-meta' ); ?>"
					style="width:100%;box-sizing:border-box;"
				/>
				<p style="margin:6px 0 0;">
					<button type="button" class="button epm-pick-video">
						<?php esc_html_e( 'Pick Local Video', 'eternal-product-meta' ); ?>
					</button>
					<span class="epm-video-type-badge" style="display:none;margin-left:8px;padding:2px 10px;border-radius:3px;font-size:11px;font-weight:600;color:#fff;"></span>
				</p>
			</div>

		</div><!-- .epm-video-row -->
		<?php
	}

	/**
	 * Outputs the inline JavaScript for the repeater, media picker, and type badge.
	 *
	 * @return void
	 */
	private function render_scripts(): void {
		$home_url = esc_js( untrailingslashit( home_url() ) );
		?>
		<script type="text/javascript">
		(function ($) {
			'use strict';

			var HOME_URL = '<?php echo $home_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above via esc_js(). ?>';

			var TYPE_COLORS = {
				YouTube : '#cc0000',
				Vimeo   : '#1ab7ea',
				Local   : '#2e7d32',
			};

			function detectType( url ) {
				if ( ! url ) { return ''; }
				if ( /youtube\.com|youtu\.be/.test( url ) ) { return 'YouTube'; }
				if ( /vimeo\.com/.test( url ) )              { return 'Vimeo'; }
				if ( url.charAt( 0 ) === '/' )               { return 'Local'; }
				return '';
			}

			function updateBadge( $input ) {
				var url    = $input.val();
				var type   = detectType( url );
				var $badge = $input.closest( '.epm-video-row' ).find( '.epm-video-type-badge' );

				if ( type ) {
					$badge.text( type ).css( 'background-color', TYPE_COLORS[ type ] ).show();
				} else {
					$badge.hide().text( '' );
				}
			}

			function renumberRows() {
				$( '#epm-videos-container .epm-video-row' ).each( function ( i ) {
					$( this ).find( '.epm-video-number' ).text( ' ' + ( i + 1 ) );
				} );
			}

			// Initialise badges and numbers for pre-existing rows.
			$( '#epm-videos-container .epm-video-url' ).each( function () {
				updateBadge( $( this ) );
			} );
			renumberRows();

			// Add video row.
			$( '#epm-add-video' ).on( 'click', function () {
				var tpl = $( '#epm-video-template' ).html();
				$( '#epm-videos-container' ).append( tpl );
				renumberRows();
			} );

			// Remove video row.
			$( document ).on( 'click', '.epm-remove-video', function () {
				$( this ).closest( '.epm-video-row' ).remove();
				renumberRows();
			} );

			// Live type detection on manual URL input.
			$( document ).on( 'input', '.epm-video-url', function () {
				updateBadge( $( this ) );
			} );

			// Media library picker — local videos only.
			$( document ).on( 'click', '.epm-pick-video', function ( e ) {
				e.preventDefault();
				var $row = $( this ).closest( '.epm-video-row' );

				var frame = wp.media({
					title    : '<?php echo esc_js( __( 'Select Video', 'eternal-product-meta' ) ); ?>',
					button   : { text: '<?php echo esc_js( __( 'Use this video', 'eternal-product-meta' ) ); ?>' },
					library  : { type: 'video' },
					multiple : false,
				});

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					var fullUrl    = attachment.url;

					// Strip the home URL to store a portable relative path.
					var relUrl = ( HOME_URL && fullUrl.indexOf( HOME_URL ) === 0 )
						? fullUrl.substring( HOME_URL.length )
						: fullUrl;

					$row.find( '.epm-video-url' ).val( relUrl ).trigger( 'input' );
				} );

				frame.open();
			} );

		}(jQuery));
		</script>
		<?php
	}

	/**
	 * AJAX handler for the "Save Video Assets" button.
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
	 * Saves all video asset rows when a product is published or updated.
	 *
	 * @param int $post_id The product post ID.
	 * @return void
	 */
	public function save_fields( int $post_id ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook fires.

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below per URL type.
		$raw_urls = wp_unslash( $_POST['product_video_url'] ?? array() );

		$videos = array();

		foreach ( $raw_urls as $url ) {
			$url = (string) $url;
			if ( '' === $url ) {
				continue;
			}
			// Relative paths (/wp-content/…) are preserved; absolute URLs are sanitised.
			$sanitized = str_starts_with( $url, '/' )
				? sanitize_text_field( $url )
				: esc_url_raw( $url );

			$videos[] = array( 'url' => $sanitized );
		}

		$encoded = wp_json_encode( $videos );
		update_post_meta( $post_id, 'product_video_assets', false !== $encoded ? $encoded : '[]' );

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
