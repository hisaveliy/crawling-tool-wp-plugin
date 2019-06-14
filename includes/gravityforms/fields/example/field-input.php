<div class='ginput_container' id='gf_coupons_container_{$form_id}'> .
  <input id='gf_coupon_code_{$form_id}' class='gf_coupon_code' onkeyup='DisableApplyButton(<?php echo $form_id; ?>);' onchange='DisableApplyButton(<?php echo $form_id; ?>);' onpaste='setTimeout(function(){DisableApplyButton(<?php echo $form_id; ?>);}, 50);' type='text' <?php echo $disabled_text; ?> <?php echo $placeholder_attribute; ?> <?php echo $this->get_tabindex(); ?> />
  <input type='button' disabled='disabled' onclick='ApplyCouponCode(<?php echo $form_id; ?>);' value='<?php echo esc_attr__( 'Apply', 'gravityformscoupons' ); ?>' id='gf_coupon_button' class='button' {$disabled_text} " . $this->get_tabindex() . '/> ' .
  <img style='display:none;' id='gf_coupon_spinner' src='" . gf_coupons()->get_base_url() . "/images/spinner.gif' alt='" . esc_attr__( 'please wait', 'gravityformscoupons' ) . "'/>" .
  <div id='gf_coupon_info'></div>" .
  <input type='hidden' id='gf_coupon_codes_{$form_id}' name='input_{$id}' value='" . esc_attr( $coupon_codes ) . "' {$logic_event} />" .
  <input type='hidden' id='gf_total_no_discount_{$form_id}'/>" .
  <input type='hidden' id='gf_coupons_{$form_id}' name='gf_coupons_{$form_id}' value='" . esc_attr( $coupons_detail ) . "' />" .
</div>";