<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wajihtagourty.ml/
 * @since      1.0.0
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

		add_action('admin_menu', array($this, 'add_admin_pages'));
	}


	public function add_admin_pages()
	{
		add_menu_page(
			'WP Job Scraper',
			'Job Scraper',
			'manage_options',
			'wp-job-scraper',
			array($this, 'admin_dashboard'),
			'dashicons-portfolio',
			null
		);
	}

	public function register_wp_job_scraper_admin_menu()
	{ }


	public function render_wp_job_scraper_admin_menu()
	{ }

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
		 * defined in Wp_Job_Scraper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Job_Scraper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-job-scraper-admin.css', array(), $this->version, 'all');
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
		 * defined in Wp_Job_Scraper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Job_Scraper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-job-scraper-admin.js', array('jquery'), $this->version, false);
	}
}
