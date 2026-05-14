<?php
/**
 * Default settings for ShutterPress Gallery.
 *
 * Single source of truth for gallery option defaults used by admin UI,
 * REST endpoints, sanitization, and rendering.
 *
 * @package Shutterpress\Gallery
 *
 * @since 1.7.0
 */

namespace Shutterpress\Gallery;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}
/**
 * Provides default option values for ShutterPress galleries and templates.
 *
 * Exposes a getter for the defaults array, and a helper that derives
 * a coarse sanitization schema from those values so other code stays in sync.
 *
 * Keys include booleans, integers, color strings, responsive arrays,
 * and free text such as `custom_css`.
 *
 * @since 1.7.0
 */
class Shutterpress_Gallery_Defaults {

	protected array $plugin_defaults = array(
		'breakpoint_tablet' => 782,
		'breakpoint_mobile' => 480,
		'show_buttons'      => true,
		'gallery_type'      => 'post',
	);

	protected array $template_defaults = array(
		
		'layout'                   => 'grid',
		'gap'                      => 20,
		'columns'                  => array(
			'desktop' => 3,
			'tablet'  => 2,
			'mobile'  => 1,
		),
		'row_height'               => 300,
		'pagination'               => false,
		'infinite_scroll'          => false,

		
		'color_primary'            => '#DD2B49',
		'color_accent'             => '#bc164d',

		
		'use_lightbox'             => true,
		'lightbox_mode'            => 'lg-slide',
		'lightbox_bg_color'        => '#000',
		'lightbox_icon_color'      => '#FFF',

		
		'rounded_corners'          => false,
		'border_radius'            => 10,

		
		'icons'                    => array( 'download' => 'download' ),
		'icon_size'                => array(
			'desktop' => 30,
			'tablet'  => 25,
			'mobile'  => 20,
		),
		'show_icons'               => 'always',
		'icon_position'            => 'bc',
		'icon_stroke_width'        => 2,
		'hover_icon_size'          => array(
			'desktop' => 50,
			'tablet'  => 40,
			'mobile'  => 30,
		),

		
		'hover_effects'            => false,
		'hover_grow_image'         => false,
		'hover_float'              => false,
		'hover_float_shadow'       => 'medium',
		'float_shadow_color'       => '0deg 0% 0%',
		'hover_overlay'            => false,
		'hover_overlay_color'      => 'rgba(0,0,0,0.5)',
		'hover_overlay_icon'       => 'search',
		'show_hover_overlay_icon'  => false,
		'hover_overlay_icon_color' => '#fff',
		'show_hover_image_caption' => false,
		'show_hover_image_title'   => false,

		
		'custom_css'               => '',
	);

	protected array $template_schema = array(
		
		'layout'                   => 'text',
		'gap'                      => 'int',
		'columns'                  => 'array_int', 
		'row_height'               => 'int',
		'pagination'               => 'bool',
		'infinite_scroll'          => 'bool',

		
		'color_primary'            => 'color',
		'color_accent'             => 'color',

		
		'use_lightbox'             => 'bool',
		'lightbox_mode'            => 'text',
		'lightbox_bg_color'        => 'color',
		'lightbox_icon_color'      => 'color',

		
		'rounded_corners'          => 'bool',
		'border_radius'            => 'int',

		
		'icons'                    => 'array_text',
		'icon_size'                => 'array_int', 
		'show_icons'               => 'text',
		'icon_position'            => 'text',
		'icon_stroke_width'        => 'float',
		'hover_icon_size'          => 'array_int', 

		
		'hover_effects'            => 'bool',
		'hover_grow_image'         => 'bool',
		'hover_float'              => 'bool',
		'hover_float_shadow'       => 'text',
		'float_shadow_color'       => 'color',
		'hover_overlay'            => 'bool',
		'hover_overlay_color'      => 'color',
		'hover_overlay_icon'       => 'text',
		'show_hover_overlay_icon'  => 'bool',
		'hover_overlay_icon_color' => 'color',
		'show_hover_image_caption' => 'bool',
		'show_hover_image_title'   => 'bool',

		
		'custom_css'               => 'css_text',
	);

	protected Shutterpress_Gallery_Utils $utils;

	/**
	 * Class constructor.
	 *
	 * Prepares the defaults provider by initializing utility helpers used for
	 * sanitizing colors and other values when deriving templates and schema.
	 *
	 * @since 1.7.0
	 * @return void
	 */
	public function __construct() {
		$this->utils = new Shutterpress_Gallery_Utils();
	}

	/**
	 * Retrieve the full template configuration.
	 *
	 * Returns both the default template values and the validation schema,
	 * allowing filters to override or extend them in one unified structure.
	 *
	 * @since 1.7.0
	 *
	 * @return array{template_defaults:array<string,mixed>,template_schema:array<string,string>} Combined defaults and schema.
	 */
	public function get_template_config(): array {
		$config = array(
			'template_defaults' => $this->template_defaults,
			'template_schema'   => $this->template_schema,
		);

		
		$config = apply_filters( 'sp_gallery_template_defaults', $config );

		return $config;
	}

	/**
	 * Retrieve the plugin-level default values.
	 *
	 * Returns core plugin defaults that apply globally across all galleries,
	 * such as responsive breakpoints, button visibility, and gallery type.
	 * These values are separate from template-specific defaults and are typically
	 * used as fallbacks when template or gallery-specific settings are not provided.
	 *
	 * @since 1.7.0
	 *
	 * @return array Plugin defaults keyed by option name (e.g., 'breakpoint_tablet', 'show_buttons').
	 */
	public function get_plugin_defaults(): array {

		$defaults = apply_filters( 'sp_gallery_plugin_defaults', $this->plugin_defaults );

		return $defaults;
	}

	/**
	 * Retrieve the default template values.
	 *
	 * Provides the base option values used for new gallery templates.
	 *
	 * @since 1.7.0
	 *
	 * @return array<string,mixed> Template defaults keyed by option name.
	 */
	public function get_template_defaults() {
		return $this->get_template_config()['template_defaults'];
	}

	/**
	 * Retrieve the template validation schema.
	 *
	 * Defines the expected type for each template option, used for sanitization.
	 *
	 * @since 1.7.0
	 *
	 * @return array<string,string> Schema keyed by option name, with type identifiers.
	 */
	public function get_template_schema(): array {
		return $this->get_template_config()['template_schema'];
	}

	/**
	 * Sanitize a single template option value.
	 *
	 * Applies type-specific cleaning based on the schema. Falls back to
	 * text sanitization if the key is unknown.
	 *
	 * @since 1.7.0
	 *
	 * @param string                    $key             Template option key.
	 * @param mixed                     $value           Value to sanitize.
	 * @param array<string,string>|null $template_schema Optional schema to use, defaults to get_template_schema().
	 *
	 * @return mixed Sanitized value of the appropriate type.
	 */
	public function sanitize_value( string $key, $value, ?array $template_schema = null ) {
		$template_schema = $template_schema ? $template_schema : $this->get_template_schema();
		$type            = $template_schema[ $key ] ? $template_schema[ $key ] : 'text';

		switch ( $type ) {
			case 'bool':
				return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'color':
				return $this->utils->sanitize_css_color( $value );
			case 'array_int':
				$arr = (array) $value;
				foreach ( $arr as $k => $v ) {
					$arr[ $k ] = is_array( $v ) ? array_map( 'intval', $v ) : (int) $v;
				}
				return $arr;
			case 'array_text':
				return array_map( 'sanitize_text_field', (array) $value );
			case 'css_text':
				return wp_kses_no_null( wp_strip_all_tags( (string) $value, true ) );
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Sanitize an entire array of template data.
	 *
	 * Iterates through all key/value pairs and applies sanitize_value()
	 * using the schema for each key.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string,mixed> $data Raw template data.
	 *
	 * @return array<string,mixed> Sanitized template data.
	 */
	public function sanitize_all( array $data ): array {
		$template_schema = $this->get_template_schema();
		foreach ( $data as $k => $v ) {
			$data[ $k ] = $this->sanitize_value( $k, $v, $template_schema );
		}
		return $data;
	}
}
