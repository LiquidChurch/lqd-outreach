<?php
    /**
     * Liquid Outreach plugin function.
     *
     * @since   0.27.0
     * @package Liquid_Outreach
     */

    /**
     * Liquid Outreach plugin function class.
     *
     * @since 0.27.0
     *
     */
    class LO_Ccb_Plugin_Function
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.27.0
         */
        protected $plugin;

        /**
         * LO_Ccb_Plugin_Function constructor.
         *
         * @param $plugin
         *
         * @since  0.27.0
         */
        public function __construct($plugin)
        {

            $this->plugin = $plugin;
            add_action('after_setup_theme', array($this, 'register_ccb_plugin_menu'));

        }

        function register_ccb_plugin_menu()
        {

            register_nav_menu('primary', __('Primary Menu', 'theme-slug'));
        }

    }