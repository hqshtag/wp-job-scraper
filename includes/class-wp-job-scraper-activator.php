<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wajihtagourty.ml/
 * @since       0.1.0
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since       0.1.0
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/includes
 * @author     Wajih Tagourty <Wajih.tagourty@gmail.com>
 */
class Wp_Job_Scraper_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since     0.1.0
	 */
	public static function activate()
	{
		if (get_option('wp-job-scraper-settings')) {
			return;
		}
		$arr = array();
		update_option('wp-job-scraper-settings', $arr);
	}
}
