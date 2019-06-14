<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 22:41
 */

namespace Savellab\Simple;

class Settings_Page {


    public static $page_title = '';


    public static $menu_title = '';


    public static $page_slug = '';


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
    public function __construct() {

        self::$page_title = 'Options';
        self::$menu_title = 'Options';
        self::$page_slug = PREFIX . '-settings';

        add_action('admin_menu', __CLASS__ . '::add_admin');

    }


    private static function get_id($name = '') {
        return PREFIX . '_' . $name;
    }


    /**
     * @return array
     */
    private static function get_options() {
        return [
            [
                'label' => 'General',
                'type' => 'headline-3',
            ],
            [
                'id'  => self::get_id('headline'),
                'type'  => 'text',
                'label' => 'Headline'
            ],
            [
                'id' => self::get_id('email'),
                'type' => 'email',
                'label' => 'Email'
            ],
            [
                'id' => self::get_id('password'),
                'type' => 'password',
                'label' => 'Password'
            ],
        ];
    }


    /**
     * @return false|string
     */
    private static function render_fields() {
        $options = self::get_options();

        ob_start();

        foreach ($options as $value) :

            switch ($value['type']) :
                case 'headline-3':
                    self::create_suf_headline_3($value);
                    break;

                case 'headline-4':
                    self::create_suf_headline_4($value);
                    break;

                case 'textarea':
                    self::create_section_for_textarea($value);
                    break;

                case 'radio':
                    self::create_section_for_radio($value);
                    break;

                default:
                    self::create_section_for_default($value);
                    break;
            endswitch;

        endforeach;

        return ob_get_clean();
    }

    private static function create_form() {

        Utility::tpl('includes/settings-page/sections/form', [ 'fields' => self::render_fields() ]);

    }


    /**
     * @param array $value
     */
    private static function create_opening_tag($value = array()) {
        $group_class = "";

        if (isset($value['grouping'])) {
            $group_class = "suf-grouping-rhs";
        }

        echo '<div class="suf-section fix">'."\n";

        if ($group_class != "") {
            echo "<div class='$group_class fix'>\n";
        }
    }


    /**
     * @param array $value
     */
    private static function create_closing_tag($value = array()) {

        if (isset($value['desc']) && !(isset($value['type']) && $value['type'] == 'checkbox')) {
            echo '<p>' . $value['desc']."</p><br />";
        }

        if (isset($value['note'])) {
            echo "<span class=\"note\">".$value['note']."</span><br />";
        }

        if (isset($value['grouping'])) {
            echo "</div>\n";
        }

        echo "</div>\n";
    }


    /**
     * @param array $value
     */
    private static function create_suf_headline_3($value = array()) {
        Utility::tpl('includes/settings-page/sections/headline-3', [ 'value' => $value ]);
    }


    /**
     * @param array $value
     */
    private static function create_suf_headline_4($value = array()) {
        Utility::tpl('includes/settings-page/sections/headline-4', [ 'value' => $value ]);
    }

    /**
     * @param array $value
     */
    private static function create_section_for_default($value = array()) {
        self::create_opening_tag($value);

        Utility::tpl('includes/settings-page/sections/default', [ 'value' => $value ]);

        self::create_closing_tag($value);
    }


    /**
     * @param array $value
     */
    private static function create_section_for_textarea($value = array()) {
        self::create_opening_tag($value);

        Utility::tpl('includes/settings-page/sections/textarea', [ 'value' => $value ]);

        self::create_closing_tag($value);
    }


    private static function create_section_for_radio($value = array()) {
        self::create_opening_tag($value);

        foreach ($value['options'] as $current_value => $label) {
            Utility::tpl('includes/settings-page/sections/radio', [
                'value' => $value,
                'current_value' => $current_value,
                'label' => $label,
            ]);
        }

        self::create_closing_tag($value);
    }

    public static function add_admin() {
        $options = self::get_options();

        if ( $_GET['page'] === self::$page_slug && 'save' === $_REQUEST['formaction'] ) {

            foreach ($options as $value) {
                if( isset( $_REQUEST[ $value['id'] ] ) ) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                } else {
                    delete_option( $value['id'] );
                }
            }

            header("Location: themes.php?page=" . self::$page_slug . "&saved=true");
            die;
        }

        add_theme_page(
            self::$page_title,
            self::$menu_title,
            'edit_themes',
            self::$page_slug,
            __CLASS__ . '::admin'
        );
    }

    public static function admin() {

        if ($_REQUEST['saved']) {
            ?>

            <div id="message" class="updated fade">
                <p><strong><?php _e( 'Settings updated', '{Text_Domain}' ); ?></strong></p>
            </div>

            <?php
        }
        ?>

        <div class="wrap">
            <h1><?php _e( 'Savellab Simple Plugin Settings', '{Text_Domain}' ); ?></h1>

            <div class="options">
                <?php self::create_form(); ?>
            </div><!-- mnt-options -->
        </div><!-- wrap -->
    <?php } // end function mynewtheme_admin()
}

try {
    Settings_Page::instance();
} catch (\Exception $e) {
}
