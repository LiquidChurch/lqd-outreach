<?php
    /**
     * Liquid Outreach Ccb_event_partners.
     *
     * @since   0.1.0
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb_event_partners post type class.
     *
     * @since 0.1.0
     *
     * @see   https://github.com/WebDevStudios/CPT_Core
     */
    class LO_Ccb_Event_Partners extends CPT_Core
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
                    esc_html__('Event Partner', 'liquid-outreach'),
                    esc_html__('Event Partners', 'liquid-outreach'),
                    'lo-event-partners',
                ),
                array(
                    'supports'     => array(
                        'title',
                        'editor',
                        'excerpt',
                    ),
                    'menu_icon'    => 'dashicons-admin-post',
                    // https://developer.wordpress.org/resource/dashicons/
                    'public'       => true,
                    'capabilities' => array(
                        'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
                    ),
                    'map_meta_cap' => true,
                    'show_in_menu' => 'edit.php?post_type=lo-events'
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
	        add_action( 'cmb2_render_list_related_ccb_events', array($this, 'cmb2_render_callback_for_list_related_ccb_events'), 10, 5 );
        }
	
	    /**
	     * Custom field for cmb2 for listing related ccb events
	     *
	     * @since 0.1.8
	     * @param $field
	     * @param $escaped_value
	     * @param $object_id
	     * @param $object_type
	     * @param $field_type_object
	     */
	    function cmb2_render_callback_for_list_related_ccb_events( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		    $event_query = new WP_Query( "post_type=lo-events&meta_key=lo_ccb_events_group_id&meta_value=" .
		                                   get_post_meta($_GET['post'], 'lo_ccb_event_partner_group_id', true ));
		    if ( $event_query->have_posts() ) {
		    	while($event_query->have_posts()) {
				    $event_query->the_post();
				    global $post;
				    echo '<a href="'.get_edit_post_link($post->ID).'" target="_blank">'.$post->post_title.'</a>';
			    }
		    }
	    }
        
        /**
         * Add custom fields to the CPT.
         *
         * @since  0.1.0
         */
        public function fields()
        {
            
            // Set our prefix.
            $prefix = 'lo_ccb_event_partner_';
            
            // Define our metaboxes and fields.
            $cmb_additional = new_cmb2_box(array(
                'id'           => $prefix . 'additional_meta',
                'title'        => esc_html__('Additional Details', 'liquid-outreach'),
                'object_types' => array('lo-event-partners'),
                'context'      => 'normal',
                'priority'     => 'high',
                'show_names'   => true, // Show field names on the left
                // 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
            
            //location meta
            $cmb_additional->add_field(array(
                'name'       => __('Location', 'liquid-outreach'),
                'desc'       => __('', 'liquid-outreach'),
                'id'         => $prefix . 'location',
                'type'       => 'text',
                'repeatable' => true,
            ));
            
            //website meta
            $cmb_additional->add_field(array(
                'name' => __('Website', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'website',
                'type' => 'text_url',
            ));
            
            //team_leader meta
            $cmb_additional->add_field(array(
                'name' => __('Team Leader', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'team_leader',
                'type' => 'text',
            ));
            
            //phone meta
            $cmb_additional->add_field(array(
                'name' => __('Phone', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'phone',
                'type' => 'text',
            ));
            
            //email meta
            $cmb_additional->add_field(array(
                'name' => __('Email', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'email',
                'type' => 'text_email',
            ));
            
            //list_of_projects meta
            $cmb_additional->add_field(array(
                'name' => __('List of Related Events', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'list_of_projects',
                'type' => 'list_related_ccb_events',
            ));
            
            //group_id meta
            $cmb_additional->add_field(array(
                'name' => __('CCB Group ID', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'group_id',
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
