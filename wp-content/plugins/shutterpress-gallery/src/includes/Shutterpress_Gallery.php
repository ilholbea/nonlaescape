<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 *
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Core plugin bootstrap for ShutterPress Gallery.
 *
 * Loads dependencies, registers admin and public hooks, and provides accessors
 * for the plugin name, loader, and version. Also initializes the debug logger
 * for normal web requests.
 *
 * @since 1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery\includes
 */
class Shutterpress_Gallery {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   protected
	 *
	 * @var Shutterpress_Gallery_Loader $loader    Maintains and registers all hooks for the plugin.
	 */
	protected Shutterpress_Gallery_Loader $loader;

	protected string $plugin_name;

	protected string $version;

	protected Shutterpress_Gallery_Debug $debug;

	/**
	 * Class constructor.
	 *
	 * Defines plugin constants, sets the plugin name and version, initializes the
	 * debug logger, loads dependencies, and registers admin and public hooks. It
	 * also starts the debug logger for normal web requests that are not AJAX,
	 * CLI, REST, or wp‑cron.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		/**
		 * Current plugin version.
		 */

		$this->version = '1.0.0';

		if ( defined( 'SP_GALLERY_VERSION' ) ) {
			$this->version = SP_GALLERY_VERSION;
		}

		$this->plugin_name = 'shutterpress-gallery';

		if ( defined( 'SP_GALLERY_NAME' ) ) {
			$this->plugin_name = SP_GALLERY_NAME;
		}
		if (! defined('SP_GALLERY_NAME')) {
            define('SP_GALLERY_NAME', 'shutterpress-gallery');
        }
        if (! defined('SP_GALLERY_DIR')) {
            define('SP_GALLERY_DIR', trailingslashit(preg_replace('#src/includes/?$#', '', __DIR__)));
        }
        if (! defined('SP_GALLERY_URL')) {
            define('SP_GALLERY_URL', trailingslashit(preg_replace('#src/includes/?$#', '', plugins_url('', __FILE__))));
        }
        if (! defined('SP_GALLERY_MB_PREFIX')) {
            define('SP_GALLERY_MB_PREFIX', 'sp_gallery_');
        }

		$this->debug = new Shutterpress_Gallery_Debug();

		$this->sp_gallery_load_dependencies();
		$this->sp_gallery_define_admin_hooks();
		$this->sp_gallery_define_public_hooks();
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( ! defined( 'DOING_AJAX' ) && ! defined( 'WP_CLI' ) && ! defined( 'REST_REQUEST' ) && stripos( $request_uri, 'wp-cron.php' ) === false ) {
			$this->debug->init();
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Shutterpress_Gallery_Loader. Orchestrates the hooks of the plugin.
	 * - Shutterpress_Gallery_i18n. Defines internationalization functionality.
	 * - Shutterpress_Gallery_Admin. Defines all hooks for the admin area.
	 * - Shutterpress_Gallery_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 *
	 * @return void
	 */
	private function sp_gallery_load_dependencies() {
		
		if ( ! class_exists( 'RW_Meta_Box' ) ) {
			$meta_box_path = plugin_dir_path( __FILE__ ) . '../../vendor/meta-box/meta-box/meta-box.php';

			if ( file_exists( $meta_box_path ) ) {
				require_once $meta_box_path;
			}
		}

		$this->loader = new Shutterpress_Gallery_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 * @return void
	 */
	protected function sp_gallery_define_admin_hooks() {
		$plugin_admin            = new Shutterpress_Gallery_Admin( $this->sp_gallery_get_plugin_name(), $this->sp_gallery_get_version() );
		$plugin_admin_settings   = new Shutterpress_Gallery_Admin_Settings();
		$plugin_admin_meta_boxes = new Shutterpress_Gallery_Meta_Box();
		$plugin_admin_rest       = new Shutterpress_Gallery_REST();

		$this->loader->sp_gallery_add_action( 'enqueue_block_assets', $plugin_admin, 'enqueue_gutenberg_block_assets' );
		$this->loader->sp_gallery_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sp_gallery_enqueue_scripts' );
		$this->loader->sp_gallery_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sp_gallery_enqueue_styles' );
		$this->loader->sp_gallery_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sp_gallery_localize_all_data' );
		$this->loader->sp_gallery_add_action( 'admin_init', $plugin_admin, 'check_and_update_version' );
		$this->loader->sp_gallery_add_action( 'init', $plugin_admin, 'sp_gallery_register_custom_post' );
		$this->loader->sp_gallery_add_action( 'init', $plugin_admin, 'sp_gallery_load_custom_fields' );
		$this->loader->sp_gallery_add_action( 'init', $plugin_admin, 'sp_gallery_register_block' );
		$this->loader->sp_gallery_add_action( 'delete_attachment', $plugin_admin, 'sp_gallery_delete_attachments_from_gallery', 10, 2 );
		$this->loader->sp_gallery_add_action( 'admin_menu', $plugin_admin_settings, 'sp_gallery_register_settings_page', 20 );
		$this->loader->sp_gallery_add_action( 'admin_init', $plugin_admin_settings, 'sp_gallery_settings_init' );
		$this->loader->sp_gallery_add_action( 'update_option_sp_gallery_slug', $plugin_admin, 'sp_gallery_flush_rewrite_on_slug_change', 10, 3 );
		$this->loader->sp_gallery_add_action( 'admin_notices', $plugin_admin, 'sp_gallery_review_notice' );
		$this->loader->sp_gallery_add_action( 'admin_enqueue_scripts', $plugin_admin, 'sp_gallery_add_upgrade_css' );
		$this->loader->sp_gallery_add_action( 'elementor/preview/enqueue_scripts', $plugin_admin, 'sp_gallery_enqueue_elementor_editor_scripts' );
		$this->loader->sp_gallery_add_action( 'rwmb_sp_gallery_image_sorting_after_save_field', $plugin_admin, 'sp_gallery_sort_images', 30, 5 );
		$this->loader->sp_gallery_add_action( 'rwmb_sp_gallery_color_primary_after_save_field', $plugin_admin, 'sp_gallery_after_color_primary_save', 30, 5 );
		$this->loader->sp_gallery_add_action( 'elementor/widgets/register', $plugin_admin, 'sp_gallery_register_elementor_widget', 10 );
		$this->loader->sp_gallery_add_action( 'elementor/elements/categories_registered', $plugin_admin, 'sp_gallery_add_elementor_category' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_admin_rest, 'register_save_field_route' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_admin_rest, 'register_dismiss_notice_route' );
		$this->loader->sp_gallery_add_filter( 'manage_edit-shutterpress-gallery_columns', $plugin_admin, 'sp_gallery_add_image_count_column' );
		$this->loader->sp_gallery_add_filter( 'manage_shutterpress-gallery_posts_columns', $plugin_admin, 'sp_gallery_add_image_count_column' );
		$this->loader->sp_gallery_add_action( 'manage_shutterpress-gallery_posts_custom_column', $plugin_admin, 'sp_gallery_render_image_count_column', 10, 2 );
		$this->loader->sp_gallery_add_filter( 'rwmb_meta_boxes', $plugin_admin_meta_boxes, 'register_meta_boxes', 10 );
		$this->loader->sp_gallery_add_filter( 'plugin_action_links', $plugin_admin, 'sp_gallery_upgrade_link', 99, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 * @return void
	 */
	protected function sp_gallery_define_public_hooks() {
		$plugin_public      = new Shutterpress_Gallery_Public( $this->sp_gallery_get_plugin_name(), $this->sp_gallery_get_version() );
		$plugin_public_rest = new Shutterpress_Gallery_REST();

		$this->loader->sp_gallery_add_action( 'wp_enqueue_scripts', $plugin_public, 'sp_gallery_enqueue_styles' );
		$this->loader->sp_gallery_add_action( 'wp_enqueue_scripts', $plugin_public, 'sp_gallery_enqueue_scripts' );
		$this->loader->sp_gallery_add_action( 'wp_enqueue_scripts', $plugin_public, 'sp_gallery_localize_frontend_data' );
		$this->loader->sp_gallery_add_action( 'init', $plugin_public, 'sp_gallery_register_gallery_shortcode' );
		$this->loader->sp_gallery_add_action( 'wp', $plugin_public, 'sp_gallery_disable_post_navigation_on_gallery_pages' );
		$this->loader->sp_gallery_add_action( 'template_redirect', $plugin_public, 'sp_gallery_sync_liked_images_before_page_load' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_public_rest, 'register_user_data_route' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_public_rest, 'register_like_toggle_route' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_public_rest, 'register_download_route' );
		$this->loader->sp_gallery_add_action( 'rest_api_init', $plugin_public_rest, 'register_gallery_route' );
		$this->loader->sp_gallery_add_filter( 'the_content', $plugin_public, 'sp_gallery_display_gallery', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function sp_gallery_run() {
		$this->loader->sp_gallery_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 *
	 * @return string The name of the plugin.
	 */
	public function sp_gallery_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return Shutterpress_Gallery_Loader Orchestrates the hooks of the plugin.
	 */
	public function sp_gallery_get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function sp_gallery_get_version() {
		return $this->version;
	}
}
