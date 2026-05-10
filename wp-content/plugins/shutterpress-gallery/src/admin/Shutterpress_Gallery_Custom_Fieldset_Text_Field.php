<?php
/**
 * Custom fieldset text field for ShutterPress Gallery.
 *
 * Renders a group of labelled numeric inputs stored under one meta key.
 * Supports defaults via `std`, per-field suffixes via `append`, and
 * normalises options so helper output is predictable.
 *
 * @package   Shutterpress_Gallery
 * @subpackage Admin
 * @since     1.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Fieldset of numeric inputs for Meta Box.
 *
 * Extends the Meta Box number field to render multiple labelled inputs as a
 * single field, using an associative array keyed by the provided `options`.
 * Handles admin assets, normalisation, value formatting for helpers, and
 * array storage on save.
 *
 * @since   1.0.0
 * @package Shutterpress_Gallery
 * @subpackage Admin
 */
class Shutterpress_Gallery_Custom_Fieldset_Text_Field extends RWMB_Number_Field {

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public static function admin_enqueue_scripts() {

		wp_enqueue_style( 'rwmb-fieldset-text', RWMB_CSS_URL . 'fieldset-text.css', array(), RWMB_VER );
		wp_style_add_data( 'rwmb-fieldset-text', 'path', RWMB_CSS_DIR . 'fieldset-text.css' );
	}

	/**
	 * Get field HTML.
	 *
	 * @param mixed $meta  Meta value.
	 * @param array $field Field parameters.
	 *
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$html = array();
		$tpl  = '<div class="sp-gallery-fieldset-text-box"><label>%s</label> %s</div>';

		$defaults = isset( $field['std'] ) && is_array( $field['std'] ) ? $field['std'] : array();
		$append   = isset( $field['append'] ) && is_array( $field['append'] ) ? $field['append'] : array();
		$meta     = array_merge( $defaults, (array) $meta );

		if ( ! is_array( $field['options'] ) ) {
			return '';
		}

		foreach ( $field['options'] as $key => $label ) {
			$value                       = $meta[ $key ] ?? '';
			$field['attributes']['name'] = $field['field_name'] . "[{$key}]";
			$field['append']             = $append[ $key ];
			$html[]                      = sprintf( $tpl, $label, parent::html( $value, $field ) );
		}

		$out = '<fieldset>' . ( $field['desc'] ? '<legend>' . $field['desc'] . '</legend>' : '' ) . implode( ' ', $html ) . '</fieldset>';

		return $out;
	}

	/**
	 * Get field description.
	 *
	 * @since    1.0.0
	 *
	 * @param array $field Field parameters.
	 *
	 * @return string
	 */
	protected static function input_description( array $field ): string {
		return '';
	}

	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field Field parameters.
	 *
	 * @return array
	 */
	public static function normalize( $field ) {
		$field                       = parent::normalize( $field );
		$field['multiple']           = false;
		$field['attributes']['id']   = false;
		$field['attributes']['type'] = 'number';

		if ( ! isset( $field['std'] ) || ! is_array( $field['std'] ) ) {
			$field['std'] = array();
		}
		if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
			foreach ( $field['options'] as $key ) {
				if ( ! array_key_exists( $key, $field['std'] ) ) {
					$field['std'][ $key ] = '';
				}
			}
		}
		return $field;
	}

	/**
	 * Format value for the helper functions.
	 *
	 * @param array        $field   Field parameters.
	 * @param string|array $value   The field meta value.
	 * @param array        $args    Additional arguments. Rarely used. See specific fields for details.
	 * @param int|null     $post_id Post ID. null for current post. Optional.
	 *
	 * @return string
	 */
	public static function format_value( $field, $value, $args, $post_id ) {

		if ( empty( $value ) && isset( $field['std'] ) && is_array( $field['std'] ) ) {
			$value = $field['std'];
		}
		$output = '<table><thead><tr>';
		foreach ( $field['options'] as $label ) {
			$output .= "<th>$label</th>";
		}
		$output .= '</tr></thead></tbody>';

		$values = $field['clone'] ? (array) $value : array( $value );
		foreach ( $values as $subvalue ) {
			$output .= self::format_single_value( $field, $subvalue, $args, $post_id );
		}
		$output .= '</tbody></table>';
		return $output;
	}

	/**
	 * Format a single value for the helper functions. Sub-fields should overwrite this method if necessary.
	 *
	 * @param array    $field   Field parameters.
	 * @param array    $value   The value.
	 * @param array    $args    Additional arguments. Rarely used. See specific fields for details.
	 * @param int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return string
	 */
	public static function format_single_value( $field, $value, $args, $post_id ) {
		$output = '<tr>';
		foreach ( $value as $subvalue ) {
			$output .= "<td>$subvalue</td>";
		}
		$output .= '</tr>';
		return $output;
	}

	/**
	 * Save handler, stores multiple instances under one meta key as an array.
	 *
	 * @param mixed $new_value The new value of the field.
	 * @param mixed $old_value The old value of the field.
	 * @param int   $post_id The ID of the post being saved.
	 * @param array $field The field configuration array.
	 *
	 * @return array
	 */
	public static function value( $new_value, $old_value, $post_id, $field ) {
		$all_empty = empty( array_filter( (array) $new_value ) );

		return $all_empty ? array() : $new_value;
	}
}
