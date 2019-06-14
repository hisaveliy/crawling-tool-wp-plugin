<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 19:43
 */

if (!class_exists('GF_Field')) {
	exit;
}

/**
 * Class GF_Field_Example
 *
 * @doc https://docs.gravityforms.com/gf_field/
 */
class GF_Field_Example extends GF_Field {

	public $type = 'example';

	/**
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Example', '{Text_Domain}' );
	}

	/**
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}

	/**
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'rules_setting',
			'visibility_setting',
			'duplicate_setting',
			'default_value_setting',
			'placeholder_setting',
			'description_setting',
			'phone_format_setting',
			'css_class_setting',
		);
	}

	/**
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * This method is used to define the fields inner markup,
	 * including the div with the ginput_container class.
	 * The default behaviour of get_field_input() is to return an empty string
	 * so you will want to override this method.
	 *
	 * @param        $form
	 * @param string $value
	 * @param null   $entry
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$id              = (int) $this->id;

		if ( $is_entry_detail ) {
			$input = "<input type='hidden' id='input_{$id}' name='input_{$id}' value='{$value}' />";

			return $input . '<br/>' . esc_html__( 'Coupon fields are not editable', 'gravityformscoupons' );
		}

		$disabled_text         = $this->is_form_editor() ? 'disabled="disabled"' : '';
		$logic_event           = version_compare( GFForms::$version, '2.4.1', '<' ) ? $this->get_conditional_logic_event( 'change' ) : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$coupons_detail        = rgpost( "gf_coupons_{$form_id}" );
		$coupon_codes          = empty( $coupons_detail ) ? '' : rgpost( "input_{$id}" );

		/**
		 * Move to a dedicated file field-input.php
		 */
		$input = "<div class='ginput_container' id='gf_coupons_container_{$form_id}'>" .
		         "<input id='gf_coupon_code_{$form_id}' class='gf_coupon_code' onkeyup='DisableApplyButton({$form_id});' onchange='DisableApplyButton({$form_id});' onpaste='setTimeout(function(){DisableApplyButton({$form_id});}, 50);' type='text'  {$disabled_text} {$placeholder_attribute} " . $this->get_tabindex() . '/>' .
		         "<input type='button' disabled='disabled' onclick='ApplyCouponCode({$form_id});' value='" . esc_attr__( 'Apply', 'gravityformscoupons' ) . "' id='gf_coupon_button' class='button' {$disabled_text} " . $this->get_tabindex() . '/> ' .
		         "<img style='display:none;' id='gf_coupon_spinner' src='" . gf_coupons()->get_base_url() . "/images/spinner.gif' alt='" . esc_attr__( 'please wait', 'gravityformscoupons' ) . "'/>" .
		         "<div id='gf_coupon_info'></div>" .
		         "<input type='hidden' id='gf_coupon_codes_{$form_id}' name='input_{$id}' value='" . esc_attr( $coupon_codes ) . "' {$logic_event} />" .
		         "<input type='hidden' id='gf_total_no_discount_{$form_id}'/>" .
		         "<input type='hidden' id='gf_coupons_{$form_id}' name='gf_coupons_{$form_id}' value='" . esc_attr( $coupons_detail ) . "' />" .
		         "</div>";

		return $input;
	}

	/**
	 * @param $value
	 * @param $force_frontend_label
	 * @param $form
	 *
	 * @return string The fields markup. string. Note: use the placeholder {FIELD}
	 * to define where the markup returned by get_field_input() should be included.
	 */
	public function get_field_content( $value, $force_frontend_label, $form ) {
		$form_id         = $form['id'];
		$admin_buttons   = $this->get_admin_buttons();
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;
		$field_label     = $this->get_field_label( $force_frontend_label, $value );
		$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
		$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

		return $field_content;
	}

	/**
	 * @param $value
	 * @param $form
	 */
	public function validate( $value, $form ) {
		$regex = '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/';
		if ( $this->phoneFormat == 'standard' && $value !== '' && $value !== 0 && ! preg_match( $regex, $value ) ) {
			$this->failed_validation = true;
			if ( ! empty( $this->errorMessage ) ) {
				$this->validation_message = $this->errorMessage;
			}
		}
	}

	/**
	 * @param $form
	 *
	 * @return string
	 */
	public function get_form_inline_script_on_page_render( $form ) {
		$script = '';
		if ( $this->phoneFormat == 'standard' ) {
			$script = "if(!/(android)/i.test(navigator.userAgent)){jQuery('#input_{$form['id']}_{$this->id}').mask('(999) 999-9999').on('keypress', function(e){if(e.which == 13){jQuery(this).blur();} } );}";
		}
		return $script;
	}

	public function get_form_editor_inline_script_on_page_render() {
		return "
		    gform.addFilter('gform_form_editor_can_field_be_added', function (canFieldBeAdded, type) {
		        if (type == 'coupon') {
		            if (GetFieldsByType(['product']).length <= 0) {
		                alert(" . json_encode( esc_html__( 'You must add a Product field to the form', 'gravityformscoupons' ) ) . ");
		                return false;
		            } else if (GetFieldsByType(['total']).length  <= 0) {
		                alert(" . json_encode( esc_html__( 'You must add a Total field to the form', 'gravityformscoupons' ) ) . ");
		                return false;
		            } else if (GetFieldsByType(['coupon']).length) {
		                alert(" . json_encode( esc_html__( 'Only one Coupon field can be added to the form', 'gravityformscoupons' ) ) . ");
		                return false;
		            }
		        }
		        return canFieldBeAdded;
		    });";
	}

	/**
	 * @param $value
	 * @param $form
	 * @param $input_name
	 * @param $lead_id
	 * @param $lead
	 *
	 * @return string
	 */
	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {

		if ( $this->phoneFormat == 'standard' && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $value, $matches ) ) {
			$value = sprintf( '(%s) %s-%s', $matches[1], $matches[2], $matches[3] );
		}

		return $value;
	}

	/**
	 * @param $value
	 * @param $input_id
	 * @param $entry
	 * @param $form
	 * @param $modifier
	 * @param $raw_value
	 * @param $url_encode
	 * @param $esc_html
	 * @param $format
	 * @param $nl2br
	 *
	 * @return mixed
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$format_modifier = empty( $modifier ) ? $this->dateFormat : $modifier;

		return GFCommon::date_display( $value, $format_modifier );
	}

	/**
	 * @param        $value
	 * @param string $currency
	 * @param bool   $use_text
	 * @param string $format
	 * @param string $media
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		if ( is_array( $value ) && ! empty( $value ) ) {
			$product_name = trim( $value[ $this->id . '.1' ] );
			$price        = trim( $value[ $this->id . '.2' ] );
			$quantity     = trim( $value[ $this->id . '.3' ] );

			$product = $product_name . ', ' . esc_html__( 'Qty: ', 'gravityforms' ) . $quantity . ', ' . esc_html__( 'Price: ', 'gravityforms' ) . $price;

			return $product;
		} else {
			return '';
		}
	}

}

GF_Fields::register( new GF_Field_Example() );