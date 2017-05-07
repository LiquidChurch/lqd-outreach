<?php
    /**
     * Liquid Outreach Ccb Events.
     *
     * @since   0.0.1
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb Events post type class.
     *
     * @since 0.0.1
     *
     * @see   https://github.com/WebDevStudios/CPT_Core
     */
    class LO_Ccb_Events extends CPT_Core
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.0.1
         */
        protected $plugin = null;
	
	    /**
	     * Bypass temp. cache
	     *
	     * @var boolean
	     * @since  0.2.4
	     */
	    public $flush = false;
	
	    /**
	     * Default WP_Query args
	     *
	     * @var   array
	     * @since 0.2.4
	     */
	    protected $query_args = array(
		    'post_type' => 'THIS(REPLACE)',
		    'post_status' => 'publish',
		    'posts_per_page' => 1,
		    'no_found_rows' => true,
	    );
        
        /**
         * Constructor.
         *
         * Register Custom Post Types.
         *
         * See documentation in CPT_Core, and in wp-includes/post.php.
         *
         * @since  0.0.1
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
                    esc_html__('Event', 'liquid-outreach'),
                    esc_html__('Events', 'liquid-outreach'),
                    'lo-events',
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
         * @since  0.0.1
         */
        public function hooks()
        {
            add_action('cmb2_init', array($this, 'fields'));
        }
        
        /**
         * Add custom fields to the CPT.
         *
         * @since  0.0.1
         */
        public function fields()
        {
            
            // Set our prefix.
            $prefix = 'lo_ccb_events_';
            
            // Define our metaboxes and fields.
            $cmb_additional = new_cmb2_box(array(
                'id'           => $prefix . 'additional_meta',
                'title'        => esc_html__('Additional Details', 'liquid-outreach'),
                'object_types' => array('lo-events'),
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
                'id'               => $prefix . 'kid_friendly',
                'type'             => 'select',
                'show_option_none' => true,
                'options'          => array(
                    'yes' => esc_html__('Yes', 'liquid-outreach'),
                    'no'   => esc_html__('No', 'liquid-outreach'),
                ),
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //cost meta
            $cmb_additional->add_field(array(
                'name'            => __('Cost', 'liquid-outreach'),
                'desc'            => __('', 'liquid-outreach'),
                'id'              => $prefix . 'cost',
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
                'id'         => $prefix . 'openings',
                'type'       => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //start date meta
            $cmb_additional->add_field(array(
                'name'        => __('Start Date', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'start_date',
                'type'        => 'text_datetime_timestamp',
                'date_format' => 'Y-m-d',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //address meta
            $cmb_additional->add_field(array(
                'name'        => __('Address', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'address',
                'type'        => 'textarea_small',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //Register Button meta
            $cmb_additional->add_field(array(
                'name'        => __('Register Button', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'regsiter_url',
                'type'        => 'text',
            ));
            
            //Event City meta
            $cmb_additional->add_field(array(
                'name'        => __('Event City', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'city',
                'type'        => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //Event ccb id meta
            $cmb_additional->add_field(array(
                'name'        => __('CCB Event ID', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'ccb_event_id',
                'type'        => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //Event group id meta
            $cmb_additional->add_field(array(
                'name'        => __('CCB Group ID', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'group_id',
                'type'        => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            // Define our metaboxes and fields.
            $cmb_team_leader = new_cmb2_box(array(
                'id'           => $prefix . 'leader',
                'title'        => esc_html__('Team Leader', 'liquid-outreach'),
                'object_types' => array('lo-events'),
                'context'      => 'normal',
                'priority'     => 'high',
                'show_names'   => true, // Show field names on the left
                // 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
            
            //team leader meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('ID', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'team_lead_id',
                'type' => 'text',
                'attributes' => array(
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ),
            ));
            
            //team leader fname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('First Name', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'event_team_lead_fname',
                'type' => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //team leader lname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Last Name', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'event_team_lead_lname',
                'type' => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //team leader email meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Email', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'event_team_lead_email',
                'type' => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
            
            //team leader phone meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Phone', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'event_team_lead_phone',
                'type' => 'text',
//                'attributes' => array(
//                    'readonly' => 'readonly',
//                    'disabled' => 'disabled',
//                ),
            ));
        }
        
        /**
         * Registers admin columns to display. Hooked in via CPT_Core.
         *
         * @since  0.0.1
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
         * @since  0.0.1
         *
         * @param array   $column  Column currently being rendered.
         * @param integer $post_id ID of post to display column for.
         */
        public function columns_display($column, $post_id)
        {
            switch ($column) {
            }
        }
	
	    /**
	     * Retrieve lo-events.
	     *
	     * @since  0.2.4
	     *
	     * @return WP_Query|LO_Events_Post object
	     */
	    public function get_many($args)
	    {
		    $defaults = $this->query_args;
		    unset($defaults['posts_per_page']);
		    unset($defaults['no_found_rows']);
		    $args['augment_posts'] = true;
		
		    $args = apply_filters('lo_get_events_args', wp_parse_args($args, $defaults));
		    $events = new WP_Query($args);
		
		    if (
			    isset($args['augment_posts'])
			    && $args['augment_posts']
			    && $events->have_posts()
			    // Don't augment for queries w/ greater than 100 posts, for perf. reasons.
			    && $events->post_count < 100
		    ) {
			    foreach ($events->posts as $key => $post) {
				    $events->posts[$key] = new LO_Events_Post($post);
			    }
		    }
		    return $events;
	    }
    }
