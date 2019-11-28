<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2/9/19
 * Time: 5:50 PM
 */

namespace Crawling_WP;

class PostTypes
{

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     * @since     1.0.0
     *
     */
    public static function instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fields constructor.
     */
    function __construct()
    {
        add_action('add_meta_boxes', __CLASS__.'::add_met_box');
        add_action('save_post', __CLASS__.'::save_details');
    }

    /**
     * Register post type
     */
    public static function add_met_box()
    {
        add_meta_box('crawl_meta', ' Crawling data', function ($post) {
            echo 'Website: <strong>'.get_post_meta($post->ID, '_crawl_class', true).'</strong><br/>';
            echo 'ID: <strong>'.get_post_meta($post->ID, '_crawl_id', true).'</strong><br/>';

            $desync = get_post_meta($post->ID, '_crawl_desync', true) === 'on' ? true : false;

            echo '<label>De-sync: ';
            printf('<input type="checkbox" name="%s" value="on" %s />', '_crawl_desync', $desync ? 'checked' : '');
            echo '</label>';

        }, 'iwp_property');
    }

    /**
     * Update Crawling Post Meta
     */
    public static function save_details()
    {
        global $post;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        update_post_meta($post->ID, "_crawl_desync", $_POST["_crawl_desync"]);
    }
}