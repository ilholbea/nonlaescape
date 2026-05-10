<?php
/**
 * Class responsible for image-related operations within Shutterpress Gallery.
 *
 * Provides methods for retrieving image metadata, file paths, download URLs,
 * and removing images from associated galleries.
 *
 * @since      1.3.4
 *
 * @updated    1.4.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 *
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

use WP_Query;


if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Handles all image-related operations in ShutterPress Gallery.
 *
 * This class provides methods for managing image metadata, URLs, file paths, captions,
 * and responsive data. It also supports generating complete HTML for gallery images
 * with Lightbox and icon overlays, retrieving EXIF data, and removing images from galleries.
 *
 * @since 1.3.4
 * @package Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */
class Shutterpress_Gallery_Image {

	protected int $attachment_id;
	protected ?object $post = null;
	protected int $post_id;
	protected int $image_id;
	protected array $allowed_svg;
	protected Shutterpress_Gallery_Debug $debug;
	protected Shutterpress_Gallery_Utils $utils;

	/**
	 * Constructor for the Shutterpress_Gallery_Image class.
	 *
	 * Initializes the image object by storing the attachment ID and loading its post object.
	 *
	 * @since 1.3.4
	 *
	 * @param int $image_id The attachment ID of the image.
	 */
	public function __construct( int $image_id = 0 ) {
		$this->image_id = absint( $image_id );

		$this->debug = new Shutterpress_Gallery_Debug();
		$this->utils = new Shutterpress_Gallery_Utils();

		if ( ! wp_attachment_is_image( $this->image_id ) ) {
			$this->debug->log( 'Attachment is not an image', array( 'Attachment ID' => $this->image_id ) );
			return;
		}

		$this->post = get_post( $image_id );

		$this->allowed_svg = $this->utils->allowed_svg;
	}

	/**
	 * Retrieves the ID of the image attachment.
	 *
	 * @since 1.3.4
	 *
	 * @return int The attachment ID.
	 */
	public function get_id() {
		return $this->image_id;
	}

	/**
	 * Retrieves the image URL for a given size.
	 *
	 * @since 1.3.4
	 *
	 * @param string $size The size of the image to retrieve (e.g., 'thumbnail', 'medium', 'full').
	 *
	 * @return string|false The image URL or false if not available.
	 */
	public function get_url( $size = 'full' ) {
		return wp_get_attachment_image_url( $this->image_id, $size );
	}

	/**
	 * Retrieves the responsive srcset attribute for the image.
	 *
	 * Useful for generating responsive image markup.
	 *
	 * @since 1.3.4
	 *
	 * @param string $size The size to use when generating the srcset (e.g., 'medium', 'large', 'full').
	 *
	 * @return string|false A srcset string or false if not available.
	 */
	public function get_srcset( $size = 'full' ) {
		return wp_get_attachment_image_srcset( $this->image_id, $size );
	}

	/**
	 * Retrieves the sizes attribute for the image.
	 *
	 * This is used in responsive image markup to define display width conditions.
	 *
	 * @since 1.3.4
	 *
	 * @param string $size The image size to retrieve sizes for (e.g., 'medium', 'large', 'full').
	 *
	 * @return string|false A sizes string or false if not available.
	 */
	public function get_sizes( $size = 'full' ) {
		return wp_get_attachment_image_sizes( $this->image_id, $size );
	}

	/**
	 * Retrieves the alt text of the image attachment.
	 *
	 * This is commonly used for accessibility and SEO purposes.
	 *
	 * @since 1.3.4
	 *
	 * @return string The alt text, or an empty string if none is set.
	 */
	public function get_alt() {
		return get_post_meta( $this->image_id, '_wp_attachment_image_alt', true );
	}

	/**
	 * Retrieves the caption (post excerpt) of the image attachment.
	 *
	 * Often used as the image caption in galleries or media display.
	 *
	 * @since 1.3.4
	 *
	 * @return string|false The caption text, or false if none is set.
	 */
	public function get_caption() {
		return wp_get_attachment_caption( $this->image_id );
	}

	/**
	 * Retrieves the title of the image attachment.
	 *
	 * This returns the WordPress post title for the attachment, which is often used
	 * as a fallback for alt text or display headings in galleries.
	 *
	 * @since 1.3.4
	 *
	 * @return string The attachment title.
	 */
	public function get_title() {
		return get_the_title( $this->image_id );
	}

	/**
	 * Retrieves the file system path of the image attachment.
	 *
	 * Optionally accepts an image ID; otherwise uses the instance's attachment ID.
	 *
	 * @since 1.3.4
	 *
	 * @param int|null $image_id Optional. The image ID to retrieve the path for. Defaults to the current attachment.
	 *
	 * @return string|false The full file path on disk, or false on failure.
	 */
	public function get_file_path( $image_id = null ) {
		if ( ! $image_id ) {
			$image_id = $this->image_id;
		}
		return get_attached_file( $this->image_id );
	}

	/**
	 * Retrieves the filename of the image attachment.
	 *
	 * Optionally accepts an image ID; otherwise uses the current attachment ID.
	 *
	 * @since 1.3.4
	 *
	 * @param int|null $image_id Optional. The image ID to retrieve the filename for.
	 *
	 * @return string|false The filename including extension (e.g., image.jpg).
	 */
	public function get_filename( $image_id = null ) {
		$file_path = $this->get_file_path( $image_id );
		if ( $file_path ) {
			return basename( $file_path );
		}
		return false;
	}

	/**
	 * Retrieves the original file system path of the image attachment.
	 *
	 * This returns the unprocessed original image path, useful for watermarking or archival use.
	 *
	 * @since 1.3.4
	 *
	 * @return string|false The full file path to the original image, or false on failure.
	 */
	public function get_original_file_path() {
		$file_path = wp_get_original_image_path( $this->image_id );
		if ( $file_path ) {
			return $file_path;
		}
		return false;
	}

	/**
	 * Retrieves the original image URL of the attachment.
	 *
	 * Returns the URL of the unprocessed full image as originally uploaded,
	 * before any WordPress-generated resizing or cropping.
	 *
	 * @since 1.6.0
	 *
	 * @return string|false The URL to the original image file, or false on failure.
	 */
	public function get_original_image_url() {
		$url = wp_get_original_image_url( $this->image_id );
		if ( $url ) {
			return $url;
		}
		return false;
	}

	/**
	 * Retrieves the original filename of the image attachment.
	 *
	 * This is derived from the original file path before any WordPress processing (e.g., cropping, resizing).
	 *
	 * @since 1.3.4
	 *
	 * @return string|false The basename of the original image file (e.g., image.jpg).
	 */
	public function get_original_filename() {
		$file_path = $this->get_original_file_path();
		if ( $file_path ) {
			return basename( $file_path );
		}
		return false;
	}

	/**
	 * Retrieves the full-size image dimensions.
	 *
	 * Returns the width and height of the image using its attachment metadata.
	 *
	 * @since 1.6.0
	 *
	 * @return array An associative array with 'width' and 'height' keys, or null values if not available.
	 */
	public function get_image_size() {
		$metadata = wp_get_attachment_metadata( $this->image_id );
		return array(
			'width'  => $metadata['width'] ?? null,
			'height' => $metadata['height'] ?? null,
		);
	}

	/**
	 * Checks whether the image exists (i.e., the attachment post is valid).
	 *
	 * @since 1.3.4
	 *
	 * @return bool True if the image exists, false otherwise.
	 */
	public function exists() {
		return ! empty( $this->post );
	}

	/**
	 * Generates the HTML markup for a gallery image, including responsive attributes,
	 * optional lightbox integration, and image action icons.
	 *
	 * This function creates a complete HTML block for a gallery image based on the provided settings.
	 * It supports responsive srcset and sizes, alt and title attributes, optional Lightbox markup,
	 * download and like icons, and includes all necessary data attributes for gallery and Lightbox use.
	 *
	 * @since 1.6.0
	 *
	 * @param int    $gallery_id The ID of the gallery containing this image.
	 * @param string $classes    Optional. Additional CSS classes to add to the image container.
	 * @param string $sizes      Optional. The value for the HTML 'sizes' attribute for responsive images.
	 * @param array  $attributes Optional. Additional attributes to add to the image container.
	 *
	 * @return string The full HTML for the gallery image, including icons and Lightbox markup as appropriate.
	 */
	public function get_image_html( $gallery_id, $classes = null, $sizes = null, $attributes = array() ) {
		$alt           = $this->get_alt();
		$caption       = $this->get_caption();
		$title         = $this->get_title();
		$src_large     = $this->get_url( 'large' );
		$srcset        = $this->get_srcset( 'large' );
		$lightbox_src  = $this->get_url( '2048x2048' );
		$download_src  = $this->get_download_url( $gallery_id );
		$width         = $this->get_image_size()['width'];
		$height        = $this->get_image_size()['height'];
		$size          = $this->get_image_size();
		$width         = isset( $size['width'] ) ? (int) $size['width'] : 0;
		$height        = isset( $size['height'] ) ? (int) $size['height'] : 0;
		$gallery_width = isset( $attributes['gallery_width'] ) ? (int) $attributes['gallery_width'] : 0;

		if ( $width <= 0 || $height <= 0 ) {
			return ''; 
		}

		$ratio = $width / $height;

		$data_download_url = '';

		if ( (bool) $attributes['show_download_icon'] === true ) {
			$data_download_url = 'data-download-url="' . esc_url( $download_src ) . '"';
		}

		$output = '';
		
		$output .= '<div class="' .
			esc_attr( $classes ) .
			'" data-gallery-id="' .
			absint( $gallery_id ) .
			'"  data-image-id="' .
			$this->get_id() .
			'" data-width="' .
			absint( $width ) .
			'" data-height="' .
			absint( $height ) .
			'" data-ratio="' .
			abs( $ratio ) .
			'"';

		
		
		if ( isset( $attributes['layout'] ) && $attributes['layout'] === 'masonry' ) {
			$style = 'aspect-ratio:' . (int) $width . ' / ' . (int) $height . '; ';

			if ( $gallery_width > 0 ) {
				$display_width = $attributes['display_width'];

				$display_height = (int) floor( $display_width / ( $ratio ? $ratio : 1 ) );

				$style .= 'width:' . $display_width . 'px; height:' . $display_height . 'px; ';
			}
			$output .= ' style="' . esc_attr( $style ) . '"';
		}

		$output .= '>';

		$output .= '<div class="sp-gallery-item-inner">';

		
		$image_html = '<img src="' . ( $src_large ? esc_url( $src_large ) : '' ) . '" srcset="' . ( $srcset ? esc_attr( $srcset ) : '' ) . '" sizes="' . ( $sizes ? esc_attr( $sizes ) : '' ) . '" alt="' . ( $alt ? esc_attr( $alt ) : '' ) . '" title="' . ( $title ? esc_attr( $title ) : '' ) . '">';

		$output .= '<div class="sp-gallery-image-container">';

		
		$use_lightbox = ( ! empty( $attributes['use_lightbox'] ) ) && ! empty( $lightbox_src );

		$open  = $use_lightbox
			? '<a href="' . ( $lightbox_src ? esc_url( $lightbox_src ) : '' ) . '" class="sp-gallery-item-lightbox" data-src="' . ( $lightbox_src ? esc_url( $lightbox_src ) : '' ) . '" ' . $data_download_url . ' data-image-id="' . absint( $this->image_id ) . '" data-external-thumb-image="' . ( $src_large ? esc_url( $src_large ) : '' ) . '" data-caption="' . ( $caption ? esc_attr( $caption ) : '' ) . '" data-elementor-open-lightbox="no">'
			: '';
		$close = $use_lightbox ? '</a>' : '';

		$output .= $open . $image_html . $close;

		$output    .= '</div>';
		$show_icon  = ! empty( $attributes['show_hover_overlay_icon'] ) && ! empty( $attributes['hover_overlay_icon'] );
		$show_title = ! empty( $attributes['show_hover_image_title'] );

		$show_caption = ! empty( $attributes['show_hover_image_caption'] );

		$output .= '<div class="sp-gallery-overlay-container">';

		if ( $show_icon || $show_title || $show_caption ) {
			
			if ( $show_icon ) {
				$icon_slug = sanitize_file_name( $attributes['hover_overlay_icon'] );
				$base_dir  = SP_GALLERY_DIR . 'src/includes/images/';
				$icon_path = realpath( $base_dir . $icon_slug . '.svg' );

				if ( $icon_path && is_readable( $icon_path ) ) {
					$raw_svg = file_get_contents( $icon_path ); 
					if ( $raw_svg ) {
						$output .= '<div class="sp-gallery-overlay-icon-container">';
						$output .= wp_kses( $raw_svg, $this->allowed_svg );
						$output .= '</div>';
					}
				}
			}

			
			$title = '';
			if ( $show_title ) {
				$t = $this->get_title();
				if ( $t !== '' && $t !== null ) {
					$title   = $t;
					$output .= '<div class="sp-gallery-image-title">' . esc_html( $title ) . '</div>';
				}
			}

			
			$caption = '';
			if ( $show_caption ) {
				$c = $this->get_caption();
				if ( $c !== '' && $c !== null ) {
					$caption = $c;
					$output .= '<div class="sp-gallery-image-caption">' . ( $caption ? esc_html( $caption ) : '' ) . '</div>';
				}
			}

			
			if ( $title !== '' && $caption !== '' ) {
				$output .= '<div class="sp-gallery-center-line"></div>';
			}
		}

		
		$output .= $this->get_image_icons_html( $gallery_id, $attributes, $download_src );

		$output .= '</div>';

		
		$output .= '</div>'; 
		$output .= '</div>';

		return $output;
	}

	/**
	 * Generates the HTML for the image icons in the gallery.
	 *
	 * This function constructs the HTML for the image icons (like, download, etc.) that are displayed on each image in the gallery. The icons to be displayed are determined by the plugin settings.
	 *
	 * @param int    $gallery_id         The ID of the post containing the image.
	 * @param array  $attributes         Optional. Additional attributes to add to the image container.
	 * @param string $download_image_url The URL for downloading the image.
	 *
	 * @return string The HTML for the image icons.
	 */
	private function get_image_icons_html( $gallery_id, $attributes, $download_image_url ) {
		

		$show_like_icon     = $attributes['show_like_icon'];
		$show_download_icon = $attributes['show_download_icon'];
		$icons              = array();

		
		if ( $show_like_icon ) {
			$icons['like'] = array(
				'type'       => 'toggle',
				'icon'       => file_get_contents( plugin_dir_path( __FILE__ ) . '../includes/images/heart.svg' ),
				'class'      => 'sp-gallery-like-icon',
				'action'     => 'add_to_favorites',
				'aria-label' => 'Add to favorites',
			);
		}

		
		if ( $show_download_icon ) {
			$icons['download'] = array(
				'type'       => 'download',
				'icon'       => file_get_contents( SP_GALLERY_DIR . 'src/includes/images/' . $attributes['icons']['download'] . '.svg' ), 
				'class'      => 'sp-gallery-download-icon sp-gallery-image-icon',
				'action'     => 'download_image',
				'aria-label' => 'Download Image',
				'url'        => $download_image_url,
			);
		}

		
		$icons = apply_filters( 'sp_gallery_image_icons', $icons, $this->image_id, $attributes );

		if ( empty( $icons ) ) {
			return '';
		}

		$output  = '<div class="sp-gallery-image-icons-container">';
		$output .= '<div class="sp-gallery-image-icons sp-gallery-icon-block">';

		foreach ( $icons as $icon_key => $icon_data ) {
			$is_link     = ( $icon_data['type'] === 'link' || $icon_data['type'] === 'download' );
			$tag         = $is_link ? 'a' : 'div';
			$href_attr   = $is_link ? ' href="' . esc_url( $icon_data['url'] ?? '' ) . '"' : '';
			$download_at = ( $icon_data['type'] === 'download' ) ? ' download' : '';
			$data_attr   = $is_link ? '' : ' data-image-id="' . absint( $this->image_id ) . '"';
			$rel_attr    = $is_link ? ' rel="nofollow"' : '';

			$output .= '<' . $tag . $href_attr . $download_at . ' class="' . esc_attr( $icon_data['class'] ) . '" aria-label="' . esc_attr( $icon_data['aria-label'] ) . '"' . $data_attr . $rel_attr . '>';
			$output .= wp_kses( $icon_data['icon'], $this->allowed_svg );
			$output .= '</' . $tag . '>';
		}

		$output .= '</div></div>';
		return $output;
	}

	/**
	 * Generates a secure download URL for the image, for a given gallery and image.
	 *
	 * The URL includes either a nonce (for logged-in users) or a secure token (for guests)
	 * as well as the gallery and image IDs. This enables REST API-controlled downloads and
	 * protects against unauthorised access.
	 *
	 * @since 1.6.0
	 *
	 * @param int      $gallery_id The ID of the gallery containing the image.
	 * @param int|null $image_id   Optional. The image ID. Defaults to the current image.
	 *
	 * @return string The REST API URL for downloading the image, including all security parameters.
	 */
	public function get_download_url( $gallery_id, $image_id = null ) {
		$image_id = $image_id ?? $this->image_id;

		if ( is_user_logged_in() ) {
			$nonce        = wp_create_nonce( "sp_gallery_download_{$image_id}_{$gallery_id}" );
			$download_url = add_query_arg(
				array(
					'image_id'       => $image_id,
					'gallery_id'     => $gallery_id,
					'download_nonce' => $nonce,
				),
				rest_url( 'shutterpress/v1/download-image' )
			);
			return $download_url;
		}

		$download_url = add_query_arg(
			array(
				'image_id'   => $image_id,
				'gallery_id' => $gallery_id,
			),
			rest_url( 'shutterpress/v1/download-image' )
		);

		return $download_url;
	}

	/**
	 * Removes the specified image from all Shutterpress galleries.
	 *
	 * This  utility scans all posts of type 'shutterpress-gallery'
	 * and removes the given image ID from the '_sp_gallery_images_array' meta.
	 * It also cleans up the meta entirely if no images remain.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function remove_image_from_all_galleries() {
		$this->debug->log( 'Running remove_image_from_all_galleries' );

		$args = array(
			'post_type'      => 'shutterpress-gallery',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		);

		$query = new WP_Query( $args );
		$ids   = array_filter(
			array_map(
				function ( $id ) {
					return is_scalar( $id ) ? absint( $id ) : 0;
				},
				(array) $query->posts
			)
		);

		if ( empty( $ids ) ) {
			return; 
		}

		$attachment_id = (int) $this->image_id;

		foreach ( $ids as $gallery_id ) {
			$gallery        = new Shutterpress_Gallery_Gallery( $gallery_id );
			$gallery_images = $gallery->get_gallery_images( $gallery_id );

			$this->debug->log( 'Deleting Gallery Images from Array', array( $gallery_images ), 'DEBUG' );

			if ( ! is_array( $gallery_images ) ) {
				continue;
			}

			$updated_gallery_images = array_values(
				array_filter(
					$gallery_images,
					function ( $id ) use ( $attachment_id ) {
						return (int) $id !== $attachment_id;
					}
				)
			);

			if ( count( $updated_gallery_images ) === count( $gallery_images ) ) {
				continue;
			}

			if ( empty( $updated_gallery_images ) ) {
				delete_post_meta( $gallery_id, '_sp_gallery_images_array' );
				continue;
			}

			$gallery->update_gallery_images( $updated_gallery_images, $gallery_id );
		}
	}

	/**
	 * Retrieves the original capture time from the image EXIF metadata.
	 *
	 * @param int|null $image_id The ID of the image attachment.
	 *
	 * @return int The Unix timestamp of the capture time, or 0 if not found.
	 */
	public function get_exif_capture_time( $image_id = null ) {
		$image_id = $image_id ? $image_id : $this->image_id;

		$image_meta = wp_get_attachment_metadata( $image_id );
		
		$timestamp = $image_meta['image_meta']['created_timestamp'];
		return $timestamp ? absint( $timestamp ) : 0;
	}

	/**
	 * Retrieves the file date of an image attachment.
	 *
	 * @param int $image_id The ID of the image attachment.
	 *
	 * @return int The timestamp of the image attachment's file date, or 0 if the date is not available.
	 */
	public function get_file_creation_time( $image_id = null ) {
		$image_id  = $image_id ? $image_id : $this->image_id;
		$file_path = get_attached_file( $image_id );
		if ( $file_path ) {
			$file_time = filemtime( $file_path );
			return $file_time ? absint( $file_time ) : 0;
		}
		return 0;
	}

	/**
	 * Retrieves the upload date of an image attachment.
	 *
	 * @param int $image_id The ID of the image attachment.
	 *
	 * @return int The timestamp of the image attachment's upload date, or 0 if the date is not available.
	 */
	public function get_file_upload_time( $image_id = null ) {
		$image_id = $image_id ? $image_id : $this->image_id;
		$post     = get_post( $image_id );
		if ( isset( $post->post_date ) ) {
			$time = strtotime( $post->post_date );
			return $time ? absint( $time ) : 0;
		}
		return 0;
	}
}
