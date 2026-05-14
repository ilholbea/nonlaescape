<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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
 * Handles plugin activation routines for ShutterPress Gallery.
 *
 * Called by WordPress on activation, this class registers the custom post type
 * via the admin layer, flushes rewrite rules so gallery URLs work immediately,
 * and seeds install metadata used for analytics and onboarding. It also triggers
 * an activation tracking event when available.
 *
 * @since 1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */
class Shutterpress_Gallery_Activator {

	/**
	 * Handles tasks that run on plugin activation.
	 *
	 * This includes registering the custom post type, flushing rewrite rules,
	 * and recording the installation timestamp if it's the first activation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function sp_gallery_activate() {

		require_once SP_GALLERY_DIR . 'src/admin/Shutterpress_Gallery_Admin.php';
		require_once SP_GALLERY_DIR . 'src/includes/Shutterpress_Gallery_Debug.php';
		require_once SP_GALLERY_DIR . 'src/includes/Shutterpress_Gallery_Tracking.php';

		$debug = new Shutterpress_Gallery_Debug();
		

		
		Shutterpress_Gallery_Admin::sp_gallery_register_custom_post();

		
		flush_rewrite_rules();

		if ( ! get_option( 'sp_gallery_first_installed' ) ) {
			
			add_option( 'sp_gallery_first_installed', time() ); 
		}

		if ( ! get_option( 'sp_gallery_install_id' ) ) {
			add_option( 'sp_gallery_install_id', wp_generate_uuid4() );
		}

		add_option( 'sp_gallery_enable_virtualiser', 1 );
		add_option( 'sp_gallery_virtualiser_low_conflict_mode', 0 );

		
		if ( class_exists( Shutterpress_Gallery_Tracking::class ) && get_option( 'sp_gallery_install_id' ) && ! get_option( 'sp_gallery_activation_tracked' ) ) {
			$install_timestamp = get_option( 'sp_gallery_first_installed' ) ? get_option( 'sp_gallery_first_installed' ) : null;
			$now               = time();

			$tracking = new Shutterpress_Gallery_Tracking();

			$tracking->track(
				'Shutterpress Gallery Plugin Activated',
				array(
					'plugin_name'          => 'Shutterpress Gallery',
					'plugin_version'       => defined( 'SP_GALLERY_VERSION' ) ? SP_GALLERY_VERSION : 'unknown',
					'install_timestamp'    => $install_timestamp,
					'install_time'         => $install_timestamp ? gmdate( 'Y-m-d H:i:s', (int) $install_timestamp ) : null,
					'activation_timestamp' => $now,
					'activation_time'      => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);
			$tracking->people_set(
				array(
					'Shutterpress Gallery Version'         => defined( 'SP_GALLERY_VERSION' ) ? SP_GALLERY_VERSION : 'unknown',
					'Shutterpress Gallery Activated'       => true,
					'Shutterpress Gallery install timestamp' => $install_timestamp,
					'Shutterpress Gallery install time'    => $install_timestamp ? gmdate( 'Y-m-d H:i:s', (int) $install_timestamp ) : null,
					'Shutterpress Gallery activation timestamp' => $now,
					'Shutterpress Gallery activation time' => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);

			update_option( 'sp_gallery_activation_tracked', 1 );
			$debug->log( 'Shutterpress Gallery activation tracked' );
		}
	}
}
