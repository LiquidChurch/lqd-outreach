<?php
    /**
     * Liquid Outreach Ccb Events.
     *
     * @since   0.1.0
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb Events post type class.
     *
     * @since 0.1.0
     *
     * @see   https://github.com/WebDevStudios/CPT_Core
     */
    class LO_Ccb_Events extends CPT_Core
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.1.0
         */
        protected $plugin = null;
        
        /**
         * Constructor.
         *
         * Register Custom Post Types.
         *
         * See documentation in CPT_Core, and in wp-includes/post.php.
         *
         * @since  0.1.0
         *
         * @param  Liquid_Outreach $plugin Main plugin object.
         */
        public function __construct($plugin)
        {
            $this->plugin = $plugin;
            $this->hooks();
            
            // Register this cpt.
            // First parameter should be an array with Singular, Plural, and Registered name.
            parent::__construct(
                array(
                    esc_html__('Project', 'liquid-outreach'),
                    esc_html__('Projects', 'liquid-outreach'),
                    'lo-ccb-events',
                ),
                array(
                    'supports'  => array(
                        'title',
                        'editor',
                        'excerpt',
                        'thumbnail',
                    ),
                    'menu_icon' => 'dashicons-admin-post',
                    // https://developer.wordpress.org/resource/dashicons/
                    'public'    => true,
                    'capabilities' => array(
                        'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
                    ),
                    'map_meta_cap' => true,
                )
            );
        }
        
        /**
         * Initiate our hooks.
         *
         * @since  0.1.0
         */
        public function hooks()
        {
            add_action('cmb2_init', array($this, 'fields'));
        }
        
        /**
         * Add custom fields to the CPT.
         *
         * @since  0.1.0
         */
        public function fields()
        {
            
            // Set our prefix.
            $prefix = 'lo_ccb_events_';
            
            // Define our metaboxes and fields.
            $cmb_additional = new_cmb2_box(array(
                'id'           => $prefix . 'additional_meta',
                'title'        => esc_html__('Additional Details', 'liquid-outreach'),
                'object_types' => array('lo-ccb-events'),
                'context'      => 'normal',
                'priority'     => 'high',
                'show_names'   => true, // Show field names on the left
                // 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
            
            //kid friendly meta
            $cmb_additional->add_field(array(
                'name'             => esc_html__('Kid Friendly', 'liquid-outreach'),
                'desc'             => esc_html__('', 'liquid-outreach'),
                'id'               => $prefix . 'event_kid_friendly',
                'type'             => 'select',
                'show_option_none' => true,
                'options'          => array(
                    'standard' => esc_html__('Yes', 'liquid-outreach'),
                    'custom'   => esc_html__('No', 'liquid-outreach'),
                ),
            ));
            
            //cost meta
            $cmb_additional->add_field(array(
                'name'            => __('Cost', 'liquid-outreach'),
                'desc'            => __('', 'liquid-outreach'),
                'id'              => $prefix . 'event_cost',
                'type'            => 'text',
                'attributes'      => array(
                    'type'    => 'number',
                    'pattern' => '\d*',
                ),
                'sanitization_cb' => 'absint',
                'escape_cb'       => 'absint',
            ));
            
            //openings meta
            $cmb_additional->add_field(array(
                'name'       => __('Openings', 'liquid-outreach'),
                'desc'       => __('', 'liquid-outreach'),
                'id'         => $prefix . 'event_openings',
                'type'       => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //start date meta
            $cmb_additional->add_field(array(
                'name'        => __('Start Date', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'event_start_date',
                'type'        => 'text_date',
                'date_format' => 'Y-m-d',
            ));
            
            //address meta
            $cmb_additional->add_field(array(
                'name'        => __('Address', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'event_address',
                'type'        => 'textarea_small',
                'date_format' => 'Y-m-d',
            ));
            
            //Register Button meta
            $cmb_additional->add_field(array(
                'name'        => __('Register Button', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'event_regsiter_url',
                'type'        => 'text',
                'date_format' => 'Y-m-d',
            ));
            
            //Project City meta
            $cmb_additional->add_field(array(
                'name'        => __('Project City', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'event_city',
                'type'        => 'text',
                'date_format' => 'Y-m-d',
            ));
            
            // Define our metaboxes and fields.
            $cmb_team_leader = new_cmb2_box(array(
                'id'           => $prefix . 'team_leader',
                'title'        => esc_html__('Team Leader', 'liquid-outreach'),
                'object_types' => array('lo-ccb-events'),
                'context'      => 'normal',
                'priority'     => 'high',
                'show_names'   => true, // Show field names on the left
                // 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
            
            //team leader meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('ID', 'cmb2'),
                'desc' => esc_html__('', 'cmb2'),
                'id'   => $prefix . 'event_team_lead_id',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //team leader fname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('First Name', 'cmb2'),
                'desc' => esc_html__('', 'cmb2'),
                'id'   => $prefix . 'event_team_lead_fname',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //team leader lname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Last Name', 'cmb2'),
                'desc' => esc_html__('', 'cmb2'),
                'id'   => $prefix . 'event_team_lead_lname',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //team leader email meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Email', 'cmb2'),
                'desc' => esc_html__('', 'cmb2'),
                'id'   => $prefix . 'event_team_lead_email',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //team leader phone meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Phone', 'cmb2'),
                'desc' => esc_html__('', 'cmb2'),
                'id'   => $prefix . 'event_team_lead_phone',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
        }
        
        /**
         * Registers admin columns to display. Hooked in via CPT_Core.
         *
         * @since  0.1.0
         *
         * @param  array $columns Array of registered column names/labels.
         * @return array          Modified array.
         */
        public function columns($columns)
        {
            $new_column = array();
            
            return array_merge($new_column, $columns);
        }
        
        /**
         * Handles admin column display. Hooked in via CPT_Core.
         *
         * @since  0.1.0
         *
         * @param array   $column  Column currently being rendered.
         * @param integer $post_id ID of post to display column for.
         */
        public function columns_display($column, $post_id)
        {
            switch ($column) {
            }
        }
    }
