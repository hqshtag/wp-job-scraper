<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wajihtagourty.ml/
 * @since       0.1.0
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div class="tab-container">

        <ul class="tabs">
            <li class="tab-link current" data-tab="tab-1">Dashboard</li>
            <li class="tab-link" data-tab="tab-2">Info</li>
        </ul>

        <div id="tab-1" class="tab-content wjs-dahsboard current">
            <div class="wjs-main">
                <div class="wjs-timer"></div>
                <div class="wjs-update-button ld-over">
                    <button id="wjs-update-btn">Update</button>
                    <div class="ld ld-ring ld-spin"></div>


                </div>

            </div>

            <div class="wjs-stats">stats</div>
        </div>
        <div id="tab-2" class="tab-content">
            <h3>About</h3>
            <p>This Plugin gets Job-ads and posts from mutliple <em>APIs</em> and add them to your job listings via the <em>WordPress</em> rest API.</p>
            <h3>Requirements</h3>
            <p>In order to run this plugin you will need:</p>
            <ul>
                <li>
                    <b>WordPress</b> version <b>5.x.x</b> or higher.
                </li>
                <li>
                    <b>WP Job Manager</b> plugin <em>Installed</em> and <em>Activated</em> on your site.
                </li>
                <li>
                    Make sure no <b>Firewalls</b> or other plugins are preventing anonymous requests to the WP API.
                    If you are having issues contact me via <span class="wjs-myemail"><em>wajihtagourty+wpjs@gmail.com</em><span>
                </li>

            </ul>
        </div>

    </div>

</div>