<?php
    /**
     * Liquid Outreach base function.
     *
     * @since   0.11.5
     * @package Liquid_Outreach
     */
    
    /**
     * Liquid Outreach base function class.
     *
     * @since 0.11.5
     *
     */
    class LO_Ccb_Base_Function
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.11.5
         */
        protected $plugin;
    
        /**
         * LO_Ccb_Base_Function constructor.
         *
         * @param $plugin
         * @since  0.11.5
         */
        public function __construct($plugin) {
            
            $this->plugin = $plugin;
    
            $this->lo_ccb_post_action_handler = new LO_Ccb_Post_Action_Handler($this->plugin);
        }
        
        public static function check_details_display_enabled($postID, $key) {
            $show = get_post_meta($postID, $key, true);
            if($show == '') {
                
                $settings = lo_get_option('additional-info', $key);
                return !empty($settings) ? '1' : '0';
                
            } else {
                return $show;
            }
        }
    }