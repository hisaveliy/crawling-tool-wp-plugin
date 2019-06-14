<?php
/**
 * Main class which sets all together
 *
 * @since      1.0.0
 */

namespace Plugin_Scope;


class Core {

	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 * @throws \Exception
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
	 * @throws \Exception
	 */
	public function __construct(){

		//autoload files from `/autoload`
		spl_autoload_register( __CLASS__ . '::autoload' );

		$dependency_plugins = [];

		if (WOOCOMMERCE_PLUGIN || PAYMENT_GATEWAY) {
			$dependency_plugins['woocommerce/woocommerce.php'] = 'WooCommerce';
		}

		if (GFORMS_ADDON) {
			$dependency_plugins['gravityforms/gravityforms.php'] = 'Gravity Forms';
		}

		// check plugin dependencies
		if (!self::has_dependency($dependency_plugins)) {
			return;
		}

		//include files from `/includes`
		self::includes();

		//enqueue css and js files
		Assets::enqueue();

		//process ajax requests
		Requests::ajax();

		//run automatic updates
		// AutoUpdate::init();

		if (SETTINGS_PAGE) :
			add_action('init', __CLASS__.'::init_plugin_action_links');
		endif;

		if (PAYMENT_GATEWAY) :
			add_action('init', __CLASS__.'::init_payment_gateway');
		endif;
	}



	/**
	 * Include files
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function includes(){

		if (COMPOSER) :
			include_once PLUGIN_DIR . '/vendor/autoload.php';
		endif;

		if (SETTINGS_PAGE) :
			include_once PLUGIN_DIR . '/includes/settings-page/class-settings-page.php';
		endif;

		if (WOOCOMMERCE_PLUGIN) :
			include_once PLUGIN_DIR . '/includes/woocommerce/class-settings.php';
		endif;

		if (GFORMS_ADDON) :
			include_once PLUGIN_DIR . '/includes/gravityforms/class-gravityforms.php';
		endif;
	}



	/**
	 * Check whether the required dependencies are met
	 * also can show a notice message
	 *
	 * @since 1.0.0
	 * @param array $plugins - an array with `path => name` of the plugin
	 * @param boolean $show_msg
	 * @return boolean
	 */
	private static function has_dependency($plugins = array(), $show_msg = true){

		if (empty($plugins)) {
			return true;
		}

		$valid = true;
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		if (is_multisite()) {

			if(is_network_admin()) {

				$active_plugins = [];
				$active_sitewide_plugins = get_site_option('active_sitewide_plugins');

				foreach($active_sitewide_plugins as $path => $item){
					$active_plugins[] = $path;
				}

			} else {

				$active_plugins = get_blog_option(get_current_blog_id(), 'active_plugins');
			}
		}

		foreach ($plugins as $path => $name) {

			if(!in_array($path, $active_plugins)){

				if($show_msg){
					Utility::show_notice(sprintf(
						__('%s plugin requires %s plugin to be installed and active!', '{Text_Domain}'),
						'<b>'.PLUGIN_NAME.'</b>',
						"<b>{$name}</b>"
					), 'error');
				}

				$valid = false;
			}
		}

		return $valid;

	}



	/**
	 * Init the action links available in plugins list page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init_plugin_action_links(){

		//add plugin action and meta links
		self::plugin_links(array(
			'actions' => array(
				PLUGIN_SETTINGS_URL => __('Settings', '{Text_Domain}'),
				// admin_url('admin.php?page=wc-status&tab=logs') => __('Logs', '{Text_Domain}'),
				// admin_url('plugins.php?action='.PREFIX.'_check_updates') => __('Check for Updates', '{Text_Domain}')
			),
			'meta' => array(
				// '#1' => __('Docs', '{Text_Domain}'),
				// '#2' => __('Visit website', '{Text_Domain}')
			),
		));
	}



	/**
	 * Init new payment gateway
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init_payment_gateway(){

		include_once PLUGIN_DIR . '/includes/woocommerce/gateway/class-gateway.php';

		add_filter('woocommerce_payment_gateways', __CLASS__ . '::add_payment_gateway');
	}



	/**
	 * Add new gateway to WooCommerce payments
	 *
	 * @since 1.0.0
	 * @param array $gateways
	 * @return array
	 */
	public static function add_payment_gateway($gateways) {

		$gateways[] = __NAMESPACE__ . '\Gateway';

		return $gateways;
	}



	public static function autoload($filename) {

		$dir = PLUGIN_DIR . '/autoload/class-*.php';
		$paths = glob($dir);

		if (defined('GLOB_BRACE')) {
			$paths = glob( '{' . $dir . '}', GLOB_BRACE );
		}

		if ( is_array($paths) && count($paths) > 0 ){
			foreach( $paths as $file ) {
				if ( file_exists( $file ) ) {
					include_once $file;
				}
			}
		}
	}



	/**
	 * Add plugin action and meta links
	 *
	 * @since 1.0.0
	 * @param array $sections
	 * @return void
	 */
	private static function plugin_links($sections = array()) {

		//actions
		if (isset($sections['actions'])){

			$actions = $sections['actions'];
			$links_hook = is_multisite() ? 'network_admin_plugin_action_links_' : 'plugin_action_links_';

			add_filter($links_hook.PLUGIN_BASENAME, function($links) use ($actions){

				foreach(array_reverse($actions) as $url => $label){
					$link = '<a href="'.$url.'">'.$label.'</a>';
					array_unshift($links, $link);
				}

				return $links;

			});
		}

		//meta row
		if (isset($sections['meta'])){

			$meta = $sections['meta'];

			add_filter( 'plugin_row_meta', function($links, $file) use ($meta){

				if (PLUGIN_BASENAME == $file){

					foreach ($meta as $url => $label){
						$link = '<a href="'.$url.'">'.$label.'</a>';
						array_push($links, $link);
					}
				}

				return $links;

			}, 10, 2 );
		}

	}



	/**
	 * Run on plugin activation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function on_activation(){

		if (version_compare(phpversion(), '7.0', '<')) {
			wp_die(sprintf(
				__('Hey! Your server must have at least PHP 7.0! Could you please upgrade! %sGo back%s', '{Text_Domain}'),
				'<a href="'.admin_url('plugins.php').'">',
				'</a>'
			));
		}

		if (version_compare(get_bloginfo('version'), '5.0', '<')) {
			wp_die(sprintf(
				__('We need at least Wordpress 5.0! Could you please upgrade! %sGo back%s', '{Text_Domain}'),
				'<a href="'.admin_url('plugins.php').'">',
				'</a>'
			));
		}
	}



	/**
	 * Run on plugin deactivation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function on_deactivation(){
	}



	/**
	 * Run when plugin is deleting
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function on_uninstall(){

	}


}
Core::instance();