<?php
/**
 * This is responsible for extending general things of WooCommerce
 *
 * @since 1.0.0
 */

namespace Crawling_WP;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Woocommerce {

	/**
	 * The instance of this class
	 *
	 * @since 1.0.0
	 * @var null|object
	 */
	protected static $instance = null;


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}



	/**
	 * @since 1.0.0
	 */
	public function __construct(){

	}

}
Woocommerce::instance();