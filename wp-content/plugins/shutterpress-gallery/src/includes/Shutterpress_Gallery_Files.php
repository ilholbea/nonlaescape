<?php
/**
 * File helpers for ShutterPress Gallery.
 *
 * Provides reusable utilities for preparing and streaming downloadable assets.
 *
 * @link https://shutterpress.io
 * @since 1.7.8
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */

namespace Shutterpress\Gallery;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}
/**
 * Provides helper methods for serving downloadable gallery assets.
 *
 * Centralises the logic for sending standard download headers and streaming files
 * from either local storage or remote sources so REST controllers and other callers
 * can deliver binary responses without duplicating boilerplate.
 *
 * @since 1.7.8
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_Files {

	/**
	 * Sends standard download headers for binary responses.
	 *
	 * Adds headers that force the browser to download the response as a file and optionally
	 * sets the content length when known.
	 *
	 * @since 1.7.8
	 *
	 * @param string $filename        The filename suggested to the browser.
	 * @param int    $content_length  Optional. Number of bytes in the response body. Default 0 (unknown).
	 *
	 * @return void
	 */
	private function send_download_headers( string $filename, int $content_length = 0 ): void {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Expires: 0' );

		if ( $content_length > 0 ) {
			header( 'Content-Length: ' . $content_length );
		}
	}


	/**
	 * Streams a file for download, checking if it's stored locally or remotely.
	 *
	 * This function first checks whether the given file URL points to a local file.
	 * If the file is local, it converts the URL to a file path and streams it using `readfile()`.
	 * If the file is stored externally (e.g., Amazon S3, CloudFront), it fetches the file using cURL
	 * and streams it to the user, forcing a download.
	 *
	 * @since 1.3.3
	 *
	 * @param string $file_reference Absolute file path or URL to stream.
	 * @param array  $args           {
	 *     Optional. Additional options for the download response.
	 *
	 *     @type string        $filename      Override the filename suggested to the browser.
	 *     @type bool          $return_errors When true, return a WP_Error instead of sending JSON.
	 *     @type callable|null $on_success    Callback executed on success before exit. Receives an
	 *                                        array containing the `source` (local|remote) and `filename`.
	 * }
	 *
	 * @return \WP_Error|null Returns a WP_Error when $return_errors is true and an error occurs. Exits on success.
	 */
	public function download_file( string $file_reference, array $args = array() ) {
		$defaults = array(
			'filename'      => '',
			'return_errors' => false,
			'on_success'    => null,
		);

		$args = wp_parse_args( $args, $defaults );

		$upload_dir  = wp_get_upload_dir();
		$base_url    = $upload_dir['baseurl'];
		$base_path   = wp_normalize_path( $upload_dir['basedir'] );
		$looks_local = strpos( $file_reference, $base_url ) === 0;
		$is_url      = (bool) filter_var( $file_reference, FILTER_VALIDATE_URL );
		$file_url    = $is_url ? $file_reference : '';

		$local_path    = '';
		$error_context = array(
			'file_reference' => $file_reference,
		);

		if ( $looks_local ) {
			$file_url                        = $file_reference;
			$is_url                          = true;
			$error_context['normalized_url'] = $file_url;
		}

		if ( ! $looks_local && ! $is_url && str_contains( $file_reference, '://' ) ) {
			$parsed = wp_parse_url( $file_reference );

			if ( ! empty( $parsed['scheme'] ) && ! empty( $parsed['host'] ) ) {
				$path = '';

				if ( isset( $parsed['path'] ) ) {
					$segments = array_map(
						static function ( string $segment ): string {
							return rawurlencode( $segment );
						},
						array_filter(
							explode( '/', $parsed['path'] ),
							static function ( string $segment ): bool {
								return $segment !== '';
							}
						)
					);

					$path = '/' . implode( '/', $segments );
				}

				$file_url = $parsed['scheme'] . '://' . $parsed['host'];

				if ( isset( $parsed['port'] ) ) {
					$file_url .= ':' . $parsed['port'];
				}

				$file_url .= $path;

				if ( isset( $parsed['query'] ) ) {
					$file_url .= '?' . $parsed['query'];
				}

				if ( isset( $parsed['fragment'] ) ) {
					$file_url .= '#' . $parsed['fragment'];
				}

				$is_url = filter_var( $file_url, FILTER_VALIDATE_URL );

				if ( $is_url ) {
					$error_context['normalized_url'] = $file_url;
				}
			}
		}

		if ( $is_url && ! isset( $error_context['normalized_url'] ) ) {
			$error_context['normalized_url'] = $file_url;
		}

		$error = function ( string $code, string $message, int $status = 404 ) use ( $args, &$error_context ) {
			if ( ! empty( $args['return_errors'] ) ) {
				return new \WP_Error( $code, $message, $error_context );
			}

			wp_send_json_error(
				array(
					'message' => $message,
					'code'    => $code,
					'data'    => $error_context,
				),
				$status
			);
		};

		if ( $is_url && strpos( $file_url, $base_url ) === 0 ) {
			$local_path = str_replace( $base_url, $base_path, $file_url );
			$local_path = wp_normalize_path( rawurldecode( $local_path ) );
		} elseif ( ! $is_url ) {
			$local_path = wp_normalize_path( $file_reference );
			if ( strpos( $local_path, $base_path ) === 0 ) {
				$file_url = str_replace( $base_path, $base_url, $local_path );
			}
		}

		$filename = $args['filename'];

		if ( ! empty( $local_path ) ) {
			$error_context['local_path'] = $local_path;
		}

		if ( empty( $filename ) ) {
			if ( $is_url ) {
				$path     = wp_parse_url( $file_reference, PHP_URL_PATH );
				$filename = $path ? basename( $path ) : basename( $file_reference );
				$filename = rawurldecode( $filename );
			} elseif ( ! empty( $local_path ) ) {
				$filename = basename( $local_path );
			} else {
				$filename = basename( $file_reference );
			}
		}

		$filename = $this->sanitize_filename( $filename );

		$send_file = function ( string $contents, string $source ) use ( $filename, $args ) {
			$this->send_download_headers( $filename, strlen( $contents ) );

			while ( ob_get_level() ) {
				ob_end_clean();
			}

			flush();
			
			
			echo $contents;
			flush();

			if ( is_callable( $args['on_success'] ) ) {
				call_user_func(
					$args['on_success'],
					array(
						'source'   => $source,
						'filename' => $filename,
					)
				);
			}

			return true;
		};

		if ( ! empty( $local_path ) ) {
			if ( ! file_exists( $local_path ) ) {
				$error_context['file_exists'] = false;
				return $error( 'file_not_found', 'Unable to read file' );
			}

			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$file_contents = $wp_filesystem->get_contents( $local_path );

			if ( false === $file_contents ) {
				$error_context['file_exists']   = true;
				$error_context['file_readable'] = is_readable( $local_path );
				return $error( 'file_read_error', 'Unable to read file' );
			}

			$send_file( $file_contents, 'local' );
			exit;
		}

		if ( ! $is_url ) {
			return $error( 'invalid_file', 'Failed to retrieve file' );
		}

		$response = wp_remote_get(
			$file_url,
			array(
				'timeout'   => 15,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return $error( 'remote_file_error', 'Failed to retrieve file' );
		}

		$file_contents = wp_remote_retrieve_body( $response );

		$send_file( $file_contents, 'remote' );
		exit;
	}

	/**
	 * Sanitizes a filename while preserving copyright glyphs.
	 *
	 * Substitutes copyright-style symbols with safe placeholders before calling
	 * WordPress core sanitization so their ASCII equivalents survive the process.
	 *
	 * @param string $filename Raw filename provided by the user.
	 *
	 * @return string Sanitized filename that maintains copyright markers.
	 */
	public function sanitize_filename( $filename ) {

		$placeholder_map = array();

		if ( strpbrk( $filename, '©℗®™' ) !== false ) {
			$placeholder_map = array(
				'©' => 'SPG_COPYRIGHT_TOKEN',
				'℗' => 'SPG_PUBLISHED_TOKEN',
				'®' => 'SPG_REGISTERED_TOKEN',
				'™' => 'SPG_TRADEMARK_TOKEN',
			);
			$filename        = strtr( $filename, $placeholder_map );
		}

		if ( function_exists( 'iconv' ) ) {
			$converted = iconv( 'UTF-8', 'ASCII//TRANSLIT', $filename );
			if ( false !== $converted ) {
				$filename = $converted;
			}
		}

		$filename = sanitize_file_name( $filename );

		if ( ! empty( $placeholder_map ) ) {
			$filename = str_replace(
				array_values( $placeholder_map ),
				array( '(c)', '(p)', '(r)', '(tm)' ),
				$filename
			);
		}
		return $filename;
	}
}
