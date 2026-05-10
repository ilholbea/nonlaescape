<?php
/**
 * Custom Image Select field for ShutterPress Gallery.
 *
 * Provides a radio style selector rendered as images. Supports labels, multiple
 * selection, and storing multiple instances under one meta key when `icon_name`
 * is provided. Normalises options and outputs accessible markup.
 *
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @since     1.0.0
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'SHUTTERPRESS_TESTING' ) ) {
	exit();
}

/**
 * Image based selector field built on Meta Box Image Select.
 *
 * Renders choices as clickable images, supports single or multiple selection,
 * and allows grouping multiple icon selections under a single meta key using
 * the `icon_name` parameter.
 *
 * @since   1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Admin
 */
class RWMB_Custom_Image_Select_Field extends RWMB_Image_Select_Field {

	/**
	 * Get field HTML.
	 *
	 * @param mixed $meta  Meta value.
	 * @param array $field Field parameters.
	 *
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$html[] = '<div class="rwmb-custom-image-select-row">';

		$has_icon       = isset( $field['icon_name'] );
		$icon_key       = $has_icon ? sanitize_key( $field['icon_name'] ) : null;
		$current_values = (
			$has_icon && is_array( $meta ) && isset( $meta[ $icon_key ] )
		)
			? array( $meta[ $icon_key ] )
			: ( $has_icon ? array() : (array) $meta );

		if ( empty( $current_values ) && isset( $field['std'] ) && $field['std'] !== '' ) {
			$current_values = array( (string) $field['std'] );
		}
		foreach ( $field['options'] as $value => $option ) {
			$opt   = is_array( $option ) ? $option : array(
				'img'   => $option,
				'label' => '',
			);
			$img   = isset( $opt['img'] ) ? $opt['img'] : '';
			$label = isset( $opt['label'] ) ? $opt['label'] : '';

			$attributes = self::get_attributes( $field, $value );
			$html[]     = sprintf(
				'<div class="rwmb-custom-image-select-container"><label class="rwmb-custom-image-select"><img src="%s"><input %s%s></label><div class="rwmb-custom-image-select-option-name">%s</div></div>',
				$img,
				self::render_attributes( $attributes ),
				checked( in_array( $value, $current_values, true ), true, false ),
				$label
			);
		}
		$html[] = '</div>';

		return implode( ' ', $html );
	}


	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field Field parameters.
	 *
	 * @return array
	 */
	public static function normalize( $field ) {
		$field            = parent::normalize( $field );
		$field['options'] = $field['options'] ?? array();

		if ( isset( $field['icon_name'] ) ) {
			$field['field_name'] .= '[' . sanitize_key( $field['icon_name'] ) . ']';
		} elseif ( ! empty( $field['multiple'] ) ) {
			$field['field_name'] .= '[]';
		}
		return $field;
	}

	/**
	 * Save handler, stores multiple instances under one meta key as an array.
	 *
	 * @param mixed $new_value The value to save.
	 * @param mixed $old_value The old value.
	 * @param int   $post_id Post ID.
	 * @param array $field Field parameters.
	 *
	 * @return void
	 */
	public static function save( $new_value, $old_value, $post_id, $field ) {

		if ( isset( $field['icon_name'] ) ) {
			$meta_key = $field['id'];
			$icon_key = sanitize_key( $field['icon_name'] );
			$stored   = get_post_meta( $post_id, $meta_key, true );
			if ( ! is_array( $stored ) ) {
				$stored = array();
			}

			$submitted = $new_value;
			if ( isset( $_POST[ $meta_key ] ) && is_array( $_POST[ $meta_key ] ) && array_key_exists( $icon_key, $_POST[ $meta_key ] ) ) {
				$raw_input = wp_unslash( $_POST[ $meta_key ][ $icon_key ] );
				if ( is_array( $raw_input ) ) {
					$raw       = array_map( 'sanitize_text_field', $raw_input );
					$submitted = reset( $raw );
				} else {
					$submitted = sanitize_text_field( $raw_input );
				}
			}

			if ( is_array( $submitted ) ) {
				$submitted = reset( $submitted );
			}

			$stored[ $icon_key ] = is_string( $submitted ) ? $submitted : '';
			update_post_meta( $post_id, $meta_key, $stored );
			return;
		}

		parent::save( $new_value, $old_value, $post_id, $field );
	}

	/**
	 * Get the attributes for a field.
	 *
	 * @param array $field Field parameters.
	 * @param mixed $value Meta value.
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes          = parent::get_attributes( $field, $value );
		$attributes['id']    = false;
		$attributes['type']  = ! empty( $field['multiple'] ) ? 'checkbox' : 'radio';
		$attributes['value'] = $value;
		return $attributes;
	}

	/**
	 * Format a single value for the helper functions. Sub-fields should overwrite this method if necessary.
	 *
	 * @param array    $field   Field parameters.
	 * @param string   $value   The value.
	 * @param array    $args    Additional arguments. Rarely used. See specific fields for details.
	 * @param int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return string
	 */
	public static function format_single_value( $field, $value, $args, $post_id ) {
		if ( ! $value ) {
			return '';
		}

		$option = isset( $field['options'][ $value ] ) ? $field['options'][ $value ] : '';
		$opt    = is_array( $option ) ? $option : array(
			'img'   => $option,
			'label' => '',
		);
		$img    = isset( $opt['img'] ) ? $opt['img'] : '';
		$label  = isset( $opt['label'] ) ? $opt['label'] : '';

		$img_html   = $img ? sprintf( '<img src="%s" alt="%s">', esc_url( $img ), esc_attr( $label ? $label : $value ) ) : '';
		$label_html = $label !== '' ? '<span class="rwmb-image-select-label">' . esc_html( $label ) . '</span>' : '';

		return $img_html . $label_html;
	}
}
