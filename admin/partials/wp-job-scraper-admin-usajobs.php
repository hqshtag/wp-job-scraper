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
    <h1>USAJOBS Api Manager</h1>

    <form method="POST" action="options.php">
        <?php
        settings_fields('wp-job-scraper-usajobs');
        do_settings_sections('wp-job-scraper-usajobs');
        submit_button();
        ?>
    </form>
</div>