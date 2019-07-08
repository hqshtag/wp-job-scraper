<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/wjs-controller.php';
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
class Usajobs_Controller extends Controller
{
    /**
     * 
     */
    private $email;
    private $key;
    private $timer;



    /**
     * stores this comonent's actions;
     */
    public $actions = array();


    /**
     * Stores to custom fields of usajobs
     */
    public $sections = array();
    public $fields = array();
    public $custom_fields = array();

    public $controller_name;


    public function __construct($controller)
    {
        $this->controller_name = $controller;
        $options = get_option('wp-job-scraper-usajobs');
        if ($options['email'] && $options['key']) {
            $this->email = $options['email'];
            $this->key = $options['key'];
        }

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
            ),
            'jobmap' => array(
                'Full-Time' => 'full-time',
                'Part-Time' => 'part-time',
                'Shift Work' => 'shift-work',
                'Intermittent' => 'intermittent',
                'Job Share' => 'job-share',
                'Multiple Schedules' => 'multiple-schedules'
            )
        );

        $this->register_sections($this->sections);
        $this->register_fields($this->fields);
        $this->actions = array(
            'admin_menu' => 'initiate',
            // 'admin_init' => 'shouldUpdate',
            'admin_init' => 'get_available_job_types',
            'load_core_script' => 'load_script',
            'wp_ajax_reset_timer' => 'reset_timer',

        );

        //add_action('load_core_script', array($this, 'load_script'));
    }
    public function initiate()
    {
        if (get_option("wp-job-scraper-usajobs")) {
            $required = $this->fields['auth'];
            if ($this->isSetUp('usajobs', $required)) {
                $options = get_option("wp-job-scraper-usajobs");
                if (array_key_exists("timer", $options)) {
                    $this->timer = $options['timer'];
                } else {
                    $this->timer =  time() - 86402;
                    // $options = array_merge($options, array("timer" => $this->timer));
                    //update_option("wp-job-scraper-usajobs", $options);
                }
                $data = array(
                    'userAuth' => $this->getUserC(),
                    'settings' => array(
                        'timer' => $this->timer,
                        'nonce' => wp_create_nonce('wp_rest'),
                        'url' => site_url('/wp-json/wp/v2/job-listings'),
                        'typeMap' => $this->get_job_type_map(),
                    ),
                    'ajax' => array(
                        'url'      => admin_url('admin-ajax.php'),
                        'nonce'    => wp_create_nonce('ajax_nonce'),
                    )
                );
                do_action('load_core_script', $data);
            }
        }
    }


    public function reset_timer()
    {
        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            wp_send_json_error(array('message' => 'Nonce is invalid.'));
        } else {
            $options = get_option("wp-job-scraper-usajobs");
            $options = array_merge($options, array("timer" => time()));
            update_option("wp-job-scraper-usajobs", $options);
            wp_send_json_success(array('message' => 'Timer reset.'));
        }
    }




    //field classbacks
    public function input_field($args)
    {
        $option_name = $args['option_name'];
        $name = $args['label_for'];
        $classes = $args['class'];

        $option = get_option($option_name);
        if ($option) {
            $value = array_key_exists($name, $option) ? $option[$name] : '';
        } else {
            $value = '';
        }

        if ($args['type'] == 'input') {

            echo '<input type="text" name="' . $option_name . '[' . $name . ']" value="' . $value . '" class = "' . $classes . '">';
        } else {
            echo '<div class="wjs-select">';
            echo '<select name="' . $option_name . '[' . $name . ']">';
            echo '<option value="" selected>--None--</option>';
            foreach ($this->jobTypes as $type) {
                $selected = $value == $type["id"] ? 'selected' : '';
                echo '<option value ="' . $type["id"] . '" ' . $selected . '>' . $type["name"] . '</option>';
            }
            echo '</select> </div>';
        }
    }

    //section callbacks
    public function auth_intro()
    {
        echo '<p> In order to use the API you will first need to obtain an API Key. To request an API Key, please go the the <a href="https://developer.usajobs.gov/APIRequest/Index" >API Request</a> page and fill out an application. </p>';
    }
    public function jobmap_intro()
    {
        echo '<p>In order to learn more about the Work schedules click <a href="https://www.usajobs.gov/Help/working-in-government/pay-and-leave/work-schedules/">here</a></p>';
    }

    public function load_script($data)
    {
        wp_enqueue_script('wp-job-scraper-usajobs', plugin_dir_url(__FILE__) . 'scripts/usajobs-fpa.js', array('jquery'), WP_JOB_SCRAPER_VERSION, false);
        wp_localize_script(
            'wp-job-scraper-usajobs',
            'secretData',
            $data
        );
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
                    'id' => "$slug",
                    'title' => $title,
                    'callback' => array($this, 'input_field'),
                    'page' => 'wp-job-scraper-usajobs',
                    'section' => "wp-job-scraper-usajobs-$section",
                    'args' => array(
                        'option_name' => 'wp-job-scraper-usajobs',
                        'label_for' => "$slug",
                        'class' => "wjs-usajobs-$section",
                        'type' => $section == 'jobmap' ? 'select' : 'input'
                    )
                );
                array_push($this->custom_fields['fields'], $arr);
            }
        }
    }
    public function getUserC()
    {
        return array(
            'email' => $this->email,
            'key' => $this->key
        );
    }

    public function get_job_type_map()
    {
        $res = array(
            '1' => '',
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => '',
            '6' => ''
        );
        $options = get_option('wp-job-scraper-usajobs');
        $jobtypes = $this->fields['jobmap'];
        $index = 1;
        foreach ($jobtypes as $key => $slug) {
            $res[$index] = $options[$slug];
            $index++;
        }
        return $res;
    }
}
