<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 19:04
 */

define( 'GF_GF_Addon_Name_ADDON_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GF_GF_Addon_Name_ADDON_VERSION', 'load' ), 5 );

class GF_GF_Addon_Name_AddOn_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once( 'class-addon.php' );

		require_once( 'fields/example/class-gf-field-example.php' );

		GFAddOn::register( 'GF_Class_Name' );
	}

}

GF_Class_Name::get_instance();