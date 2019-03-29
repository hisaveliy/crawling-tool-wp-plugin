<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2/9/19
 * Time: 5:50 PM
 */

namespace Savellab_Plugin;

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
        'name'               => __('Guides', 'limevpn'),
        'singular_name'      => __('Guide', 'limevpn'),
        'add_new'            => __('Add Guide', 'limevpn'),
        'add_new_item'       => __('Add new Guide', 'limevpn'),
        'edit_item'          => __('Edit Guide', 'limevpn'),
        'new_item'           => __('New Guide', 'limevpn'),
        'view_item'          => __('See Guide', 'limevpn'),
        'search_items'       => __('Search Guides', 'limevpn'),
        'not_found'          => __('Not Found', 'limevpn'),
        'not_found_in_trash' => __('Not Found in Trash', 'limevpn'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Guides', 'limevpn'),
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
        'name'              => __('Categories', 'limevpn'),
        'singular_name'     => __('Category', 'limevpn'),
        'search_items'      => __('Search Categories', 'limevpn'),
        'all_items'         => __('All Categories', 'limevpn'),
        'view_item '        => __('View Category', 'limevpn'),
        'parent_item'       => __('Parent Category', 'limevpn'),
        'parent_item_colon' => __('Parent Category:', 'limevpn'),
        'edit_item'         => __('Edit Category', 'limevpn'),
        'update_item'       => __('Update Category', 'limevpn'),
        'add_new_item'      => __('Add New Category', 'limevpn'),
        'new_item_name'     => __('New Category Name', 'limevpn'),
        'menu_name'         => __('Categories', 'limevpn'),
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