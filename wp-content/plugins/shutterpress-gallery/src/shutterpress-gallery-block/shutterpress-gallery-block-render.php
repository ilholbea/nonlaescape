<?php
/**
 * Render callback for the dynamic block.
 *
 * @package Shutterpress_Gallery
 */

namespace Shutterpress\Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


$sp_gallery_defaults_class = new Shutterpress_Gallery_Defaults();
$sp_gallery_defaults       = $sp_gallery_defaults_class->get_template_defaults();

$sp_gallery_gallery_attributes = array(
	'gallery_id'   => isset( $attributes['galleryId'] ) && intval( $attributes['galleryId'] ) !== 0 ? intval( $attributes['galleryId'] ) : 0,
	'post_id'      => isset( $attributes['postId'] ) ? intval( $attributes['postId'] ) : ( isset( $_GET['post'] ) ? intval( $_GET['post'] ) : ( get_the_ID() ? intval( get_the_ID() ) : 0 ) ),
	'use_lightbox' => isset( $attributes['useLightbox'] ) ? filter_var( $attributes['useLightbox'], FILTER_VALIDATE_BOOLEAN ) : filter_var( $sp_gallery_defaults['use_lightbox'], FILTER_VALIDATE_BOOLEAN ),
	'layout'       => isset( $attributes['galleryLayout'] ) ? sanitize_text_field( $attributes['galleryLayout'] ) : sanitize_text_field( $sp_gallery_defaults['layout'] ),
	'gap'          => isset( $attributes['galleryGap'] ) ? intval( $attributes['galleryGap'] ) : intval( $sp_gallery_defaults['gap'] ),
	'row_height'   => isset( $attributes['rowHeight'] ) ? intval( $attributes['rowHeight'] ) : intval( $sp_gallery_defaults['row_height'] ),
	'columns'      => array(
		'desktop' => isset( $attributes['columns_desktop'] ) ? intval( $attributes['columns_desktop'] ) : intval( $sp_gallery_defaults['columns']['desktop'] ),
		'tablet'  => isset( $attributes['columns_tablet'] ) ? intval( $attributes['columns_tablet'] ) : intval( $sp_gallery_defaults['columns']['tablet'] ),
		'mobile'  => isset( $attributes['columns_mobile'] ) ? intval( $attributes['columns_mobile'] ) : intval( $sp_gallery_defaults['columns']['mobile'] ),
	),
	'show_buttons' => isset( $attributes['showDefaultButtons'] ) ? filter_var( $attributes['showDefaultButtons'], FILTER_VALIDATE_BOOLEAN ) : true,
	'gallery_type' => 'block',
);


if ( ! function_exists( __NAMESPACE__ . '\\sp_gallery_render_gallery_block' ) ) {
	/**
	 * Renders the ShutterPress Gallery dynamic block.
	 *
	 * Builds context, resolves gallery and post IDs, applies editor context rules,
	 * configures the renderer, fetches images, then outputs buttons, gallery,
	 * loader, and pagination inside the block wrapper. When a filter supplies
	 * replacement content, for example a password form, returns the sanitised form.
	 *
	 * @since 1.7.0
	 *
	 * @param array                                                   $sp_gallery_gallery_attributes Normalised attributes from the block editor.
	 * @param \Shutterpress\Gallery\Shutterpress_Gallery_Gallery|null $renderer          Optional renderer, mainly for tests.
	 *
	 * @return string Block HTML.
	 */
	function sp_gallery_render_gallery_block( array $sp_gallery_gallery_attributes, ?Shutterpress_Gallery_Gallery $renderer = null ): string {
		$debug = new Shutterpress_Gallery_Debug();
		$utils = new Shutterpress_Gallery_Utils();

		static $renderers = array();

		$gallery_id = isset( $sp_gallery_gallery_attributes['gallery_id'] ) ? (int) $sp_gallery_gallery_attributes['gallery_id'] : 0;

		$context_post_id = 0;
		if ( ! empty( $sp_gallery_gallery_attributes['post_id'] ) ) {
			$context_post_id = (int) $sp_gallery_gallery_attributes['post_id'];
		} elseif ( get_the_ID() ) {
			$context_post_id = (int) get_the_ID();
		} elseif ( isset( $_GET['post'] ) ) {
			$context_post_id = (int) $_GET['post'];
		}

		if ( $gallery_id === 0 || $gallery_id === -1 ) {
			if ( $context_post_id && get_post_type( $context_post_id ) === 'shutterpress-gallery' ) {
				$gallery_id = $context_post_id;
			}
			$sp_gallery_gallery_attributes['gallery_id'] = $gallery_id;
			if ( $context_post_id ) {
				$sp_gallery_gallery_attributes['post_id'] = $context_post_id;
			}
		}

		$effective_gallery_id = $gallery_id > 0 ? $gallery_id : 0;

		if ( $gallery_id <= 0 && empty( $sp_gallery_gallery_attributes['post_id'] ) ) {
			return '<div ' . \get_block_wrapper_attributes() . '></div>';
		}

		$debug->log( 'Rendering Gallery Block', array( $sp_gallery_gallery_attributes ), 'DEBUG' );

		if ( ! $renderer ) {
			$cache_key = (int) ( $effective_gallery_id ? $effective_gallery_id : $context_post_id );
			if ( ! isset( $renderers[ $cache_key ] ) ) {
				$renderers[ $cache_key ] = new Shutterpress_Gallery_Gallery( $effective_gallery_id );
			}
			$renderer = $renderers[ $cache_key ];
		}

		if ( ! $renderer instanceof Shutterpress_Gallery_Gallery ) {
			$debug->log( 'Renderer not available', array( 'gallery_id' => $effective_gallery_id ), 'ERROR' );
			return '<div ' . \get_block_wrapper_attributes() . '></div>';
		}

		try {
			$output = '<div ' . \get_block_wrapper_attributes() . '>';

			if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_GET['context'] ) && $_GET['context'] === 'edit' ) {
				$sp_gallery_gallery_attributes['infinite_scroll'] = '0';
				$sp_gallery_gallery_attributes['use_lightbox']    = '0';
			}

			$renderer->set_attributes( $sp_gallery_gallery_attributes );

			if ( method_exists( $renderer, 'set_post_id' ) && ! empty( $sp_gallery_gallery_attributes['post_id'] ) ) {
				$renderer->set_post_id( (int) $sp_gallery_gallery_attributes['post_id'] );
			}
			if ( method_exists( $renderer, 'set_is_post_gallery' ) ) {
				$renderer->set_is_post_gallery( $effective_gallery_id === 0 && ! empty( $sp_gallery_gallery_attributes['post_id'] ) );
			}

			$has_images = (array) $renderer->get_gallery_images();

			if ( ! empty( $has_images ) ) {
					$output .= $renderer->get_buttons();

					$gallery_output = $renderer->get_the_gallery();

				if ( ! is_wp_error( $gallery_output ) ) {
					$output .= $gallery_output;
				}

					$output .= $renderer->get_infinite_scroll_loader();

					$output .= $renderer->get_gallery_pagination();
			}

			$output .= '</div>';

			$filtered_content = apply_filters( 'sp_gallery_content', null, $output, ( $effective_gallery_id > 0 ? $effective_gallery_id : null ) );

			if ( ! empty( $filtered_content ) ) {
				return $utils->sanitize_form_html( $filtered_content );
			}

			return $output;
		} catch ( \Throwable $e ) {
			if ( function_exists( 'Shutterpress\\Gallery\\$debug->log' ) ) {
				$debug->log( 'Block render exception', array( 'message' => $e->getMessage() ), 'ERROR' );
			}

			return '<div class="components-error"><p>' . esc_html__( 'Error rendering ShutterPress block:', 'shutterpress-gallery' ) . ' ' . esc_html( $e->getMessage() ) . '</p></div>';
		}
	}
}





echo sp_gallery_render_gallery_block( $sp_gallery_gallery_attributes );
