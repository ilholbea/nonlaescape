<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/public
 *
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Public facing controller for ShutterPress Gallery.
 *
 * Enqueues front end scripts and styles, registers and renders the gallery
 * shortcode, filters the content on single gallery posts, passes data to
 * JavaScript, disables post navigation on gallery pages, and syncs liked
 * images from the cookie to user meta for logged in users.
 *
 * @since 1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/public
 */
class Shutterpress_Gallery_Public {

	private string $plugin_name;
	private string $version;
	private Shutterpress_Gallery_Gallery $gallery;
	private Shutterpress_Gallery_Utils $utils;
	protected Shutterpress_Gallery_Debug $debug;
	public array $renderers = array();

	/**
	 * Class constructor.
	 *
	 * Stores the plugin name and version, sets up utilities and debug logging,
	 * and prepares a gallery renderer instance for front end use.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name Unique slug for the plugin.
	 * @param string $version     Current plugin version.
	 *
	 * @return void
	 */
	public function __construct( ?string $plugin_name = null, ?string $version = null ) {
		$this->plugin_name = $plugin_name ? $plugin_name : SP_GALLERY_NAME;
		$this->version     = $version ? $version : SP_GALLERY_VERSION;
		$this->utils       = new Shutterpress_Gallery_Utils();
		$this->debug       = new Shutterpress_Gallery_Debug();
		$this->gallery     = new Shutterpress_Gallery_Gallery();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_enqueue_styles() {
		wp_enqueue_style( 'lightgallery', SP_GALLERY_URL . 'includes/css/lightgallery-bundle.min.css', array(), '2.7.1' );
		wp_enqueue_style( $this->plugin_name . '-public', SP_GALLERY_URL . 'includes/css/shutterpress-gallery-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_enqueue_scripts() {

		if ( $this->utils->sp_gallery_is_elementor_in_edit_mode() ) {
			return;
		}
		wp_enqueue_script(
			$this->plugin_name . '-public',
			SP_GALLERY_URL . 'includes/js/shutterpress-gallery-public.js',
			array( 'jquery', 'js-cookie', 'lightgallery', 'imagesloaded', 'infinite-scroll', 'isotope', 'shutterpress-gallery-isotope', 'wp-i18n' ),
			$this->version,
			true
		);

		wp_set_script_translations( $this->plugin_name . '-public', 'shutterpress-gallery', SP_GALLERY_DIR . 'languages/' );

		wp_enqueue_script( 'js-cookie', SP_GALLERY_URL . 'includes/js/js.cookie.min.js', array(), '3.0.1', true );
		wp_enqueue_script( 'lightgallery', SP_GALLERY_URL . 'includes/js/lightgallery.bundle.js', array( 'jquery' ), '2.7.1', true );
		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'infinite-scroll', SP_GALLERY_URL . 'includes/js/infinite-scroll.pkgd.min.js', array( 'jquery' ), '4.0.1', true );
		wp_enqueue_script( 'isotope', SP_GALLERY_URL . 'includes/js/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true );
		wp_enqueue_script( 'shutterpress-gallery-isotope', SP_GALLERY_URL . 'src/public/js/shutterpress-gallery-isotope.js', array( 'isotope', 'jquery' ), $this->version, true );
	}

	/**
	 * Displays the gallery on the public-facing side of the site.
	 *
	 * This function checks if the current page is a single gallery post (`shutterpress-gallery` post type).
	 * If so, it creates an instance of `Shutterpress_Gallery_Public_Render`, sets the necessary attributes,
	 * and returns the rendered gallery content. Otherwise, it returns the original content unchanged.
	 *
	 * Steps:
	 * 1. Check if the current page is a singular `shutterpress-gallery` post.
	 * 2. If true, instantiate `Shutterpress_Gallery_Public_Render`.
	 * 3. Set render attributes using `sp_gallery_set_render_attributes()`.
	 * 4. Retrieve the gallery content using `sp_gallery_get_the_gallery()`.
	 * 5. Return the rendered gallery content; otherwise, return the original content.
	 *
	 * Expected Outcome:
	 * - If the page is a gallery post, it returns the gallery's rendered content.
	 * - If the page is not a gallery post, it returns the unmodified content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The original content of the page.
	 *
	 * @return string The modified content with the gallery rendered, or the original content if not a gallery.
	 */
	public function sp_gallery_display_gallery( $content ) {
		if ( ! is_singular( 'shutterpress-gallery' ) ) {
			return $content;
		}

		if ( $this->utils->third_party_plugin_content_fixes() ) {
			return $content;
		}

		$gallery_id = get_the_ID();
		if ( false === $gallery_id ) {
			return $content;
		}

		$gallery_id = (int) $gallery_id;
		$gallery    = $this->renderers[ $gallery_id ] ?? new Shutterpress_Gallery_Gallery( $gallery_id );

		if ( ! empty( $gallery->get_gallery_images() ) ) {
			$output = '';

			$gallery->set_attributes();

			$parts = array( $gallery->get_the_gallery(), $gallery->get_infinite_scroll_loader(), $gallery->get_gallery_pagination() );

			foreach ( $parts as $part ) {
				if ( is_wp_error( $part ) ) {
					$this->debug->log( 'Gallery part error', array( 'error' => $part->get_error_message() ), 'ERROR' );
					continue;
				}
				$output .= $part;
			}

			return $output;
		}
		return $content;
	}

	/**
	 * Registers the 'sp_gallery' shortcode.
	 *
	 * This method registers the 'sp_gallery' shortcode, which allows users to embed a Shutterpress Gallery on their WordPress site. The shortcode is handled by the `sp_gallery_render_gallery_shortcode()` method.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_register_gallery_shortcode() {
		add_shortcode( 'sp_gallery', array( $this, 'sp_gallery_render_gallery_shortcode' ) );
	}

	/**
	 * Determines if a given value is considered truthy for shortcode style inputs.
	 *
	 * Rules:
	 *  - null or empty string, treated as true, convenient defaulting for attributes
	 *  - Booleans, returned as given
	 *  - Numeric strings or ints, 0 is false, non zero is true
	 *  - Strings, case insensitive: 'false','no','off','0','n' are false, everything else is true
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool
	 */
	public function sp_gallery_is_truthy( $value ) {

		if ( $value === null || $value === '' ) {
			return true;
		}

		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && is_numeric( $value ) ) ) {
			return intval( $value ) !== 0;
		}

		if ( is_string( $value ) ) {
			$v      = strtolower( trim( $value ) );
			$falsey = array( 'false', 'no', 'off', '0', 'n' );
			return ! in_array( $v, $falsey, true );
		}

		return true;
	}

	/**
	 * Renders the Shutterpress Gallery shortcode.
	 *
	 * This method is responsible for rendering the Shutterpress Gallery based on the
	 * provided attributes. It sets up the necessary rendering attributes, and then
	 * calls the `sp_gallery_get_the_gallery()` method to generate the gallery HTML.
	 *
	 * @param array              $attributes    An associative array of shortcode attributes.
	 * @param string|object|null $content      Shortcode content when used with enclosing form, unused.
	 *                                         Tests may pass a renderer object here for injection.
	 * @param string             $shortcode_tag The shortcode tag name supplied by WordPress.
	 *
	 * @return string The rendered gallery HTML.
	 */
	public function sp_gallery_render_gallery_shortcode( array $attributes, $content = null, string $shortcode_tag = '' ) {
		$renderer = null;

		if ( is_object( $content ) ) {
			$renderer = $content;
			$content  = null;
		}

		$this->debug->log( 'Rendering gallery shortcode', $attributes );

		$use_lightbox = isset( $attributes['use_lightbox'] ) ? $this->sp_gallery_is_truthy( $attributes['use_lightbox'] ) : null;

		$show_buttons = isset( $attributes['show_buttons'] ) ? $this->sp_gallery_is_truthy( $attributes['show_buttons'] ) : null;

		$gallery_attributes = array(
			'gallery_id'   => isset( $attributes['id'] ) ? intval( $attributes['id'] ) : null,
			'use_lightbox' => $use_lightbox,
			'layout'       => isset( $attributes['layout'] ) ? $attributes['layout'] : null,
			'gap'          => isset( $attributes['gap'] ) ? intval( $attributes['gap'] ) : null,
			'row_height'   => isset( $attributes['row_height'] ) ? intval( $attributes['row_height'] ) : null,
			'show_buttons' => $show_buttons,
			'gallery_type' => 'shortcode',
		);

		if ( empty( $gallery_attributes['gallery_id'] ) ) {
			$current_id = get_the_ID();
			if ( $current_id && is_numeric( $current_id ) ) {
				$gallery_attributes['gallery_id'] = (int) $current_id;
			}
		}

		$gallery_id = ! empty( $gallery_attributes['gallery_id'] ) ? (int) $gallery_attributes['gallery_id'] : null;
		$this->debug->log( 'Resolved gallery ID', array( 'gallery_id' => $gallery_id ) );

		if ( ! $renderer ) {
			$cache_key = $gallery_id ?? 0;
			if ( ! isset( $this->renderers[ $cache_key ] ) ) {
				$this->renderers[ $cache_key ] = new Shutterpress_Gallery_Gallery( $gallery_id );
			}
			$renderer = $this->renderers[ $cache_key ];
		}
		$output = '';
		if ( ! empty( $renderer->get_gallery_images() ) ) {
			$renderer->set_attributes( $gallery_attributes );

			$output .= $renderer->get_the_gallery();
			$output .= $renderer->get_infinite_scroll_loader();
			$output .= $renderer->get_gallery_pagination();
		}

		$filtered_content = apply_filters( 'sp_gallery_content', null, $output, $gallery_id );

		if ( ! empty( $filtered_content ) ) {
			$this->debug->log( 'Shortcode rendering overridden by filter', array( 'gallery_id' => $gallery_id ) );
			return $this->utils->sanitize_form_html( $filtered_content );
		}

		return $output;
	}

	/**
	 * Localise data to send to javascript
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function sp_gallery_localize_frontend_data() {
		$virtualiser_enabled      = get_option( 'sp_gallery_enable_virtualiser', 1 );
		$virtualiser_low_conflict = get_option( 'sp_gallery_virtualiser_low_conflict_mode', 0 );
		$localize_data            = array(
			'rest_url'                      => esc_url_raw( rest_url() ),
			'nonce'                         => wp_create_nonce( 'wp_rest' ),
			'virtualiser_enabled'           => ! empty( $virtualiser_enabled ),
			'virtualiser_low_conflict_mode' => ! empty( $virtualiser_low_conflict ),
		);
		$debug_enabled            = get_option( 'sp_gallery_debug', false );
		$debug_enabled            = is_string( $debug_enabled ) ? in_array( strtolower( trim( $debug_enabled ) ), array( '1', 'true', 'yes', 'on' ), true ) : ! empty( $debug_enabled );
		if ( $debug_enabled ) {
			$localize_data['debug'] = true;
		}

		$localize_data = apply_filters( 'sp_gallery_localize_data', $localize_data );

		$this->debug->log( 'Localizing Data', array( $localize_data ) );

		wp_localize_script( $this->plugin_name . '-public', 'shutterpressData', $localize_data );
	}

	/**
	 * Disable post navigation on gallery pages
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_disable_post_navigation_on_gallery_pages(): void {
		add_filter(
			'navigation_markup_template',
			function ( $template, $template_class ) {

				if ( is_singular( 'shutterpress-gallery' ) && strpos( (string) $template_class, 'post-navigation' ) !== false ) {
					return '';
				}
				return $template;
			},
			10,
			2
		);
	}

	/**
	 * Synchronizes the user's liked images between the cookie and the user meta data.
	 *
	 * This function checks if the user is logged in and if there is a 'liked_images' cookie. If the cookie exists, it compares the liked images in the cookie with the liked images stored in the user's meta data. If they differ, it merges the new liked images from the cookie and removes the unliked images, and updates the user's meta data with the synced liked images.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function sp_gallery_sync_liked_images_before_page_load() {
		$this->debug->log( 'Syncing liked images from cookie', array( 'user_id' => get_current_user_id() ) );

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			if ( isset( $_COOKIE['liked_images'] ) ) {
				$cookie_raw = isset( $_COOKIE['liked_images'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['liked_images'] ) ) : '[]';

				$decoded = json_decode( $cookie_raw, true );
				if ( $decoded === null && json_last_error() !== JSON_ERROR_NONE ) {
					$this->debug->log( 'Invalid liked_images cookie JSON, skipping sync', array( 'raw' => $cookie_raw ), 'WARNING' );
					return false;
				}
				$liked_images_from_cookie = is_array( $decoded ) ? array_map( 'intval', $decoded ) : array();

				$stored_liked_images = $this->gallery->get_liked_images( $user_id );

				if ( ! is_array( $stored_liked_images ) ) {
					$stored_liked_images = array();
				}

				if ( $liked_images_from_cookie !== $stored_liked_images ) {
					$unliked_images     = array_diff( $stored_liked_images, $liked_images_from_cookie );
					$newly_liked_images = array_diff( $liked_images_from_cookie, $stored_liked_images );

					$updated_liked_images = array_unique( array_merge( array_diff( $stored_liked_images, $unliked_images ), $newly_liked_images ) );

					update_user_meta( $user_id, 'sp_gallery_liked_images', $updated_liked_images );
					$this->debug->log(
						'Liked image data mismatch – syncing',
						array(
							'cookie' => $liked_images_from_cookie,
							'stored' => $stored_liked_images,
						)
					);
				}
			}
		}
		return true;
	}

	/**
	 * Convert a hex color to rgba format.
	 *
	 * @param string $hex   Hex color code (e.g., "#b0acabff" or "ff5733").
	 * @param float  $alpha Alpha (opacity) value between 0 and 1.
	 *
	 * @return string RGBA color string (e.g., "rgba(255, 87, 51, 0.5)").
	 * @phpstan-ignore method.unused
	 */
	private function sp_gallery_hex_to_rgba( $hex, $alpha = 1 ) {

		$hex = ltrim( trim( (string) $hex ), '#' );

		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );

		$len = strlen( $hex );
		if ( $len === 0 ) {
			return 'rgba(0, 0, 0, 1)';
		}

		if ( $len === 3 ) {
			$hex = str_repeat( $hex[0], 2 ) . str_repeat( $hex[1], 2 ) . str_repeat( $hex[2], 2 );
			$len = 6;
		}

		if ( $len !== 6 && $len !== 8 ) {
			return 'rgba(0, 0, 0, 1)';
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		$embedded_alpha = null;
		if ( $len === 8 ) {
			$embedded_alpha = hexdec( substr( $hex, 6, 2 ) ) / 255;
		}

		$a = is_numeric( $alpha ) ? (float) $alpha : 1.0;
		if ( $embedded_alpha !== null && (float) $alpha === 1.0 ) {
			$a = $embedded_alpha;
		}

		$a = max( 0.0, min( 1.0, $a ) );

		return sprintf( 'rgba(%d, %d, %d, %.3g)', $r, $g, $b, $a );
	}
}
