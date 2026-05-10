<?php
/**
 * Meta Box custom code field for ShutterPress Gallery.
 *
 * Provides a textarea enhanced by the WordPress Code Editor so users can
 * edit code with CodeMirror. Supports optional `mode` and `height` args.
 *
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @since     1.7.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Code field for Meta Box, renders a textarea enhanced by the WordPress Code Editor.
 *
 * Enqueues CodeMirror assets via `wp_enqueue_code_editor`, and accepts `mode`
 * and `height` in the field settings. Extends the base textarea field.
 *
 * @since   1.7.0
 * @package Shutterpress_Gallery
 * @subpackage Admin
 */
class RWMB_Code_Field extends RWMB_Textarea_Field {

	/**
	 * Enqueue CodeMirror scripts/styles for the code field.
	 *
	 * @return void
	 * @since 1.7.0
	 */
	public static function admin_enqueue_scripts() {

		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
	}

	/**
	 * Generate the field HTML (textarea element).
	 *
	 * @param mixed $meta  The stored meta value (string).
	 * @param array $field The field settings.
	 *
	 * @return string HTML markup for the field.
	 */
	public static function html( $meta, $field ) {

		$meta_value = is_array( $meta ) ? implode( "\n", $meta ) : $meta;
		$meta_value = is_string( $meta_value ) ? $meta_value : '';

		$mode   = ! empty( $field['mode'] ) ? $field['mode'] : 'text/css';
		$height = ! empty( $field['height'] ) ? $field['height'] : '';

		$style = '';
		if ( $height ) {
			if ( is_numeric( $height ) ) {
				$height .= 'px';
			}
			$style = ' style="height:' . esc_attr( $height ) . ';"';
		}

		return sprintf(
			'<textarea name="%s" id="%s" class="rwmb-code large-text code"%s data-mode="%s">%s</textarea>',
			esc_attr( $field['field_name'] ),
			esc_attr( $field['id'] ),
			$style,
			esc_attr( $mode ),
			esc_textarea( $meta_value )
		);
	}

	/**
	 * Normalize field parameters for this field type.
	 * Ensures custom settings have default values and merges with parent defaults.
	 *
	 * @param array $field Field settings (passed by reference).
	 *
	 * @return array Normalized field settings.
	 */
	public static function normalize( $field ) {

		$field = wp_parse_args(
			$field,
			array(
				'mode'   => 'text/css',
				'height' => '',
			)
		);

		$field = parent::normalize( $field );
		return $field;
	}
}
