<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 19:04
 */

require_once Crawling_WP\PLUGIN_DIR . '/includes/gravityforms/class-addon.php';

require_once Crawling_WP\PLUGIN_DIR . '/includes/gravityforms/fields/example/class-gf-field-example.php';

define( 'GF_GF_Addon_Name_ADDON_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GF_GF_Addon_Name_ADDON_VERSION', 'load' ), 5 );

class GF_GF_Addon_Name_AddOn_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		GFAddOn::register( 'GF_Class_Name' );
	}

}

GF_Class_Name::get_instance();