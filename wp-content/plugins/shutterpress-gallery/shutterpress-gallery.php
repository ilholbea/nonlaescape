<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://shutterpress.io
 * @since             1.0.0
 *
 * @package           Shutterpress-Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       ShutterPress Gallery
 * Plugin URI:        https://shutterpress.io
 * Description:       WordPress photo gallery plugin for photographers and creatives. Build fast, responsive image galleries with Masonry, Justified, and Grid layouts, lightbox support, and Infinite Scroll.
 * Version:           1.7.12
 * Author:            ShutterPress
 * Author URI:        https://shutterpress.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shutterpress-gallery
 * Domain Path:       /languages
 */


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Current plugin version.
 */
define( 'SP_GALLERY_VERSION', '1.7.12' );
define( 'SP_GALLERY_NAME', 'shutterpress-gallery' );



define( 'SP_GALLERY_DIR', plugin_dir_path( __FILE__ ) );
define( 'SP_GALLERY_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'SP_GALLERY_MB_PREFIX' ) ) {
	define( 'SP_GALLERY_MB_PREFIX', 'sp_gallery_' );
}

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0.0
 * @return void
 */
function sp_gallery_activate_shutterpress_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'src/includes/Shutterpress_Gallery_Activator.php';
	Shutterpress\Gallery\Shutterpress_Gallery_Activator::sp_gallery_activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since 1.0.0
 * @return void
 */
function sp_gallery_deactivate_shutterpress_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'src/includes/Shutterpress_Gallery_Deactivator.php';
	Shutterpress\Gallery\Shutterpress_Gallery_Deactivator::sp_gallery_deactivate();
}

if ( ! class_exists( 'Shutterpress\\Gallery' ) && ! defined( 'SHUTTERPRESS_GALLERY_INCLUDED' ) ) {
	register_activation_hook( __FILE__, 'sp_gallery_activate_shutterpress_gallery' );
	register_deactivation_hook( __FILE__, 'sp_gallery_deactivate_shutterpress_gallery' );
}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 * @return void
 */
function sp_gallery_run_shutterpress_gallery() {
	$plugin = new \Shutterpress\Gallery\Shutterpress_Gallery();
	$plugin->sp_gallery_run();
}

add_action(
	'plugins_loaded',
	function () {
		if ( ! defined( 'SHUTTERPRESS_GALLERY_INCLUDED' ) && ! class_exists( 'Shutterpress\\Gallery' ) && ! class_exists( 'Shutterpress\\Gallery\\Pro\\Includes\\Shutterpress_Gallery_Pro' ) ) {
			require_once SP_GALLERY_DIR . 'vendor/autoload.php';
			sp_gallery_run_shutterpress_gallery();
		}
	},
	15
);
