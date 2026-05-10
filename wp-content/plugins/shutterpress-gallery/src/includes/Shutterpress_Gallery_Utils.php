<?php
/**
 * Shutterpress Gallery Utility Helper Class
 *
 * @since      1.3.4
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 *
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

use WP_Error;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Utility helpers for ShutterPress Gallery.
 *
 * Provides sanitisation helpers, colour conversion, palette discovery from
 * WordPress and Elementor, editor context detection, token generation and
 * verification for secure downloads, and shared KSES allowlists for HTML and SVG.
 *
 * @since 1.3.4
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_Utils {

	/**
	 * Sanitises HTML for the password protected gallery login form.
	 *
	 * Allows only tags and attributes from the internal allowlist.
	 *
	 * @since 1.3.4
	 *
	 * @param string $html Raw HTML content to sanitise.
	 * @return string Sanitised HTML safe for output.
	 */

	/**
	 * Allowed SVG elements and attributes for `wp_kses` sanitisation.
	 *
	 * @var array
	 */
	public $allowed_svg = array(
		'svg'      => array(
			'xmlns'             => true,
			'xmlns:xlink'       => true,
			'width'             => true,
			'height'            => true,
			'viewbox'           => true, 
			'viewBox'           => true,
			'fill'              => true,
			'stroke'            => true,
			'stroke-width'      => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'role'              => true,
			'aria-hidden'       => true,
		),
		'g'        => array(
			'transform' => true,
			'fill'      => true,
			'stroke'    => true,
			'class'     => true,
			'id'        => true,
			'opacity'   => true,
		),
		'path'     => array(
			'd'                 => true,
			'fill'              => true,
			'fill-rule'         => true,
			'clip-rule'         => true,
			'stroke'            => true,
			'stroke-width'      => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'opacity'           => true,
			'transform'         => true,
			'class'             => true,
			'id'                => true,
		),
		'circle'   => array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
			'class'        => true,
			'id'           => true,
		),
		'rect'     => array(
			'x'            => true,
			'y'            => true,
			'width'        => true,
			'height'       => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
			'class'        => true,
			'id'           => true,
			'transform'    => true,
		),
		'ellipse'  => array(
			'cx'           => true,
			'cy'           => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
		),
		'polygon'  => array(
			'points'       => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
		),
		'polyline' => array(
			'points'       => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
		),
		'line'     => array(
			'x1'           => true,
			'y1'           => true,
			'x2'           => true,
			'y2'           => true,
			'stroke'       => true,
			'stroke-width' => true,
			'opacity'      => true,
		),
		'defs'     => array(),
		'clipPath' => array( 'id' => true ),
		'mask'     => array(
			'id'        => true,
			'x'         => true,
			'y'         => true,
			'width'     => true,
			'height'    => true,
			'maskUnits' => true,
		),
		'use'      => array(
			'href'       => true,
			'xlink:href' => true,
			'x'          => true,
			'y'          => true,
			'width'      => true,
			'height'     => true,
			'transform'  => true,
		),
		'title'    => array(),
		'desc'     => array(),
	);

	/**
	 * Allowed HTML elements and attributes for `wp_kses` sanitisation.
	 *
	 * @var array
	 */
	public $allowed_html = array(
		'div'   => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(
				'display' => array(),
			),
		),
		'p'     => array( 'class' => array() ),
		'h1'    => array( 'class' => array() ),
		'h2'    => array( 'class' => array() ),
		'h3'    => array( 'class' => array() ),
		'h4'    => array( 'class' => array() ),
		'h5'    => array( 'class' => array() ),
		'h6'    => array( 'class' => array() ),
		'form'  => array(
			'id'     => array(),
			'name'   => array(),
			'action' => array(),
			'method' => array(),
			'class'  => array(),
		),
		'label' => array( 'for' => array() ),
		'input' => array(
			'type'         => array(),
			'name'         => array(),
			'id'           => array(),
			'class'        => array(),
			'value'        => array(),
			'size'         => array(),
			'autocomplete' => array(),
			'spellcheck'   => array(),
			'checked'      => array(),
		),
		'a'     => array(
			'href'  => array(),
			'title' => array(),
			'class' => array(),
		),
	);

	protected Shutterpress_Gallery_Debug $debug;

	/**
	 * Class constructor.
	 *
	 * Initialises the ShutterPress Gallery utilities helper and sets up a
	 * debug instance for internal logging.
	 *
	 * @since 1.7.7
	 */
	public function __construct() {
		$this->debug = new Shutterpress_Gallery_Debug();
	}

	/**
	 * Sanitises the HTML for the password protected gallery login form.
	 *
	 * Allows only elements and attributes from the internal allowlist to prevent XSS.
	 *
	 * @since 1.3.4
	 *
	 * @param string $html Raw HTML content to sanitise.
	 * @return string Sanitised HTML safe for output.
	 */
	public function sanitize_form_html( $html ) {
		return wp_kses( $html, $this->allowed_html );
	}

	/**
	 * Generates a secure, time-limited download token for gallery files.
	 *
	 * The token can be used to authorise and validate file downloads,
	 * ensuring that only legitimate requests can access protected files.
	 * It typically includes a hash derived from the gallery ID, timestamp,
	 * and a secret key for verification.
	 *
	 * @since 1.3.4
	 *
	 * @param int $image_id The ID of the image to generate a token for.
	 * @param int $gallery_id The ID of the gallery to generate a token for.
	 * @param int $expires_in Optional. Token lifetime in seconds. Default 3600.
	 *
	 * @return string The generated secure download token.
	 */
	public function generate_download_token( $image_id, $gallery_id, $expires_in = 300 ) {
		$expires = time() + $expires_in;
		$data    = "{$image_id}:{$gallery_id}:{$expires}";
		
		$secret    = AUTH_KEY; 
		$signature = hash_hmac( 'sha256', $data, $secret );
		return base64_encode( "{$data}:{$signature}" ); 
	}

	/**
	 * Verifies a secure download token for gallery image downloads.
	 *
	 * Decodes and validates the download token, checking the signature and expiry time.
	 * Returns an array with 'image_id' and 'gallery_id' if valid, or false if invalid or expired.
	 *
	 * @since 1.6.0
	 *
	 * @param string $token The encoded download token to verify.
	 *
	 * @return array|false Array with 'image_id' and 'gallery_id' if valid, or false if invalid or expired.
	 */
	public function verify_download_token( $token ) {
		
		$secret = AUTH_KEY;
		$parts  = explode( ':', base64_decode( $token ) ); 
		if ( count( $parts ) !== 4 ) {
			return false;
		}

		[$image_id, $gallery_id, $expires, $signature] = $parts;

		if ( time() > intval( $expires ) ) {
			return false;
		}

		$data     = "{$image_id}:{$gallery_id}:{$expires}";
		$expected = hash_hmac( 'sha256', $data, $secret );

		if ( ! hash_equals( $expected, $signature ) ) {
			return false;
		}

		return array(
			'image_id'   => (int) $image_id,
			'gallery_id' => (int) $gallery_id,
		);
	}

	/**
	 * Detect if the current request is inside Elementor edit or preview mode.
	 *
	 * Checks query string parameters, AJAX actions, admin screen context,
	 * and Elementor runtime state to determine whether the plugin is running
	 * in an Elementor editing environment. Used to prevent conflicts with
	 * Gutenberg and to conditionally enqueue assets only when needed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if Elementor is in edit or preview mode, false otherwise.
	 */
	public function sp_gallery_is_elementor_in_edit_mode() {
		
		
        
		$get_action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		if ( $get_action === 'elementor' ) {
			return true;
		}
        
		$has_preview = isset( $_GET['elementor-preview'] );
		if ( $has_preview ) {
			
			return true;
		}
        
		$builder = isset( $_GET['builder'] ) ? sanitize_key( wp_unslash( $_GET['builder'] ) ) : '';
		if ( $builder === 'elementor' ) {
			return true;
		}

		
		if ( wp_doing_ajax() ) {
            
			$ajax_action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
			if ( $ajax_action !== '' && strpos( $ajax_action, 'elementor' ) !== false ) {
				return true;
			}
		}

		
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id && strpos( (string) $screen->id, 'elementor' ) !== false ) {
				return true;
			}
		}

		
		if ( did_action( 'elementor/loaded' ) && class_exists( '\\Elementor\\Plugin' ) ) {
			try {
				$plugin = \Elementor\Plugin::$instance;
				if (
					$plugin && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode()
				) {
					return true;
				}
			} catch ( \Throwable $e ) {
				$this->debug->log( 'Elementor editor check error: ', array( 'error' => $e->getMessage() ), 'ERROR' );
			}
			try {
				$plugin = isset( $plugin ) ? $plugin : ( \Elementor\Plugin::$instance );
				if (
					$plugin && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode()
				) {
					return true;
				}
			} catch ( \Throwable $e ) {
				$this->debug->log( 'Elementor editor check error: ', array( 'error' => $e->getMessage() ), 'ERROR' );
			}
		}

		return false;
	}

	/**
	 * Convert a HEX or RGBA color string to HSL(A).
	 *
	 * @param string $color Hex (#rrggbb or #rgb) or rgba(r,g,b,a).
	 *
	 * @return array [h, s, l, a] with h in degrees (0–360), s and l in percent (0–100), a in 0–1
	 */
	public function color_to_hsl( $color ) {
		$r = 0;
		$g = 0;
		$b = 0;
		$a = 1;

		
		$patterns = array(
			'hex3' => '/^#([0-9a-f]{3})$/i',
			'hex6' => '/^#([0-9a-f]{6})$/i',
			'rgba' => '/rgba?\(([^)]+)\)/i',
		);

		$parsed = false;
		foreach ( $patterns as $type => $regex ) {
			if ( ! preg_match( $regex, $color, $matches ) ) {
				continue;
			}

			switch ( $type ) {
				case 'hex3':
					$r = hexdec( str_repeat( $matches[1][0], 2 ) );
					$g = hexdec( str_repeat( $matches[1][1], 2 ) );
					$b = hexdec( str_repeat( $matches[1][2], 2 ) );
					break;

				case 'hex6':
					$r = hexdec( substr( $matches[1], 0, 2 ) );
					$g = hexdec( substr( $matches[1], 2, 2 ) );
					$b = hexdec( substr( $matches[1], 4, 2 ) );
					break;

				case 'rgba':
					$parts = array_map( 'trim', explode( ',', $matches[1] ) );
					$r     = (int) ( $parts[0] ?? 0 );
					$g     = (int) ( $parts[1] ?? 0 );
					$b     = (int) ( $parts[2] ?? 0 );
					$a     = isset( $parts[3] ) ? (float) $parts[3] : 1;
					break;
			}

			$parsed = true;
			break;
		}

		if ( ! $parsed ) {
			return array( 0, 0, 0, 1 ); 
		}

		
		$r /= 255;
		$g /= 255;
		$b /= 255;

		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );
		$h   = ( $max + $min ) / 2;
		$s   = ( $max + $min ) / 2;
		$l   = ( $max + $min ) / 2;

		
		$d        = $max - $min;
		$is_equal = ( $max === $min );

		
		$s = $is_equal ? 0 : ( $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min ) );

		
		$h_map = array(
			(string) $r => ( $g - $b ) / ( $d ? $d : 1 ) + ( $g < $b ? 6 : 0 ),
			(string) $g => ( $b - $r ) / ( $d ? $d : 1 ) + 2,
			(string) $b => ( $r - $g ) / ( $d ? $d : 1 ) + 4,
		);

		$h  = $is_equal ? 0 : ( $h_map[ (string) $max ] ?? 0 );
		$h /= 6;

		return array(
			round( $h * 360, 2 ), 
			round( $s * 100, 2 ), 
			round( $l * 100, 2 ), 
			$a,                   
		);
	}

	/**
	 * Return "Hdeg S% L%;" for a given color.
	 * Accepts #rgb, #rrggbb, rgb(), rgba().
	 *
	 * @param string $color The color to convert.
	 * @return string The HSL tokens string.
	 */
	public function color_to_hsl_tokens_string( $color ): string {
		

		[$h, $s, $l] = array_slice( $this->color_to_hsl( $color ), 0, 3 );

		$fmt = function ( $num ) {
			
			return preg_replace( '/\.00$/', '', number_format( (float) $num, 2, '.', '' ) );
		};

		return sprintf( '%sdeg %s%% %s%%;', $fmt( $h ), $fmt( $s ), $fmt( $l ) );
	}

	/**
	 * Get WordPress color palettes from Global Styles and theme support.
	 * Returns a normalised array of [slug, name, color, source].
	 *
	 * @return array The color palettes.
	 */
	public function get_wp_colors(): array {
		$out = array();

		
		$theme   = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
		$custom  = wp_get_global_settings( array( 'color', 'palette', 'custom' ) );
		$default = wp_get_global_settings( array( 'color', 'palette', 'default' ) );

		$push = static function ( $items, $source ) use ( &$out ) {
			foreach ( (array) $items as $c ) {
				if ( ! is_array( $c ) || empty( $c['color'] ) ) {
					continue;
				}
				$out[] = array(
					'slug'   => $c['slug'] ?? sanitize_key( $c['name'] ?? $c['color'] ),
					'name'   => $c['name'] ?? ( $c['slug'] ?? $c['color'] ),
					'color'  => $c['color'],
					'source' => $source,
				);
			}
		};

		$push( $custom, 'wp-custom' ); 
		$push( $theme, 'wp-theme' );
		$push( $default, 'wp-default' );

		
		$palette = get_theme_support( 'editor-color-palette' );
		if ( is_array( $palette ) && isset( $palette[0] ) && is_array( $palette[0] ) ) {
			foreach ( $palette[0] as $c ) {
				if ( empty( $c['color'] ) ) {
					continue;
				}
				$out[] = array(
					'slug'   => $c['slug'] ?? sanitize_key( $c['name'] ?? $c['color'] ),
					'name'   => $c['name'] ?? ( $c['slug'] ?? $c['color'] ),
					'color'  => $c['color'],
					'source' => 'wp-theme-support',
				);
			}
		}

		
		$bg = get_theme_mod( 'background_color' );
		if ( $bg ) {
			$out[] = array(
				'slug'   => 'background',
				'name'   => 'Background',
				'color'  => '#' . ltrim( $bg, '#' ),
				'source' => 'wp-theme-mod',
			);
		}

		return $out;
	}

	/**
	 * Get Elementor Kit colors, both system and custom. Returns the same shape as sp_get_wp_colors().
	 */
	public function get_elementor_colors(): array {
		$out = array();

		
		if ( function_exists( 'did_action' ) && did_action( 'elementor/loaded' ) ) {
			try {
				$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
			} catch ( \Throwable $e ) {
				$kit = null;
			}
			if ( $kit ) {
				$sys = (array) $kit->get_settings( 'system_colors' );
				foreach ( $sys as $item ) {
					if ( empty( $item['color'] ) ) {
						continue;
					}
					$out[] = array(
						'slug'   => $item['_id'] ?? sanitize_key( $item['title'] ?? '' ),
						'name'   => $item['title'] ?? ( $item['_id'] ?? 'System' ),
						'color'  => $item['color'],
						'source' => 'elementor-system',
					);
				}
				$custom = (array) $kit->get_settings( 'custom_colors' );
				foreach ( $custom as $item ) {
					if ( empty( $item['color'] ) ) {
						continue;
					}
					$out[] = array(
						'slug'   => $item['_id'] ?? sanitize_key( $item['title'] ?? '' ),
						'name'   => $item['title'] ?? ( $item['_id'] ?? 'Custom' ),
						'color'  => $item['color'],
						'source' => 'elementor-custom',
					);
				}
				return $out;
			}
		}

		
		$kit_id = (int) get_option( 'elementor_active_kit' );
		if ( $kit_id ) {
			$settings = (array) get_post_meta( $kit_id, '_elementor_page_settings', true );
			foreach ( (array) ( $settings['system_colors'] ?? array() ) as $item ) {
				if ( empty( $item['color'] ) ) {
					continue;
				}
				$out[] = array(
					'slug'   => $item['_id'] ?? sanitize_key( $item['title'] ?? '' ),
					'name'   => $item['title'] ?? ( $item['_id'] ?? 'System' ),
					'color'  => $item['color'],
					'source' => 'elementor-system',
				);
			}
			foreach ( (array) ( $settings['custom_colors'] ?? array() ) as $item ) {
				if ( empty( $item['color'] ) ) {
					continue;
				}
				$out[] = array(
					'slug'   => $item['_id'] ?? sanitize_key( $item['title'] ?? '' ),
					'name'   => $item['title'] ?? ( $item['_id'] ?? 'Custom' ),
					'color'  => $item['color'],
					'source' => 'elementor-custom',
				);
			}
		}

		
		$scheme = get_option( 'elementor_scheme_color' );
		if ( is_array( $scheme ) ) {
			$i = 0;
			foreach ( $scheme as $hex ) {
				if ( ! $hex ) {
					continue;
				}
				$out[] = array(
					'slug'   => 'scheme_' . $i,
					'name'   => 'Scheme ' . $i,
					'color'  => $hex,
					'source' => 'elementor-scheme',
				);
				++$i;
			}
		}

		return $out;
	}

	/**
	 * Unified colors, WordPress first then Elementor. Duplicates are collapsed by hex.
	 *
	 * @return array{slug:string,name:string,color:string,source:string}[]
	 */
	public function get_global_colors(): array {
		$all    = array_merge( $this->get_wp_colors(), $this->get_elementor_colors() );
		$seen   = array();
		$unique = array();

		foreach ( $all as $c ) {
			
			if ( ! is_array( $c ) || empty( $c['color'] ) ) {
				continue;
			}
			$raw = trim( (string) $c['color'] );

			
			if ( ! preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $raw ) ) {
				continue;
			}

			
			$norm = '#' . strtoupper( ltrim( $raw, '#' ) );
			$key  = substr( $norm, 1 ); 

			if ( isset( $seen[ $key ] ) ) {
				continue; 
			}
			$seen[ $key ] = true;

			
			$unique[] = array(
				'slug'   => $c['slug'] ?? sanitize_key( $norm ),
				'name'   => $c['name'] ?? $norm,
				'color'  => $norm,
				'source' => $c['source'] ?? 'unknown',
			);
		}

		return $unique;
	}

	/**
	 * Retrieves the primary colour used by WordPress or Elementor.
	 *
	 * Checks Elementor's system and global colour sets for common primary or
	 * accent colour slugs, then falls back to WordPress palette entries named
	 * 'primary' or 'secondary'. If none are found, returns the provided fallback.
	 *
	 * @since 1.7.0
	 *
	 * @param string $fallback Fallback colour in hex format when no match is found. Default '#EE2E4F'.
	 * @return string The detected or fallback primary colour string.
	 */
	public function get_primary_color( string $fallback = '#EE2E4F' ): string {
		
		foreach ( $this->get_elementor_colors() as $c ) {
			if ( in_array( $c['slug'], array( 'primary', 'accent', 'e-global-color-primary' ), true ) ) {
				return $c['color'];
			}
		}

		
		foreach ( $this->get_wp_colors() as $c ) {
			if ( in_array( $c['slug'], array( 'primary', 'secondary' ), true ) ) {
				return $c['color'];
			}
		}

		return $fallback;
	}

	/**
	 * Generates a darker or lighter accent colour based on the input colour and percentage.
	 *
	 * Accepts hex, rgb(), or rgba() formats and adjusts each RGB channel by the given percentage,
	 * reducing brightness to create a darker accent. Alpha transparency is preserved where applicable.
	 *
	 * Examples:
	 * - get_accent_color('#ff0000', 0.2) → '#CC0000'
	 * - get_accent_color('rgb(255, 0, 0)', 0.5) → 'rgb(128, 0, 0)'
	 * - get_accent_color('rgba(255, 0, 0, 0.5)', 0.1) → 'rgba(230, 0, 0, 0.5)'
	 *
	 * @since 1.7.0
	 *
	 * @param string $color   The base colour in hex (#rgb or #rrggbb), rgb(), or rgba() format.
	 * @param float  $percent The adjustment percentage as a decimal (e.g. 0.2 for 20% darker).
	 *
	 * @return string The adjusted accent colour in the same format as the input.
	 */
	public function get_accent_color( string $color, float $percent ): string {
		$r     = 0;
		$g     = 0;
		$b     = 0;
		$a     = 1.0;
		$color = trim( $color );

		
		$patterns = array(
			'rgba' => '/rgba\s*\((\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*([0-9\.]+)\)/i',
			'rgb'  => '/rgb\s*\((\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\)/i',
			'hex'  => '/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i',
		);

		
		foreach ( $patterns as $type => $regex ) {
			if ( ! preg_match( $regex, $color, $m ) ) {
				continue;
			}

			$parsers = array(
				'rgba' => static function ( array $m ) {
					return array(
						'r' => (int) $m[1],
						'g' => (int) $m[2],
						'b' => (int) $m[3],
						'a' => (float) $m[4],
					);
				},
				'rgb'  => static function ( array $m ) {
					return array(
						'r' => (int) $m[1],
						'g' => (int) $m[2],
						'b' => (int) $m[3],
						'a' => 1.0,
					);
				},
				'hex'  => static function ( array $m ) {
					$hex = ltrim( $m[1], '#' );
					if ( strlen( $hex ) === 3 ) {
						$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
					}
					return array(
						'r' => hexdec( substr( $hex, 0, 2 ) ),
						'g' => hexdec( substr( $hex, 2, 2 ) ),
						'b' => hexdec( substr( $hex, 4, 2 ) ),
						'a' => 1.0,
					);
				},
			);

			$parser = $parsers[ $type ];

			$parse = $parser( $m );

			$r = (int) $parse['r'];
			$g = (int) $parse['g'];
			$b = (int) $parse['b'];
			$a = (float) $parse['a'];
			break;
		}

		
		$adjust = fn ( $v ) => max( 0, min( 255, (int) round( $v - $v * $percent ) ) );
		$r      = $adjust( $r );
		$g      = $adjust( $g );
		$b      = $adjust( $b );

		
		if ( stripos( $color, 'rgba' ) === 0 ) {
			return sprintf( 'rgba(%d, %d, %d, %.3g)', $r, $g, $b, $a );
		}

		if ( stripos( $color, 'rgb' ) === 0 ) {
			return sprintf( 'rgb(%d, %d, %d)', $r, $g, $b );
		}

		return sprintf( '#%02X%02X%02X', $r, $g, $b );
	}

	/**
	 * Sanitizes a CSS color value.
	 *
	 * Accepts hex values (#rgb or #rrggbb) or rgb()/rgba() functional notation.
	 * Values are clamped to valid ranges, alpha is constrained to 0–1.
	 * Returns a normalised color string or falls back to black (#000000).
	 *
	 * @since 1.7.0
	 *
	 * @param string $val Raw input color string.
	 *
	 * @return string Sanitized CSS color string safe for output.
	 */
	public function sanitize_css_color( $val ) {
		$val = trim( (string) $val );

		$hex = sanitize_hex_color( $val );

		
		if ( ! empty( $hex ) ) {
			return $hex;
		}
		
		if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/i', $val, $m ) ) {
			
			$r = min( 255, (int) $m[1] );
			$g = min( 255, (int) $m[2] );
			$b = min( 255, (int) $m[3] );
			$a = isset( $m[4] ) ? max( 0, min( 1, (float) $m[4] ) ) : null;
			return $a === null ? "rgb({$r}, {$g}, {$b})" : "rgba({$r}, {$g}, {$b}, {$a})";
		}
		
		return '#000000';
	}


	/**
	 * Saves a single metabox field value for the gallery.
	 *
	 * Handles saving and sanitisation for a single metabox field, including re-sorting images
	 * if the image sorting field is changed, or updating checked values for multi-value fields.
	 * Also triggers appropriate hooks after save and after images are added.
	 *
	 * @since 1.6.0
	 *
	 * @param int    $post_id     The post ID of the gallery.
	 * @param string $field_id    The meta field key to save (e.g. 'sp_gallery_image_sorting').
	 * @param mixed  $field_value The value to save for the field (array or scalar).
	 *
	 * @return array|WP_Error {
	 *     Success array on success, WP_Error on failure.
	 *
	 * @type string      $message     Message describing the result.
	 * @type array|null  $image_order The new image order, if images were re-sorted.
	 *              }
	 */
	public function save_metabox_field( int $post_id, string $field_id, $field_value ) {
		
		if ( $post_id <= 0 ) {
			return new WP_Error( 'invalid_post_id', __( 'Invalid post ID provided.', 'shutterpress-gallery' ) );
		}

		
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'post_not_found', __( 'Post does not exist.', 'shutterpress-gallery' ) );
		}

		
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error( 'permission_denied', __( 'You do not have permission to edit this post.', 'shutterpress-gallery' ) );
		}

		
		if ( empty( $field_id ) ) {
			return new WP_Error( 'invalid_field_id', __( 'Invalid field ID provided.', 'shutterpress-gallery' ) );
		}

		
		if ( ! function_exists( 'rwmb_set_meta' ) ) {
			return new WP_Error( 'missing_dependency', __( 'Meta Box plugin function not available.', 'shutterpress-gallery' ) );
		}

		
		if ( $field_id !== 'sp_gallery_image_sorting' && $field_id !== '_sp_gallery_images_array' ) {
			$meta_value  = $this->sanitize_meta_value( $field_value );
			$nested_meta = $this->build_nested_meta_payload( (string) $field_id, $meta_value );

			if ( $nested_meta ) {
				$base_field    = $nested_meta['base_field'];
				$nested_value  = $nested_meta['nested_value'];
				$current_value = get_post_meta( $post_id, $base_field, true );

				if ( ! is_array( $current_value ) ) {
					$current_value = array();
				}

				$merged_value = array_replace_recursive( $current_value, $nested_value );
				update_post_meta( $post_id, $base_field, $merged_value );
			} else {
				rwmb_set_meta( $post_id, $field_id, $meta_value );
			}

			do_action( 'sp_gallery_after_rest_save_field', $post_id, $field_id, $field_value );
			return array(
				'message' => 'Field saved successfully',
			);
		}

		
		$gallery       = new Shutterpress_Gallery_Gallery( $post_id );
		$sorted_images = null;

		
		if ( $field_id === 'sp_gallery_image_sorting' ) {
			$images = $gallery->get_gallery_images();

			if ( is_wp_error( $images ) ) {
				return new WP_Error( 'image_fetch_failed', __( 'Failed to retrieve gallery images.', 'shutterpress-gallery' ), array( 'error' => $images ) );
			}

			$sorted        = $gallery->sort_images_by_option( is_array( $images ) ? $images : array(), (string) $field_value );
			$sorted_images = array_map( 'intval', (array) $sorted );
			$update_result = update_post_meta( $post_id, '_sp_gallery_images_array', $sorted_images );

			if ( false === $update_result ) {
				$error = new WP_Error( 'meta_update_failed', __( 'Failed to update image order.', 'shutterpress-gallery' ) );
				return array(
					'wp_error'    => $error,
					'message'     => 'Failed to update image order.',
					'image_order' => $sorted_images,
				);
			}

			rwmb_set_meta( $post_id, $field_id, sanitize_text_field( $field_value ) );
		}

		
		if ( $field_id === '_sp_gallery_images_array' && $gallery->requires_sorting() ) {
			$sort_type = $gallery->get_sort_type();

			if ( is_wp_error( $sort_type ) ) {
				return new WP_Error( 'sort_type_failed', __( 'Failed to retrieve sort type.', 'shutterpress-gallery' ), array( 'error' => $sort_type ) );
			}

			$sorted        = $gallery->sort_images_by_option( is_array( $field_value ) ? $field_value : array(), (string) $sort_type );
			$sorted_images = array_map( 'intval', (array) $sorted );
			$update_result = update_post_meta( $post_id, '_sp_gallery_images_array', $sorted_images );

			if ( false === $update_result ) {
				return new WP_Error( 'meta_update_failed', __( 'Failed to update sorted images.', 'shutterpress-gallery' ) );
			}

			do_action( 'sp_gallery_after_add_images', $sorted_images, $post_id, $sort_type );
		}

		do_action( 'sp_gallery_after_rest_save_field', $post_id, $field_id, $field_value );

		return array(
			'message'     => 'Field saved successfully',
			'image_order' => $sorted_images,
		);
	}

	/**
	 * Builds a nested payload for bracket-notation meta field identifiers.
	 *
	 * Converts field IDs like 'sp_gallery_icons[cart]' into a base field key
	 * with a nested array that can be merged into the stored meta.
	 *
	 * @since 1.7.0
	 *
	 * @param string $field_id Field identifier from the request.
	 * @param mixed  $value    Sanitised value to assign.
	 *
	 * @return array|null Associative array containing base_field and nested_value or null when no bracket notation is present.
	 */
	private function build_nested_meta_payload( string $field_id, $value ): ?array {
		if ( ! preg_match( '/^([^\[\]]+)((?:\[[^\[\]]*\])+)$/', $field_id, $matches ) ) {
			return null;
		}

		$base_field = $matches[1];
		$path       = trim( $matches[2], '[]' );

		if ( '' === $path ) {
			return null;
		}

		$segments = preg_split( '/\]\[/', $path );

		if ( empty( $segments ) ) {
			return null;
		}

		$nested = $value;

		for ( $i = count( $segments ) - 1; $i >= 0; $i-- ) {
			$key    = $segments[ $i ];
			$key    = is_numeric( $key ) ? (int) $key : sanitize_key( $key );
			$nested = array( $key => $nested );
		}

		return array(
			'base_field'   => $base_field,
			'nested_value' => $nested,
		);
	}

	/**
	 * Sanitises meta values recursively while preserving numeric and boolean types.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $value Meta value to sanitise.
	 * @return mixed Sanitised meta value.
	 */
	private function sanitize_meta_value( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'sanitize_meta_value' ), $value );
		}

		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return $value + 0;
		}

		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Determines whether Avada/Fusion is currently rendering a template override.
	 *
	 * When Fusion Builder is drawing header/footer/page title bars, we must skip
	 * injecting the gallery output to avoid duplicate markup or recursion.
	 * 
	 * @since 1.7.12
	 *
	 * @return bool
	 */
	public function third_party_plugin_content_fixes(): bool {
		if (function_exists( 'Fusion_Template_Builder' ) ) {
			
			$current = Fusion_Template_Builder()->get_current_override_name();
			return in_array( $current, array( 'header', 'footer', 'page_title_bar', 'content' ), true );
		}

		return false;
	}
}
