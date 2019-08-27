<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2/9/19
 * Time: 5:50 PM
 */

namespace Crawling_WP;

class PostTypes {

  /**
   * @var null
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
   * Fields constructor.
   */
  function __construct () {

    add_action( 'init', __CLASS__ . '::register_guides_post_type' );

  }

  /**
   * Register post type
   */
  public static function register_guides_post_type() {


    /**
     * @link https://wp-kama.ru/function/register_post_type
     */
    register_post_type('guide', array(
      'label'  => null,
      'labels' => array(
        'name'               => __('Guides', TEXT_DOMAIN),
        'singular_name'      => __('Guide', TEXT_DOMAIN),
        'add_new'            => __('Add Guide', TEXT_DOMAIN),
        'add_new_item'       => __('Add new Guide', TEXT_DOMAIN),
        'edit_item'          => __('Edit Guide', TEXT_DOMAIN),
        'new_item'           => __('New Guide', TEXT_DOMAIN),
        'view_item'          => __('See Guide', TEXT_DOMAIN),
        'search_items'       => __('Search Guides', TEXT_DOMAIN),
        'not_found'          => __('Not Found', TEXT_DOMAIN),
        'not_found_in_trash' => __('Not Found in Trash', TEXT_DOMAIN),
        'parent_item_colon'  => '',
        'menu_name'          => __('Guides', TEXT_DOMAIN),
      ),
      'description'         => '',
      'public'              => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => false,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_admin_bar'   => true,
      'show_in_nav_menus'   => true,
      'show_in_rest'        => true,
      'rest_base'           => true,
      'menu_position'       => null,
      'menu_icon'           => 'dashicons-book-alt',
      'hierarchical'        => true,
      'supports'            => array( 'title', 'editor', 'thumbnail' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
      'taxonomies'          => array('guide-cat'),
      'has_archive'         => true,
      'rewrite'             => true,
      'query_var'           => true,
    ) );


    /**
     * @link https://wp-kama.ru/function/register_taxonomy
     */
    register_taxonomy('guide-cat', array('guide'), array(
      'label'                 => '',
      'labels'                => array(
        'name'              => __('Categories', TEXT_DOMAIN),
        'singular_name'     => __('Category', TEXT_DOMAIN),
        'search_items'      => __('Search Categories', TEXT_DOMAIN),
        'all_items'         => __('All Categories', TEXT_DOMAIN),
        'view_item '        => __('View Category', TEXT_DOMAIN),
        'parent_item'       => __('Parent Category', TEXT_DOMAIN),
        'parent_item_colon' => __('Parent Category:', TEXT_DOMAIN),
        'edit_item'         => __('Edit Category', TEXT_DOMAIN),
        'update_item'       => __('Update Category', TEXT_DOMAIN),
        'add_new_item'      => __('Add New Category', TEXT_DOMAIN),
        'new_item_name'     => __('New Category Name', TEXT_DOMAIN),
        'menu_name'         => __('Categories', TEXT_DOMAIN),
      ),
      'description'           => '',
      'public'                => true,
      'publicly_queryable'    => null,
      'show_in_nav_menus'     => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'show_tagcloud'         => true,
      'show_in_rest'          => null,
      'rest_base'             => null,
      'hierarchical'          => false,
      'update_count_callback' => '',
      'rewrite'               => true,
      'capabilities'          => array(),
      'meta_box_cb'           => null,
      'show_admin_column'     => true,
      '_builtin'              => false,
      'show_in_quick_edit'    => null,
    ) );


  }

}