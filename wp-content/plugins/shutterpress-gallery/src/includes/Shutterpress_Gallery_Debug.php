<?php
/**
 * Provides debug logging for Shutterpress Gallery plugin events.
 *
 * This class manages writing debug messages and context to a custom log file,
 * based on whether debugging is enabled in plugin options.
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
 * Debug logger for ShutterPress Gallery.
 *
 * Writes formatted messages to a plugin specific log file when debugging is
 * enabled in plugin options. Provides lazy loaded enable flags, manual enable
 * and disable helpers, and a simple context aware log API that accepts arrays,
 * strings, and mixed values.
 *
 * @since 1.3.7
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_Debug {

	/**
	 * Absolute path to the debug log file.
	 *
	 * @var string
	 */
	private $log_file = '';
	/**
	 * Whether logging is enabled, null indicates the value has not been initialised yet.
	 *
	 * @var bool|null
	 */
	private $enabled = null;

	/**
	 * Initialises the debug logger for the current request.
	 *
	 * Sets the log file path, records the request URI for context, then lazily
	 * loads the debug enabled option. When enabled, writes an initial message to
	 * confirm that debugging has started.
	 *
	 * @since 1.3.7
	 * @return void
	 */
	public function init() {
		$this->log_file = WP_CONTENT_DIR . '/shutterpress-debug.log';
		$req_uri        = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$this->log( 'Server request URI', array( 'uri' => $req_uri ) );

		
		if ( $this->enabled === null ) {
			$this->enabled = get_option( 'sp_gallery_debug', false );
		}

		if ( $this->enabled ) {
			$this->log( 'Debugging initialized.' );
		}
	}

	/**
	 * Logs a message to the debug log file if debugging is enabled.
	 *
	 * @param string $message The log message.
	 * @param array  $context Optional. Additional context to include.
	 * @param string $level   Optional. The log level (e.g., 'INFO', 'ERROR'). Default 'INFO'.
	 * @return void
	 */
	public function log( string $message, array $context = array(), string $level = 'INFO' ): void {
		if ( $this->enabled === null ) {
			$this->enabled = get_option( 'sp_gallery_debug', false );
		}

		if ( ! $this->enabled ) {
			return;
		}

		$timestamp         = gmdate( 'Y-m-d H:i:s' );
		$level             = strtoupper( sanitize_text_field( $level ) );
		$formatted_message = "[{$timestamp}] [{$level}] {$message}";

		if ( ! empty( $context ) ) {
			foreach ( $context as $key => $val ) {
				$formatted_message .= "\n--- {$key} ---\n";
				$context_str        = ( is_object( $val ) || is_array( $val ) )
					? ( print_r( $val, true ) ) 
					: (string) $val;
				$formatted_message .= $context_str;
			}
		}

		
        
		error_log( $formatted_message . PHP_EOL, 3, $this->get_log_file() );
	}

	/**
	 * Retrieves the path to the debug log file.
	 *
	 * @return string The full path to the log file.
	 */
	private function get_log_file() {
		return $this->log_file ? $this->log_file : WP_CONTENT_DIR . '/shutterpress-debug.log';
	}

	/**
	 * Enables debugging manually regardless of the saved option.
	 * Useful for development overrides.
	 *
	 * @return void
	 */
	public function enable_manually(): void {
		$this->enabled = true;
		$this->log( 'Debugging manually enabled.' );
	}

	/**
	 * Disables debugging manually and logs the action.
	 *
	 * @return void
	 */
	public function disable_manually(): void {
		$this->log( 'Debugging manually disabled.' );
		$this->enabled = false;
	}
}
