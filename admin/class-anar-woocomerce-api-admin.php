<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://panjerestudio.com
 * @since      1.0.0
 *
 * @package    Anar_Woocomerce_Api
 * @subpackage Anar_Woocomerce_Api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Anar_Woocomerce_Api
 * @subpackage Anar_Woocomerce_Api/admin
 * @author     پنجره استودیو <SamanAzizi1394@gmail.com>
 */
class Anar_Woocomerce_Api_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Anar_Woocomerce_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Anar_Woocomerce_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/anar-woocomerce-api-admin.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-toastify-style', plugin_dir_url(__FILE__) . 'css/toastify.min.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Anar_Woocomerce_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Anar_Woocomerce_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/anar-woocomerce-api-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . '-toastify-script', plugin_dir_url(__FILE__) . 'js/toastify.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'awca_ajax_object', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'awca_handle_token_activation_ajax_nonce' => wp_create_nonce(),
		));
	}
}
