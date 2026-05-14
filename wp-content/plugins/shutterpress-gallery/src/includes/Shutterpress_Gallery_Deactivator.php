<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
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
 * Handles plugin deactivation for ShutterPress Gallery.
 *
 * Flushes rewrite rules, records a deactivation tracking event when the
 * tracking class is available, and clears the stored activation flag.
 *
 * @since 1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_Deactivator {

	/**
	 * Runs on plugin deactivation.
	 *
	 * Flushes rewrite rules so custom routes are removed immediately. When
	 * tracking is available, sends a deactivation event and updates people
	 * properties, then removes the activation tracked option.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function sp_gallery_deactivate() {

		require_once SP_GALLERY_DIR . 'src/includes/Shutterpress_Gallery_Tracking.php';
		
		flush_rewrite_rules();

		if ( class_exists( Shutterpress_Gallery_Tracking::class ) ) {
			$now = time();

			$tracking = new Shutterpress_Gallery_Tracking();

			$tracking->track(
				'Shutterpress Gallery Plugin Deactivated',
				array(
					'plugin_name'          => 'Shutterpress Gallery',
					'plugin_version'       => defined( 'SP_GALLERY_VERSION' ) ? SP_GALLERY_VERSION : 'unknown',
					'deactivate_timestamp' => $now,
					'deactivate_time'      => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);
			$tracking->people_set(
				array(
					'Shutterpress Gallery Activated' => false,
					'Shutterpress Gallery Deactivation timestamp' => $now,
					'Shutterpress Gallery Deactivation time' => gmdate( 'Y-m-d H:i:s', $now ),
				)
			);
			if ( get_option( 'sp_gallery_activation_tracked' ) ) {
				delete_option( 'sp_gallery_activation_tracked' );
			}
		}
	}
}
