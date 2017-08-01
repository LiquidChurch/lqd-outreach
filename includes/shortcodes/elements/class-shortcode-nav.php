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
         * @var   string
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
         * @return array
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

            return $fields;
        }
    }


    class LO_Shortcodes_Nav_Element_Run extends LO_Shortcodes_Run_Base
    {

        /**
         * The Shortcode Tag
         *
         * @var string
         * @since 0.7.0
         */
        public $shortcode = 'lo_nav_element';

        /**
         * Default attributes applied to the shortcode.
         *
         * @var array
         * @since 0.7.0
         */
        public $atts_defaults
            = array(
                'force_cat_slug' => NULL
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

            $content_arr['cities']   = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();

            $content_arr['force_cat_page'] = $this->force_cat_page;

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