<?php
/**
 * This creates settings tab in WooCommerce settings page
 *
 * @since 1.0.0
 */

namespace Crawling_WP;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


if (!class_exists('\WC_Settings_Page')) {
	$file = WP_CONTENT_DIR . '/plugins/woocommerce/includes/admin/settings/class-wc-settings-page.php';

	if ( file_exists( $file ) ) {
		include_once $file;
	} else {
		exit;
	}
}


class WC_Settings extends \WC_Settings_Page {

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
	public function __construct() {

		$this->id = PREFIX.'-settings';

		add_filter('woocommerce_settings_tabs_array', array( $this, 'add_settings_tab'), 50);
		add_action('woocommerce_sections_' . $this->id, array( $this, 'output_sections' ));
		add_action('woocommerce_settings_' . $this->id, array( $this, 'output' ));
		add_action('woocommerce_settings_save_' . $this->id, array( $this, 'save' ));

	}



	/**
	 * Add new settings tab name
	 *
	 * @since 1.0.0
	 * @param array $settings_tabs
	 * @return array
	 */
	public function add_settings_tab($settings_tabs) {

		$settings_tabs[$this->id] = __('Settings Tab', 'savellab');

		return $settings_tabs;
	}



	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'' => __( 'General', 'savellab' ),
			'second' => __( 'Second', 'savellab' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}



	/**
	 * Get sections
	 *
	 * @since 1.0.0
	 * @param string $section
	 * @return array
	 */
	public function get_settings( $section = null ) {

		switch ($section){

			case 'second':

				$settings = array(
					array(
						'name'          => '',//__( 'Second', 'savellab' ),
						'type'          => 'title',
						'desc'          => '',
					),
					array(
						'name'          => __( 'Layout', 'savellab' ),
						'type'          => 'select',
						'id'            => PREFIX .'_layouts',
						'options'       => array(
							'default'    => __('Default', 'savellab')
						)
					),
					array(
						'type'          => 'sectionend',
					)
				);
				break;


			default:

				$settings = array(
					array(
						'name' => __( 'Information', 'savellab' ),
						'type' => 'title',
						'id'   => PREFIX .'_details_title',
					),
					array(
						'name' => __( 'Name', 'savellab' ),
						'type' => 'text',
						'id'   => PREFIX .'_shop_name',
						'default' => get_bloginfo('name')
					),
					array(
						'type' => 'sectionend',
						'id'   => PREFIX .'_details_end'
					),
				);
		}

		return $settings;
	}



	/**
	 * Output settings
	 *
	 * @return void
	 */
	public function output() {

		global $current_section;

		$settings = $this->get_settings( $current_section );

		woocommerce_admin_fields( $settings );
	}



	/**
	 * Save settings
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save() {

		global $current_section;

		$settings = $this->get_settings( $current_section );

		woocommerce_update_options( $settings );
	}


}
WC_Settings::instance();