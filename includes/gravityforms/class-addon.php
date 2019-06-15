<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-14
 * Time: 19:10
 */

GFForms::include_addon_framework();

class GF_Class_Name extends GFAddOn {

	protected $_version = GF_GF_Addon_Name_ADDON_VERSION;

	protected $_min_gravityforms_version = '1.9';

	protected $_slug = '{Plugin_Prefix}';

	protected $_path = '{Text_Domain}/bootstrap.php';

	protected $_full_path = __FILE__;

	protected $_title = '{Plugin_Name}';

	protected $_short_title = 'GF_Addon_Name Add-On';

	private static $_instance = null;

	/**
	 * @return GF_Class_Name|null
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GF_Class_Name();
		}

		return self::$_instance;
	}

	public function init() {
		parent::init();
		add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
	}

	function form_submit_button( $button, $form ) {
		$settings = $this->get_form_settings( $form );
		if ( isset( $settings['enabled'] ) && true == $settings['enabled'] ) {
			$text   = $this->get_plugin_setting( 'mytextbox' );
			$button = "<div>{$text}</div>" . $button;
		}

		return $button;
	}

	public function plugin_page() {
		echo 'This page appears in the Forms menu';
	}

	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Simple Add-On Settings', 'simpleaddon' ),
				'fields' => array(
					array(
						'name'              => 'mytextbox',
						'tooltip'           => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'label'             => esc_html__( 'This is the label', '{Text_Domain}' ),
						'type'              => 'text',
						'class'             => 'small',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					)
				)
			)
		);
	}

	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'GF_Addon_Name Settings', '{Text_Domain}' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'My checkbox', '{Text_Domain}' ),
						'type'    => 'checkbox',
						'name'    => 'enabled',
						'tooltip' => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Enabled', '{Text_Domain}' ),
								'name'  => 'enabled',
							),
						),
					),
					array(
						'label'   => esc_html__( 'My checkboxes', '{Text_Domain}' ),
						'type'    => 'checkbox',
						'name'    => 'checkboxgroup',
						'tooltip' => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', '{Text_Domain}' ),
								'name'  => 'first',
							),
							array(
								'label' => esc_html__( 'Second Choice', '{Text_Domain}' ),
								'name'  => 'second',
							),
							array(
								'label' => esc_html__( 'Third Choice', '{Text_Domain}' ),
								'name'  => 'third',
							),
						),
					),
					array(
						'label'   => esc_html__( 'My Radio Buttons', '{Text_Domain}' ),
						'type'    => 'radio',
						'name'    => 'myradiogroup',
						'tooltip' => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', '{Text_Domain}' ),
							),
							array(
								'label' => esc_html__( 'Second Choice', '{Text_Domain}' ),
							),
							array(
								'label' => esc_html__( 'Third Choice', '{Text_Domain}' ),
							),
						),
					),
					array(
						'label'      => esc_html__( 'My Horizontal Radio Buttons', '{Text_Domain}' ),
						'type'       => 'radio',
						'horizontal' => true,
						'name'       => 'myradiogrouph',
						'tooltip'    => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'choices'    => array(
							array(
								'label' => esc_html__( 'First Choice', '{Text_Domain}' ),
							),
							array(
								'label' => esc_html__( 'Second Choice', '{Text_Domain}' ),
							),
							array(
								'label' => esc_html__( 'Third Choice', '{Text_Domain}' ),
							),
						),
					),
					array(
						'label'   => esc_html__( 'My Dropdown', '{Text_Domain}' ),
						'type'    => 'select',
						'name'    => 'mydropdown',
						'tooltip' => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'First Choice', '{Text_Domain}' ),
								'value' => 'first',
							),
							array(
								'label' => esc_html__( 'Second Choice', '{Text_Domain}' ),
								'value' => 'second',
							),
							array(
								'label' => esc_html__( 'Third Choice', '{Text_Domain}' ),
								'value' => 'third',
							),
						),
					),
					array(
						'label'             => esc_html__( 'My Text Box', '{Text_Domain}' ),
						'type'              => 'text',
						'name'              => 'mytext',
						'tooltip'           => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					),
					array(
						'label'   => esc_html__( 'My Text Area', '{Text_Domain}' ),
						'type'    => 'textarea',
						'name'    => 'mytextarea',
						'tooltip' => esc_html__( 'This is the tooltip', '{Text_Domain}' ),
						'class'   => 'medium merge-tag-support mt-position-right',
					),
					array(
						'label' => esc_html__( 'My Hidden Field', '{Text_Domain}' ),
						'type'  => 'hidden',
						'name'  => 'myhidden',
					),
					array(
						'label' => esc_html__( 'My Custom Field', '{Text_Domain}' ),
						'type'  => 'my_custom_field_type',
						'name'  => 'my_custom_field',
						'args'  => array(
							'text'     => array(
								'label'         => esc_html__( 'A textbox sub-field', '{Text_Domain}' ),
								'name'          => 'subtext',
								'default_value' => 'change me',
							),
							'checkbox' => array(
								'label'   => esc_html__( 'A checkbox sub-field', '{Text_Domain}' ),
								'name'    => 'my_custom_field_check',
								'choices' => array(
									array(
										'label'         => esc_html__( 'Activate', '{Text_Domain}' ),
										'name'          => 'subcheck',
										'default_value' => true,
									),
								),
							),
						),
					),
				),
			),
		);
	}

	public function settings_my_custom_field_type( $field, $echo = true ) {
		echo '<div>' . esc_html__( 'My custom field contains a few settings:', '{Text_Domain}' ) . '</div>';

		// get the text field settings from the main field and then render the text field
		$text_field = $field['args']['text'];
		$this->settings_text( $text_field );

		// get the checkbox field settings from the main field and then render the checkbox field
		$checkbox_field = $field['args']['checkbox'];
		$this->settings_checkbox( $checkbox_field );
	}

	public function is_valid_setting( $value ) {
		return strlen( $value ) > 5;
	}

}