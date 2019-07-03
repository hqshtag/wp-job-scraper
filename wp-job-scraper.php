<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wajihtagourty.ml/
 * @since             0.1.0
 * @package           Wp_Job_Scraper
 *
 * @wordpress-plugin
 * Plugin Name:       Job Scraper
 * Plugin URI:        https://github.com/kikinass/wp-job-scraper
 * Description:       This plugin helps you load job-ads from multiple* APIs. Requires WP Job Manager.
 * Version:           0.3.0
 * Author:            Wajih Tagourty
 * Author URI:        https://wajihtagourty.ml/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-job-scraper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_JOB_SCRAPER_VERSION', '0.3.0');

define('WP_JOB_SCRAPER_PATH', plugin_dir_path(__FILE__));


require WP_JOB_SCRAPER_PATH . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/kikinass/wp-job-scraper/',
	__FILE__,
	'wp-job-scraper'
);
$myUpdateChecker->setBranch('master');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-job-scraper-activator.php
 */
function activate_wp_job_scraper()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-job-scraper-activator.php';
	Wp_Job_Scraper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-job-scraper-deactivator.php
 */
function deactivate_wp_job_scraper()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-job-scraper-deactivator.php';
	Wp_Job_Scraper_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_job_scraper');
register_deactivation_hook(__FILE__, 'deactivate_wp_job_scraper');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-job-scraper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_wp_job_scraper()
{

	$plugin = new Wp_Job_Scraper();
	$plugin->run();
}
run_wp_job_scraper();
