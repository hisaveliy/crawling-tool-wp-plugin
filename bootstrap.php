<?php
/**
 * Plugin Name: Crawling Tool WP Plugin
 * Description: Parser Tools
 * Version: 1.0.0
 * Author: Savellab
 * Author URI:  https://savellab.nl/
 * Developer: TheArdent
 * Developer URI: mailto:oleksii.yaryi@gmail.com
 * Text Domain: savellab
 * Domain Path: /languages
 * Network: false
 *
 * WC requires at least: 3.5.0
 * WC tested up to: 3.5.4
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


namespace Crawling_WP;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define(__NAMESPACE__ . '\PREFIX', 'crawling_wp');

define(__NAMESPACE__ . '\PLUGIN_VERSION', '1.0.0');

define(__NAMESPACE__ . '\PLUGIN_NAME', 'Crawling Tool WP Plugin');

define(__NAMESPACE__ . '\PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));

define(__NAMESPACE__ . '\PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));

define(__NAMESPACE__ . '\PLUGIN_BASENAME', plugin_basename(PLUGIN_DIR) . '/bootstrap.php');

define(__NAMESPACE__ . '\PLUGIN_FOLDER', plugin_basename(PLUGIN_DIR));

define(__NAMESPACE__ . '\PLUGIN_INSTANCE', sanitize_title(crypt($_SERVER['SERVER_NAME'] ?? 'localhost', $salt = PLUGIN_FOLDER)));

// @todo resolve url
define(__NAMESPACE__ . '\PLUGIN_SETTINGS_URL', admin_url('options-general.php?page='.PREFIX.'-settings'));

define(__NAMESPACE__ . '\CHANGELOG_COVER', PLUGIN_URL . '/assets/images/plugin-cover.jpg');

define(__NAMESPACE__ . '\AUTO_UPDATE_URL', 'https://savellab.com');

define(__NAMESPACE__ . '\ERROR_PATH', plugin_dir_path(__FILE__) . 'error.log');

define(__NAMESPACE__ . '\TEXT_DOMAIN', 'savellab');

define(__NAMESPACE__ . '\COMPOSER', false);

/**
 * Native Settings page
 */

define(__NAMESPACE__ . '\SETTINGS_PAGE', true);

/**
 * Woocommerce Settings
 */

define(__NAMESPACE__ . '\WOOCOMMERCE_PLUGIN', false);

define(__NAMESPACE__ . '\PAYMENT_GATEWAY', false);

/**
 * Gravity Forms settings
 */

define(__NAMESPACE__ . '\GFORMS_ADDON', false);

define(__NAMESPACE__ . '\GFORMS_ADDON_NAME', 'GF_Addon_Name');

define(__NAMESPACE__ . '\GFORMS_CLASS_NAME', 'GF_Class_Name');


//init
if(!class_exists( __NAMESPACE__ . '\Core')){
	include_once PLUGIN_DIR . '/includes/class-core.php';
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\Core::on_activation');
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\Core::on_deactivation');

//load translation, make sure this hook runs before all, so we set priority to 1
add_action('init', function(){
	load_plugin_textdomain( __NAMESPACE__ . '\TEXT_DOMAIN', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}, 1);