<?php

/**
 * 
 *
 * Loads and defines the USAJOBS API settings.
 *
 * @link       https://wajihtagourty.ml/
 * @since       0.3.2
 *
 * @package    Wp_Job_Scraper
 * @subpackage Wp_Job_Scraper/includes
 * @author     Wajih Tagourty <Wajih.tagourty@gmail.com>
 */
class Usajobs_Controller
{
    /**
     * Stores to custom fields of usajobs
     */
    public $sections = array();
    public $fields = array();
    public $custom_fields = array();

    public function __construct()
    {
        $this->custom_fields = array(
            "settings" => array(
                array(
                    'option_group' => 'wp-job-scraper-usajobs',
                    'option_name' => 'wp-job-scraper-usajobs',
                    //'callback' => array($this, 'checkbox_sanitize')
                )
            ),
            "sections" => array(),
            "fields" => array()
        );

        // id => title
        $this->sections = array(
            'auth' => 'Authentification',
            'jobmap' => 'Configure Job-type Mapping'
        );

        //section => array ( title => slug )
        $this->fields = array(
            'auth' => array(
                'Email' => 'email',
                'Key' => 'key'
            )
        );

        $this->register_sections($this->sections);
        $this->register_fields($this->fields);
    }


    public function input_field($args)
    {
        $option_name = $args['option_name'];
        $name = $args['label_for'];
        $classes = $args['class'];

        $option = get_option($option_name);
        $value = $option[$name];

        echo '<input type="text" name="' . $option_name . '[' . $name . ']" value="' . $value . ' class = "' . $classes . '">';
    }



    public function register_sections($sections)
    {
        foreach ($sections as $id => $title) {
            $arr = array(
                'id' => "wp-job-scraper-usajobs-$id",
                'title' => $title,
                'callback' => array($this, $id . '_intro'),
                'page' => 'wp-job-scraper-usajobs'
            );
            array_push($this->custom_fields['sections'], $arr);
        }
    }

    public function register_fields($fields)
    {
        foreach ($fields as $section => $field) {
            foreach ($field as $title => $slug) {
                $arr = array(
                    'id' => "usajobs-$slug",
                    'title' => $title,
                    'callback' => array($this, 'input_field'),
                    'page' => 'wp-job-scraper-usajobs',
                    'section' => "wp-job-scraper-usajobs-$section",
                    'args' => array(
                        'option_name' => 'wp-job-scraper-usajobs',
                        'label_for' => "usajobs-$slug",
                        'class' => "wjs-usajobs-$section"
                    )
                );
                array_push($this->custom_fields['fields'], $arr);
            }
        }
    }
}
