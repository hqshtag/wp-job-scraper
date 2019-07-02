<?php

/**
 * @package WP Job Scraper
 * @since       0.2.1
 */



class Settings_Api
{

    /**
     * Admin pages.
     *
     * @since     0.2.1
     * @access   public
     * @var      array    $pages    Stores admin pages.
     */
    public $admin_pages = array();

    /**
     * Admin subpage.
     * @since 0.2.5
     *@var array $admin_subpages Stores admin subpages.
     */
    public $admin_subpages = array();

    /**
     * The loader class.
     * Set up in the cunstructor to avoid requiring 
     * the class in two different locations.
     *
     * @since     0.2.3
     * @access   public
     * @var      Wp_Job_Scraper_loader.
     */
    public $loader;

    /**
     * Settings.
     * @since 0.2.8
     *@var array $settings
     */
    public $settings = array();
    /**
     * Sections.
     * @since 0.2.8
     *@var array $sections
     */
    public $sections = array();
    /**
     * Fields.
     * @since 0.2.8
     *@var array $fields
     */
    public $fields = array();


    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    /**
     * Register the admin pages.
     * @since 0.2.1
     */
    public function register()
    {
        if (!empty($this->admin_pages)) {
            $this->loader->add_action('admin_menu', $this, 'add_admin_menu');
        }
        if (!empty($this->settings)) {
            $this->loader->add_action('admin_init', $this, 'register_custom_fields');
        }
    }

    /**
     * Add pages to local variable admin pages
     * @since 0.2.1
     */
    public function add_pages(array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }


    public function add_subpages(array $subpage)
    {
        $this->admin_subpages = array_merge($this->admin_subpages, $subpage);
        return $this;
    }

    public function with_subpage(string $title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }
        $admin_page = $this->admin_pages[0];
        $subpage = array(
            array(
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            )
        );
        $this->admin_subpages = $subpage;

        return $this;
    }

    /**
     * @since 0.2.1
     */
    public function add_admin_menu()
    {
        foreach ($this->admin_pages as $page) {
            add_menu_page(
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback'],
                $page['icon_url'],
                $page['position']
            );
        }
        foreach ($this->admin_subpages as $page) {
            add_submenu_page(
                $page['parent_slug'],
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback']
            );
        }
    }


    public function set_custom_fields($custom_fields)
    {
        $this->set_settings($custom_fields["settings"])->set_sections($custom_fields["sections"])->set_fields($custom_fields["fields"]);
        return $this;
    }

    public function set_settings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }
    public function set_sections(array $sections)
    {
        $this->sections = $sections;
        return $this;
    }
    public function set_fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function register_custom_fields()
    {
        //register setting
        foreach ($this->settings as $setting) {
            register_setting(
                $setting["option_group"],
                $setting["option_name"],
                (isset($setting["callback"]) ?  $setting["callback"] : '')
            );
        }

        //add settings section
        foreach ($this->sections as $section) {
            add_settings_section(
                $section['id'],
                $section['title'],
                (isset($section["callback"]) ?  $section["callback"] : ''),
                $section['page']
            );
        }

        //add settings field
        foreach ($this->fields as $field) {
            add_settings_field(
                $field['id'],
                $field['title'],
                (isset($field["callback"]) ?  $field["callback"] : ''),
                $field["page"],
                $field["section"],
                (isset($field["args"]) ?
                    $field["args"] : '')
            );
        }
    }
}
