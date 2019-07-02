<?php

/**
 * @package WP Job Scraper
 * @since       0.2.1
 */



class Settings_Api
{



    public $admin_pages = array();

    public $loader;

    public function __construct($loader)
    {
        $this->loader = $loader;
    }
    public function register()
    {
        if (!empty($this->admin_pages)) {
            $this->loader->add_action('admin_menu', array($this, 'add_admin_menu'));
        }
    }
    public function add_pages(array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }
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
    }
}
