<?php
/**
 * This extends WooCommerce Payments
 *
 * @replace {Gateway_name}
 *
 * @since 1.0.0
 */

namespace Crawling_WP;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;

if (!class_exists('\WC_Payment_Gateway')) {
	exit;
}

class Gateway extends \WC_Payment_Gateway {


	/**
	 * @since 1.0.0
	 */
	public function __construct(){


		$this->id                 = 'gateway_' . PREFIX;
		$this->icon               = apply_filters('woocommerce_offline_icon', '');
		$this->has_fields         = true;
		// $this->enabled            = 'no';
		$this->method_title       = __('{Gateway_name}', 'savellab');
		$this->method_description = __('Take payments via {Gateway_name} platform.', 'savellab');

		$this->supports = array(
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			// 'subscription_reactivation',
			// 'subscription_amount_changes',
			// 'subscription_date_changes',
			// 'subscription_payment_method_change',
			// 'subscription_payment_method_change_customer',
			// 'subscription_payment_method_change_admin',
			// 'multiple_subscriptions',
			// 'gateway_scheduled_payments',
		);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option('title');
		$this->description  = $this->get_option('description');
		$this->payType      = $this->get_option('payType', 'direct_debit');
		// $this->paymentTerms = $this->get_option('paymentTerms');
		$this->minimumTerms = $this->get_option('minimumTerms', '0');


		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_scheduled_subscription_payment_'.$this->id, array($this, 'renewal_subscription'), 10, 2);

	}



	/**
	 * Renew a WC subscription
	 *
	 * @since 1.0.0
	 * @param string $renewal_total
	 * @param object $renewal_order
	 * @return void
	 */
	public function renewal_subscription($renewal_total, $renewal_order){
	}



	/**
	 * Check If The Gateway Is Available For Use.
	 * display the payment method if there is only one subscription in cart
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_available() {

		return parent::is_available();
	}



	/**
	 * Add extra payment fields
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function payment_fields() {

		$description = $this->get_description();
		if ( $description ) {
			echo wpautop( wptexturize( $description ) );
		}

		Utility::tpl('includes/woocommerce/payment/extra-fields');
	}


	/**
	 * @return mixed
	 */
	public function validate_fields() {

		$is_valid = parent::validate_fields();

		return $is_valid;
	}



	/**
	 * Process the payment
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment($order_id) {
		global $woocommerce;

		$order = new \WC_Order($order_id);

		return array(
			'result'   => 'success',
			'redirect' => ''
		);
	}



	/**
	 * Gateway settings
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			array(
				'title'       => __('Automatic updates & support', 'savellab'),
				'type'        => 'title',
			),
			'license_key'    => array(
				'title'       => __('API Key', 'savellab'),
				'type'        => 'text',
			),
			'license_email'  => array(
				'title'       => __('API Email', 'savellab'),
				'type'        => 'text',
			),
			array(
				'title'       => __('Settings', 'savellab'),
				'type'        => 'title',
				'description' => '',
			),
			'enabled'        => array(
				'title'       => __('{Gateway_name} Payments', 'savellab'),
				'type'        => 'checkbox',
				'label'       => __('Enable/Disable', 'savellab'),
				'default'     => 'yes'
			),
			'title'          => array(
				'title'       => __('Title', 'savellab'),
				'type'        => 'text',
				'desc_tip'    => __('The title which the user sees during checkout.', 'savellab' ),
				'default'     => __('{Gateway_name}', 'savellab'),
			),
			'description'    => array(
				'title'       => __('Description', 'savellab'),
				'type'        => 'text',
				'desc_tip'    => __('The description which the user sees during checkout.', 'savellab'),
				'default'     => __('Pay via {Gateway_name} platform. Please select a payment method', 'savellab'),
			),
			array(
				'title'       => __('API Information', 'savellab'),
				'type'        => 'title',
				'description' => __('Provide the corresponding information to connect this webshop with {Gateway_name} platform', 'savellab'),
			),
			'api_token'          => array(
				'title'           => __('Live API token', 'savellab'),
				'type'            => 'password',
				'desc_tip'        => __('The API token used in production environment.', 'savellab'),
				'default'         => '',
			),
			'testmode'           => array(
				'title'           => __('Test mode', 'savellab'),
				'type'            => 'checkbox',
				'label'           => __('Enable/Disable', 'savellab'),
				'default'         => 'no'
			),
			'test_api_token'     => array(
				'title'           => __('Test API token', 'savellab'),
				'type'            => 'password',
				'desc_tip'        => __('The API token used for test environment.', 'savellab'),
				'default'         => '',
			),
		);
	}


}