<?php
    
    /**
     * Class Liquid Outreach Abstract
     *
     * @package Liquid_Outreach
     */
    abstract class Lo_Abstract
    {
        /**
         * Parent plugin class
         *
         * @var   Liquid_Outreach   $plugin
         * @since 0.0.6
         */
        protected $plugin = null;
        
        /**
         * Detect AJAX Calls
         *
         * @since  0.0.6
         * @var bool|null   $ajax_call
         */
        protected $ajax_call = null;
        
        /**
         * Constructor
         *
         * @since  0.0.6
         * @param  object $plugin Main plugin object.
         * @return void
         */
        public function __construct($plugin)
        {
            $this->plugin = $plugin;
            $this->ajax_call = defined('DOING_AJAX') && DOING_AJAX;
            
            $this->hooks();
        }
    
        /**
         * Abstract method will be implemented in all child classes
         *
         * @since  0.0.6
         * @return mixed
         */
        public abstract function hooks();
    }