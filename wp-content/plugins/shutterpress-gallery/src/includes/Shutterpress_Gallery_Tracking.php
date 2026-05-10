<?php
/**
 * Handles all tracking for Shutterpress Gallery plugin events.
 *
 * This class manages anonymous and user-based event tracking for important
 * plugin lifecycle actions such as installation, activation, updates, and deactivation.
 * It ensures tracking only happens if user consent has been given.
 *
 * @since      1.3.7
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
 * Event and profile tracking for ShutterPress Gallery.
 *
 * Sends signed JSON payloads to a secure proxy for install, activation,
 * update, and deactivation events, as well as user profile updates. Respects
 * the tracking consent option, skips all network calls during tests, and
 * logs requests when debug is enabled.
 *
 * @since 1.3.7
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_Tracking {

	protected Shutterpress_Gallery_Debug $debug;

	/**
	 * Class constructor.
	 *
	 * Prepares the tracking client by initialising the debug logger used to
	 * record request context and errors.
	 *
	 * @since 1.3.7
	 * @return void
	 */
	public function __construct() {
		$this->debug = new Shutterpress_Gallery_Debug();
	}

	/**
	 * Sends a tracking event to a secure proxy.
	 *
	 * @since 1.3.7
	 *
	 * @param string $event      The name of the event.
	 * @param array  $properties Optional. Additional properties to send.
	 *
	 * @return string|void
	 */
	public function track( $event, $properties = array() ) {
		if ( (int) get_option( 'sp_gallery_tracking_allowed', 0 ) !== 1 ) {
			return 'Tracking disabled by user';
		}
		if ( defined( 'SHUTTERPRESS_TESTING' ) ) {
			return;
		}

		$server_software = $this->get_server_software();

		$body = array(
			'event'      => $event,
			'properties' => array_merge(
				array(
					'distinct_id'     => $this->get_install_id(),
					'site_hash'       => md5( home_url() ),
					'wp_version'      => get_bloginfo( 'version' ),
					'php_version'     => phpversion(),
					'server_software' => $server_software,
					'site_language'   => get_locale(),
					'is_multisite'    => is_multisite(),
				),
				$properties
			),
		);
		$this->debug->log( 'Running tracking event: ' . $event, array( $body ), 'DEBUG' );

		$token     = 'f82e87d34d5ca9746f9e3c02d3b1e3f91f35c6cf36d02de242d0d0a74e6a6a57';
		$body_json = wp_json_encode( $body );
		if ( ! $body_json ) {
			return;
		}
		$signature = hash_hmac( 'sha256', $body_json, $token );

		$response = wp_remote_post(
			'https://webhook.shutterpress.io/tracking.php',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'X-SP-Signature' => $signature,
				),
				'body'    => $body_json,
				'timeout' => 5,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->debug->log( 'Proxy error:', array( 'message', $response->get_error_message() ), 'ERROR' );

			$code = wp_remote_retrieve_response_code( $response );
			if ( $code !== 200 ) {
				$this->debug->log( 'Proxy returned HTTP', array( 'code' => $code ), 'ERROR' );
			}
		}
	}



	/**
	 * Sends a user profile update to the secure proxy.
	 *
	 * @since 1.4.0
	 *
	 * @param array $profile_properties Associative array of profile properties to update.
	 *
	 * @return string|void
	 */
	public function people_set( $profile_properties = array() ) {
		if ( (int) get_option( 'sp_gallery_tracking_allowed', 0 ) !== 1 ) {
			return 'Tracking disabled by user';
		}
		if ( defined( 'SHUTTERPRESS_TESTING' ) ) {
			return;
		}

		$server_software = $this->get_server_software();

		$body = array(
			'distinct_id' => $this->get_install_id(),
			'profile'     => array_merge(
				array(
					'Shutterpress Gallery Wordpress Version' => get_bloginfo( 'version' ),
					'Shutterpress Gallery PHP Version'     => phpversion(),
					'Shutterpress Gallery Server Software' => $server_software,
					'Shutterpress Gallery Site Language'   => get_locale(),
					'Shutterpress Gallery is Multisite'    => is_multisite(),
				),
				$profile_properties
			),
		);

		$this->debug->log( 'running people Set', array( $body ), 'DEBUG' );

		$token     = 'c93f5e5fc3a4d63fd96d06c06a73cb12b8d6ac356c9614378dc939663198abef'; 
		$body_json = wp_json_encode( $body );
		if ( ! $body_json ) {
			return;
		}
		$signature = hash_hmac( 'sha256', $body_json, $token );

		$response = wp_remote_post(
			'https://webhook.shutterpress.io/people-set.php',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'X-SP-Signature' => $signature,
				),
				'body'    => $body_json,
				'timeout' => 5,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->debug->log( 'Proxy error:', array( 'message', $response->get_error_message() ), 'ERROR' );

			$code = wp_remote_retrieve_response_code( $response );
			if ( $code !== 200 ) {
				$this->debug->log( 'Proxy returned HTTP', array( 'code' => $code ), 'ERROR' );
			}
		}
	}

	/**
	 * Gets the web server software string for telemetry context.
	 *
	 * @since 1.3.7
	 * @return string Server software name and version when available.
	 */
	private function get_server_software() {
		$server_software = 'unknown';

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$server_software = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
		}

		return $server_software;
	}

	/**
	 * Retrieves or generates the install ID used for tracking.
	 *
	 * This ID is unique to each WordPress site installation and persists across sessions.
	 * If the installation ID does not already exist, it will generate a new UUID and store it.
	 *
	 * @since 1.3.7
	 *
	 * @return string Unique distinct ID for this plugin installation.
	 */
	public function get_install_id(): string {
		if ( defined( 'SHUTTERPRESS_TESTING' ) ) {
			return 'testing-install-id';
		}

		$install_id = get_option( 'sp_gallery_install_id' );

		if ( ! $install_id ) {
			$install_id = wp_generate_uuid4();
			update_option( 'sp_gallery_install_id', $install_id );
		}

		return $install_id;
	}
}
