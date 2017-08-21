<?php

/**
 * Liquid Outreach Nav Element Shortcode
 *
 * @since   0.7.0
 * @package Liquid_Outreach
 */
class LO_Shortcodes_Nav_Element extends LO_Shortcodes_Base
{

    /**
     * Constructor
     *
     * @since  0.7.0
     *
     * @param  object $plugin Main plugin object.
     *
     */
    public function __construct($plugin)
    {
        $this->run   = new LO_Shortcodes_Nav_Element_Run($plugin->lo_ccb_events,
            $plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories);
        $this->admin = new LO_Shortcodes_Nav_Element_Admin($this->run);

        parent::hooks();
    }

}


class LO_Shortcodes_Nav_Element_Admin extends LO_Shortcodes_Admin_Base
{

    /**
     * Shortcode prefix for field ids.
     *
     * @var   string $prefix
     * @since 0.7.0
     */
    protected $prefix = 'lo_nav_element';

    /**
     * Sets up the button
     *
     * @since 0.7.0
     * @return array
     */
    function js_button_data()
    {
        return array(
            'qt_button_text' => __('LO Nav Element', 'liquid-outreach'),
            'button_tooltip' => __('Nav Element', 'liquid-outreach'),
            'icon'           => 'dashicons-media-interactive',
            // 'mceView'        => true, // The future
        );
    }

    /**
     * Adds fields to the button modal using CMB2
     *
     * @since 0.7.0
     *
     * @param $fields
     * @param $button_data
     *
     * @return array    $fields
     */
    function fields($fields, $button_data)
    {
        $fields[] = array(
            'name'           => 'Select Event Category',
            'desc'           => 'Select event category',
            'id'             => 'force_cat_slug',
            'taxonomy'       => 'event-category', //Enter Taxonomy Slug
            'type'           => 'taxonomy_select',
            'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
        );

        $fields[] = array(
            'name' => 'Select menu options below',
            'desc' => '',
            'type' => 'title',
            'id'   => 'menu_option_title'
        );

        $fields[] = array(
            'name'    => 'Index',
            'desc'    => '',
            'id'      => 'menu_option_index',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'Search',
            'desc'    => '',
            'id'      => 'menu_option_search',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'Categories',
            'desc'    => '',
            'id'      => 'menu_option_categories',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'City',
            'desc'    => '',
            'id'      => 'menu_option_city',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'Days',
            'desc'    => '',
            'id'      => 'menu_option_days',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'Partners',
            'desc'    => '',
            'id'      => 'menu_option_partners',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        $fields[] = array(
            'name'    => 'Campus',
            'desc'    => '',
            'id'      => 'menu_option_campus',
            'type'    => 'checkbox',
            'default' => TRUE,
        );

        return $fields;
    }
}


class LO_Shortcodes_Nav_Element_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string $shortcode
     * @since 0.7.0
     */
    public $shortcode = 'lo_nav_element';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array $atts_defaults
     * @since 0.7.0
     */
    public $atts_defaults
        = array(
            'force_cat_slug'         => NULL,
            'menu_option_index'      => FALSE,
            'menu_option_search'     => FALSE,
            'menu_option_categories' => FALSE,
            'menu_option_city'       => FALSE,
            'menu_option_days'       => FALSE,
            'menu_option_partners'   => FALSE,
            'menu_option_campus'   => FALSE,
        );

    /**
     * Shortcode Output
     *
     * @since 0.7.0
     */
    public function shortcode()
    {
        parent::shortcode();

        if ( ! wp_script_is('jquery-ui-sortable', $list = 'enqueued'))
        {
            wp_enqueue_script('jquery-ui-sortable');
        }

        if ( ! wp_script_is('lo-vandertable', $list = 'enqueued'))
        {
            wp_enqueue_script('lo-vandertable',
                Liquid_Outreach::$url . '/assets/js/vandertable.js');
        }

        if ( ! wp_script_is('lo-index', $list = 'enqueued'))
        {
            wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');
        }

        $content_arr = [];

        $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();

        $content_arr['force_cat_page'] = $this->force_cat_page;
        $content_arr['menu_option_index'] = $this->att('menu_option_index');
        $content_arr['menu_option_search'] = $this->att('menu_option_search');
        $content_arr['menu_option_categories'] = $this->att('menu_option_categories');
        $content_arr['menu_option_city'] = $this->att('menu_option_city');
        $content_arr['menu_option_days'] = $this->att('menu_option_days');
        $content_arr['menu_option_partners'] = $this->att('menu_option_partners');
        $content_arr['menu_option_campus'] = $this->att('menu_option_campus');

        $content_arr['page_settings'] = $this->page_settings;

        $content_arr = array_merge($content_arr, $this->get_category_list());

        $content_arr = array_merge($content_arr, $this->get_base_pages());

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= LO_Template_Loader::output_template('nav', $content_arr);

        return $content;
    }

}