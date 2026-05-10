<?php
/**
 * Custom Image Advanced field for ShutterPress Gallery.
 *
 * Extends the Meta Box Image Advanced field, provides sanitisation and storage as an
 * array of attachment IDs, enqueues admin assets for the media interface, and exposes
 * prepared attachment data to JavaScript for rendering.
 *
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @since     1.0.0
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Image selector field built on Meta Box Image Advanced.
 *
 * Saves a normalised array of image IDs, flattens nested values, validates image
 * attachments, and prepares attributes including `data-attachments` for the media
 * template. Also enqueues the custom admin script for enhanced UI.
 *
 * @since   1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Admin
 */
class RWMB_Custom_Image_Advanced_Field extends RWMB_Image_Advanced_Field {

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		RWMB_Image_Field::admin_enqueue_scripts();
		wp_enqueue_script(
			'shutterpress-gallery-image-advanced',
			plugin_dir_url( __FILE__ ) . 'js/shutterpress-gallery-image-advanced.js',
			array( 'jquery', 'rwmb-media', 'rwmb', 'wp-util', 'underscore' ),
			RWMB_VER,
			true
		);
	}

	/**
	 * Saves the custom image advanced field data.
	 *
	 * This method is responsible for sanitizing and updating the post meta with the
	 * array of image IDs for the custom image advanced field.
	 *
	 * @param mixed $new_value     The new value of the field.
	 * @param mixed $old_value     The old value of the field.
	 * @param int   $post_id The ID of the post being saved.
	 * @param array $field   The field configuration array.
	 *
	 * @return void
	 */
	public static function save( $new_value, $old_value, $post_id, $field ) {

		$new_flat = is_array( $new_value ) ? array_values( array_filter( $new_value ) ) : (array) $new_value;

		$image_ids = array_map( 'intval', $new_flat );

		$image_ids = array_filter(
			$image_ids,
			function ( $id ) {
				return wp_attachment_is_image( $id );
			}
		);

		$meta_field_id = sanitize_key( $field['id'] );

		if ( empty( $image_ids ) ) {
			delete_post_meta( $post_id, $meta_field_id );
			return;
		}

		update_post_meta( $post_id, $meta_field_id, $image_ids );
	}

	/**
	 * Gets the attributes for the custom image advanced field.
	 *
	 * This method is responsible for preparing the attributes for the custom image
	 * advanced field, including setting the type, name, ID, value, and class.
	 * It also adds attachment details to the attributes.
	 *
	 * @param array $field The field configuration array.
	 * @param mixed $value The field value.
	 *
	 * @return array The prepared attributes for the field.
	 */
	public static function get_attributes( $field, $value = null ) {
		$value = (array) $value;

		if ( isset( $value[0] ) && is_array( $value[0] ) ) {
			$value = $value[0];
		}

		$attributes           = parent::get_attributes( $field, $value );
		$attributes['type']   = 'hidden';
		$attributes['name']   = $field['clone'] ? str_replace( '[]', '', $attributes['name'] ) : $attributes['name'];
		$attributes['id']     = false;
		$attributes['value']  = implode( ',', $value );
		$attributes['class'] .= ' rwmb-media';

		$attachments                    = array_values( array_filter( array_map( 'wp_prepare_attachment_for_js', $value ) ) );
		$attributes['data-attachments'] = wp_json_encode( $attachments );

		if ( empty( $attachments ) ) {
			unset( $attributes['value'] );
		}
		return $attributes;
	}
}
