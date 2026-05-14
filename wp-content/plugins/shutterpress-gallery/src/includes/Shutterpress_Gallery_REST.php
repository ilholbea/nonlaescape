<?php
/**
 * ShutterPress Gallery REST endpoints.
 *
 * Declares the plugin REST API, including routes for saving meta,
 * fetching user data, dismissing the review notice, toggling likes,
 * secure downloads, and paginated gallery responses for infinite scroll.
 *
 * @link      https://shutterpress.io
 * @since     1.4.0
 * @package   Shutterpress_Gallery
 * @subpackage Includes
 */

namespace Shutterpress\Gallery;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * REST API controller for ShutterPress Gallery.
 *
 * Registers and handles routes for saving meta fields, fetching user data,
 * dismissing the review notice, toggling image likes, serving secure downloads,
 * and paginated gallery responses for infinite scroll.
 * Uses capability checks, nonces, and referer validation where appropriate
 * and returns `WP_REST_Response` or `WP_Error` objects.
 *
 * @since 1.4.0
 * @package Shutterpress_Gallery
 * @subpackage Includes
 */
class Shutterpress_Gallery_REST {

	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Utils $utils;

	/**
	 * Class constructor.
	 *
	 * Sets up the REST controller by initializing the debug logger used to
	 * record request details and error context during REST operations.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function __construct() {
		$this->debug = new Shutterpress_Gallery_Debug();
		$this->utils = new Shutterpress_Gallery_Utils();
	}

	/**
	 * Private helper to register a REST route with common defaults.
	 *
	 * @param string        $route               The route path.
	 * @param string|array  $methods             The HTTP methods allowed for the route.
	 * @param string        $callback            The callback function to handle the route.
	 * @param array         $args                Optional. Additional arguments for the route.
	 * @param callable|null $permission_callback Optional. The permission callback function.
	 *
	 * @since 1.7.7
	 * @return void
	 */
	protected function register_route( string $route, $methods, string $callback, array $args = array(), callable $permission_callback = null ): void {
		register_rest_route(
			'shutterpress/v1',
			$route,
			array(
				'methods'             => $methods,
				'callback'            => array( $this, $callback ),
				'args'                => $args,
				'permission_callback' => $permission_callback ? $permission_callback :
					function () {
						return current_user_can( 'edit_posts' );
					},
			)
		);
	}

	/**
	 * Registers a custom REST API route to save a single metabox field.
	 *
	 * This route is used by the admin UI to save custom field values (like image sorting or image arrays)
	 * for a Shutterpress Gallery post via a RESTful interface. It expects `post_id`, `field_id`,
	 * and `field_value` in the request payload.
	 *
	 * Route: `/wp-json/shutterpress/v1/save-meta`
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_save_field_route() {
		$this->register_route(
			'/save-meta',
			'POST',
			'rest_save_metabox_field',
			array(
				'post_id'     => array(
					'required' => true,
					'type'     => 'integer',
				),
				'field_id'    => array(
					'required' => true,
					'type'     => 'string',
				),
				'field_value' => array( 'required' => true ),
			)
		);
	}

	/**
	 * Handles saving of a single metabox field via REST API.
	 *
	 * This endpoint supports saving both simple and complex field types, such as:
	 * - Image sorting values (`sp_gallery_image_sorting`)
	 * - Image ID arrays (`_sp_gallery_images_array`)
	 * - Other custom text or numeric fields.
	 *
	 * If sorting is required after image updates, it applies the appropriate sort type automatically.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_REST_Request $request The REST request object containing post ID, field ID, and value.
	 * @phpstan-param \WP_REST_Request<array<string, mixed>> $request
	 *
	 * @return WP_REST_Response|WP_Error A REST response on success, or WP_Error on failure.
	 */
	public function rest_save_metabox_field( WP_REST_Request $request ) {
		$post_id  = $request->get_param( 'post_id' );
		$field_id = str_replace( '[]', '', sanitize_text_field( $request->get_param( 'field_id' ) ) );

		if ( ! $post_id || ! $field_id ) {
			return new WP_Error( 'missing_fields', 'Missing required fields', array( 'status' => 400 ) );
		}
		$field_value = $request->get_param( 'field_value' );

		$result = $this->utils->save_metabox_field( $post_id, $field_id, $field_value );

		$this->debug->log( 'Save Metabox Result', array( 'result' => $result ), 'DEBUG' );
		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $result->get_error_message(),
					'data'    => $result,
				)
			);
		}

		if ( isset( $result['wp_error'] ) && is_wp_error( $result['wp_error'] ) ) {
			return new WP_REST_Response(
				array(
					'success'     => false,
					'message'     => $result['wp_error']->get_error_message(),
					'image_order' => isset( $result['image_order'] ) ? $result['image_order'] : '',
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success'     => true,
				'message'     => $result['message'],
				'image_order' => isset( $result['image_order'] ) ? $result['image_order'] : '',
			),
			200
		);
	}

	/**
	 * Registers a public REST API route to retrieve user-specific gallery data.
	 *
	 * This route is accessible at `/wp-json/shutterpress/v1/user-data` and is used to fetch
	 * data such as the user's liked images. It is publicly accessible but only returns
	 * full data if the user is logged in.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_user_data_route() {
		$this->register_route(
			'/user-data',
			'GET',
			'rest_get_user_data_for_api',
			array(),
			function () {
						return current_user_can( 'read' );
			},
		);
	}
	/**
	 * Retrieves the currently logged-in user's liked gallery images via REST API.
	 *
	 * This is the callback for the `/shutterpress/v1/user-data` route. If the user is authenticated,
	 * it returns an array containing:
	 * - `user_id`: The WordPress user ID.
	 * - `liked_images`: An array of image attachment IDs the user has liked.
	 * - `logged_in`: A boolean indicating login status.
	 *
	 * If the user is not logged in, the request is rejected with a 401 error.
	 *
	 * @since 1.4.0
	 *
	 * @return WP_REST_Response|WP_Error JSON response with user data or error if unauthenticated.
	 */
	public function rest_get_user_data_for_api() {
		
		if ( is_user_logged_in() ) {
			$user_id      = get_current_user_id();
			$gallery      = new Shutterpress_Gallery_Gallery();
			$liked_images = $gallery->get_liked_images( $user_id );
			return rest_ensure_response(
				array(
					'user_id'      => $user_id,
					'liked_images' => array_map( 'intval', (array) $liked_images ),
					'logged_in'    => is_user_logged_in(),
				)
			);
		}
		return new WP_Error( 'not_logged_in', 'User not logged in.', array( 'status' => 401 ) );
	}

	/**
	 * Registers a custom REST API route to dismiss the review notice.
	 *
	 * This endpoint allows authenticated administrators to mark the plugin review
	 * notice as dismissed. It is used to store the dismissal time in user meta.
	 *
	 * Route: `/wp-json/shutterpress/v1/dismiss-review-notice`
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_dismiss_notice_route() {
		$this->register_route(
			'/dismiss-review-notice',
			'POST',
			'rest_dismiss_review_notice',
			array()
		);
	}

	/**
	 * Handles the REST API request to dismiss the plugin review notice.
	 *
	 * Stores the current timestamp in the user's meta to indicate that the notice
	 * was dismissed. This prevents the notice from appearing again for a set duration.
	 *
	 * Used by the `/shutterpress/v1/dismiss-review-notice` REST endpoint.
	 *
	 * @since 1.4.0
	 *
	 * @return WP_REST_Response|WP_Error A REST response on success, or WP_Error if the user is not logged in.
	 */
	public function rest_dismiss_review_notice() {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return new WP_Error( 'not_logged_in', 'User not logged in.', array( 'status' => 401 ) );
		}

		update_user_meta( $user_id, 'sp_gallery_review_dismissed', time() );
		do_action( 'sp_gallery_review_notice_dismissed', $user_id );
		return rest_ensure_response( array( 'message' => 'Review notice dismissed' ) );
	}

	/**
	 * Registers a public REST API route to toggle the user's "like" status for an image.
	 *
	 * The route is accessible at `/wp-json/shutterpress/v1/toggle-like` and expects a POST
	 * request with an `image_id`. It returns the new like state (liked or unliked) for that image.
	 *
	 * Only logged-in users are permitted to access this route.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_like_toggle_route() {
		$this->register_route(
			'/toggle-like',
			'POST',
			'rest_toggle_user_like',
			array(
				'image_id' => array(
					'required' => true,
					'type'     => 'integer',
				),
			),
			function () {
						return current_user_can( 'read' );
			},
		);
	}

	/**
	 * Retrieves liked image data for the currently logged-in user via REST API.
	 *
	 * This method is the callback for the `/shutterpress/v1/user-data` route.
	 * It fetches the user's liked images from meta and returns them along with
	 * the user ID and login status. If the user is not logged in, it returns a 401 error.
	 *
	 * @since 1.4.0
	 * @param WP_REST_Request $request The request object.
	 *   @phpstan-param \WP_REST_Request<array<string, mixed>> $request
	 *
	 * @return WP_REST_Response|WP_Error The REST response with user data, or an error if unauthenticated.
	 */
	public function rest_toggle_user_like( WP_REST_Request $request ) {
		
		if ( is_user_logged_in() ) {
			try {
				$this->debug->log( 'Toggle Like Request Endpoint', array( $request->get_json_params() ), 'DEBUG' );

				$user_id  = get_current_user_id();
				$image_id = (int) $request->get_param( 'image_id' );

				if ( ! $image_id ) {
					return new WP_Error( 'missing_image_id', 'Image ID is required.', array( 'status' => 400 ) );
				}

				$liked_images = get_user_meta( $user_id, 'sp_gallery_liked_images', true ) ? get_user_meta( $user_id, 'sp_gallery_liked_images', true ) : array();

				if ( in_array( $image_id, $liked_images, true ) ) {
					$liked_images = array_diff( $liked_images, array( $image_id ) );
					update_user_meta( $user_id, 'sp_gallery_liked_images', $liked_images );
					return rest_ensure_response(
						array(
							'liked'   => false,
							'message' => 'Image unliked',
						)
					);
				}
				$liked_images[] = $image_id;
				update_user_meta( $user_id, 'sp_gallery_liked_images', $liked_images );
				return rest_ensure_response(
					array(
						'liked'   => true,
						'message' => 'Image liked',
					)
				);
			} catch ( \Throwable $e ) {
				return new WP_Error( 'toggle_like_failed', 'Internal error occurred.', array( 'status' => 500 ) );
			}
		}
		return new WP_Error( 'not_logged_in', 'User not logged in.', array( 'status' => 401 ) );
	}
	/**
	 * Registers a public REST API route to securely download a gallery image.
	 *
	 * This route is accessible at `/wp-json/shutterpress/v1/download-image` and supports
	 * both authenticated and token-based access. It expects `gallery_id` and `image_id` as required
	 * parameters, along with either a `download_nonce` (for logged-in users) or a `download_token`
	 * (for guests with secure tokens).
	 *
	 * The permission callback verifies user capability or validates the download token.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function register_download_route() {
		register_rest_route(
			'shutterpress/v1',
			'/download-image',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_download_image' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					$gallery_id = (int) $request->get_param( 'gallery_id' );
					$image_id   = (int) $request->get_param( 'image_id' );

					if ( is_user_logged_in() ) {
						$nonce = sanitize_text_field( $request->get_param( 'download_nonce' ) );
						return wp_verify_nonce( $nonce, "sp_gallery_download_{$image_id}_{$gallery_id}" );
					}
					$ref = wp_get_raw_referer();
					if ( ! $ref || wp_parse_url( $ref, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
						return false;
					}
					return true;
				},
				'args'                => array(
					'gallery_id'     => array(
						'required' => true,
						'type'     => 'integer',
					),
					'image_id'       => array(
						'required' => true,
						'type'     => 'integer',
					),
					'download_nonce' => array(
						'required' => false,
						'type'     => 'string',
					),
					'download_token' => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);
	}

	/**
	 * Handles the secure download of a gallery image via REST API.
	 *
	 * This method is the callback for the `/shutterpress/v1/download-image` route.
	 * It verifies the provided `gallery_id` and `image_id`, ensures the image exists
	 * in the specified gallery, and then streams the image file for download.
	 *
	 * Requires either a valid nonce (for logged-in users) or a download token (for guests).
	 * Logs the download request and triggers the `sp_gallery_image_downloaded` action.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_REST_Request $request The REST request object containing gallery_id, image_id, and auth token/nonce.
	 * @phpstan-param \WP_REST_Request<array<string, mixed>> $request.
	 *
	 * @return WP_REST_Response|WP_Error Will return 403 if access is denied, 404 if file is missing, or stream the file and exit.
	 */
	public function rest_download_image( WP_REST_Request $request ) {
		$gallery_id = intval( $request->get_param( 'gallery_id' ) );
		$image_id   = intval( $request->get_param( 'image_id' ) );
		$nonce      = sanitize_text_field( $request->get_param( 'download_nonce' ) );

		$this->debug->log( 'REST download image request', compact( 'gallery_id', 'image_id', 'nonce' ) );
		nocache_headers();
		header( 'Cache-Control: no-store, no-cache, must-revalidate, private' );

		$gallery_images = get_post_meta( $gallery_id, '_sp_gallery_images_array', true );
		if ( ! is_array( $gallery_images ) || ! in_array( $image_id, $gallery_images, true ) ) {
			return new WP_REST_Response( array( 'message' => 'Unauthorized access' ), 403 );
		}

		$download_fullsize = get_post_meta( $gallery_id, 'sp_gallery_download_fullsize_image', true );
		$original_url      = wp_get_original_image_url( $image_id );

		$image_url = $download_fullsize && $original_url ? $original_url : wp_get_attachment_url( $image_id );

		$image_url = apply_filters( 'sp_gallery_download_url', $image_url, $image_id, $gallery_id );

		if ( ! $image_url ) {
			return new WP_REST_Response( array( 'message' => 'File not found' ), 404 );
		}

		do_action( 'sp_gallery_image_downloaded', $gallery_id, $image_id );

		$files = new Shutterpress_Gallery_Files();

		
		$files->download_file( $image_url );
		exit;
	}

	/**
	 * Registers a public REST API route for gallery infinite scroll.
	 *
	 * Adds the `/wp-json/shutterpress/v1/gallery/scroll` endpoint, which allows public (unauthenticated)
	 * requests for paginated gallery images. The route responds to GET requests and uses the
	 * `rest_infinite_scroll` method as its callback.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function register_gallery_route() {

		$this->register_route(
			'/gallery',
			'POST',
			'rest_gallery',
			array(),
			'__return_true',
		);
	}

	/**
	 * Handles the REST API request for gallery infinite scroll pagination.
	 *
	 * Returns the HTML markup for the next page of images in a gallery, for use with infinite scroll AJAX requests.
	 * Accepts `gallery_id` and `page` as GET parameters. Returns the generated HTML for the images and the next page number.
	 *
	 * This is the callback for the `/wp-json/shutterpress/v1/gallery/scroll` REST endpoint.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_REST_Request $request The REST request containing 'gallery_id' and 'page' parameters.
	 * @phpstan-param \WP_REST_Request<array<string, mixed>> $request
	 * @return WP_REST_Response|WP_Error The response containing image HTML and the next page number.
	 */
	public function rest_gallery( WP_REST_Request $request ) {
		
		$gallery_id = (int) $request->get_param( 'gallery_id' );
		if ( ! $gallery_id ) {
			return new WP_REST_Response(
				array(
					'code'    => 'missing_gallery_id',
					'message' => 'Parameter "gallery_id" is required.',
				),
				400
			);
		}

		
		$page = (int) $request->get_param( 'page' );
		$page = $page > 0 ? $page : 1;

		
		$attributes = $request->get_param( 'attributes' );
		if ( is_string( $attributes ) ) {
			$decoded = json_decode( $attributes, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$attributes = $decoded;
			}
		}
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		$gallery = new Shutterpress_Gallery_Gallery( $gallery_id );
		$gallery->set_attributes( $attributes );

		$images_per_page = max( 1, (int) $gallery->get_images_per_page() );
		$images_count    = (int) $gallery->get_gallery_images_count(); 

		
		$has_more  = $page * $images_per_page < $images_count;
		$next_page = $has_more ? $page + 1 : false;

		
		$html = $gallery->get_the_gallery( $gallery_id, $page, false );

		return new WP_REST_Response(
			array(
				'html'     => $html,
				'page'     => $page,
				'nextPage' => $next_page,
				'gallery'  => $gallery_id,
			),
			200
		);
	}
}
