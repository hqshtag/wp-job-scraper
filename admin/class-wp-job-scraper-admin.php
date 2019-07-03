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
			"settings" => array(
				array(
					'option_group' => 'wp-job-scraper-options-group',
					'option_name' => 'text_example',
					'callback' => array($this, 'my_option_group')
				)
			),
			"sections" => array(
				array(
					'id' => 'wp-job-scraper-settings',
					'title' => 'API Settings',
					'callback' => array($this, 'my_section_callback'),
					'page' => 'wp-job-scraper'
				)
			),
			"fields" => array(
				array(
					'id' => 'text_example',
					'title' => 'API Settings field',
					'callback' => array($this, 'my_field_callback'),
					'page' => 'wp-job-scraper',
					'section' => 'wp-job-scraper-settings',
					'args' => array(
						'label_for' => 'text_example',
						'class' => 'example-class'
					)
				)
			)

		);

		add_action('admin_init', array($this, 'setup_sections'));
		add_action('admin_init', array($this, 'setup_api_fields'));
	}

	public function setup_sections()
	{
		add_settings_section(
			'wp-job-scraper-toggels',
			'APIs',
			false,
			'wp-job-scraper-settings'
		);
	}

	public function setup_api_fields()
	{
		add_settings_field(
			'wp-job-scrapper-apis-usajobs',
			'USAJobs',
			array($this, 'my_cake'),
			'wp-job-scraper-settings',
			'wp-job-scraper-toggels'
		);
		register_setting(
			'wp-job-scraper-settings',
			'wp-job-scraper-toggels'
		);
	}

	public function my_cake()
	{
		$value = esc_attr(get_option('wp-job-scrapper-apis-usajobs'));
		echo '<input type ="text" class="regular-text" name="text_example" value="' . $value . '" placeholder="something awesome" >';
	}


	public function my_section_callback()
	{
		echo 'check out this callback funciton';
	}
	public function my_field_callback()
	{
		$value = esc_attr(get_option('text_example'));
		echo '<input type ="text" class="regular-text" name="text_example" value="' . $value . '" placeholder="something awesome" >';
	}

	public function set_settings()
	{
		$args = array(
			'option_group' => 'wp-job-scraper-options-group',
			'option_name' => 'text_example',
			'callback' => array($this, 'my_option_group')
		);

		//$this->settings->setSettings
	}

	public function my_option_group($input)
	{
		return $input;
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
}
