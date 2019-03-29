<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2/9/19
 * Time: 3:49 PM
 */

namespace Savellab_Plugin;

class Settings_Page extends Abstract_Settings_Page {

  public function __construct() {
    self::$page_title = __('Savellab Settings', PREFIX);
    self::$menu_title = __('Savellab Settings', PREFIX);
    self::$capability = 'manage_options';
    self::$menu_slug  = PREFIX . '-settings';

    self::$fields_id = PREFIX;

    if (self::$menu_slug)
      parent::__construct();
  }

  public function get_fields() {
    return array(
      array(
        'uid'   => PREFIX . '_title',
        'label' => __('Title', PREFIX),
        'section' => 'settings',
        'type'    => 'text',
        'options' => false,
        'placeholder' => '',
        'helper'      => 'Does this help?',
        'supplemental' => 'I am underneath!',
        'default'      => ''
      ),
      array(
        'uid'   => PREFIX . '_description',
        'label' => __('Description', PREFIX),
        'section' => 'settings',
        'type'    => 'textarea',
        'options' => false,
      )
    );
  }

  public function get_sections() {
    return array(
      'settings' => array(
        'title' => __('Settings', PREFIX)
      ),
    );
  }

}

new Settings_Page();