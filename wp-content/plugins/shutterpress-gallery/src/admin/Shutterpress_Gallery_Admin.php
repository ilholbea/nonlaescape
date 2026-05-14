<?php
/**
 * Admin layer for ShutterPress Gallery.
 *
 * Registers admin scripts and styles, integrates with the block editor and Elementor,
 * registers custom post types and the gallery block, runs version checks and migrations,
 * localises data for scripts, manages review notices and upgrade prompts, and provides
 * helper hooks for image maintenance and rewrite flushing.
 *
 * @link      https://shutterpress.io
 * @since     1.0.0
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @author    Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

use Elementor\Shutterpress_Gallery_Elementor_Widget;
use WP_Query;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Core admin controller for ShutterPress Gallery.
 *
 * Enqueues admin, editor and Elementor assets, registers post types and the block,
 * performs version checks and migrations, localises data to JavaScript, adds admin
 * notices, and exposes helper methods used by other layers and tests.
 *
 * @since 1.0.0
 */
class Shutterpress_Gallery_Admin {

	private string $plugin_name;
	private string $plugin_readable_name;
	private string $version;
	protected string $meta_box_prefix;
	private Shutterpress_Gallery_Gallery $gallery;
	protected Shutterpress_Gallery_Defaults $defaults;
	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Tracking $tracking;
	private Shutterpress_Gallery_Utils $utils;

	/**
	 * Class constructor.
	 *
	 * Initialises the core admin controller for ShutterPress Gallery by setting
	 * key properties such as plugin name, version, and prefix constants, and
	 * instantiating core helper classes including Gallery, Debug, Tracking, and
	 * Utils. These provide the base functionality for all admin and public tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name Unique slug identifying the plugin.
	 * @param string $version     Current plugin version string.
	 */
	public function __construct( ?string $plugin_name = null, ?string $version = null ) {
		$this->plugin_name          = $plugin_name ? $plugin_name : SP_GALLERY_NAME;
		$this->version              = $version ? $version : SP_GALLERY_VERSION;
		$this->meta_box_prefix      = defined( 'SP_GALLERY_MB_PREFIX' ) ? SP_GALLERY_MB_PREFIX : 'sp_gallery_';
		$this->plugin_readable_name = 'Shutterpress Gallery';
		$this->gallery              = new Shutterpress_Gallery_Gallery();
		$this->debug                = new Shutterpress_Gallery_Debug();
		$this->tracking             = new Shutterpress_Gallery_Tracking();
		$this->utils                = new Shutterpress_Gallery_Utils();
	}

	/**
	 * Sets the internal gallery object, used mainly for testing or overriding.
	 *
	 * @param Shutterpress_Gallery_Gallery $gallery The gallery object to assign.
	 *
	 * @since 1.0.0
	 * @return bool     True if the gallery was successfully set, false otherwise.
	 */
	public function set_gallery( Shutterpress_Gallery_Gallery $gallery ) {
		$this->gallery = $gallery;
		return true;
	}

	/**
	 * Register the CSS stylesheet for the admin area.
	 *
	 * This function enqueues the plugin's admin - specific CSS stylesheet .
	 *
	 * @param string $hook_suffix The current admin page hook supplied by WordPress .
	 *
	 * @since    1.0.0
	 * @return bool
	 */
	public function sp_gallery_enqueue_styles( $hook_suffix ) {
		$this->debug->log( 'Running sp_gallery_enqueue_styles', array( 'hook_suffix' => $hook_suffix ) );
		wp_enqueue_style( $this->plugin_name . '-admin', SP_GALLERY_URL . 'includes/css/shutterpress-gallery-admin.css', array(), $this->version, 'all' );

		if ( $hook_suffix !== 'shutterpress-gallery_page_sp-gallery-settings' ) {
			return false;
		}
		return true;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @return bool
	 */
	public function sp_gallery_enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name . '-admin',
			SP_GALLERY_URL . 'includes/js/shutterpress-gallery-admin.js',
			array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch', 'wp-i18n', 'imagesloaded', 'jquery-ui-sortable' ),
			$this->version,
			false
		);

		wp_set_script_translations( $this->plugin_name . '-admin', 'shutterpress-gallery', SP_GALLERY_DIR . 'languages' );
		return true;
	}

	/**
	 * Enqueue scripts and styles for the Gutenberg editor for ShutterPress galleries.
	 *
	 * Loads the public stylesheet and the editor script used in the block editor, registers
	 * Isotope and Infinite Scroll libraries, localises data for the editor script including
	 * the REST URL, a nonce and the debug flag, then appends inline CSS created from plugin
	 * settings. The routine exits early when Elementor is in edit mode so it only runs in
	 * the block editor.
	 *
	 * @since 1.6.4
	 *
	 * @return void
	 */
	public function enqueue_gutenberg_block_assets() {
		if ( ! $this->sp_gallery_should_enqueue_block_editor_assets() ) {
			return;
		}

		if ( $this->utils->sp_gallery_is_elementor_in_edit_mode() ) {
			return;
		}

		wp_enqueue_script( 'imagesloaded' );
		wp_register_script( 'isotope', SP_GALLERY_URL . 'includes/js/isotope.pkgd.min.js', array( 'jquery', 'imagesloaded' ), '3.0.0', false );
		wp_enqueue_script( $this->plugin_name . '-editor', SP_GALLERY_URL . 'includes/js/shutterpress-gallery-editor.js', array( 'jquery', 'imagesloaded', 'isotope', 'wp-i18n' ), $this->version, false );
		wp_enqueue_style( $this->plugin_name . '-public', SP_GALLERY_URL . 'includes/css/shutterpress-gallery-public.css', array(), $this->version, 'all' );

		$defaults_class = new Shutterpress_Gallery_Defaults();
		$defaults       = $defaults_class->get_template_defaults();

		$localize_data = array(
			'use_lightbox'    => (bool) $defaults['use_lightbox'],
			'layout'          => $defaults['layout'],
			'gap'             => (int) $defaults['gap'],
			'row_height'      => (int) $defaults['row_height'],
			'columns_desktop' => (int) $defaults['columns']['desktop'],
			'columns_tablet'  => (int) $defaults['columns']['tablet'],
			'columns_mobile'  => (int) $defaults['columns']['mobile'],
		);
		if ( get_option( 'sp_gallery_debug' ) === '1' ) {
			$localize_data['debug'] = true;
		}

		$localize_data['is'] = false;
		$localize_data['lg'] = false;

		$this->debug->log( 'Localising Data', array( $localize_data ) );

		wp_localize_script( 'shutterpress-gallery-editor', 'shutterpressDefaults', $localize_data );
		wp_localize_script( 'shutterpress-gallery-editor', 'shutterpressData', $localize_data );

		wp_set_script_translations( 'shutterpress-gallery-editor', 'shutterpress-gallery', SP_GALLERY_DIR . 'languages' );
	}

	/**
	 * Determines whether block editor assets should be enqueued for the current request.
	 *
	 * This allows editor preview assets to load inside the block editor canvas while
	 * preventing the editor bundle from leaking onto normal frontend requests.
	 *
	 * @since 1.7.12
	 *
	 * @return bool
	 */
	private function sp_gallery_should_enqueue_block_editor_assets(): bool {
		if ( is_admin() ) {
			return true;
		}

		if ( function_exists( 'wp_should_load_block_editor_scripts_and_styles' ) ) {
			return wp_should_load_block_editor_scripts_and_styles();
		}

		return false;
	}

	/**
	 * Enqueue the Shutterpress Gallery Elementor editor script.
	 *
	 * This function is responsible for enqueuing the Shutterpress Gallery Elementor editor script, which is required for the plugin's integration with the Elementor page builder.
	 *
	 * @since 4.1.4
	 * @return bool
	 */
	public function sp_gallery_enqueue_elementor_editor_scripts() {
		wp_enqueue_script( 'shutterpress-gallery-elementor', SP_GALLERY_URL . 'src/admin/js/shutterpress-gallery-elementor.js', array( 'jquery', 'elementor-frontend', 'wp-i18n' ), '4.1.4', true );

		wp_enqueue_style( $this->plugin_name . '-public', SP_GALLERY_URL . 'includes/css/shutterpress-gallery-public.css', array(), $this->version, 'all' );

		$localize_data = array(
			'rest_url' => esc_url_raw( rest_url() ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
		);
		if ( get_option( 'sp_gallery_debug' ) === '1' ) {
			$localize_data['debug'] = true;
		}

		wp_set_script_translations( 'shutterpress-gallery-elementor', 'shutterpress-gallery', SP_GALLERY_DIR . 'languages' );

		$localize_data['is'] = false;

		$this->debug->log( 'Localising Data', array( $localize_data ) );

		wp_localize_script( 'shutterpress-gallery-elementor', 'shutterpressData', $localize_data );
		return true;
	}

	/**
	 * Detect whether this site already has ShutterPress data, used to decide
	 * if a missing stored version means a fresh install or an older install that failed to save the version.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	private function site_has_existing_data(): bool {
		if ( get_option( 'sp_gallery_slug' ) !== false ) {
			return true;
		}
		if ( get_option( 'sp_gallery_settings' ) !== false ) {
			return true;
		}

		if ( ! post_type_exists( 'shutterpress-gallery' ) ) {
			return false;
		}

		$q = new WP_Query(
			array(
				'post_type'              => 'shutterpress-gallery',
				'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'fields'                 => 'ids',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'cache_results'          => false,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);
		return $q->have_posts();
	}

	/**
	 * Checks and updates the plugin's database version.
	 *
	 * This function checks the stored version and updates it if it doesn't match the current version. If the stored version is less than or equal to 1.0.2, it runs migration logic to update the old gallery images.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function check_and_update_version(): bool {

		if ( ! is_admin() ) {
			return false;
		}
		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return false;
		}
		if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
			return false;
		}
		if ( ( function_exists( 'wp_doing_rest' ) && wp_doing_rest() ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		if ( get_transient( 'sp_gallery_upgrade_lock' ) ) {
			$this->debug->log( 'Upgrade locked, skipping' );
			return false;
		}
		set_transient( 'sp_gallery_upgrade_lock', 1, 5 * MINUTE_IN_SECONDS );

		try {
			$stored_version = get_option( 'shutterpress_gallery_version' );

			$migrations = array(
				'2024_11_fix_images_v1.0.2'      => function () {
					$this->sp_gallery_migrate_old_gallery_images();
				},
				'2025_03_template_defaults_v1.7' => function () {
					$this->convert_defaults_to_template();
				},
			);

			$applied = get_option( 'sp_gallery_migrations_applied', array() );

			if ( ! is_array( $applied ) ) {
				$applied = array();
			}

			if ( $stored_version === false ) {
				if ( ! $this->site_has_existing_data() ) {
					foreach ( $migrations as $key => $fn ) {
						$applied[ $key ] = 'not_required';
					}
					$this->track_version( $stored_version, $this->version );
					update_option( 'sp_gallery_migrations_applied', $applied, false );
					$this->debug->log(
						'Fresh install detected, setting version and skipping migrations',
						array( 'current_version' => $this->version ),
						'DEBUG'
					);
					return true;
				}

				$this->debug->log(
					'No stored version but existing data found, treating as upgrade and running migrations',
					array(),
					'WARNING'
				);
			}

			foreach ( $migrations as $key => $fn ) {
				if ( isset( $applied[ $key ] ) ) {
					continue;
				}
				try {
					$fn();
					$applied[ $key ] = time();
				} catch ( \Throwable $e ) {
					$this->debug->log(
						'Migration failed',
						array(
							'key'   => $key,
							'error' => $e->getMessage(),
						),
						'ERROR'
					);
				}
			}

			update_option( 'sp_gallery_migrations_applied', $applied, false );

			if ( $stored_version !== $this->version ) {
				$this->track_version( $stored_version, $this->version );
			}

			$this->debug->log(
				'Checking plugin version',
				array(
					'stored_version'  => $stored_version,
					'current_version' => $this->version,
				)
			);
			return true;
		} finally {
			delete_transient( 'sp_gallery_upgrade_lock' );
		}
	}

	/**
	 * Tracks and updates the stored plugin version.
	 *
	 * Optionally sends tracking data if Shutterpress_Gallery_Tracking is available,
	 * including old and new versions and timestamps. Always updates the stored
	 * version in the database.
	 *
	 * @since 1.1.0
	 *
	 * @param string|false $stored_version The previous stored version, or false if none.
	 * @param string       $new_version    The new version being set.
	 *
	 * @return void
	 */
	private function track_version( $stored_version, $new_version ) {

		if ( class_exists( '\\Shutterpress\\Gallery\\Shutterpress_Gallery_Tracking' ) && get_option( 'sp_gallery_version_tracked' ) !== $new_version ) {
			$now      = time();
			$tracking = new Shutterpress_Gallery_Tracking();
			$tracking->track(
				'Shutterpress Gallery Plugin Updated',
				array(
					'plugin_name'      => 'Shutterpress Gallery',
					'old_version'      => $stored_version ? $stored_version : 'none',
					'new_version'      => $new_version,
					'update_timestamp' => $now,
					'update_time'      => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);
			$tracking->people_set(
				array(
					'Shutterpress Gallery Version'     => defined( 'SP_GALLERY_VERSION' ) ? SP_GALLERY_VERSION : 'unknown',
					'Shutterpress Gallery Activated'   => true,
					'Shutterpress Gallery update timestamp' => $now,
					'Shutterpress Gallery update time' => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);
			update_option( 'sp_gallery_version_tracked', $new_version );
		}

		update_option( 'shutterpress_gallery_version', $new_version );
	}

	/**
	 * Loads the custom image advanced field class.
	 *
	 * This function includes the file that defines the custom image advanced field class, which is used in the plugin's meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_load_custom_fields() {
		require_once plugin_dir_path( __DIR__ ) . '/admin/RWMB_Custom_Image_Advanced_Field.php';
		require_once plugin_dir_path( __DIR__ ) . '/admin/RWMB_Custom_Image_Select_Field.php';
		require_once plugin_dir_path( __DIR__ ) . '/admin/RWMB_Custom_Fieldset_Text_Field.php';
		require_once plugin_dir_path( __DIR__ ) . '/admin/RWMB_Code_Field.php';
	}

	/**
	 * Localizes all necessary data for the Shutterpress Gallery plugin.
	 *
	 * This function prepares an array of data to be passed to the front-end, including the AJAX URL, a nonce for security, and potentially other data that needs to be accessible on the client-side.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_localize_all_data() {

		$post_id       = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : get_the_ID();
		$localize_data = array(
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'postID'   => $post_id,
			'rest_url' => esc_url_raw( rest_url() ),
		);

		if ( get_option( 'sp_gallery_debug' ) === '1' ) {
			$localize_data['debug'] = true;
		}

		$this->debug->log( 'Localising Data', array( $localize_data ) );
		wp_localize_script( $this->plugin_name . '-admin', 'shutterpressData', $localize_data );
	}

	/**
	 * Register custom post type shutterpress-gallery
	 *
	 * @since    1.0.0
	 *
	 * @return bool True if the post type was registered, false otherwise.
	 */
	public static function sp_gallery_register_custom_post(): bool {
		$slug = get_option( 'sp_gallery_slug', 'gallery' );
		$slug = sanitize_title( $slug );

		if ( post_type_exists( $slug ) ) {
			$slug = 'sp-gallery';
		}

		$labels = array(
			'name'                     => esc_html__( 'ShutterPress Galleries', 'shutterpress-gallery' ),
			'singular_name'            => esc_html__( 'ShutterPress Gallery', 'shutterpress-gallery' ),
			'menu_name'                => esc_html__( 'ShutterPress', 'shutterpress-gallery' ),
			'all_items'                => esc_html__( 'All Galleries', 'shutterpress-gallery' ),
			'add_new'                  => esc_html__( 'Add new', 'shutterpress-gallery' ),
			'add_new_item'             => esc_html__( 'Add new Gallery', 'shutterpress-gallery' ),
			'edit_item'                => esc_html__( 'Edit Gallery', 'shutterpress-gallery' ),
			'new_item'                 => esc_html__( 'New Gallery', 'shutterpress-gallery' ),
			'view_item'                => esc_html__( 'View Gallery', 'shutterpress-gallery' ),
			'view_items'               => esc_html__( 'View Galleries', 'shutterpress-gallery' ),
			'search_items'             => esc_html__( 'Search Galleries', 'shutterpress-gallery' ),
			'not_found'                => esc_html__( 'No Galleries found', 'shutterpress-gallery' ),
			'not_found_in_trash'       => esc_html__( 'No Galleries found in trash', 'shutterpress-gallery' ),
			'parent'                   => esc_html__( 'Parent Gallery:', 'shutterpress-gallery' ),
			'featured_image'           => esc_html__( 'Featured image for this Gallery', 'shutterpress-gallery' ),
			'set_featured_image'       => esc_html__( 'Set featured image for this Gallery', 'shutterpress-gallery' ),
			'remove_featured_image'    => esc_html__( 'Remove featured image for this Gallery', 'shutterpress-gallery' ),
			'use_featured_image'       => esc_html__( 'Use as featured image for this Gallery', 'shutterpress-gallery' ),
			'archives'                 => esc_html__( 'Gallery archives', 'shutterpress-gallery' ),
			'insert_into_item'         => esc_html__( 'Insert into Gallery', 'shutterpress-gallery' ),
			'uploaded_to_this_item'    => esc_html__( 'Upload to this Gallery', 'shutterpress-gallery' ),
			'filter_items_list'        => esc_html__( 'Filter Galleries list', 'shutterpress-gallery' ),
			'items_list_navigation'    => esc_html__( 'Galleries list navigation', 'shutterpress-gallery' ),
			'items_list'               => esc_html__( 'Galleries list', 'shutterpress-gallery' ),
			'attributes'               => esc_html__( 'Gallery attributes', 'shutterpress-gallery' ),
			'name_admin_bar'           => esc_html__( 'ShutterPress Gallery', 'shutterpress-gallery' ),
			'item_published'           => esc_html__( 'Gallery published', 'shutterpress-gallery' ),
			'item_published_privately' => esc_html__( 'Gallery published privately.', 'shutterpress-gallery' ),
			'item_reverted_to_draft'   => esc_html__( 'Gallery reverted to draft.', 'shutterpress-gallery' ),
			'item_scheduled'           => esc_html__( 'Gallery scheduled', 'shutterpress-gallery' ),
			'item_updated'             => esc_html__( 'Gallery updated.', 'shutterpress-gallery' ),
			'parent_item_colon'        => esc_html__( 'Parent Gallery:', 'shutterpress-gallery' ),
		);

		$args = array(
			'label'                 => esc_html__( 'ShutterPress Galleries', 'shutterpress-gallery' ),
			'labels'                => $labels,
			'description'           => '',
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_rest'          => true,
			'rest_base'             => sanitize_key( '' ),
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rest_namespace'        => sanitize_text_field( 'wp/v2' ),
			'has_archive'           => false,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'delete_with_user'      => false,
			'exclude_from_search'   => false,
			'capability_type'       => 'page',
			'map_meta_cap'          => true,
			'hierarchical'          => true,
			'can_export'            => true,
			'rewrite'               => array(
				'slug'       => $slug,
				'with_front' => true,
			),
			'query_var'             => true,
			'menu_icon'             => esc_attr( 'dashicons-camera' ),
			'supports'              => array_map( 'sanitize_key', array( 'title', 'thumbnail', 'page-attributes' ) ),
			'show_in_graphql'       => false,
		);

		$template_labels = array(
			'name'               => _x( 'Gallery Templates', 'post type general name', 'shutterpress-gallery' ),
			'singular_name'      => _x( 'Gallery Template', 'post type singular name', 'shutterpress-gallery' ),
			'menu_name'          => _x( 'Gallery Templates', 'admin menu', 'shutterpress-gallery' ),
			'name_admin_bar'     => _x( 'Gallery Template', 'add new on admin bar', 'shutterpress-gallery' ),
			'add_new'            => _x( 'Add New', 'Gallery template', 'shutterpress-gallery' ),
			'add_new_item'       => __( 'Add New Gallery Template', 'shutterpress-gallery' ),
			'new_item'           => __( 'New Gallery Template', 'shutterpress-gallery' ),
			'edit_item'          => __( 'Edit Gallery Template', 'shutterpress-gallery' ),
			'view_item'          => __( 'View Gallery Template', 'shutterpress-gallery' ),
			'all_items'          => __( 'Gallery Templates', 'shutterpress-gallery' ),
			'search_items'       => __( 'Search Gallery Templates', 'shutterpress-gallery' ),
			'parent_item_colon'  => __( 'Parent Gallery Templates:', 'shutterpress-gallery' ),
			'not_found'          => __( 'No gallery templates found.', 'shutterpress-gallery' ),
			'not_found_in_trash' => __( 'No gallery templates found in Trash.', 'shutterpress-gallery' ),
		);

		$template_args = array(
			'labels'             => $template_labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=shutterpress-gallery',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'sp-gallery-template' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' ),
			'menu_icon'          => 'dashicons-email',
		);

		register_post_type( 'shutterpress-gallery', $args );
		register_taxonomy(
			'sp_gallery_category',
			array( 'shutterpress-gallery' ),
			array(
				'labels'            => array(
					'name'              => esc_html__( 'Gallery Categories', 'shutterpress-gallery' ),
					'singular_name'     => esc_html__( 'Gallery Category', 'shutterpress-gallery' ),
					'search_items'      => esc_html__( 'Search Gallery Categories', 'shutterpress-gallery' ),
					'all_items'         => esc_html__( 'All Gallery Categories', 'shutterpress-gallery' ),
					'parent_item'       => esc_html__( 'Parent Gallery Category', 'shutterpress-gallery' ),
					'parent_item_colon' => esc_html__( 'Parent Gallery Category:', 'shutterpress-gallery' ),
					'edit_item'         => esc_html__( 'Edit Gallery Category', 'shutterpress-gallery' ),
					'update_item'       => esc_html__( 'Update Gallery Category', 'shutterpress-gallery' ),
					'add_new_item'      => esc_html__( 'Add New Gallery Category', 'shutterpress-gallery' ),
					'new_item_name'     => esc_html__( 'New Gallery Category Name', 'shutterpress-gallery' ),
					'menu_name'         => esc_html__( 'Gallery Categories', 'shutterpress-gallery' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'gallery-category' ),
			)
		);
		register_taxonomy(
			'sp_gallery_tag',
			array( 'shutterpress-gallery' ),
			array(
				'labels'            => array(
					'name'                       => esc_html__( 'Gallery Tags', 'shutterpress-gallery' ),
					'singular_name'              => esc_html__( 'Gallery Tag', 'shutterpress-gallery' ),
					'search_items'               => esc_html__( 'Search Gallery Tags', 'shutterpress-gallery' ),
					'popular_items'              => esc_html__( 'Popular Gallery Tags', 'shutterpress-gallery' ),
					'all_items'                  => esc_html__( 'All Gallery Tags', 'shutterpress-gallery' ),
					'edit_item'                  => esc_html__( 'Edit Gallery Tag', 'shutterpress-gallery' ),
					'update_item'                => esc_html__( 'Update Gallery Tag', 'shutterpress-gallery' ),
					'add_new_item'               => esc_html__( 'Add New Gallery Tag', 'shutterpress-gallery' ),
					'new_item_name'              => esc_html__( 'New Gallery Tag Name', 'shutterpress-gallery' ),
					'separate_items_with_commas' => esc_html__( 'Separate gallery tags with commas', 'shutterpress-gallery' ),
					'add_or_remove_items'        => esc_html__( 'Add or remove gallery tags', 'shutterpress-gallery' ),
					'choose_from_most_used'      => esc_html__( 'Choose from the most used gallery tags', 'shutterpress-gallery' ),
					'menu_name'                  => esc_html__( 'Gallery Tags', 'shutterpress-gallery' ),
				),
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'gallery-tag' ),
			)
		);
		register_post_type( 'sp_gallery_template', $template_args );
		return true;
	}

	/**
	 * Adds an image count column to the gallery admin list table.
	 *
	 * Inserts the column before the default date column so gallery managers can
	 * quickly see how many images each gallery contains.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, string> $columns Existing list table columns.
	 *
	 * @return array<string, string> Modified list table columns.
	 */
	public function sp_gallery_add_image_count_column( array $columns ): array {
		if ( ! $this->sp_gallery_is_gallery_list_screen() ) {
			return $columns;
		}

		$updated_columns = array();

		foreach ( $columns as $key => $label ) {
			if ( $key === 'date' ) {
				$updated_columns['sp_gallery_image_count'] = esc_html__( 'Images', 'shutterpress-gallery' );
			}

			$updated_columns[ $key ] = $label;
		}

		if ( ! isset( $updated_columns['sp_gallery_image_count'] ) ) {
			$updated_columns['sp_gallery_image_count'] = esc_html__( 'Images', 'shutterpress-gallery' );
		}

		return $updated_columns;
	}

	/**
	 * Renders the image count column content for gallery rows.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column  Current column key.
	 * @param int    $post_id Gallery post ID.
	 *
	 * @return void
	 */
	public function sp_gallery_render_image_count_column( string $column, int $post_id ): void {
		if ( $column !== 'sp_gallery_image_count' || get_post_type( $post_id ) !== 'shutterpress-gallery' ) {
			return;
		}

		echo esc_html( (string) $this->gallery->get_gallery_images_count( $post_id ) );
	}

	/**
	 * Determines whether the current admin list table is for galleries.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function sp_gallery_is_gallery_list_screen(): bool {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				return $screen->post_type === 'shutterpress-gallery';
			}
		}

		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( $post_type ) {
			return $post_type === 'shutterpress-gallery';
		}

		global $typenow;

		return $typenow === 'shutterpress-gallery';
	}

	/**
	 * Delete an attachment from the gallery metadata of all posts.
	 *
	 * @param int $attachment_id The attachment ID to delete.
	 *
	 * @since 1.0.0
	 * @return bool True if the attachment was deleted from all galleries, false otherwise.
	 */
	public function sp_gallery_delete_attachments_from_gallery( $attachment_id ): bool {
		$this->debug->log( 'running sp_gallery_delete_attachments_from_gallery' );

		$post = get_post( $attachment_id );
		if ( $post && $post->post_type !== 'attachment' ) {
			return false;
		}
		$image = new Shutterpress_Gallery_Image( $attachment_id );
		$image->remove_image_from_all_galleries();
			return true;
	}

	/**
	 * Migrate old individual 'sp_gallery_images' meta entries into a single '_sp_gallery_images_array' in batches.
	 *
	 * @param int $batch_size The number of posts to process per batch.
	 * @param int $offset     The number of posts to skip (used for batch processing).
	 *
	 * @return int The number of posts migrated in the current batch.
	 */
	public function sp_gallery_migrate_old_gallery_images( $batch_size = 100, $offset = 0 ): int {
		$args = array(
			'post_type'      => 'shutterpress-gallery',
			'meta_key'       => 'sp_gallery_images',
			'posts_per_page' => $batch_size,
			'offset'         => $offset,
			'fields'         => 'ids',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return 0;
		}

		$posts = $query->posts;

		foreach ( $posts as $post_id ) {
			assert( is_int( $post_id ) );

			$image_ids = get_post_meta( $post_id, 'sp_gallery_images', false );

			$image_ids = array_filter(
				$image_ids,
				function ( $id ) {
					return is_numeric( $id );
				}
			);

			$image_ids = array_map( 'intval', $image_ids );

			if ( ! empty( $image_ids ) ) {
				$this->gallery->update_gallery_images( $image_ids, $post_id );
			}

			if ( get_post_meta( $post_id, 'sp_gallery_images', true ) ) {
				delete_post_meta( $post_id, 'sp_gallery_images' );
			}
		}

		return count( $query->posts );
	}

	/**
	 * Creates a Default Template from legacy options, then assigns it to galleries without a template.
	 *
	 * Looks up an existing template by title, collects legacy options with the meta box prefix into
	 * a defaults array, deletes the old options, creates a sp_gallery_template via
	 * Shutterpress_Gallery_Template::create_from_defaults, then updates shutterpress-gallery posts in
	 * batches to set the new template ID where missing.
	 *
	 * Safe to run more than once, it is idempotent in effect, assignment runs in batches to reduce
	 * load on low resource hosts.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	private function convert_defaults_to_template(): bool {
		$post_title = 'Default Template';
		$prefix     = $this->meta_box_prefix;

		$args = array(
			'title'          => $post_title,
			'post_type'      => 'sp_gallery_template',
			'post_status'    => 'any',
			'posts_per_page' => 1,
		);

		$query         = new WP_Query( $args );
		$existing_post = $query->have_posts() ? $query->posts[0] : null;

		if ( ! $existing_post ) {
			$defaults = array();

			$option = get_option( $prefix . 'button_color' );
			if ( $option !== false ) {
				$defaults['color_primary'] = $option;
				delete_option( $prefix . 'button_color' );
			}
			$option = get_option( $prefix . 'column_gap' );
			if ( $option !== false ) {
				$defaults['gap'] = $option;
				delete_option( $prefix . 'column_gap' );
			}

			$columns = array();
			$option  = get_option( $prefix . 'columns_desktop' );
			if ( $option !== false ) {
				$columns['desktop'] = $option;
				delete_option( $prefix . 'columns_desktop' );
			}
			$option = get_option( $prefix . 'columns_tablet' );
			if ( $option !== false ) {
				$columns['tablet'] = $option;
				delete_option( $prefix . 'columns_tablet' );
			}
			$option = get_option( $prefix . 'columns_mobile' );
			if ( $option !== false ) {
				$columns['mobile'] = $option;
				delete_option( $prefix . 'columns_mobile' );
			}
			$defaults['columns'] = $columns;

			$option = get_option( $prefix . 'icon_size' );
			if ( $option !== false ) {
				$defaults['icon_size']['desktop'] = $option;
				$defaults['icon_size']['tablet']  = $option;
				$defaults['icon_size']['mobile']  = $option;
				delete_option( $prefix . 'icon_size' );
			}
			$option = get_option( $prefix . 'justified_row_height' );
			if ( $option !== false ) {
				$defaults['row_height'] = $option;
				delete_option( $prefix . 'justified_row_height' );
			}
			$option = get_option( $prefix . 'layout' );
			if ( $option !== false ) {
				$defaults['layout'] = $option;
				delete_option( $prefix . 'layout' );
			}
			$option = get_option( $prefix . 'use_infinite_scroll' );
			if ( $option !== false ) {
				$defaults['infinite_scroll'] = $option;
				delete_option( $prefix . 'use_infinite_scroll' );
			}
			$option = get_option( $prefix . 'use_lightbox' );
			if ( $option !== false ) {
				$defaults['lightbox'] = $option;
				delete_option( $prefix . 'use_lightbox' );
			}
			$option = get_option( $prefix . 'use_pagination' );
			if ( $option !== false ) {
				$defaults['pagination'] = $option;
				delete_option( $prefix . 'use_pagination' );
			}

			$template = new Shutterpress_Gallery_Template();

			$template_id = $template->create_from_defaults( $post_title, $defaults );

			if ( ! is_wp_error( $template_id ) && ! empty( $template_id ) ) {
				$meta_key      = $this->meta_box_prefix . 'template_id';
				$batch_size    = 200;
				$paged         = 1;
				$total_updated = 0;

				do {
					$q = new WP_Query(
						array(
							'post_type'      => 'shutterpress-gallery',
							'post_status'    => 'any',
							'posts_per_page' => $batch_size,
							'paged'          => $paged,
							'fields'         => 'ids',
							'orderby'        => 'ID',
							'order'          => 'ASC',
							'no_found_rows'  => false,
							'meta_query'     => array(
								'relation' => 'OR',
								array(
									'key'     => $meta_key,
									'compare' => 'NOT EXISTS',
								),
								array(
									'key'     => $meta_key,
									'value'   => '',
									'compare' => '=',
								),
								array(
									'key'     => $meta_key,
									'value'   => 0,
									'type'    => 'NUMERIC',
									'compare' => '=',
								),
							),
						)
					);

					if ( empty( $q->posts ) ) {
						break;
					}

					$posts = $q->posts;

					foreach ( $posts as $gallery_post_id ) {
						assert( is_int( $gallery_post_id ) );
						update_post_meta( $gallery_post_id, $meta_key, (int) $template_id );
					}

					$total_updated += count( $q->posts );
					++$paged;

					if ( function_exists( 'wp_raise_memory_limit' ) ) {
						wp_raise_memory_limit( 'admin' );
					}
				} while ( $q->post_count === $batch_size );

				if ( class_exists( 'Shutterpress_Gallery_Debug' ) ) {
					$this->debug->log(
						'Assigned default template to galleries (batched)',
						array(
							'template_id' => (int) $template_id,
							'updated'     => $total_updated,
							'batch_size'  => $batch_size,
						),
						'INFO'
					);
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Registers the Shutterpress Gallery block.
	 *
	 * This function registers the Shutterpress Gallery block, which allows users to
	 * insert a Shutterpress Gallery into their WordPress posts or pages.
	 *
	 * @return bool
	 */
	public function sp_gallery_register_block(): bool {
		register_block_type( __DIR__ . '/../../includes/blocks/shutterpress-gallery-block/' );
		return true;
	}

	/**
	 * Sorts the images associated with a Shutterpress Gallery post based on the selected sorting option.
	 *
	 * This function is a callback for the 'update_post_meta' action, which is triggered when the sorting option
	 * for a Shutterpress Gallery post is updated. It retrieves the existing images array, sorts it based on the
	 * new sorting option, and updates the '_sp_gallery_images_array' post meta with the sorted array.
	 *
	 * @param mixed  $null_value Unused parameter.
	 * @param string $field      The name of the custom field being updated.
	 * @param mixed  $new_value  The new value of the custom field.
	 * @param mixed  $old_value  The old value of the custom field.
	 *
	 * @return mixed Always returns $null to preserve filter behavior.
	 */
	public function sp_gallery_sort_images( $null_value, $field, $new_value, $old_value ) {

		if ( $new_value !== $old_value && $new_value !== 'drag_n_drop' ) {
			$images = $this->gallery->get_gallery_images();

			$sorting_option = $new_value;

			if ( empty( $images ) || empty( $sorting_option ) ) {
				return $images;
			}

			$sorted_images = $this->gallery->sort_images_by_option( $images, $sorting_option );

			if ( ! $sorted_images ) {
				return $images;
			}

			$this->gallery->update_gallery_images( $sorted_images );

			return $sorted_images;
		}
	}

	/**
	 * Automatically generates and saves an accent color when the primary color changes.
	 *
	 * When a new primary color is saved in the gallery settings, this method calculates
	 * a derived accent color (using 30% mix) and stores it in post meta. If the primary
	 * color is cleared, the related accent color meta is removed.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed  $null_value Unused, required by the filter signature.
	 * @param string $field      The field name being saved.
	 * @param string $new_value  The new primary color value.
	 * @param string $old_value  The old primary color value.
	 * @param int    $object_id  The ID of the gallery post being updated.
	 *
	 * @return mixed Always returns $null to preserve filter behavior.
	 */
	public function sp_gallery_after_color_primary_save( $null_value, $field, $new_value, $old_value, $object_id ) {
		if ( ! empty( $new_value ) ) {
			$utils        = new Shutterpress_Gallery_Utils();
			$accent_color = $utils->get_accent_color( $new_value, 0.3 );
			update_post_meta( $object_id, $this->meta_box_prefix . 'color_accent', $accent_color );
		} else {
			delete_post_meta( $object_id, $this->meta_box_prefix . 'color_accent' );
		}

		return $null_value;
	}

	/**
	 * Register List Widget.
	 *
	 * Include widget file and register widget class.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the widget was registered, false otherwise.
	 */
	public function sp_gallery_register_elementor_widget(): bool {
		if ( $this->sp_gallery_elementor_loaded() ) {
			require_once plugin_dir_path( __DIR__ ) . 'admin/Shutterpress_Gallery_Elementor_Widget.php';

			if ( class_exists( '\Elementor\Shutterpress_Gallery_Elementor_Widget' ) ) {
				\Elementor\Plugin::instance()->widgets_manager->register( new Shutterpress_Gallery_Elementor_Widget() );
			}
			return true;
		}
		return false;
	}

	/**
	 * Checks if Elementor is loaded.
	 *
	 * This method checks if the Elementor plugin is loaded and active. It returns true if Elementor is loaded, and false otherwise.
	 *
	 * @return bool True if Elementor is loaded, false otherwise.
	 */
	public function sp_gallery_elementor_loaded(): bool {
		if ( did_action( 'elementor/loaded' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Register a custom category for Shutterpress Gallery widgets in Elementor.
	 *
	 * This method adds a new category named 'shutterpress' to the Elementor widgets manager,
	 * with the title 'Shutterpress' and the icon 'fa fa-plug'.
	 *
	 * @param \Elementor\Widgets_Manager $elements_manager The Elementor widgets manager instance.
	 *
	 * @return void
	 */
	public function sp_gallery_add_elementor_category( $elements_manager ) {

		if ( ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$category_id = sanitize_key( 'shutterpress' );

		$elements_manager->add_category(
			$category_id,
			array(
				'title' => esc_html__( 'Shutterpress', 'shutterpress-gallery' ),
				'icon'  => esc_attr( 'fa fa-plug' ),
			)
		);
	}

	/**
	 * Flushes the rewrite rules when the gallery slug changes.
	 *
	 * This function is a callback for the 'update_option' action, which is triggered
	 * when the 'shutterpress_gallery_slug' option is updated. If the new slug value
	 * is different from the old value, this function will flush the rewrite rules
	 * to ensure the new slug is properly registered.
	 *
	 * @param string $old_value The previous value of the 'shutterpress_gallery_slug' option.
	 * @param string $new_value The new value of the 'shutterpress_gallery_slug' option.
	 *
	 * @return void
	 */
	public function sp_gallery_flush_rewrite_on_slug_change( $old_value, $new_value ) {
		if ( $old_value !== $new_value ) {
			$this->sp_gallery_register_custom_post();
			flush_rewrite_rules();
		}
	}

	/**
	 * Add an upgrade link to the plugin row meta.
	 *
	 * @param array  $links Array of the existing links for the plugin.
	 * @param string $file  Path to the plugin file relative to the plugins directory.
	 *
	 * @return array Modified links array with the upgrade link.
	 */
	public function sp_gallery_upgrade_link( $links, $file ) {

		if ( is_plugin_active( 'shutterpress-gallery-pro/shutterpress-gallery-pro.php' ) ) {
			return $links;
		}

		$distinct_id = $this->tracking->get_install_id();
		if ( $file === 'shutterpress-gallery/shutterpress-gallery.php' ) {
			$upgrade_url = 'https://shutterpress.io/buy-now?utm_source=plugin_upgrade_link&utm_medium=plugin&utm_campaign=shutterpress_plugin&install_id=' . $distinct_id;
			$links[]     = '<br /><a href="' . esc_url( $upgrade_url ) . '" target="_blank" class="sp-gallery-plugin-upgrade-link">Upgrade to Pro</a>';
		}
		return $links;
	}

	/**
	 * Display a dismissible admin notice asking for a plugin review.
	 *
	 * This notice is only shown to administrators who haven't dismissed it in the past 7 days
	 * and only after the plugin has been installed for at least 7 days.
	 *
	 * @since 1.3.4
	 *
	 * @return void
	 */
	public function sp_gallery_review_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$user_id = get_current_user_id();

		$dismissed = get_user_meta( $user_id, 'sp_gallery_review_dismissed', true );
		if ( $dismissed && time() - (int) $dismissed < 7 * DAY_IN_SECONDS ) {
			return;
		}

		$installed = get_option( 'sp_gallery_first_installed' );
		if ( ! $installed ) {
			update_option( 'sp_gallery_first_installed', time() );
			return;
		}

		if ( time() - $installed < 7 * DAY_IN_SECONDS ) {
			return;
		}
		echo '<div class="notice notice-info is-dismissible sp-gallery-review-notice">';
		echo '<p> ❤️ ' . esc_html__( 'Enjoying', 'shutterpress-gallery' ) . ' <strong>' . esc_html( $this->plugin_readable_name ) . '</strong>? ';
		echo '<a href="' . esc_url( 'https://wordpress.org/support/plugin/shutterpress-gallery/reviews/?rate=5#new-post' ) . '" target="_blank" rel="noopener noreferrer">';
		echo esc_html__( 'Please leave a review', 'shutterpress-gallery' ) . '</a> ' . esc_html__( 'to support the plugin!', 'shutterpress-gallery' ) . '</p>';
		echo '</div>';
	}

	/**
	 * Adds custom CSS to highlight upgrade prompts in the admin interface.
	 *
	 * This method injects inline styles into the admin area only if the Pro version
	 * of the Shutterpress Gallery plugin is not active. It highlights elements like
	 * the upgrade link in the admin menu and plugin row with a prominent color and font weight.
	 *
	 * @since 1.3.4
	 *
	 * @return void
	 */
	public function sp_gallery_add_upgrade_css() {
		if ( ! is_plugin_active( 'shutterpress-gallery-pro/shutterpress-gallery-pro.php' ) ) {
			$css = '#menu-posts-shutterpress-gallery ul.wp-submenu li:last-child a,
            .sp-gallery-plugin-upgrade-link {
                color: #ee2e4f;
                font-weight: 700;
            }';
			wp_add_inline_style( 'shutterpress-gallery-admin', $css );
		}
	}
}
