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
}
