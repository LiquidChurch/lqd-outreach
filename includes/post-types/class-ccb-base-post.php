<?php
    /**
     * Liquid Outreach Ccb post base.
     *
     * @since   0.11.5
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb post base class.
     *
     * @since 0.11.5
     *
     * @see   https://github.com/WebDevStudios/CPT_Core
     */
    class LO_Ccb_Base_Post extends CPT_Core
    {
        /**
         * Bypass temp. cache
         *
         * @var boolean
         * @since  0.2.4
         */
        public $flush = false;
        /**
         * @var string
         * @since  0.2.4
         */
        public $meta_prefix = '';
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.0.1
         */
        protected $plugin = null;
        /**
         * @var bool
         * @since  0.2.4
         */
        protected $overrides_processed = false;
        /**
         * The identifier for this object
         *
         * @var string
         * @since  0.2.4
         */
        //        protected $id = '';
        /**
         * Default WP_Query args
         *
         * @var   array
         * @since 0.2.4
         */
        protected $query_args
            = array(
                'post_type'      => 'THIS(REPLACE)',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'no_found_rows'  => true,
            );
        
        /**
         * Initiate our hooks.
         *
         * @since  0.0.1
         */
        public function hooks()
        {
            add_action('cmb2_init', array($this, 'fields'));
        }
        
        /**
         * Provides access to protected class properties.
         *
         * @since  0.2.4
         *
         * @param  boolean $key Specific CPT parameter to return
         *
         * @return mixed        Specific CPT parameter or array of singular, plural and registered name
         */
        public function post_type($key = 'post_type')
        {
            if (!$this->overrides_processed) {
                $this->filter_values();
            }
            
            return parent::post_type($key);
        }
        
        /**
         * Filter for overriding class properties
         * which will be used as post arguments
         *
         * @since  0.2.4
         */
        public function filter_values()
        {
            if ($this->overrides_processed) {
                return;
            }
            
            $args = array(
                'singular'      => $this->singular,
                'plural'        => $this->plural,
                'post_type'     => $this->post_type,
                'arg_overrides' => $this->arg_overrides,
            );
            
            $filtered_args = apply_filters('lo_post_types_' . $this->id, $args, $this);
            
            if ($filtered_args !== $args) {
                foreach ($args as $arg => $val) {
                    if (isset($filtered_args[$arg])) {
                        $this->{$arg} = $filtered_args[$arg];
                    }
                }
            }
            
            $this->overrides_processed = true;
        }
        
        /**
         * Actually registers our CPT with the merged arguments
         *
         * @since  0.1.0
         */
        public function register_post_type()
        {
            // Register our CPT
            $args = register_post_type($this->post_type, $this->get_args());
            // If error, yell about it.
            if (is_wp_error($args)) {
                wp_die($args->get_error_message());
            }
            
            // Success. Set args to what WP returns
            $this->cpt_args = $args;
            
            // Add this post type to our custom_post_types array
            self::$custom_post_types[$this->post_type] = $this;
            
            $flush_base_rewrite = get_option('lo_ccb_flush_base_rewrite');
            
            if ($flush_base_rewrite == 'flush') {
                flush_rewrite_rules();
                update_option('lo_ccb_flush_base_rewrite', false);
            }
            
        }
        
        /**
         * Registers admin columns to display. Hooked in via CPT_Core.
         *
         * @since  0.0.1
         *
         * @param  array $columns Array of registered column names/labels.
         *
         * @return array          Modified array.
         */
        public function columns($columns)
        {
            $last = [];
            if ($this->id == 'lo-events') {
                $last = array_splice($columns, 3);
                $columns['lo-event-start-date'] = __('Event Start Date', 'liquid-outreach');
            }

            return array_merge($columns, $last);
        }
        
        /**
         * Handles admin column display. Hooked in via CPT_Core.
         *
         * @since  0.0.1
         *
         * @param array   $column  Column currently being rendered.
         * @param integer $post_id ID of post to display column for.
         */
        public function columns_display($column, $post_id)
        {
            if ($this->id == 'lo-events') {
                if ($column == 'lo-event-start-date') {
                    $date = get_post_meta($post_id, 'lo_ccb_events_start_date', true);
                    $date = !empty($date) ? date("Y-m-d H:i", $date) : '';
                    echo $date;
                }
            }
        }
        
        /**
         * Magic getter for our object. Allows getting but not setting.
         *
         * @param string $field
         *
         * @throws Exception Throws an exception if the field is invalid.
         * @return mixed
         * @since  0.2.4
         */
        public function __get($field)
        {
            switch ($field) {
                case 'id':
                case 'arg_overrides':
                case 'cpt_args':
                    return $this->{$field};
                default:
                    throw new Exception('Invalid ' . __CLASS__ . ' property: ' . $field);
            }
        }
        
        /**
         * Check common page option for default if no option is saved
         *
         * @param $key
         * @return bool
         * @since 0.20.0
         */
        public function global_details_page_setting($key)
        {
            if (!isset($_GET['post'])) {
                return null;
            }
            $post_config_val = get_post_meta($_GET['post'], $key, 1);
            if ($post_config_val == '') {
                $post_config_val = lo_get_option('additional-info', $key);
            }
            
            return $post_config_val;
        }
        
    }
