<?php
/**
 * Handles registration and configuration of meta boxes for ShutterPress Gallery.
 *
 * This class is responsible for registering and managing meta boxes that appear on the
 * gallery and template admin screens. It provides methods to register meta boxes for
 * galleries (such as image selection, sorting, and gallery options) and for gallery
 * templates (such as layout, color, icon, lightbox, hover, and custom CSS options).
 *
 * @since 1.7.0
 * @package Shutterpress\Gallery
 */

namespace Shutterpress\Gallery;

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Handles registration and configuration of meta boxes for ShutterPress Gallery.
 *
 * The Shutterpress_Gallery_Meta_Box class manages the creation and registration
 * of meta boxes used within the ShutterPress Gallery admin interface. It provides
 * configuration for both gallery and template screens, including fields for image
 * selection, gallery options, layout, colour, icons, hover effects, and custom CSS.
 *
 * It also retrieves default settings and defines utility methods for colour palettes
 * and field registration, integrating with the Meta Box framework.
 *
 * @since 1.7.0
 *
 * @package Shutterpress\Gallery
 *
 * @property string $meta_box_prefix Prefix applied to all meta box field IDs.
 * @property int|null $gallery_id Current gallery post ID.
 * @property object $defaults_class Instance of Shutterpress_Gallery_Defaults for retrieving default settings.
 * @property Shutterpress_Gallery_Debug $debug Debug logging utility.
 * @property Shutterpress_Gallery_Utils $utils Utility helper for global colour and configuration methods.
 * @property array $defaults Default gallery and template configuration values.
 */
class Shutterpress_Gallery_Meta_Box {

	protected string $meta_box_prefix;
	protected int $gallery_id;
	protected object $gallery;
	protected object $zip;
	protected object $defaults_class;
	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Utils $utils;
	public array $defaults;

	/**
	 * Class constructor.
	 *
	 * Initialises dependencies and sets up the meta box environment.
	 * Defines the meta box prefix if not already defined, retrieves the current gallery ID,
	 * and loads default settings, debugging, and utility helpers used across the admin meta boxes.
	 *
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! defined( 'SP_GALLERY_MB_PREFIX' ) ) {
			define( 'SP_GALLERY_MB_PREFIX', 'sp_gallery_' );
		}
		$this->meta_box_prefix = SP_GALLERY_MB_PREFIX;
		$this->gallery_id      = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : ( get_the_ID() ? intval( get_the_ID() ) : 0 );

		$this->defaults_class = new Shutterpress_Gallery_Defaults();
		$this->debug          = new Shutterpress_Gallery_Debug();
		$this->utils          = new Shutterpress_Gallery_Utils();
		$this->defaults       = $this->defaults_class->get_template_defaults();
	}

	/**
	 * Retrieves the meta box prefix used for ShutterPress Gallery Pro.
	 *
	 * This method returns the prefix string that is used to uniquely identify
	 * meta box fields associated with the ShutterPress Gallery Pro plugin.
	 *
	 * @since 1.7.0
	 *
	 * @return string The meta box prefix.
	 */
	public function get_meta_box_prefix() {
		return $this->meta_box_prefix;
	}

	/**
	 * Registers all meta boxes for ShutterPress Gallery screens.
	 *
	 * Adds the Images meta box and Gallery Options for the shutterpress-gallery post type,
	 * and registers the Template meta boxes for the sp_gallery_template post type. Returns
	 * the augmented meta box array so it can be consumed by the Meta Box framework.
	 *
	 * @since 1.7.0
	 *
	 * @param array $meta_boxes The existing meta boxes passed in by the filter.
	 *
	 * @return array The modified array including ShutterPress Gallery meta boxes.
	 */
	public function register_meta_boxes( $meta_boxes ) {
		$meta_boxes = $this->register_gallery_meta_box( $meta_boxes );
		$meta_boxes = $this->register_template_meta_boxes( $meta_boxes );

		return $meta_boxes;
	}

	/**
	 * Builds a small color palette for pickers.
	 *
	 * Starts with white and black, then appends any global colors discovered via
	 * Shutterpress_Gallery_Utils::get_global_colors. Duplicates are removed and the
	 * list is limited to eight entries for a compact UI palette.
	 *
	 * @since 1.7.0
	 *
	 * @return string[] Array of hex color strings, up to eight entries.
	 *
	 * @see Shutterpress_Gallery_Utils::get_global_colors()
	 */
	public function get_color_options(): array {
		$raw = $this->utils->get_global_colors();

		$palette = array( '#ffffff', '#000000' );

		foreach ( $raw as $item ) {
			$value = null;
			if ( is_array( $item ) && isset( $item['color'] ) ) {
				$value = $item['color'];
			} elseif ( is_object( $item ) && isset( $item->color ) ) {
				$value = $item->color;
			}

			if ( ! is_string( $value ) ) {
				continue;
			}

			$value = trim( $value );

			if ( $value !== '' && $value[0] !== '#' ) {
				$value = '#' . $value;
			}

			$is_hex  = preg_match( '/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value );
			$is_rgba = preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)$/', $value );

			if ( $is_hex || $is_rgba ) {
				$palette[] = strtolower( $value );
			}
		}

		$palette = array_values( array_unique( $palette ) );
		$palette = array_slice( $palette, 0, 8 );

		/**
		 * Filter the final palette if Pro or themes need to adjust it.
		 *
		 * @param string[] $palette
		 */
		return apply_filters( 'sp_gallery_color_palette', $palette );
	}

	/**
	 * Registers the meta boxes for the gallery admin page
	 *
	 * @since    1.0.0
	 *
	 * @param array $meta_boxes An array of meta boxes to register.
	 * @return array The modified array of meta boxes.
	 */
	public function register_gallery_meta_box( array $meta_boxes ): array {
		$prefix = $this->meta_box_prefix;

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Images', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'images-box' ),
			'context'    => 'normal',
			'post_types' => 'shutterpress-gallery',
			'fields'     => array(
				array(
					'type'     => 'custom_image_advanced',
					'id'       => sanitize_key( '_' . $prefix . 'images_array' ),
					'name'     => esc_html__( 'Images', 'shutterpress-gallery' ),
					'multiple' => true,
					'class'    => sanitize_key( $prefix . 'images-array' ),
				),
				array(
					'name'     => esc_html__( 'Sorting', 'shutterpress-gallery' ),
					'id'       => sanitize_key( $prefix . 'image_sorting' ),
					'type'     => 'select',
					'multiple' => false,
					'options'  => array(
						'drag_n_drop'      => esc_html__( 'Drag & Drop', 'shutterpress-gallery' ),
						'filename_asc'     => esc_html__( 'Filename Ascending', 'shutterpress-gallery' ),
						'filename_desc'    => esc_html__( 'Filename Descending', 'shutterpress-gallery' ),
						'shoot_date_asc'   => esc_html__( 'Shoot Time Ascending', 'shutterpress-gallery' ),
						'shoot_date_desc'  => esc_html__( 'Shoot Time Descending', 'shutterpress-gallery' ),
						'file_date_asc'    => esc_html__( 'Save Time Ascending', 'shutterpress-gallery' ),
						'file_date_desc'   => esc_html__( 'Save Time Descending', 'shutterpress-gallery' ),
						'upload_date_asc'  => esc_html__( 'Upload Time Ascending', 'shutterpress-gallery' ),
						'upload_date_desc' => esc_html__( 'Upload Time Descending', 'shutterpress-gallery' ),
					),
				),
			),
		);

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Gallery Options', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'options-box' ),
			'context'    => 'normal',
			'post_types' => 'shutterpress-gallery',
			'fields'     => array(
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Favorite Images', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'show_like_icon' ),
					'style' => 'rounded',
					'std'   => intval( 1 ),
					'desc'  => esc_html__( 'Choose whether to show the option to like each image. This shows the heart icon and the filter button.', 'shutterpress-gallery' ),
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Downloadable Images', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'show_download_icon' ),
					'style' => 'rounded',
					'std'   => intval( 1 ),
					'desc'  => esc_html__( 'Choose whether to make images individually downloadable. This shows the download icon on each image and in the lightbox.', 'shutterpress-gallery' ),
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Download Full-size Image', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'download_fullsize_image' ),
					'style' => 'rounded',
					'std'   => intval( 0 ),
					'desc'  => esc_html__(
						'Choose whether to download the original full-size image or the scaled image. WordPress automatically scales images larger than 2560px on either side.',
						'shutterpress-gallery'
					),
				),
				array(
					'name'        => esc_html__( 'Gallery Template', 'shutterpress-gallery' ),
					'id'          => sanitize_key( $prefix . 'template_id' ),
					'type'        => 'post',
					'post_type'   => 'sp_gallery_template',
					'field_type'  => 'select_advanced',
					'placeholder' => esc_html__( 'Select a Gallery Template', 'shutterpress-gallery' ),
					'query_args'  => array(
						'post_status'    => 'publish',
						'posts_per_page' => -1,
					),
				),
			),
		);

		if ( $this->gallery_id ) {
			$meta_boxes[] = array(
				'title'      => esc_html__( 'Gallery Shortcode', 'shutterpress-gallery' ),
				'id'         => sanitize_key( $prefix . 'shortcode-box' ),
				'context'    => 'side',
				'priority'   => 'low',
				'post_types' => 'shutterpress-gallery',
				'fields'     => array(
					array(
						'type' => 'custom_html',
						'std'  => '<p>' .
							esc_html__( 'Use this shortcode to display the gallery on the frontend:', 'shutterpress-gallery' ) .
							'</p>' .
							'<input type="text" readonly="readonly" value="[sp_gallery id=' .
							absint( $this->gallery_id ) .
							']" style="width: 100%;" />',
					),
				),
			);
		}

		return $meta_boxes;
	}

	/**
	 * Registers Template meta boxes for the sp_gallery_template post type.
	 *
	 * Adds layout options, color controls, columns and sizing, icon options, lightbox options,
	 * hover effects, and a Custom CSS panel. Returns the augmented array for the Meta Box framework.
	 *
	 * @since 1.7.0
	 *
	 * @param array $meta_boxes Incoming meta boxes from the filter.
	 *
	 * @return array Modified meta boxes including Template controls.
	 */
	public function register_template_meta_boxes( $meta_boxes ) {
		$prefix = $this->meta_box_prefix;

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Layout Options', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'layout_options' ),
			'context'    => 'normal',
			'post_types' => 'sp_gallery_template',
			'fields'     => array(
				array(
					'id'      => sanitize_key( $prefix . 'layout' ),
					'name'    => esc_html__( 'Gallery Layout', 'shutterpress-gallery' ),
					'type'    => 'custom_image_select',
					'options' => array(
						'grid'      => array(
							'img'   => SP_GALLERY_URL . 'src/includes/images/grid-icon.svg',
							'label' => __( 'Grid', 'shutterpress-gallery' ),
						),
						'masonry'   => array(
							'img'   => SP_GALLERY_URL . 'src/includes/images/masonry-icon.svg',
							'label' => __( 'Masonry', 'shutterpress-gallery' ),
						),
						'justified' => array(
							'img'   => SP_GALLERY_URL . 'src/includes/images/justified-icon.svg',
							'label' => __( 'Justified', 'shutterpress-gallery' ),
						),
					),
					'std'     => $this->defaults['layout'],
				),
				array(
					'type' => 'divider',
				),
				array(
					'name'          => esc_html__( 'Primary Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'color_primary' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['color_primary'],
					'js_options'    => array(
						'palettes' => $this->get_color_options(),
					),
					'desc'          => esc_html__( 'This color is used for buttons and icons, and some other ui elements.', 'shutterpress-gallery' ),
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Pagination', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'pagination' ),
					'style' => 'rounded',
					'std'   => $this->defaults['pagination'],
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Infinite Scroll', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'infinite_scroll' ),
					'style' => 'rounded',
					'std'   => $this->defaults['infinite_scroll'],
					'desc'  => esc_html__(
						'Controls how images load in large galleries. Infinite scroll replaces pagination, but pagination is still used if multiple galleries are on the same page or when editing in Gutenberg or Elementor.',
						'shutterpress-gallery'
					),
				),

				array(
					'type' => 'divider',
				),
				array(
					'name'   => esc_html__( 'Image Gap', 'shutterpress-gallery' ),
					'id'     => sanitize_key( $prefix . 'gap' ),
					'type'   => 'number',
					'min'    => 0,
					'class'  => 'sp-gallery-template-number',
					'std'    => $this->defaults['gap'],
					'append' => 'px',
				),
				array(
					'name'   => esc_html__( 'Row Height', 'shutterpress-gallery' ),
					'id'     => sanitize_key( $prefix . 'row_height' ),
					'type'   => 'number',
					'min'    => 1,
					'class'  => 'sp-gallery-template-number',
					'std'    => $this->defaults['row_height'],
					'append' => 'px',
					'before' => '<div id="sp-gallery-justified-options">',
					'after'  => '</div>',
				),

				array(
					'id'      => sanitize_key( $prefix . 'columns' ),
					'name'    => esc_html__( 'Columns', 'shutterpress-gallery' ),
					'type'    => 'custom_fieldset_text',
					'min'     => 1,
					'options' => array(
						'desktop' => esc_html__( 'Desktop', 'shutterpress-gallery' ),
						'tablet'  => esc_html__( 'Tablet', 'shutterpress-gallery' ),
						'mobile'  => esc_html__( 'Mobile', 'shutterpress-gallery' ),
					),
					'std'     => array(
						'desktop' => $this->defaults['columns']['desktop'],
						'tablet'  => $this->defaults['columns']['tablet'],
						'mobile'  => $this->defaults['columns']['mobile'],
					),
					'append'  => array(
						'desktop' => 'px',
						'tablet'  => 'px',
						'mobile'  => 'px',
					),

					'class'   => 'sp-gallery-three-column',
					'before'  => '<div id="sp-gallery-grid-options">',
					'after'   => '</div>',
				),
				array(
					'type' => 'divider',
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Rounded Corners', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'rounded_corners' ),
					'style' => 'rounded',
					'std'   => $this->defaults['rounded_corners'],
				),
				array(
					'name'   => esc_html__( 'Corner Radius', 'shutterpress-gallery' ),
					'id'     => sanitize_key( $prefix . 'border_radius' ),
					'type'   => 'number',
					'min'    => 0,
					'class'  => 'sp-gallery-template-number',
					'std'    => $this->defaults['border_radius'],
					'append' => 'px',
					'before' => '<div id="sp-gallery-rounded-corner-options">',
					'after'  => '</div>',
				),
			),
		);

		$download_icon_options = array(
			'download'            => array( 'img' => SP_GALLERY_URL . 'src/includes/images/download.svg' ),
			'cloud-download'      => array( 'img' => SP_GALLERY_URL . 'src/includes/images/cloud-download.svg' ),
			'arrow-down-to-line'  => array( 'img' => SP_GALLERY_URL . 'src/includes/images/arrow-down-to-line.svg' ),
			'image-down'          => array( 'img' => SP_GALLERY_URL . 'src/includes/images/image-down.svg' ),
			'hard-drive-download' => array( 'img' => SP_GALLERY_URL . 'src/includes/images/hard-drive-download.svg' ),
		);

		$download_icon_options = apply_filters( 'sp_gallery_download_icon_options', $download_icon_options );

		$icons = array(
			array(
				'id'           => sanitize_key( $prefix . 'icons' ),
				'icon_name'    => 'download',
				'name'         => esc_html__( 'Download Icon', 'shutterpress-gallery' ),
				'type'         => 'custom_image_select',
				'options'      => $download_icon_options,
				'std'          => $this->defaults['icons']['download'],
				'save_default' => true,
				'multiple'     => false,
				'clone'        => false,
			),
		);

		$icons = apply_filters( 'sp_gallery_icon_fields', $icons, $this->gallery_id );

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Icon Options', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'template_icon_options' ),
			'context'    => 'normal',
			'post_types' => 'sp_gallery_template',
			'fields'     => array_merge(
				array(
					array(
						'id'      => sanitize_key( $prefix . 'icon_size' ),
						'name'    => esc_html__( 'Icon Size', 'shutterpress-gallery' ),
						'type'    => 'custom_fieldset_text',
						'min'     => 1,
						'options' => array(
							'desktop' => esc_html__( 'Desktop', 'shutterpress-gallery' ),
							'tablet'  => esc_html__( 'Tablet', 'shutterpress-gallery' ),
							'mobile'  => esc_html__( 'Mobile', 'shutterpress-gallery' ),
						),
						'std'     => array(
							'desktop' => $this->defaults['icon_size']['desktop'],
							'tablet'  => $this->defaults['icon_size']['tablet'],
							'mobile'  => $this->defaults['icon_size']['mobile'],
						),
						'append'  => array(
							'desktop' => 'px',
							'tablet'  => 'px',
							'mobile'  => 'px',
						),
						'class'   => 'sp-gallery-three-column',
					),
					array(
						'id'      => sanitize_key( $prefix . 'show_icons' ),
						'name'    => esc_html__( 'Show Icons', 'shutterpress-gallery' ),
						'type'    => 'radio',
						'inline'  => true,
						'options' => array(
							'always'   => esc_html__( 'Always', 'shutterpress-gallery' ),
							'on_hover' => esc_html__( 'On Hover', 'shutterpress-gallery' ),
						),
						'std'     => $this->defaults['show_icons'],
					),
					array(
						'name'       => esc_html__( 'Stroke Width', 'shutterpress-gallery' ),
						'id'         => sanitize_key( $prefix . 'icon_stroke_width' ),
						'type'       => 'slider',
						'js_options' => array(
							'min'  => 0.5,
							'max'  => 4,
							'step' => 0.5,
						),
						'class'      => 'sp-gallery-template-number',
						'std'        => $this->defaults['icon_stroke_width'],
						'append'     => 'px',
					),
					array(
						'name'    => esc_html__( 'Icon Position', 'shutterpress-gallery' ),
						'id'      => $prefix . 'icon_position',
						'type'    => 'button_group',
						'class'   => 'sp-gallery-icon-position',
						'options' => array(
							'tl' => '<i class="dashicons dashicons-arrow-up-alt tl"></i>',
							'tc' => '<i class="dashicons dashicons-arrow-up-alt"></i>',
							'tr' => '<i class="dashicons dashicons-arrow-up-alt tr"></i>',
							'ml' => '<i class="dashicons dashicons-arrow-left-alt"></i>',
							'mc' => '<i class="dashicons dashicons-plus-alt2"></i>',
							'mr' => '<i class="dashicons dashicons-arrow-right-alt"></i>',
							'bl' => '<i class="dashicons dashicons-arrow-down-alt bl"></i>',
							'bc' => '<i class="dashicons dashicons-arrow-down-alt"></i>',
							'br' => '<i class="dashicons dashicons-arrow-down-alt br"></i>',
						),
						'inline'  => false,
						'std'     => $this->defaults['icons']['download'],
					),
				),
				$icons
			),
		);

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Lightbox Options', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'template_lightbox_options' ),
			'context'    => 'normal',
			'post_types' => 'sp_gallery_template',
			'fields'     => array(
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Lightbox', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'lightbox' ),
					'style' => 'rounded',
					'std'   => $this->defaults['use_lightbox'],
					'class' => 'sp-gallery-use-lightbox',
				),
				array(
					'name'          => esc_html__( 'Background Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'lightbox_bg_color' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['lightbox_bg_color'],
					'js_options'    => array(
						'palettes' => $this->get_color_options(),
					),
					'before'        => '<div id="sp-gallery-lightbox-options">',
				),
				array(
					'name'          => esc_html__( 'Icon Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'lightbox_icon_color' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['lightbox_icon_color'],
					'js_options'    => array(
						'palettes' => $this->get_color_options(),
					),
				),

				array(
					'name'    => esc_html__( 'Transition style', 'shutterpress-gallery' ),
					'id'      => sanitize_key( $prefix . 'lightbox_mode' ),
					'type'    => 'radio',
					'inline'  => true,
					'options' => array(
						'lg-slide' => esc_html__( 'Slide', 'shutterpress-gallery' ),
						'lg-fade'  => esc_html__( 'Fade', 'shutterpress-gallery' ),
					),
					'std'     => $this->defaults['lightbox_mode'],
					'after'   => '</div>',
				),
			),
		);

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Hover Effects', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'template_hover_options' ),
			'context'    => 'normal',
			'post_types' => 'sp_gallery_template',
			'fields'     => array(
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Grow Image', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'hover_grow_image' ),
					'style' => 'rounded',
					'std'   => $this->defaults['hover_grow_image'],
				),
				array(
					'type' => 'divider',
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Float Image', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'hover_float' ),
					'style' => 'rounded',
					'std'   => $this->defaults['hover_float'],
				),
				array(
					'id'      => sanitize_key( $prefix . 'hover_float_shadow' ),
					'name'    => esc_html__( 'Height', 'shutterpress-gallery' ),
					'type'    => 'radio',
					'inline'  => true,
					'options' => array(
						'low'    => esc_html__( 'Low', 'shutterpress-gallery' ),
						'medium' => esc_html__( 'Medium', 'shutterpress-gallery' ),
						'high'   => esc_html__( 'High', 'shutterpress-gallery' ),
					),
					'std'     => $this->defaults['hover_float_shadow'],
					'before'  => '<div id="sp-gallery-lightbox-float-options">',
				),
				array(
					'name'          => esc_html__( 'Shadow Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'float_shadow_color' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['float_shadow_color'],
					'after'         => '</div>',
				),
				array(
					'type' => 'divider',
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Overlay Color', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'hover_overlay' ),
					'style' => 'rounded',
					'std'   => $this->defaults['hover_overlay'],
				),
				array(
					'name'          => esc_html__( 'Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'hover_overlay_color' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['hover_overlay_color'],
					'js_options'    => array(
						'palettes' => $this->get_color_options(),
					),
					'before'        => '<div id="sp-gallery-color-overlay-options">',
					'after'         => '</div>',
				),
				array(
					'type' => 'divider',
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Overlay Icon', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'show_hover_overlay_icon' ),
					'style' => 'rounded',
					'std'   => $this->defaults['show_hover_overlay_icon'],
				),
				array(
					'id'      => sanitize_key( $prefix . 'hover_overlay_icon' ),
					'name'    => esc_html__( 'Overlay Icon', 'shutterpress-gallery' ),
					'type'    => 'custom_image_select',
					'options' => array(
						'search'             => array( 'img' => SP_GALLERY_URL . 'src/includes/images/search.svg' ),
						'circle-fading-plus' => array( 'img' => SP_GALLERY_URL . 'src/includes/images/circle-fading-plus.svg' ),
						'circle-plus'        => array( 'img' => SP_GALLERY_URL . 'src/includes/images/circle-plus.svg' ),
						'diamond-plus'       => array( 'img' => SP_GALLERY_URL . 'src/includes/images/diamond-plus.svg' ),
						'eye'                => array( 'img' => SP_GALLERY_URL . 'src/includes/images/eye.svg' ),
						'plus'               => array( 'img' => SP_GALLERY_URL . 'src/includes/images/plus.svg' ),
						'square-plus'        => array( 'img' => SP_GALLERY_URL . 'src/includes/images/square-plus.svg' ),
						'zoom-in'            => array( 'img' => SP_GALLERY_URL . 'src/includes/images/zoom-in.svg' ),
					),
					'std'     => $this->defaults['hover_overlay_icon'],
					'before'  => '<div id="sp-gallery-icon-overlay-options">',
				),
				array(
					'name'          => esc_html__( 'Icon Color', 'shutterpress-gallery' ),
					'id'            => sanitize_key( $prefix . 'hover_overlay_icon_color' ),
					'type'          => 'color',
					'alpha_channel' => true,
					'std'           => $this->defaults['hover_overlay_icon_color'],
					'js_options'    => array(
						'palettes' => $this->get_color_options(),
					),
				),
				array(
					'id'      => sanitize_key( $prefix . 'hover_icon_size' ),
					'name'    => esc_html__( 'Icon Size', 'shutterpress-gallery' ),
					'type'    => 'custom_fieldset_text',
					'min'     => 1,
					'options' => array(
						'desktop' => esc_html__( 'Desktop', 'shutterpress-gallery' ),
						'tablet'  => esc_html__( 'Tablet', 'shutterpress-gallery' ),
						'mobile'  => esc_html__( 'Mobile', 'shutterpress-gallery' ),
					),
					'std'     => array(
						'desktop' => $this->defaults['hover_icon_size']['desktop'],
						'tablet'  => $this->defaults['hover_icon_size']['tablet'],
						'mobile'  => $this->defaults['hover_icon_size']['mobile'],
					),
					'append'  => array(
						'desktop' => 'px',
						'tablet'  => 'px',
						'mobile'  => 'px',
					),
					'class'   => 'sp-gallery-three-column',
					'after'   => '</div>',
				),
				array(
					'type' => 'divider',
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Show Image Title', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'show_hover_image_title' ),
					'style' => 'rounded',
					'std'   => $this->defaults['show_hover_image_title'],
				),
				array(
					'type'  => 'switch',
					'name'  => esc_html__( 'Show Image Caption', 'shutterpress-gallery' ),
					'id'    => sanitize_key( $prefix . 'show_hover_image_caption' ),
					'style' => 'rounded',
					'std'   => $this->defaults['show_hover_image_caption'],
				),
			),
		);

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Custom CSS', 'shutterpress-gallery' ),
			'id'         => sanitize_key( $prefix . 'template_custom_css' ),
			'context'    => 'normal',
			'post_types' => 'sp_gallery_template',

			'fields'     => array(
				array(
					'type'   => 'code',
					'name'   => esc_html__( 'Custom CSS', 'shutterpress-gallery' ),
					'id'     => sanitize_key( $prefix . 'custom_css' ),
					'mode'   => 'text/css',
					'height' => '300px',
					'desc'   => esc_html__( 'Custom CSS is placed inside the class .sp-gallery and will only affect the current gallery template', 'shutterpress-gallery' ),
				),
			),
		);

		return $meta_boxes;
	}
}
