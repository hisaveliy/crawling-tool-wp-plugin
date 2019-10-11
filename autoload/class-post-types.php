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
    }

    /**
     * Register post type
     */
    public static function add_met_box()
    {
        add_meta_box('crawl_meta', ' Crawling data', function ($post) {
            echo 'Website: <strong>'.get_post_meta($post->ID, '_crawl_class', true).'</strong><br/>';
            echo 'ID: <strong>'.get_post_meta($post->ID, '_crawl_id', true).'</strong>';

        }, 'iwp_property');
    }
}