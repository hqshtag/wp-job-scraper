<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wajihtagourty.ml/
 * @since       0.1.0
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since       0.1.0
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/includes
 * @author     Wajih Tagourty <Wajih.tagourty@gmail.com>
 */
class Wp_Job_Scraper
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     0.1.0
	 * @access   protected
	 * @var      Wp_Job_Scraper_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Wraper over The Settings API.
	 *
	 * @since     0.2.0
	 * @access   public
	 * @var      Settings_Api    $settings   
	 */
	public $settings;

	/**
	 * Stores the controllers
	 * 
	 * @since 0.3.2
	 * @access public
	 * @var Controller $controllers
	 */
	public $controllers;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     0.1.0
	 */
	public function __construct()
	{
		if (defined('WP_JOB_SCRAPER_VERSION')) {
			$this->version = WP_JOB_SCRAPER_VERSION;
		} else {
			$this->version = '0.2.0';
		}
		$this->plugin_name = 'wp-job-scraper';
		$this->controllers = array();


		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->load();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Job_Scraper_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Job_Scraper_i18n. Defines internationalization functionality.
	 * - Wp_Job_Scraper_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     0.1.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-job-scraper-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-job-scraper-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-job-scraper-admin.php';


		/**
		 * a wraper over the Settings API
		 */

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-settings-api.php';

		$options = get_option('wp-job-scraper-settings');
		foreach ($options as $key => $value) {
			if ($value) {
				require_once plugin_dir_path(dirname(__FILE__)) . "includes/wjs-$key.php";
				$controller_name = ucfirst($key) . '_Controller';
				$controller = new $controller_name();
				array_push($this->controllers, $controller);
			}
		}

		$this->loader = new Wp_Job_Scraper_Loader();
		$this->settings = new Settings_Api($this->loader);
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Job_Scraper_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since     0.1.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Wp_Job_Scraper_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     0.1.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Wp_Job_Scraper_Admin($this->get_plugin_name(), $this->get_version());
		$usajobs = new Usajobs_Controller();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		var_dump($this->controllers);
		$this->settings->set_custom_fields($plugin_admin->custom_fields);
		$this->settings->set_custom_fields($usajobs->custom_fields);

		$this->settings->add_pages($plugin_admin->pages)->with_subpage('Dashboard')->add_subpages($plugin_admin->subpages)->register();
	}

	public function generate_ui()
	{
		$options = get_options('wp-job-scraper-settings');
		if (is_array($options)) {
			foreach ($options as $option => $value) {
				# code...
			}
		}
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     0.1.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since      0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since      0.1.0
	 * @return    Wp_Job_Scraper_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since      0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
