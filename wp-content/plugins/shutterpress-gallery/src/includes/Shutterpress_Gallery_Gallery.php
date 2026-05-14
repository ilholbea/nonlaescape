<?php
/**
 * This class defines all code necessary to manipulate images.
 *
 * @since 1.3.4
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 *
 * @author Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

use WP_Error;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Core class responsible for building, rendering, and managing galleries in ShutterPress.
 *
 * This class handles gallery setup, metadata retrieval, image management, sorting, layout rendering,
 * and output of gallery HTML and CSS for both frontend and editor contexts. It also integrates with
 * templates, supports pagination, infinite scroll, and user interactions such as likes and downloads.
 *
 * @since 1.3.4
 * @package Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */
class Shutterpress_Gallery_Gallery {

	protected static array $svg_cache = array();

	
	protected ?int $gallery_id = 0;
	protected int $post_id;
	public ?object $post                            = null;
	public ?Shutterpress_Gallery_Template $template = null;
	public int $template_id;
	public array $attributes;
	public string $gallery_type;
	public bool $show_buttons;
	public bool $show_like_icon;
	public bool $show_download_icon;
	public bool $show_fullsize_images;
	public int $images_per_page;
	public int $page;
	public string $page_counter;
	public int $gallery_width;
	public int $viewport_width;

	
	public string $layout;
	public int $gap;
	public int $columns_desktop;
	public int $columns_tablet;
	public int $columns_mobile;
	public int $row_height;
	public int $breakpoint_tablet;
	public int $breakpoint_mobile;
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
	public int $icon_size_desktop;
	public int $icon_size_tablet;
	public int $icon_size_mobile;
	public string $show_icons;
	public string $icon_position;
	public float $icon_stroke_width;
	public int $hover_icon_size_desktop;
	public int $hover_icon_size_tablet;
	public int $hover_icon_size_mobile;

	
	public bool $hover_effects;
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

	protected array $gallery_images_cache       = array();
	protected array $gallery_images_count_cache = array();
	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Utils $utils;

	/**
	 * Initializes the gallery object for a given post.
	 *
	 * Loads the associated WordPress post based on the provided post ID.
	 *
	 * @since 1.3.4
	 *
	 * @param int|null $gallery_id Optional. The ID of the post associated with the gallery.
	 */
	public function __construct( ?int $gallery_id = 0 ) {
		$this->debug = new Shutterpress_Gallery_Debug();
		$this->utils = new Shutterpress_Gallery_Utils();
		if ( $gallery_id === 0 ) {
			$source = 'not found';
			if ( isset( $_GET['post'] ) ) { 
				$gallery_id = absint( $_GET['post'] ); 
				$source     = '$_GET["post"]';
			}
			if ( ! $gallery_id && get_the_ID() ) {
				$gallery_id = (int) get_the_ID();
				$source     = 'get_the_ID()';
			}
		}

		$prefix = SP_GALLERY_MB_PREFIX;

		$this->template_id = intval( get_post_meta( $gallery_id, $prefix . 'template_id', true ) );

		if ( $this->template_id > 0 ) {
			$this->template = new Shutterpress_Gallery_Template( $this->template_id );
			if ( ! $this->template->exists() ) {
				$this->template = null;
			}
			$template_info = $this->template;
		}

		$this->gallery_id = $gallery_id;
		$this->post       = get_post( $gallery_id );
	}

	/**
	 * Checks whether the gallery post exists.
	 *
	 * Validates that the post object was successfully loaded during initialization.
	 *
	 * @since 1.3.4
	 *
	 * @return bool True if the gallery post exists, false otherwise.
	 */
	public function exists() {
		return ! empty( $this->post );
	}

	/**
	 * Sets the core rendering attributes for the Shutterpress Gallery.
	 *
	 * This method is used to initialize the core rendering attributes for the
	 * Shutterpress Gallery based on the provided attributes array. The attributes
	 * are then used throughout the rendering process to control the appearance and
	 * behavior of the gallery.
	 *
	 * @param array $attributes An associative array of gallery rendering attributes.
	 * @return array
	 */
	public function set_attributes( array $attributes = array() ): array {

		$defaults_class = new Shutterpress_Gallery_Defaults();

		$defaults = array(
			'gallery_id'           => $this->gallery_id ? $this->gallery_id : intval( get_the_ID() ),
			'show_like_icon'       => $this->get_show_like_icon(),
			'show_download_icon'   => $this->get_show_download_icon(),
			'show_fullsize_images' => $this->get_download_fullsize_image_status(),
			'images_per_page'      => $this->get_images_per_page(),
			'gallery_width'        => '',
			'viewport_width'       => '',
		);

		$defaults = array_merge( $defaults, $defaults_class->get_template_defaults(), $defaults_class->get_plugin_defaults() );

		$tpl = $this->template ?? null;

		$this->debug->log('TPL',[ 'tpl' => $tpl ], '');

		if ( $tpl && $tpl->post_status === 'publish' ) {
			$defaults = $this->merge_template_defaults( $defaults, $tpl );
		}

		
		foreach ( $defaults as $k => $v ) {
			if ( ! array_key_exists( $k, $attributes ) || $attributes[ $k ] === '' || $attributes[ $k ] === null ) {
				$attributes[ $k ] = $v;
				continue;
			}
			
			if ( is_array( $v ) && is_array( $attributes[ $k ] ) ) {
				$attributes[ $k ] = $this->merge_attribute_defaults( $v, $attributes[ $k ] );
			}
		}

		
		
		$this->gallery_id           = intval( $attributes['gallery_id'] );
		$this->gallery_type         = sanitize_text_field( $attributes['gallery_type'] );
		$this->show_buttons         = filter_var( $attributes['show_buttons'], FILTER_VALIDATE_BOOLEAN );
		$this->show_like_icon       = filter_var( $attributes['show_like_icon'], FILTER_VALIDATE_BOOLEAN );
		$this->show_download_icon   = filter_var( $attributes['show_download_icon'], FILTER_VALIDATE_BOOLEAN );
		$this->show_fullsize_images = filter_var( $attributes['show_fullsize_images'], FILTER_VALIDATE_BOOLEAN );
		$this->images_per_page      = intval( $attributes['images_per_page'] );
		$this->gallery_width        = intval( $attributes['gallery_width'] );
		$this->viewport_width       = intval( $attributes['viewport_width'] );

		
		$this->layout            = sanitize_text_field( $attributes['layout'] );
		$this->gap               = intval( $attributes['gap'] );
		$this->columns_desktop   = intval( $attributes['columns']['desktop'] );
		$this->columns_tablet    = intval( $attributes['columns']['tablet'] );
		$this->columns_mobile    = intval( $attributes['columns']['mobile'] );
		$this->row_height        = intval( $attributes['row_height'] );
		$this->breakpoint_tablet = intval( $attributes['breakpoint_tablet'] );
		$this->breakpoint_mobile = intval( $attributes['breakpoint_mobile'] );
		$this->pagination        = filter_var( $attributes['pagination'], FILTER_VALIDATE_BOOLEAN );
		$this->infinite_scroll   = filter_var( $attributes['infinite_scroll'], FILTER_VALIDATE_BOOLEAN );

		
		$this->color_primary = sanitize_text_field( $attributes['color_primary'] );
		$this->color_accent  = sanitize_text_field( $attributes['color_accent'] );

		
		$this->use_lightbox        = filter_var( $attributes['use_lightbox'], FILTER_VALIDATE_BOOLEAN );
		$this->lightbox_mode       = sanitize_text_field( $attributes['lightbox_mode'] );
		$this->lightbox_bg_color   = sanitize_text_field( $attributes['lightbox_bg_color'] );
		$this->lightbox_icon_color = sanitize_text_field( $attributes['lightbox_icon_color'] );

		
		$this->rounded_corners = filter_var( $attributes['rounded_corners'], FILTER_VALIDATE_BOOLEAN );
		$this->border_radius   = intval( $attributes['border_radius'] );

		
		$this->icons                   = is_array( $attributes['icons'] ) ? $attributes['icons'] : array();
		$this->icon_size_desktop       = intval( $attributes['icon_size']['desktop'] );
		$this->icon_size_tablet        = intval( $attributes['icon_size']['tablet'] );
		$this->icon_size_mobile        = intval( $attributes['icon_size']['mobile'] );
		$this->show_icons              = sanitize_text_field( $attributes['show_icons'] );
		$this->icon_position           = sanitize_text_field( $attributes['icon_position'] );
		$this->icon_stroke_width       = floatval( $attributes['icon_stroke_width'] );
		$this->hover_icon_size_desktop = intval( $attributes['hover_icon_size']['desktop'] );
		$this->hover_icon_size_tablet  = intval( $attributes['hover_icon_size']['tablet'] );
		$this->hover_icon_size_mobile  = intval( $attributes['hover_icon_size']['mobile'] );

		
		$this->hover_effects            = filter_var( $attributes['hover_effects'], FILTER_VALIDATE_BOOLEAN );
		$this->hover_grow_image         = filter_var( $attributes['hover_grow_image'], FILTER_VALIDATE_BOOLEAN );
		$this->hover_float              = filter_var( $attributes['hover_float'], FILTER_VALIDATE_BOOLEAN );
		$this->hover_float_shadow       = sanitize_text_field( $attributes['hover_float_shadow']);
		$this->float_shadow_color       = sanitize_text_field( $attributes['float_shadow_color'] );
		$this->hover_overlay            = filter_var( $attributes['hover_overlay'], FILTER_VALIDATE_BOOLEAN );
		$this->hover_overlay_color      = sanitize_text_field( $attributes['hover_overlay_color'] );
		$this->hover_overlay_icon       = sanitize_text_field( $attributes['hover_overlay_icon'] );
		$this->show_hover_overlay_icon  = filter_var( $attributes['show_hover_overlay_icon'], FILTER_VALIDATE_BOOLEAN );
		$this->hover_overlay_icon_color = sanitize_text_field( $attributes['hover_overlay_icon_color'] );
		$this->show_hover_image_caption = filter_var( $attributes['show_hover_image_caption'], FILTER_VALIDATE_BOOLEAN );
		$this->show_hover_image_title   = filter_var( $attributes['show_hover_image_title'], FILTER_VALIDATE_BOOLEAN );

		
		$this->custom_css = sanitize_text_field( $attributes['custom_css'] );

		$this->attributes = $attributes;

		return $attributes;
	}

	/**
	 * Recursively merges defaults with incoming attributes while preserving additional keys.
	 *
	 * @param array $defaults Default configuration values.
	 * @param array $incoming Incoming attribute overrides.
	 *
	 * @return array
	 */
	private function merge_attribute_defaults( array $defaults, array $incoming ): array {
		$result = $defaults;

		foreach ( $incoming as $key => $value ) {
			if ( is_array( $value ) ) {
				$base = array();
				if ( isset( $result[ $key ] ) && is_array( $result[ $key ] ) ) {
					$base = $result[ $key ];
				}
				$result[ $key ] = $this->merge_attribute_defaults( $base, $value );
				continue;
			}

			if ( $value === '' || $value === null ) {
				continue;
			}

			$result[ $key ] = $value;
		}

		return $result;
	}

	/**
	 * Retrieves the post ID associated with the gallery.
	 *
	 * @since 1.3.4
	 *
	 * @return int|null The post ID, or null if not set.
	 */
	public function get_id() {
		return $this->gallery_id;
	}

	/**
	 * Retrieves the post name (slug) for the gallery.
	 *
	 * Given a gallery post ID, this method returns the WordPress post slug (post_name).
	 * If no gallery ID is provided, it uses the current gallery ID set on the object.
	 * Returns an empty string if the post does not exist.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The ID of the gallery post. Defaults to the current gallery.
	 *
	 * @return string The post slug (post_name) if found, or an empty string if not found.
	 */
	public function get_name( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return '';
		}

		$name = get_post_field( 'post_name', $gallery_id );
		if ( ! is_string( $name ) ) {
			$name = '';
		}
		$this->debug->log(
			'gallery->get_name',
			array(
				'name'       => $name,
				'gallery_id' => $gallery_id,
			),
			'DEBUG'
		);
		return $name;
	}

	/**
	 * Retrieves the image IDs associated with the gallery post.
	 *
	 * This pulls from the '_sp_gallery_images_array' post meta field.
	 *
	 * @since 1.3.4
	 *
	 * @param int|null $gallery_id Optional. The ID of the gallery post. Defaults to the current gallery.
	 *
	 * @return array|false Array of image IDs if found, or false if none are stored.
	 */
	public function get_gallery_images( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ? absint( $gallery_id ) : $this->gallery_id );

		if ( ! $gallery_id ) {
			return false;
		}

		if ( array_key_exists( $gallery_id, $this->gallery_images_cache ) ) {
			return $this->gallery_images_cache[ $gallery_id ];
		}

		$images = get_post_meta( $gallery_id, '_sp_gallery_images_array', true );
		$images = ! empty( $images ) ? $images : false;

		$this->gallery_images_cache[ $gallery_id ] = $images;

		return $images;
	}

	/**
	 * Retrieves the total number of images in the gallery.
	 *
	 * Returns the count of image IDs stored in the gallery's '_sp_gallery_images_array' meta field.
	 * Returns false if there are no images.
	 *
	 * @since 1.6.0
	 *
	 * @param int|0 $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return int|false The number of images in the gallery, or false if none are found.
	 */
	public function get_gallery_images_count( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id ) {
			return 0;
		}

		if ( array_key_exists( $gallery_id, $this->gallery_images_count_cache ) ) {
			return $this->gallery_images_count_cache[ $gallery_id ];
		}

		$gallery_images = $this->get_gallery_images( $gallery_id );
		$count          = ! empty( $gallery_images ) ? count( $gallery_images ) : 0;

		$this->gallery_images_count_cache[ $gallery_id ] = $count;

		return $count;
	}

	/**
	 * Checks if a given image ID belongs to this gallery.
	 *
	 * Looks up the stored image ID list for the current gallery,
	 * then confirms whether the provided attachment ID is present.
	 *
	 * @since 1.6.0
	 *
	 * @param int $image_id The attachment ID to check.
	 *
	 * @return bool True if the image is in the gallery, false otherwise.
	 */
	public function image_is_in_gallery( $image_id ) {
		$gallery_images = $this->get_gallery_images();
		if ( is_array( $gallery_images ) && in_array( $image_id, $gallery_images, true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Checks whether the like icon is enabled for the gallery.
	 *
	 * Retrieves the 'sp_gallery_show_like_icon' meta field for the specified gallery and
	 * returns 1 if enabled or 0 if not. Returns 0 if the gallery is not found.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return int 1 if the like icon is enabled, 0 otherwise.
	 */
	public function get_show_like_icon( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return 0;
		}

		$status = get_post_meta( $gallery_id, 'sp_gallery_show_like_icon', true );
		return $status === '1' || $status === 1 || $status === true ? 1 : 0;
	}

	/**
	 * Checks whether the download icon is enabled for the gallery.
	 *
	 * Retrieves the 'sp_gallery_show_download_icon' meta field for the specified gallery and
	 * returns 1 if enabled or 0 if not. Returns 0 if the gallery is not found.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return int 1 if the download icon is enabled, 0 otherwise.
	 */
	public function get_show_download_icon( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return 0;
		}

		$status = get_post_meta( $gallery_id, 'sp_gallery_show_download_icon', true );
		return $status === '1' || $status === 1 || $status === true ? 1 : 0;
	}

	/**
	 * Checks whether full-size image downloads are enabled for the gallery.
	 *
	 * Retrieves the 'sp_gallery_download_fullsize_image' meta field for the specified gallery and
	 * returns 1 if enabled or 0 if not. Returns 0 if the gallery is not found.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return bool if full-size downloads are enabled, 0 otherwise.
	 */
	public function get_download_fullsize_image_status( $gallery_id = null ): bool {
		$gallery_id = absint( $gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return false;
		}

		$status = get_post_meta( $gallery_id, 'sp_gallery_download_fullsize_image', true );
		return $status;
	}

	/**
	 * Retrieves the gallery layout type for the specified gallery.
	 *
	 * Returns the value of the 'sp_gallery_layout' meta field for the given gallery ID.
	 * If not set, returns 'grid' as the default layout.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return string The layout type ('grid', 'masonry', 'justified', etc.).
	 */
	public function get_gallery_layout( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( get_post_meta( $gallery_id, 'sp_gallery_layout', true ) ) {
			return get_post_meta( $gallery_id, 'sp_gallery_layout', true );
		}
		return 'grid';
	}

	/**
	 * Retrieves the array of image IDs liked by the given user.
	 *
	 * Returns an array of attachment IDs the user has liked, based on the
	 * 'sp_gallery_liked_images' user meta field. Returns an empty array if
	 * the user does not exist or has not liked any images.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id The ID of the user whose liked images are to be retrieved.
	 *
	 * @return array Array of image IDs the user has liked, or an empty array if none.
	 */
	public static function get_liked_images( $user_id ) {
		$user_id = absint( $user_id );

		if ( ! $user_id || ! get_userdata( $user_id ) ) {
			return array();
		}

		$liked = get_user_meta( $user_id, 'sp_gallery_liked_images', true );
		return is_array( $liked ) ? $liked : array();
	}

	/**
	 * Retrieves the list of checked attachment IDs for the gallery.
	 *
	 * Returns an array of attachment IDs that have been checked or selected for the specified gallery.
	 * The IDs are read from the '_sp_gallery_checked_attachments' post meta field.
	 * Returns an empty array if the gallery does not exist or if there are no checked attachments.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return array Array of checked attachment IDs, or an empty array if none.
	 */
	public function get_checked_attachment_list( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return array();
		}

		$attachments = get_post_meta( $gallery_id, '_sp_gallery_checked_attachments', true );
		return is_array( $attachments ) ? $attachments : array();
	}

	/**
	 * Retrieves the number of images to display per page in the gallery.
	 *
	 * Returns the per-page image count for the specified gallery. If not set for the gallery,
	 * returns the value from the 'sp_gallery_images_per_page' option, or the object's default property.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return int The number of images to display per page.
	 */
	public function get_images_per_page( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return 50;
		}

		$result = get_option( 'sp_gallery_images_per_page', 50 );
		return null !== $result ? (int) $result : (int) $this->images_per_page;
	}

	/**
	 * Retrieves the image sorting type for the gallery.
	 *
	 * Returns the value of the 'sp_gallery_image_sorting' meta field for the given gallery ID.
	 * If not set or if the gallery does not exist, returns 'drag_n_drop' as the default.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The gallery ID. Defaults to the current gallery.
	 *
	 * @return string The sorting type, such as 'drag_n_drop', 'shoot_date_desc', 'filename_asc', etc.
	 */
	public function get_sort_type( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return 'drag_n_drop';
		}

		$result = get_post_meta( $gallery_id, 'sp_gallery_image_sorting', true );
		return $result ?? 'drag_n_drop';
	}

	/**
	 * Saves the provided image IDs to the gallery post's metadata.
	 *
	 * Updates the '_sp_gallery_images_array' post meta with the given array of image IDs.
	 *
	 * @since 1.3.4
	 *
	 * @param array    $images Array of image attachment IDs to be saved to the gallery.
	 * @param int|null $gallery_id Optional. The ID of the gallery post. Defaults to the current gallery.
	 *
	 * @return bool True on successful update, false on failure.
	 */
	public function update_gallery_images( $images, $gallery_id = null ) {
		$gallery_id = $gallery_id ? $gallery_id : $this->gallery_id;

		if ( ! is_array( $images ) ) {
			return false;
		}

		$images = array_filter( $images, 'is_numeric' );
		if ( empty( $images ) || ! get_post( $gallery_id ) ) {
			return false;
		}

		$updated = update_post_meta( $gallery_id, '_sp_gallery_images_array', $images ) !== false;

		if ( $updated ) {
			$this->gallery_images_cache[ $gallery_id ]       = $images;
			$this->gallery_images_count_cache[ $gallery_id ] = count( $images );
		}

		return $updated;
	}

	/**
	 * Sorts the given array of images based on the specified sorting option.
	 *
	 * @param array|string $images         The array of images to be sorted, or a comma-separated list of IDs.
	 * @param string       $sorting_option The sorting option to use, such as 'shoot_date_asc', 'filename_desc', etc.
	 *
	 * @return array|false The sorted array of images.
	 */
	public function sort_images_by_option( $images, string $sorting_option ) {
		if ( is_string( $images ) ) {
			$images = explode( ',', $images );
		}

		if ( ! is_array( $images ) ) {
			return false;
		}

		$images = array_values(
			array_filter(
				array_map(
					static function ( $item ) {
						if ( $item instanceof Shutterpress_Gallery_Image ) {
							return $item->get_id();
						}

						if ( is_numeric( $item ) ) {
							return (int) $item;
						}

						return null;
					},
					$images
				),
				static function ( $value ) {
					return null !== $value;
				}
			)
		);

		if ( empty( $images ) ) {
			return $images;
		}

		switch ( $sorting_option ) {
			case 'shoot_date_asc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_exif_capture_time' )
				);
				break;

			case 'shoot_date_desc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_exif_capture_time', true )
				);
				break;

			case 'filename_asc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_filename', false, 'string' )
				);
				break;

			case 'filename_desc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_filename', true, 'string' )
				);
				break;

			case 'file_date_asc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_file_creation_time' )
				);
				break;

			case 'file_date_desc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_file_creation_time', true )
				);
				break;

			case 'upload_date_asc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_file_upload_time' )
				);
				break;

			case 'upload_date_desc':
				usort(
					$images,
					$this->create_image_sort_callback( 'get_file_upload_time', true )
				);
				break;

			case 'drag_n_drop':
				
				break;

			default:
				
				break;
		}

		return $images;
	}

	/**
	 * Creates a comparator closure for sorting gallery images by an image accessor.
	 *
	 * @param string $method      Shutterpress_Gallery_Image method to invoke.
	 * @param bool   $descending  Whether to sort in descending order.
	 * @param string $comparison  Comparison mode: 'numeric' or 'string'.
	 *
	 * @return callable Closure suitable for usort comparisons.
	 */
	private function create_image_sort_callback( string $method, bool $descending = false, string $comparison = 'numeric' ): callable {
		return function ( $a, $b ) use ( $method, $descending, $comparison ) {
			$img_a = $a instanceof Shutterpress_Gallery_Image ? $a : new Shutterpress_Gallery_Image( $a );
			$img_b = $b instanceof Shutterpress_Gallery_Image ? $b : new Shutterpress_Gallery_Image( $b );

			if ( ! method_exists( $img_a, $method ) || ! method_exists( $img_b, $method ) ) {
				return 0;
			}

			$value_a = $img_a->{$method}();
			$value_b = $img_b->{$method}();

			if ( $comparison === 'string' ) {
				if ( ! $value_a || ! $value_b ) {
					return 0;
				}

				$result = strnatcasecmp( (string) $value_a, (string) $value_b );
			} else {
				$numeric_a = is_numeric( $value_a ) ? (int) $value_a : 0;
				$numeric_b = is_numeric( $value_b ) ? (int) $value_b : 0;
				$result    = $numeric_a <=> $numeric_b;
			}

			return $descending ? -$result : $result;
		};
	}

	/**
	 * Check if sorting is required for the given post based on 'sp_gallery_image_sorting' value.
	 *
	 * @param int $gallery_id The post ID.
	 *
	 * @return bool True if sorting is required; otherwise, false.
	 */
	public function requires_sorting( $gallery_id = null ) {
		$gallery_id = absint( $gallery_id ?? $this->gallery_id );

		if ( ! $gallery_id || ! get_post( $gallery_id ) ) {
			return false; 
		}

		$sorting = get_post_meta( $gallery_id, 'sp_gallery_image_sorting', true );
		return $sorting !== 'drag_n_drop';
	}

	/**
	 * Generates the HTML markup for gallery action buttons.
	 *
	 * Builds the HTML output for gallery action buttons, including like and download icons,
	 * as well as any additional buttons added via the 'sp_gallery_add_pro_buttons' filter.
	 * The styles and markup adjust based on the gallery type (e.g., block, elementor).
	 *
	 * @since 1.6.0
	 *
	 * @return string|void The HTML markup for the gallery buttons, or an empty string if no buttons are enabled.
	 */
	public function get_buttons() {
		if ( $this->show_buttons !== true ) {
			return;
		}

		$gallery_id = $this->gallery_id;

		$buttons_container_styles = '';
		$button_container_styles  = '';
		$button_styles            = '';

		if ( ! empty( $this->gallery_type ) ) {
			$prefix                   = ' sp-gallery-' . $this->gallery_type . '-';
			$buttons_container_styles = $prefix . 'buttons';
			$button_container_styles  = $prefix . 'button-container';
			$button_styles            = $prefix . 'button';
		}

		
		if ( ! empty( $this->gallery_type ) && $this->gallery_type === 'block' ) {
			$buttons_container_styles = ' wp-block-buttons' . $buttons_container_styles;
			$button_container_styles  = ' wp-block-button' . $button_container_styles;
			$button_styles            = ' wp-block-button__link' . $button_styles;
		}

		$buttons = array();
		$output  = '';
		

		$show_like_icon     = $this->show_like_icon;
		$show_download_icon = $this->show_download_icon;

		if ( $show_like_icon ) {
			$buttons[] = array(
				'id'    => 'sp-gallery-filter-liked-photos-' . intval( $gallery_id ),
				'label' => esc_html__( 'Show Favorite Photos', 'shutterpress-gallery' ),
			);
		}
		
		$buttons = apply_filters( 'sp_gallery_add_pro_buttons', $buttons, $gallery_id );

		
		if ( empty( $buttons ) ) {
			return $output;
		}

		$output .= '<div id="sp-gallery-buttons-' . absint( $gallery_id ) . '" class="sp-gallery-buttons' . esc_attr( $buttons_container_styles ) . ' ">';

		foreach ( $buttons as $button ) {
			$output .= '<div class="sp-gallery-button-container' . esc_attr( $button_container_styles ) . '">';

			
			$data_attrs  = '';
			$button_data = $button['data'] ?? array();
			if ( is_array( $button_data ) ) {
				foreach ( $button_data as $key => $val ) {
					$data_attrs .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
				}
			}

			$tag       = isset( $button['url'] ) ? 'a' : 'button';
			$href_attr = isset( $button['url'] ) ? ' href="' . esc_url( $button['url'] ) . '" download' : '';

			$output .= '<' . $tag . $href_attr . ' id="' . esc_attr( $button['id'] ) . '" class="sp-gallery-button' . esc_attr( $button_styles ) . '"' . $data_attrs . '>';
			$output .= esc_html( $button['label'] );
			$output .= '</' . $tag . '>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Generates and returns the full HTML markup for a gallery.
	 *
	 * Assembles the HTML output for the gallery, including the main container,
	 * responsive attributes, all image items, and required data attributes for layout,
	 * Lightbox, and pagination. Handles paging logic and pre-fetches attachment metadata
	 * for the current set of images. Returns a WP_Error if the gallery contains no images.
	 *
	 * @since 1.6.0
	 *
	 * @param int|null $gallery_id Optional. The ID of the gallery. Defaults to the current gallery.
	 * @param int      $page       Optional. Page number for pagination. Default is -1 (show all).
	 * @param bool     $wrapper    Optional. Whether to wrap the gallery in a container. Default is true.
	 *
	 * @return string|\WP_Error The HTML markup for the gallery, or WP_Error if no images found.
	 */
	public function get_the_gallery( $gallery_id = null, $page = -1, $wrapper = true ) {
		$gallery_id       = $gallery_id ?? $this->gallery_id;
		$this->gallery_id = (int) $gallery_id;

		$layout       = $this->layout;
		$gallery_type = $this->gallery_type;
		$page_counter = 'sp_page_' . $gallery_id;

		
		$class_list = array( 'sp-gallery', 'sp-gallery-hidden', 'sp-gallery-' . $layout . '-gallery', 'sp-gallery-' . $gallery_type . '-gallery', 'sp-gallery-icons-' . $this->icon_position );

		
		$style_vars = array(
			'--sp-gallery-icon-stroke-width' => $this->icon_stroke_width,
			'--sp-gallery-icon-bg-color'     => 'rgba(0, 0, 0, 0.5)',
		);

		$class_list[]    = $this->show_icons === 'on_hover' ? 'sp-gallery-icons-on-hover' : 'sp-gallery-icons-always';
		$total_images    = $this->get_gallery_images_count();
		$images_per_page = $this->images_per_page;
		$total_pages     = $images_per_page > 0 ? ceil( $total_images / $images_per_page ) : 1;
		$gallery_images  = $this->get_gallery_images();

		if ( empty( $gallery_images ) ) {
			return new WP_Error( 'no_images', esc_html__( 'No images found in this gallery.', 'shutterpress-gallery' ) );
		}

		$output = '';

		if ( $wrapper === true ) {
			$svg       = $this->get_cached_svg( SP_GALLERY_DIR . 'src/includes/images/' . $this->icons['download'] . '.svg' );
			$clean_svg = '';
			if ( $svg ) {
				$clean_svg = wp_kses( $svg, $this->utils->allowed_svg );
			}

			$colors = array(
				'primary' => $this->color_primary,
				'accent'  => $this->color_accent,
			);

			$breakpoints = array(
				'tablet' => (int) $this->breakpoint_tablet,
				'mobile' => (int) $this->breakpoint_mobile,
			);

			$sizes = array(
				'desktop' => $this->icon_size_desktop,
				'tablet'  => $this->icon_size_tablet,
				'mobile'  => $this->icon_size_mobile,
			);

				$data = array(
					'id'            => (int) $this->gallery_id,
					'layout'        => (string) $this->layout,
					'lightbox'      => (int) $this->use_lightbox,
					'likes'         => (int) $this->show_like_icon,
					'downloads'     => (int) $this->show_download_icon,
					'colors'        => wp_json_encode( $colors ),
					'gap'           => (int) $this->gap,
				'pages'         => (int) $total_pages,
				'breakpoints'   => wp_json_encode( $breakpoints ),
				'is'            => (int) $this->infinite_scroll,
				'pagination'    => (int) $this->pagination,
				'download-icon' => $clean_svg,
				'icon-size'     => wp_json_encode( $sizes ),
			);

			if ( $this->layout === 'masonry' || $this->layout === 'grid' ) {
				$columns         = array(
					'desktop' => (int) $this->columns_desktop,
					'tablet'  => (int) $this->columns_tablet,
					'mobile'  => (int) $this->columns_mobile,
				);
				$data['columns'] = wp_json_encode( $columns );
			}

			if ( $this->layout === 'justified' ) {
				$data['row-height'] = (int) $this->row_height;
			}

			if ( $this->use_lightbox === true ) {
				$lg              = array(
					'iconColor' => $this->lightbox_icon_color,
					'bgColor'   => $this->lightbox_bg_color,
				);
				$data['lg-mode'] = (string) $this->lightbox_mode;
				$data['lg']      = wp_json_encode( $lg );
			}

			if ( $this->rounded_corners ) {
				$class_list[]                             = ' sp-gallery-rounded';
				$style_vars['--sp-gallery-border-radius'] = $this->border_radius . 'px';
			}

			if ( $this->hover_grow_image === true ) {
				$class_list[] = 'sp-gallery-image-grow';
			}

			if ( $this->hover_float === true ) {
				$class_list[] = 'sp-gallery-float';

				switch ( $this->hover_float_shadow ) {
					case 'low':
						$style_vars['--sp-gallery-float-shadow'] = 'var(--sp-gallery-shadow-elevation-low)';
						$style_vars['--sp-gallery-float-scale']  = 'scale(1.03);';
						break;

					case 'medium':
						$style_vars['--sp-gallery-float-shadow'] = 'var(--sp-gallery-shadow-elevation-medium)';
						$style_vars['--sp-gallery-float-scale']  = 'scale(1.075);';
						break;

					case 'high':
						$style_vars['--sp-gallery-float-shadow'] = 'var(--sp-gallery-shadow-elevation-high)';
						$style_vars['--sp-gallery-float-scale']  = 'scale(1.1)';
						break;
				}
				$hsl_color                    = $this->utils->color_to_hsl_tokens_string( $this->float_shadow_color );
				$style_vars['--sp-gallery-shadow-color'] = $hsl_color;
			}
			if ( $this->hover_overlay === true ) {
				$class_list[]                             = 'sp-gallery-overlay';
				$style_vars['--sp-gallery-overlay-color'] = $this->hover_overlay_color;
			}

			if ( $this->show_hover_overlay_icon === true ) {
				$class_list[]                                  = 'sp-gallery-overlay-icon';
				$style_vars['--sp-gallery-overlay-icon-color'] = $this->hover_overlay_icon_color;
				$sizes               = array(
					'desktop' => $this->hover_icon_size_desktop,
					'tablet'  => $this->hover_icon_size_tablet,
					'mobile'  => $this->hover_icon_size_mobile,
				);
				$data['icon-size-h'] = wp_json_encode( $sizes );
			}

			if ( $this->show_hover_image_title === true ) {
				$class_list[] = 'sp-gallery-overlay-title';
			}

			if ( $this->show_hover_image_caption === true ) {
				$class_list[] = 'sp-gallery-overlay-caption';
			}

			$extra_css = apply_filters( 'sp_gallery_add_gallery_css', $this->attributes );
			if ( ! empty( $extra_css ) && is_string( $extra_css ) ) {
				$safe_css = wp_strip_all_tags( $extra_css, true );
				$safe_css = str_ireplace( array( '</style', '</script' ), array( '', '' ), $safe_css );
			}

			if ( ! empty( $this->custom_css ) || ! empty( $safe_css ) ) {
				$data['custom-css'] = '
';
				if ( ! empty( $this->custom_css ) ) {
					$data['custom-css'] .=
						$this->custom_css .
						' 
';
				}
				if ( ! empty( $safe_css ) ) {
					$data['custom-css'] .=
						$safe_css .
						'
';
				}
			}

			$class_list = apply_filters( 'sp_gallery_container_classes', $class_list, $this );
			
			$classes = trim( implode( ' ', array_filter( array_unique( $class_list ) ) ) );

			$style_vars = apply_filters( 'sp_gallery_container_style_vars', $style_vars, $this );
			
			$styles = '';
			foreach ( $style_vars as $var => $val ) {
				if ( $val === null || $val === '' ) {
					continue;
				}
				$styles .= sprintf( '%s: %s; ', trim( $var ), trim( (string) $val ) );
			}

				$attr_html = ' id="sp-gallery-' . absint( $gallery_id ) . '" class="' . esc_attr( $classes ) . '" style="' . esc_attr( $styles ) . '"';

				/**
				 * Filters gallery container data attributes before rendering.
				 *
				 * Use this to add or override `data-*` attributes on the main gallery wrapper.
				 * Keys should be provided without the `data-` prefix.
				 *
				 * @since 1.7.13
				 *
				 * @param array                      $data       Gallery data attributes.
				 * @param Shutterpress_Gallery_Gallery $gallery    Current gallery object.
				 * @param int                        $gallery_id Current gallery ID.
				 */
				$data = apply_filters( 'sp_gallery_container_data_attributes', $data, $this, (int) $gallery_id );

				foreach ( $data as $k => $v ) {
					if ( is_array( $v ) || ( is_object( $v ) && ! method_exists( $v, '__toString' ) ) ) {
						$v = wp_json_encode( $v );
					}

					$key = sanitize_key( (string) $k );
					if ( '' === $key ) {
						continue;
					}

					$attr_html .= ' data-' . $key . '="' . esc_attr( (string) $v ) . '"';
				}

			$output = '<div' . $attr_html . '>';
		}

		if ( $page === -1 ) {
			if ( $this->pagination === true ) {
				
				$page = isset( $_GET[ $page_counter ] ) ? max( 1, intval( $_GET[ $page_counter ] ) ) : 1; 
			} elseif ( $this->infinite_scroll === true ) {
				
				$page = 1;
			}
		}

		$paged_ids = $gallery_images;
		$per_page  = -1; 
		if ( $page !== null && $page > 0 ) {
			$per_page  = $this->images_per_page;
			$offset    = ( $page - 1 ) * $per_page;
			$paged_ids = array_slice( $gallery_images, $offset, $per_page );
		}

		$this->page         = $page;
		$this->page_counter = $page_counter;

		
		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post__in'       => $paged_ids,
				'orderby'        => 'post__in',
				'posts_per_page' => $per_page,
			)
		);

		$sizes =
			'(max-width: ' .
			$this->breakpoint_mobile .
			'px) calc(100vw / ' .
			$this->columns_mobile .
			'), ' .
			'(max-width: ' .
			$this->breakpoint_tablet .
			'px) calc(100vw / ' .
			$this->columns_tablet .
			'), ' .
			'calc(100vw / ' .
			$this->columns_desktop .
			')';

		

		$gallery_width = isset( $this->attributes['gallery_width'] ) ? (int) $this->attributes['gallery_width'] : 0;

		$viewport_width = isset( $this->attributes['viewport_width'] ) ? (int) $this->attributes['viewport_width'] : 0;
		$columns        = isset( $this->attributes['columns_desktop'] ) ? max( 1, (int) $this->attributes['columns_desktop'] ) : 1;

		if ( $gallery_width > 0 ) {
			if ( $viewport_width > 0 && $this->breakpoint_mobile > 0 && $this->breakpoint_tablet > 0 ) {
				$chosen_columns = $this->columns_desktop;

				if ( $viewport_width <= $this->breakpoint_tablet ) {
					$chosen_columns = $this->columns_tablet;
				}
				if ( $viewport_width <= $this->breakpoint_mobile ) {
					$chosen_columns = $this->columns_mobile;
				}

				$this->attributes['display_width'] = (int) floor( ( $gallery_width - ( $chosen_columns - 1 ) * $this->gap ) / $chosen_columns );
			}
		}

		$cap = isset( $this->attributes['display_width'] ) && $this->attributes['display_width'] > 0 ? (int) $this->attributes['display_width'] : 2048;

			$button_bar_output = '';
			$after_images_output  = '';

			$max_srcset_image_width_filter = static function () use ( $cap ) {
				return $cap;
			};

			try {
				add_filter( 'max_srcset_image_width', $max_srcset_image_width_filter );

				
				ob_start();
				do_action( 'sp_gallery_button_bar', (int) $gallery_id, $this, $attachments, $this->attributes );
				$button_bar_output = (string) ob_get_clean();

				foreach ( $attachments as $attachment ) {
					$image        = new Shutterpress_Gallery_Image( $attachment->ID );
					$item_classes = 'sp-gallery-item sp-gallery-' . $layout . '-item sp-gallery-' . $this->gallery_type . '-item sp-gallery-item-' . $attachment->ID;
					$output      .= $image->get_image_html( $gallery_id, $item_classes, $sizes, $this->attributes );
				}

				
				ob_start();
				do_action( 'sp_gallery_after_images', (int) $gallery_id, $this, $attachments, $this->attributes );
				$after_images_output = (string) ob_get_clean();
			} finally {
				remove_filter( 'max_srcset_image_width', $max_srcset_image_width_filter );
			}
			if ( $wrapper === true ) {
				$output .= '</div>';
			}
			$button_bar_content = $this->get_buttons() . $button_bar_output;
			$button_bar_data    = array(
				'gallery-id' => absint( $gallery_id ),
			);
			$button_bar_data    = apply_filters( 'sp_gallery_button_bar_data_attributes', $button_bar_data, $gallery_id, $this->template_id  );
			$button_bar_attrs   = '';
			foreach ( $button_bar_data as $key => $value ) {
				if ( '' === $value || null === $value ) {
					continue;
				}

				$button_bar_attrs .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
			$button_bar         = '';
			if ( '' !== trim( $button_bar_content ) ) {
				$button_bar = '<div id="sp-gallery-button-bar-' . absint( $gallery_id ) . '" class="sp-gallery-button-bar-container"' . $button_bar_attrs . '>' . $button_bar_content . '</div>';
			}

			$after_container = '';
			if ( '' !== trim( $after_images_output ) ) {
				$after_container = '<div id="sp-gallery-after-images-' . absint( $gallery_id ) . '" class="sp-gallery-after-images-container" data-gallery-id="' . absint( $gallery_id ) . '">' . $after_images_output . '</div>';
			}

			return $button_bar . $output . $after_container;
		}

	/**
	 * Reads an SVG file once per request and reuses it for later gallery renders.
	 *
	 * @param string $path Absolute path to the SVG file.
	 *
	 * @return string
	 */
	private function get_cached_svg( string $path ): string {
		if ( isset( self::$svg_cache[ $path ] ) ) {
			return self::$svg_cache[ $path ];
		}

		if ( ! is_readable( $path ) ) {
			self::$svg_cache[ $path ] = '';
			return self::$svg_cache[ $path ];
		}

		$svg = file_get_contents( $path ); 
		self::$svg_cache[ $path ] = is_string( $svg ) ? $svg : '';

		return self::$svg_cache[ $path ];
	}

	/**
	 * Builds inline CSS for a single gallery instance.
	 *
	 * Generates CSS custom properties and layout rules for the current gallery,
	 * including responsive icon sizes, grid or masonry rules, and LightGallery variables.
	 * The CSS is injected via wp_add_inline_style, and the complete CSS string is returned.
	 *
	 * @since 1.7.0
	 *
	 * @return string The CSS string that is added inline.
	 */
	public function add_template_styles() {
		$gallery_id = $this->gallery_id;

		$lg_gallery_css_id = '.lg-sp-gallery-' . $gallery_id;
		$output            = '';

		$output .= "
#sp-gallery-buttons-{$gallery_id}, #sp-gallery-{$gallery_id}, #sp-gallery-page-load-container-{$gallery_id}, #sp-gallery-pagination-{$gallery_id}, #sp-gallery-button-bar-{$gallery_id}, #sp-gallery-after-images-{$gallery_id}  {
    --sp-gallery-color-primary: {$this->color_primary};
    --sp-gallery-color-accent: {$this->color_accent};
}";

		$output .= "
/* Default for desktop */
#sp-gallery-{$gallery_id} {
    --sp-gallery-hover-icon-size: {$this->hover_icon_size_desktop}px;
    --sp-gallery-icon-size: {$this->icon_size_desktop}px;

}

/* For tablets (max-width: {$this->breakpoint_tablet}px) */
@media (max-width: {$this->breakpoint_tablet}px) {
    #sp-gallery-{$gallery_id} {
        --sp-gallery-hover-icon-size: {$this->hover_icon_size_tablet}px;
        --sp-gallery-icon-size: {$this->icon_size_tablet}px;
    }
}

/* For mobile (max-width: {$this->breakpoint_mobile}px) */
@media (max-width: {$this->breakpoint_mobile}px) {
    #sp-gallery-{$gallery_id} {
    --sp-gallery-hover-icon-size: {$this->hover_icon_size_mobile}px;
    --sp-gallery-icon-size: {$this->icon_size_mobile}px;
    }
}";

		$output .= "
{$lg_gallery_css_id} {
    --lg-icon-color: {$this->lightbox_icon_color};
    --lg-hover-color: {$this->color_primary};
    --lg-background-color: {$this->lightbox_bg_color};
}";

		if ( $this->layout === 'grid' ) {
			$base_selector = "#sp-gallery-{$gallery_id}.sp-gallery-grid-gallery";
			$output       .= "
{$base_selector} {
    display: grid;
    grid-template-columns: repeat({$this->columns_desktop}, 1fr);
    grid-gap: {$this->gap}px;
}
@media (max-width: {$this->breakpoint_tablet}px) {
    {$base_selector} { grid-template-columns: repeat({$this->columns_tablet}, 1fr); }
}
@media (max-width: {$this->breakpoint_mobile}px) {
    {$base_selector} { grid-template-columns: repeat({$this->columns_mobile}, 1fr); }
}";
		}

		if ( $this->layout === 'masonry' ) {
			$base_selector = "#sp-gallery-{$gallery_id}.sp-gallery-masonry-gallery";
			$output       .=
				"
{$base_selector} {
    display: grid;
    grid-template-columns: repeat({$this->columns_desktop}, 1fr);
    grid-gap: {$this->gap}px;
}
{$base_selector} .sp-gallery-masonry-item {
    margin-bottom: {$this->gap}px;
    width: calc((var(--sp-gallery-width) - " .
				$this->get_item_width( $this->columns_desktop ) .
				"px) / {$this->columns_desktop});
}
@media (max-width: {$this->breakpoint_tablet}px) {
    {$base_selector} {
        grid-template-columns: repeat({$this->columns_tablet}, 1fr);
    }
    {$base_selector} .sp-gallery-masonry-item { 
        width: calc((var(--sp-gallery-width) - " .
				$this->get_item_width( $this->columns_tablet ) .
				"px) / {$this->columns_tablet}); 
    }
}
@media (max-width: {$this->breakpoint_mobile}px) {
    {$base_selector} {
        grid-template-columns: repeat({$this->columns_mobile}, 1fr);
    }
    {$base_selector} .sp-gallery-masonry-item { 
        width: calc((var(--sp-gallery-width) - " .
				$this->get_item_width( $this->columns_mobile ) .
				"px) / {$this->columns_mobile}); 
    }    
}";
		}

		$extra_css = apply_filters( 'sp_gallery_add_gallery_css', $this->attributes );
		if ( ! empty( $extra_css ) && is_string( $extra_css ) ) {
			$safe_css = wp_strip_all_tags( $extra_css, true );
			$safe_css = str_ireplace( array( '</style', '</script' ), array( '', '' ), $safe_css );
			$output  .= "\n" . $safe_css . "\n";
		}

		if ( ! empty( $this->custom_css ) ) {
			$output .= "
#sp-gallery-{$gallery_id} {
";
			$output .= $this->custom_css;
			$output .= '
}';
		}

		return $output;
	}

	/**
	 * Calculates the total gap width for a given number of columns.
	 *
	 * Used in masonry and grid layouts to determine the available width for items,
	 * by subtracting the cumulative gutter space from the gallery container.
	 *
	 * @since 1.7.0
	 *
	 * @param int $columns Number of columns in the layout.
	 *
	 * @return int The total width taken up by gaps between columns.
	 */
	private function get_item_width( $columns ) {
		return ( $columns - 1 ) * $this->gap;
	}

	/**
	 * Generates the HTML markup for the infinite scroll loader UI.
	 *
	 * Returns the loader HTML including animated dots and a back-to-top button, only if
	 * the 'sp_gallery_use_infinite_scroll' option is enabled. If infinite scroll is not enabled,
	 * returns an empty string.
	 *
	 * @since 1.6.0
	 *
	 * @return string The HTML markup for the infinite scroll loader, or an empty string if disabled.
	 */
	public function get_infinite_scroll_loader() {
		if ( $this->infinite_scroll !== true ) {
			return '';
		}

		return '
        <div id="sp-gallery-page-load-container-' .
			absint( $this->gallery_id ) .
			'" class="sp-gallery-page-load-container">
            <div class="sp-gallery-page-load-status">
                <div class="infinite-scroll-request">
                    <div class="sp-gallery-loader">
                        <div class="dot dot1"></div>
                        <div class="dot dot2"></div>
                        <div class="dot dot3"></div>
                    </div>
                </div>
            <div id="sp-gallery-back-to-top" class="sp-gallery-back-to-top infinite-scroll-last" style="display: block; cursor: pointer; text-align: center; margin: 20px auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            </div>
            <p class="infinite-scroll-error">No more pages to load</p>
            </div>
            </div>';
	}

	/**
	 * Generates the HTML markup for gallery pagination links.
	 *
	 * Creates the pagination controls for the gallery, including previous, next, and numbered page links.
	 * Uses the current page, total images, and images per page to calculate the number of pages.
	 * Returns an empty string if neither infinite scroll nor pagination is enabled.
	 *
	 * @since 1.6.0
	 *
	 * @return string The HTML markup for the pagination controls, or an empty string if not enabled.
	 */
	public function get_gallery_pagination() {
		if ( $this->pagination !== true ) {
			return '';
		}
		$current_page    = $this->page;
		$page_counter    = $this->page_counter;
		$total_images    = $this->get_gallery_images_count();
		$images_per_page = $this->images_per_page;
		$total_pages     = $images_per_page > 0 ? ceil( $total_images / $images_per_page ) : 1;

		$output = '';

		if ( $total_pages > 1 ) {
			$output .= '<div id="sp-gallery-pagination-' . absint( $this->gallery_id ) . '" class="sp-gallery-pagination">';

			
			if ( $current_page > 1 ) {
				$prev_url = esc_url( add_query_arg( $page_counter, $current_page - 1 ) );
				$output  .= '<a href="' . $prev_url . '" class="prev">&raquo; ' . __( 'Prev', 'shutterpress-gallery' ) . '</a> ';
			}

			for ( $i = 1; $i <= $total_pages; $i++ ) {
				$url     = esc_url( add_query_arg( $page_counter, $i ) );
				$active  = $i === $current_page ? ' class="active"' : '';
				$output .= "<a href=\"{$url}\"{$active}>{$i}</a> ";
			}

			
			if ( $current_page < $total_pages ) {
				$next_url = esc_url( add_query_arg( $page_counter, $current_page + 1 ) );
				$output  .= '<a href="' . $next_url . '" class="next">' . __( 'Next', 'shutterpress-gallery' ) . ' &raquo;</a>';
			}

			$output .= '</div>';
		}
		return $output;
	}

	/**
	 * Merges template settings into defaults without overwriting with blank values.
	 *
	 * This method recursively merges template properties into the defaults array,
	 * but only accepts non-blank values (non-empty strings, non-null, and non-zero).
	 * This prevents template properties with default/empty values from overwriting
	 * plugin defaults, particularly for breakpoint values which default to 0 in templates
	 * but have meaningful defaults (782/480) in the plugin.
	 *
	 * @since 1.7.8
	 *
	 * @param array                                $defaults The base defaults array.
	 * @param Shutterpress_Gallery_Template|object $template The template object with properties to merge.
	 *
	 * @return array The merged defaults array.
	 */
	private function merge_template_defaults( array $defaults, $template ): array {
		
		$tpl_vars = get_object_vars( $template );

		
		$merge_nonblank = function ( $base, $incoming ) use ( &$merge_nonblank ) {
			if ( ! is_array( $base ) || ! is_array( $incoming ) ) {
				
				if ( $incoming === '' || $incoming === null || $incoming === 0 ) {
					return $base;
				}
				return $incoming;
			}
			
			foreach ( $base as $k => $v ) {
				if ( array_key_exists( $k, $incoming ) ) {
					$iv = $incoming[ $k ];
					if ( is_array( $v ) && is_array( $iv ) ) {
						$base[ $k ] = $merge_nonblank( $v, $iv );
					} elseif ( $iv !== '' && $iv !== null ) {
						$base[ $k ] = $iv;
					}
				}
			}
			
			foreach ( $incoming as $k => $iv ) {
				if ( array_key_exists( $k, $base ) ) {
					continue;
				}

				if ( is_array( $iv ) ) {
					$base[ $k ] = $merge_nonblank( array(), $iv );
					continue;
				}

				if ( $iv === '' || $iv === null ) {
					continue;
				}

				$base[ $k ] = $iv;
			}
			return $base;
		};

		
		$tpl_subset = array_intersect_key( $tpl_vars, $defaults );
		return $merge_nonblank( $defaults, $tpl_subset );
	}
}
