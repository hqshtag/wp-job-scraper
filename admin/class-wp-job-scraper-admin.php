<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wajihtagourty.ml/
 * @since       0.1.0
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/admin
 * @author     Wajih Tagourty <Wajih.tagourty@gmail.com>
 */
class Wp_Job_Scraper_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since     0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Admin pages.
	 *
	 * @since     0.2.3
	 * @access   public
	 * @var      array    $pages    Stores admin pages.
	 */
	public $pages;


	/**
	 * Admin subpages.
	 *
	 * @since     0.2.5
	 * @access   public
	 * @var      array    $pages    Stores admin subpages.
	 */
	public $subpages;


	/**
	 * Admin subpages.
	 *
	 * @since     0.2.8
	 * @access   public
	 * @var      array    $pages    Stores admin subpages.
	 */
	public $custom_fields;


	/**
	 * Available APIs
	 * 
	 * @since 0.3.1
	 * @access public
	 * @var array $apis Stores available apis
	 */
	public $apis;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     0.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->pages = array(
			array(
				'page_title' => 'WP Job Scraper',
				'menu_title' => 'Job Scraper',
				'capability' => 'manage_options',
				'menu_slug' => 'wp-job-scraper',
				'callback' => array($this, 'admin_dashboard'),
				'icon_url' => 'dashicons-portfolio',
				'position' => 110
			)
		);

		$this->subpages = array(
			array(
				'parent_slug' => 'wp-job-scraper',
				'page_title' => 'Settings',
				'menu_title' => 'Settings',
				'capability' => 'manage_options',
				'menu_slug' => 'wp-job-scraper-settings',
				'callback' => array($this, 'admin_settings'),
			)
		);

		$this->custom_fields = array(

			"sections" => array(
				array(
					'id' => 'wp-job-scraper-toggels',
					'title' => 'APIs Manager',
					'callback' => array($this, 'toggles_intro'),
					'page' => 'wp-job-scraper-settings'
				)
			),
			"settings" => array(
				array(
					'option_group' => 'wp-job-scraper-settings',
					'option_name' => 'wp-job-scraper-settings',
					'callback' => array($this, 'checkbox_sanitize')
				)
			),
			"fields" => array()

		);

		//title => slug
		$this->apis = array(
			"USAJOBS" => "usajobs"
		);
		$this->register_apis($this->apis);
	}


	public function checkbox_sanitize($input)
	{
		$output = array();
		foreach ($this->apis as $title => $slug) {
			$output[$slug] =  isset($input[$slug]) ? true : false;
		}
		return $output;
	}


	public function toggles_intro()
	{
		echo 'Manage which APIs to load your jobads from by turning on/off the switches bellow:';
	}


	public function checkbox_field($args)
	{

		$option_name = $args['option_name'];
		$name = $args['label_for'];
		$classes = $args['class'];


		$checkbox = get_option($option_name);

		$checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

		echo	'<div class="wjs-ui-check">
					<input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="' . $classes . '"' . ($checked ? 'checked' : null) . '>
					<label for="' . $name . '"></label>
				</div>';
	}


	public function admin_dashboard()
	{
		ob_start();
		include(WP_JOB_SCRAPER_PATH . 'admin/partials/wp-job-scraper-admin-dashboard.php');
		$content = ob_get_contents();
		ob_get_clean();
		echo $content;
	}

	public function admin_settings()
	{
		ob_start();
		include(WP_JOB_SCRAPER_PATH . 'admin/partials/wp-job-scraper-admin-settings.php');
		$content = ob_get_contents();
		ob_get_clean();
		echo $content;
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     0.1.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-job-scraper-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     0.1.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-job-scraper-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Generate API fields 
	 * @since 0.3.1
	 * @param array $apis  List of APIS
	 * 
	 */

	public function register_apis($apis)
	{
		foreach ($apis as $title => $slug) {
			$api_fields = array(
				'id' => $slug,
				'title' => $title,
				'callback' => array($this, 'checkbox_field'),
				'page' => 'wp-job-scraper-settings',
				'section' => 'wp-job-scraper-toggels',
				'args' => array(
					'option_name' => 'wp-job-scraper-settings',
					'label_for' => $slug,
					'class' => 'wjs-ui-toggle'

				)
			);
			array_push($this->custom_fields["fields"], $api_fields);
		}
	}
}
