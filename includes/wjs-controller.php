<?php

class Controller
{

    public $jobTypes;




    public function get_available_job_types()
    {
        $api_url = site_url('/wp-json/wp/v2/job-types');
        $res = wp_remote_get($api_url);

        if (!is_wp_error($res)) {
            $this->jobTypes = json_decode($res['body'], true);
        }
    }
    /**
     * Checks if the required fields are all set up
     * @since 0.3.3 
     * @param String $controller Controller Name (API)
     * @param Array $required Required fields in each settings page
     */
    public function isSetUp($controller, $required)
    {
        $options = get_option("wp-job-scraper-$controller");
        //var_dump($options);
        foreach ($options as $field => $value) {
            if (in_array($field, $required)) {
                if (!$value) return false;
            }
        }
        return true;
    }
}
