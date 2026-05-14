<?php
/**
 * Elementor widget for ShutterPress Gallery.
 *
 * Provides a visual widget to insert ShutterPress galleries in Elementor. Registers
 * controls for layout, lightbox, gaps, justified row height, and responsive columns,
 * maps settings to the gallery renderer, and outputs the live preview in the editor.
 *
 * @link      https://shutterpress.io
 * @since     1.0.0
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @author    Shutterpress <info@shutterpress.io>
 */

namespace Elementor;

use Shutterpress\Gallery\Shutterpress_Gallery_Debug;
use Shutterpress\Gallery\Shutterpress_Gallery_Defaults;
use Shutterpress\Gallery\Shutterpress_Gallery_Gallery;
use Shutterpress\Gallery\Shutterpress_Gallery_Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Elementor integration widget for ShutterPress Gallery.
 *
 * Registers controls, resolves responsive breakpoints, prepares gallery attributes,
 * and delegates rendering to the core gallery class. Supports editor preview and
 * dynamic content. Returns a stable widget name and icon for the Elementor panel.
 *
 * @since 1.0.0
 */
class Shutterpress_Gallery_Elementor_Widget extends Widget_Base {

	public array $gallery_attributes = array();
	public int $gallery_id;
	public array $defaults;
	public bool $use_lightbox;
	public string $layout;
	public int $gap;
	public int $row_height;
	public bool $show_buttons;
	public int $columns_desktop;
	public int $columns_tablet;
	public int $columns_mobile;
	public int $breakpoint_tablet;
	public int $breakpoint_mobile;
	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Utils $utils;
	protected Shutterpress_Gallery_Defaults $defaults_class;
	private array $renderers = array();

	/**
	 * Class constructor.
	 *
	 * Sets up debug, utilities, and defaults helpers for the Elementor widget,
	 * preparing it to render ShutterPress galleries in the editor and front end.
	 *
	 * @since 1.0.0
	 * @param array $data  The widget data.
	 * @param mixed $args  The widget arguments.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		$this->debug          = new Shutterpress_Gallery_Debug();
		$this->utils          = new Shutterpress_Gallery_Utils();
		$this->defaults_class = new Shutterpress_Gallery_Defaults();
	}



	/**
	 * Gets the unique widget name used by Elementor.
	 *
	 * @since 1.0.0
	 * @return string Widget slug.
	 */
	public function get_name() {
		return 'shutterpress_gallery';
	}

	/**
	 * Gets the human readable widget title shown in the Elementor panel.
	 *
	 * @since 1.0.0
	 * @return string Widget title.
	 */
	public function get_title() {
		return 'ShutterPress Gallery';
	}

	/**
	 * Gets the icon name displayed for the widget in Elementor.
	 *
	 * @since 1.0.0
	 * @return string Icon slug from the Elementor icon set.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Gets the Elementor categories this widget belongs to.
	 *
	 * @since 1.0.0
	 * @return array List of category slugs.
	 */
	public function get_categories() {
		return array( 'shutterpress' );
	}

	/**
	 * Gets search keywords that help users find the widget.
	 *
	 * @since 1.0.0
	 * @return array List of keyword strings.
	 */
	public function get_keywords() {
		return array( 'gallery', 'masonry', 'lightbox', 'shutterpress' );
	}

	/**
	 * Gets the custom help URL for this widget.
	 *
	 * @since 1.0.0
	 * @return string Absolute URL to the help page.
	 */
	public function get_custom_help_url() {
		return esc_url( 'https://shutterpress.io' );
	}

	/**
	 * Gets data used by Elementor to show any upgrade prompts.
	 *
	 * Returns an empty array when there is no upsell in this context.
	 *
	 * @since 1.0.0
	 * @return array Upsell data structure.
	 */
	protected function get_upsale_data() {
		return array();
	}

	/**
	 * Declares script handles that must be enqueued for this widget.
	 *
	 * @since 1.0.0
	 * @return array List of WordPress script handles.
	 */
	public function get_script_depends() {
		return array( 'shutterpress-gallery-elementor', 'imagesloaded' );
	}

	/**
	 * Declares style handles that must be enqueued for this widget.
	 *
	 * @since 1.0.0
	 * @return array List of WordPress style handles.
	 */
	public function get_style_depends() {
		return array( 'shutterpress-gallery-public', 'lightgallery-css', 'elementor-frontend' );
	}

	/**
	 * Registers Elementor controls for the ShutterPress Gallery widget.
	 *
	 * Adds controls for gallery selection, lightbox toggle, default buttons,
	 * layout choice, gap size, justified row height, and responsive columns.
	 * Values are sanitised and defaults are loaded from the template defaults.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function register_controls() {

		$this->defaults = $this->defaults_class->get_template_defaults();

		$this->start_controls_section(
			'sp_gallery_section',
			array(
				'label' => esc_html__( 'Gallery', 'shutterpress-gallery' ),

				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'sp_gallery_id',
			array(
				'label'       => esc_html__( 'Gallery', 'shutterpress-gallery' ),

				'type'        => \Elementor\Controls_Manager::SELECT2,

				'options'     => $this->sp_gallery_get_galleries(),
				'dynamic'     => array(
					'active' => true,
				),
				'description' => esc_html__( 'Uses the default post gallery if no gallery is selected.', 'shutterpress-gallery' ),
			)
		);

		$lightbox_default = $this->defaults['use_lightbox'] ? 'yes' : 'no';

		$this->add_control(
			'sp_gallery_use_lightbox',
			array(
				'label'     => esc_html__( 'Use Lightbox', 'shutterpress-gallery' ),

				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => in_array( $lightbox_default, array( 'yes', 'no' ), true ) ? $lightbox_default : 'yes',
				'label_on'  => esc_html__( 'Yes', 'shutterpress-gallery' ),
				'label_off' => esc_html__( 'No', 'shutterpress-gallery' ),
			)
		);

		$description = sprintf(
			wp_kses_post(
				__(
					'If you want to add custom buttons, create a button and set the Button ID to "sp-gallery-filter-liked-photos-{{Gallery_id}}". See <a href="%s">Shutterpress.io</a> for more info.',
					'shutterpress-gallery'
				)
			),
			esc_url( 'https://shutterpress.io' )
		);

		$this->add_control(
			'sp_gallery_show_default_buttons',
			array(
				'label'       => esc_html__( 'Use Default Buttons', 'shutterpress-gallery' ),

				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'label_on'    => esc_html__( 'Yes', 'shutterpress-gallery' ),
				'label_off'   => esc_html__( 'No', 'shutterpress-gallery' ),
				'description' => $description,
			)
		);

		$this->add_control(
			'sp_gallery_layout',
			array(
				'label'   => esc_html__( 'Layout', 'shutterpress-gallery' ),

				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => sanitize_text_field( $this->defaults['layout'] ),
				'options' => array(
					'grid'      => esc_html__( 'Grid', 'shutterpress-gallery' ),
					'masonry'   => esc_html__( 'Masonry', 'shutterpress-gallery' ),
					'justified' => esc_html__( 'Justified', 'shutterpress-gallery' ),
				),
			)
		);

		$this->add_control(
			'sp_gallery_gap',
			array(
				'label'      => esc_html__( 'Image Gap', 'shutterpress-gallery' ),

				'type'       => \Elementor\Controls_Manager::SLIDER,
				'default'    => array(
					'size' => absint( $this->defaults['gap'] ),
				),
				'range'      => array(
					'px' => array(

						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
				'dynamic'    => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'sp_gallery_row_height',
			array(
				'label'      => esc_html__( 'Row Height', 'shutterpress-gallery' ),

				'type'       => \Elementor\Controls_Manager::SLIDER,
				'default'    => array(
					'size' => absint( $this->defaults['row_height'] ),
				),
				'range'      => array(
					'px' => array(

						'min' => 0,
						'max' => 1000,
					),
				),
				'size_units' => array( 'px' ),
				'dynamic'    => array(
					'active' => true,
				),
				'condition'  => array(
					'sp_gallery_layout' => 'justified',
				),
			)
		);

		$this->add_responsive_control(
			'sp_gallery_columns',
			array(
				'label'          => esc_html__( 'Number of Columns', 'shutterpress-gallery' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => intval( $this->defaults['columns']['desktop'] ),
				'tablet_default' => intval( $this->defaults['columns']['tablet'] ),
				'mobile_default' => intval( $this->defaults['columns']['mobile'] ),
				'options'        => array(
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
				),
				'condition'      => array(
					'sp_gallery_layout' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Override get_data to coerce settings to array.
	 *
	 * @param string|null $property     Property to get data for.
	 * @return mixed
	 */
	public function get_data( $property = null ) {
		$data = parent::get_data( $property );
		if ( $property === 'settings' ) {
			return is_array( $data ) ? $data : array();
		}
		if ( $property === null ) {
			if ( ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
				$data['settings'] = array();
			}
		}
		return $data;
	}



	/**
	 * Renders the gallery for the Elementor widget.
	 *
	 * Collects settings, resolves defaults and breakpoints, builds attributes,
	 * and uses the core gallery renderer to output buttons, gallery, loader,
	 * and pagination. Accepts an optional renderer for testing.
	 *
	 * @since 1.0.0
	 * @param \Shutterpress\Gallery\Shutterpress_Gallery_Gallery|null $renderer Optional renderer, mainly for tests.
	 * @return void Outputs HTML directly.
	 */
	protected function render( ?\Shutterpress\Gallery\Shutterpress_Gallery_Gallery $renderer = null ) {
		$this->defaults = $this->defaults_class->get_template_defaults();

		$settings         = $this->get_settings_for_display();
		$this->gallery_id = isset( $settings['sp_gallery_id'] ) && $settings['sp_gallery_id'] !== '0' ? intval( $settings['sp_gallery_id'] ) : ( get_the_ID() ? intval( get_the_ID() ) : 0 );

		$use_lightbox = $settings['sp_gallery_use_lightbox'] === 'yes';
		$show_buttons = $settings['sp_gallery_show_default_buttons'] === 'yes';

		$elementor_breakpoints       = \Elementor\Plugin::$instance->breakpoints->get_breakpoints_config();
		$elementor_mobile_breakpoint = isset( $elementor_breakpoints['mobile']['value'] ) ? intval( $elementor_breakpoints['mobile']['value'] ) : 480;
		$elementor_tablet_breakpoint = isset( $elementor_breakpoints['tablet']['value'] ) ? intval( $elementor_breakpoints['tablet']['value'] ) : 768;

		$this->use_lightbox = $use_lightbox;
		$this->layout       = isset( $settings['sp_gallery_layout'] ) ? esc_attr( $settings['sp_gallery_layout'] ) : 'grid';
		$this->gap          = isset( $settings['sp_gallery_gap']['size'] ) ? intval( $settings['sp_gallery_gap']['size'] ) : $this->defaults['gap'];
		$this->row_height   = isset( $settings['sp_gallery_row_height']['size'] ) ? intval( $settings['sp_gallery_row_height']['size'] ) : $this->defaults['row_height'];
		$this->show_buttons = $show_buttons;

		$this->columns_desktop = isset( $settings['sp_gallery_columns'] ) && ! empty( $settings['sp_gallery_columns'] ) ? intval( $settings['sp_gallery_columns'] ) : $this->defaults['columns']['desktop'];

		$this->columns_tablet = isset( $settings['sp_gallery_columns_tablet'] ) && ! empty( $settings['sp_gallery_columns_tablet'] ) ? intval( $settings['sp_gallery_columns_tablet'] ) : $this->defaults['columns']['tablet'];

		$this->columns_mobile = isset( $settings['sp_gallery_columns_mobile'] ) && ! empty( $settings['sp_gallery_columns_mobile'] ) ? intval( $settings['sp_gallery_columns_mobile'] ) : $this->defaults['columns']['mobile'];

		$columns = array(
			'desktop' => $this->columns_desktop,
			'tablet'  => $this->columns_tablet,
			'mobile'  => $this->columns_mobile,
		);

		$this->breakpoint_tablet = $elementor_tablet_breakpoint;
		$this->breakpoint_mobile = $elementor_mobile_breakpoint;

		$this->gallery_attributes = array(
			'gallery_id'        => $this->gallery_id,
			'use_lightbox'      => $this->use_lightbox,
			'layout'            => $this->layout,
			'gap'               => $this->gap,
			'row_height'        => $this->row_height,
			'show_buttons'      => $this->show_buttons,
			'columns'           => $columns,
			'breakpoint_tablet' => $this->breakpoint_tablet,
			'breakpoint_mobile' => $this->breakpoint_mobile,
			'gallery_type'      => 'elementor',
		);

		$gallery_id = $this->gallery_id ? $this->gallery_id : null;

		if ( ! $renderer ) {
			if ( ! isset( $this->renderers[ $gallery_id ] ) ) {
				$this->renderers[ $gallery_id ] = new Shutterpress_Gallery_Gallery( $gallery_id );
			}
			$renderer = $this->renderers[ $gallery_id ];
		}

		assert( $renderer instanceof \Shutterpress\Gallery\Shutterpress_Gallery_Gallery );

		$output = '';

		$renderer->set_attributes( $this->gallery_attributes );
		$this->debug->log( 'Elementor Attributes', array( 'this->gallery_attributes' => $this->gallery_attributes ), '' );

		if ( ! empty( $renderer->get_gallery_images() ) ) {
			if ( $this->utils->sp_gallery_is_elementor_in_edit_mode() ) {
				$output .= '<style>';
				$output .= ':root {
                        --sp-gallery-width: 100%;
                    }';
				$output .= $renderer->add_template_styles();
				$output .= '</style>';
			}

			$gallery_output = $renderer->get_the_gallery();
			if ( ! is_wp_error( $gallery_output ) ) {
				$output .= $gallery_output;
			}
			$output .= $renderer->get_infinite_scroll_loader();
			$output .= $renderer->get_gallery_pagination();
		}

		$filtered_content = apply_filters( 'sp_gallery_content', null, $output, $gallery_id );

		if ( ! empty( $filtered_content ) ) {
			echo $this->utils->sanitize_form_html( $filtered_content );
			return;
		}

		echo $output;
	}

	/**
	 * Backbone template for the editor preview.
	 *
	 * Intentionally left empty, the widget relies on the PHP `render()` method
	 * to output the real gallery in the Elementor editor preview.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function content_template(): void {
	}

	/**
	 * Retrieves a list of all published Shutterpress Gallery posts.
	 *
	 * @return array An associative array of gallery post IDs and titles, where the key is the post ID and the value is the post title.
	 */
	protected function sp_gallery_get_galleries() {

		$args = array(
			'post_type'      => 'shutterpress-gallery',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);

		$posts = get_posts( $args );

		$options = array(
			'0' => esc_html__( 'Use post gallery', 'shutterpress-gallery' ),
		);

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$options[ intval( $post->ID ) ] = esc_html( $post->post_title );
			}
			return $options;
		}
		$options['0'] = esc_html__( 'No galleries found', 'shutterpress-gallery' );
		return $options;
	}

	/**
	 * Retrieves an array of available image size options.
	 *
	 * The returned array includes the 'full' image size as well as all the intermediate image sizes defined in WordPress.
	 *
	 * @return array An associative array of image size options, where the key is the size name and the value is the human-readable size name.
	 * @phpstan-ignore method.unused
	 */
	private function sp_gallery_get_image_size_options() {

		$image_sizes = get_intermediate_image_sizes();
		$options     = array();

		$options['full'] = esc_html__( 'Full', 'shutterpress-gallery' );

		foreach ( $image_sizes as $size ) {
			$options[ $size ] = esc_html( ucfirst( $size ) );
		}

		return $options;
	}
}
