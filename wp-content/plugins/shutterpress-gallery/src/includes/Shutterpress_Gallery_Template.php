<?php
/**
 * Gallery Template class for ShutterPress Gallery.
 *
 * Encapsulates all template-specific options such as layout, colors, icons,
 * hover effects, lightbox settings, and custom CSS. Provides helpers to read
 * and sanitize post meta, create templates from defaults, and retrieve key
 * information about a template post.
 *
 * @package Shutterpress\Gallery
 *
 * @since 1.7.0
 */

namespace Shutterpress\Gallery;

use WP_Post;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Template model for ShutterPress Gallery.
 *
 * Represents a gallery template post and exposes typed properties for layout,
 * colors, icons, hover behavior, lightbox settings, pagination, and custom CSS.
 * Loads values from post meta with sane defaults from `Shutterpress_Gallery_Defaults`,
 * and provides helpers to create a template from defaults and to read meta in a
 * safe and consistent way.
 *
 * @since 1.7.0
 * @package Shutterpress\Gallery
 */
class Shutterpress_Gallery_Template {

	
	protected ?int $template_id = null;
	protected ?WP_Post $post    = null;
	protected string $prefix;
	protected Shutterpress_Gallery_Defaults $defaults_class;
	public array $attributes = array();
	public array $defaults;
	public string $post_status = '';

	
	public string $layout;
	public int $gap;
	public array $columns;
	public int $columns_desktop;
	public int $columns_tablet;
	public int $columns_mobile;
	public int $row_height;
	public bool $pagination;
	public bool $infinite_scroll;

	
	public string $color_primary;
	public string $color_accent;

	
	public bool $use_lightbox;
	public string $lightbox_mode;
	public string $lightbox_bg_color;
	public string $lightbox_icon_color;

	
	public bool $rounded_corners;
	public int $border_radius;

	
	public array $icons;
	public array $icon_size;
	public string $show_icons;
	public string $icon_position;
	public float $icon_stroke_width;
	public array $hover_icon_size;

	
	public bool $hover_grow_image;
	public bool $hover_float;
	public string $hover_float_shadow;
	public string $float_shadow_color;
	public bool $hover_overlay;
	public string $hover_overlay_color;
	public string $hover_overlay_icon;
	public bool $show_hover_overlay_icon;
	public string $hover_overlay_icon_color;
	public bool $show_hover_image_title;
	public bool $show_hover_image_caption;

	
	public string $custom_css;

	/**
	 * Initializes the template object for a given post.
	 *
	 * Loads the associated WordPress post based on the provided post ID.
	 *
	 * @since 1.7.0
	 *
	 * @param int|null $template_id Optional. The ID of the post associated with the template.
	 */
	public function __construct( ?int $template_id = null ) {
		if ( ! $template_id ) {
			
            
			if ( isset( $_GET['post'] ) ) {
                
				$template_id = absint( $_GET['post'] );
			} elseif ( get_the_ID() ) {
				$template_id = (int) get_the_ID();
			}
		}

		
		
		$this->prefix         = defined( 'SP_GALLERY_MB_PREFIX' ) ? SP_GALLERY_MB_PREFIX : 'sp_gallery_';
		$this->defaults_class = new Shutterpress_Gallery_Defaults();
		$this->defaults       = $this->defaults_class->get_template_defaults();
		$defaults             = $this->defaults;

		$this->template_id = $template_id;
		$this->post        = get_post( $template_id );
		$this->post_status = $this->post instanceof \WP_Post ? $this->post->post_status : '';

		
		$this->layout          = $this->read_text( 'layout', $defaults['layout'] );
		$this->gap             = $this->read_int( 'gap', $defaults['gap'], 0 );
		$columns_default       = array(
			'desktop' => (int) $defaults['columns']['desktop'],
			'tablet'  => (int) $defaults['columns']['tablet'],
			'mobile'  => (int) $defaults['columns']['mobile'],
		);
		$this->columns         = wp_parse_args( $this->read_array( 'columns', array() ), $columns_default );
		$this->row_height      = $this->read_int( 'row_height', $defaults['row_height'], 0 );
		$this->pagination      = $this->read_bool( 'pagination', $defaults['pagination'] );
		$this->infinite_scroll = $this->read_bool( 'infinite_scroll', $defaults['infinite_scroll'] );

		
		$this->color_primary = $this->read_text( 'color_primary', $defaults['color_primary'] );
		$this->color_accent  = $this->read_text( 'color_accent', $defaults['color_accent'] );

		
		$this->use_lightbox        = $this->read_bool( 'lightbox', $defaults['use_lightbox'] );
		$this->lightbox_mode       = $this->read_text( 'lightbox_mode', $defaults['lightbox_mode'] );
		$this->lightbox_bg_color   = $this->read_text( 'lightbox_bg_color', $defaults['lightbox_bg_color'] );
		$this->lightbox_icon_color = $this->read_text( 'lightbox_icon_color', $defaults['lightbox_icon_color'] );

		
		$this->rounded_corners = $this->read_bool( 'rounded_corners', $defaults['rounded_corners'] );
		$this->border_radius   = $this->read_int( 'border_radius', $defaults['border_radius'], 0 );

		
		$this->icons              = $this->read_array( 'icons', (array) $defaults['icons'] );
		$icon_size_defaults       = array(
			'desktop' => (int) $defaults['icon_size']['desktop'],
			'tablet'  => (int) $defaults['icon_size']['tablet'],
			'mobile'  => (int) $defaults['icon_size']['mobile'],
		);
		$this->icon_size          = wp_parse_args( $this->read_array( 'icon_size', array() ), $icon_size_defaults );
		$this->show_icons         = $this->read_text( 'show_icons', $defaults['show_icons'] );
		$this->icon_position      = $this->read_text( 'icon_position', $defaults['icon_position'] );
		$this->icon_stroke_width  = (float) $this->read_text( 'icon_stroke_width', (string) $defaults['icon_stroke_width'] );
		$hover_icon_size_defaults = array(
			'desktop' => (int) $defaults['hover_icon_size']['desktop'],
			'tablet'  => (int) $defaults['hover_icon_size']['tablet'],
			'mobile'  => (int) $defaults['hover_icon_size']['mobile'],
		);
		$this->hover_icon_size    = wp_parse_args( $this->read_array( 'hover_icon_size', array() ), $hover_icon_size_defaults );

		
		$this->hover_grow_image         = $this->read_bool( 'hover_grow_image', $defaults['hover_grow_image'] );
		$this->hover_float              = $this->read_bool( 'hover_float', $defaults['hover_float'] );
		$this->hover_float_shadow       = $this->read_text( 'hover_float_shadow', $defaults['hover_float_shadow'] );
		$this->float_shadow_color       = $this->read_text( 'float_shadow_color', $defaults['float_shadow_color'] );
		$this->hover_overlay            = $this->read_bool( 'hover_overlay', $defaults['hover_overlay'] );
		$this->hover_overlay_color      = $this->read_text( 'hover_overlay_color', $defaults['hover_overlay_color'] );
		$this->hover_overlay_icon       = $this->read_text( 'hover_overlay_icon', $defaults['hover_overlay_icon'] );
		$this->show_hover_overlay_icon  = $this->read_bool( 'show_hover_overlay_icon', $defaults['show_hover_overlay_icon'] );
		$this->hover_overlay_icon_color = $this->read_text( 'hover_overlay_icon_color', $defaults['hover_overlay_icon_color'] );
		$this->show_hover_image_title   = $this->read_bool( 'show_hover_image_title', $defaults['show_hover_image_title'] );
		$this->show_hover_image_caption = $this->read_bool( 'show_hover_image_caption', $defaults['show_hover_image_caption'] );

		
		$raw_css          = $this->meta( 'custom_css' );
		$this->custom_css = $raw_css === '' || $raw_css === null ? (string) $defaults['custom_css'] : wp_kses_no_null( wp_strip_all_tags( (string) $raw_css, true ) );
	}

	/**
	 * Retrieve a raw post meta value for the template.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key Meta key without prefix.
	 *
	 * @return mixed The stored value or empty string if not found.
	 */
	private function meta( $key ) {
		return get_post_meta( $this->template_id, $this->prefix . $key, true );
	}

	/**
	 * Retrieve a boolean option from post meta.
	 *
	 * Falls back to the provided default if unset.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key     Meta key without prefix.
	 * @param bool   $default_value Default value if not set.
	 *
	 * @return bool Sanitized boolean value.
	 */
	protected function read_bool( $key, $default_value ) {
		$v = $this->meta( $key );
		return $v === '' || $v === null ? (bool) $default_value : (bool) filter_var( $v, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Retrieve an integer option from post meta.
	 *
	 * Falls back to the provided default if unset, and optionally enforces a minimum value.
	 *
	 * @since 1.7.0
	 *
	 * @param string   $key     Meta key without prefix.
	 * @param int      $default_value Default value if not set.
	 * @param int|null $min     Optional minimum value.
	 *
	 * @return int Sanitized integer value.
	 */
	protected function read_int( $key, $default_value, $min = null ) {
		$v = $this->meta( $key );
		$i = $v === '' || $v === null ? (int) $default_value : (int) $v;
		return $min !== null ? max( $min, $i ) : $i;
	}

	/**
	 * Retrieve a text option from post meta.
	 *
	 * Falls back to the provided default if unset, and sanitizes the text field.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key     Meta key without prefix.
	 * @param string $default_value Default value if not set.
	 *
	 * @return string Sanitized text value.
	 */
	protected function read_text( $key, $default_value ) {
		$v = $this->meta( $key );
		return $v === '' || $v === null ? $default_value : sanitize_text_field( $v );
	}

	/**
	 * Retrieve an array option from post meta.
	 *
	 * Falls back to the provided default if unset or empty.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key     Meta key without prefix.
	 * @param array  $default_value Default array if not set.
	 *
	 * @return array Array of values.
	 */
	protected function read_array( $key, $default_value ) {
		$v   = $this->meta( $key );
		$arr = is_array( $v ) ? $v : array();
		return ! empty( $arr ) ? $arr : $default_value;
	}

	/**
	 * Creates a new gallery template post from the plugin defaults.
	 *
	 * @since 1.7.0
	 *
	 * @param string $title Optional. Title for the new template. Default "New Gallery Template".
	 * @param array  $defaults Default array if not set.
	 *
	 * @return int|\WP_Error The new post ID on success, or WP_Error on failure.
	 */
	public function create_from_defaults( $title = 'New Gallery Template', $defaults = array() ) {
		$defaults = array_merge( $this->defaults, $defaults );
		$prefix   = $this->prefix;

		$post_id = wp_insert_post(
			array(
				'post_title'  => sanitize_text_field( $title ),
				'post_type'   => 'sp_gallery_template',
				'post_status' => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		
		$schema = $this->defaults_class->get_template_schema();

		foreach ( $schema as $key => $_type ) {
			if ( ! array_key_exists( $key, $defaults ) ) {
				continue;
			}

			$val = $this->defaults_class->sanitize_value( $key, $defaults[ $key ], $schema );
			update_post_meta( $post_id, $prefix . $key, $val );
		}

		/**
		 * Action fired after a template is created from defaults.
		 *
		 * @param int   $post_id  The new template post ID.
		 * @param array $defaults The defaults written.
		 */
		do_action( 'sp_gallery_template_created', $post_id, $defaults );

		return $post_id;
	}

	/**
	 * Checks whether the template post exists.
	 *
	 * Validates that the post object was successfully loaded during initialization.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if the gallery post exists, false otherwise.
	 */
	public function exists(): bool {
		return $this->post instanceof WP_Post && $this->post->ID > 0;
	}

	/**
	 * Retrieves the post ID associated with the template.
	 *
	 * @since 1.7.0
	 *
	 * @return int|null The post ID, or null if not set.
	 */
	public function get_id() {
		return $this->template_id;
	}
}
