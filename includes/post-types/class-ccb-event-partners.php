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
    class LO_Ccb_Event_Partners extends LO_Ccb_Base_Post
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.1.0
         */
        protected $plugin = null;
	
	    /**
	     * Bypass temp. cache
	     *
	     * @var boolean
	     * @since  0.2.7
	     */
	    public $flush = false;
	
	    /**
	     * @var bool
	     * @since  0.2.7
	     */
	    protected $overrides_processed = false;
	
	    /**
	     * The identifier for this object
	     *
	     * @var string
	     * @since  0.2.7
	     */
	    protected $id = 'lo-event-partners';
	
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
	     * @var string
	     * @since  0.2.7
	     */
	    public $meta_prefix = 'lo_ccb_event_partner_';
        
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
            $page_settings = get_option('liquid_outreach_ccb_events_page_settings');
            $slug_base = !empty($page_settings['lo_events_page_permalink_base']) ? $page_settings['lo_events_page_permalink_base'] : 'outreach';
            $partner_base = !empty($page_settings['lo_events_page_permalink_base_partners']) ? $page_settings['lo_events_page_permalink_base_partners'] : 'partners';
            $final_base = $slug_base . '/' . $partner_base;

            // Register this cpt.
            // First parameter should be an array with Singular, Plural, and Registered name.
            parent::__construct(
                array(
                    esc_html__('Outreach Partner', 'liquid-outreach'),
                    esc_html__('Outreach Partners', 'liquid-outreach'),
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
                        'edit_others_posts' => 'edit_others_outreach_partners',
                        'delete_others_posts' => 'delete_others_outreach_partners',
                        'delete_private_posts' => 'delete_private_outreach_partners',
                        'edit_private_posts' => 'edit_private_outreach_partners',
                        'read_private_posts' => 'read_private_outreach_partners',
                        'edit_published_posts' => 'edit_published_outreach_partners',
                        'publish_posts' => 'publish_outreach_partners',
                        'delete_published_posts' => 'delete_published_outreach_partners',
                        'edit_posts' => 'edit_outreach_partners',
                        'delete_posts' => 'delete_outreach_partners',
                        'edit_post' => 'edit_outreach_partner',
                        'read_post' => 'read_outreach_partner',
                        'delete_post' => 'delete_outreach_partner',
                        'read' => 'read_outreach_partner',
                    ),
                    'map_meta_cap' => true,
                    'show_in_menu' => 'edit.php?post_type=lo-events',
                    'rewrite' => array('slug' => $final_base),
                )
            );
    
            $this->plugin = $plugin;
            $this->hooks();
    
            add_action( 'plugins_loaded', array( $this, 'filter_values' ), 4 );
        }

        /**
         * Initiate our hooks.
         *
         * @since  0.1.0
         */
        public function hooks()
        {
            parent::hooks();
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
				    echo '<a href="'.get_edit_post_link($post->ID).'" target="_blank">'.$post->post_title.'</a><br/>';
			    }
		    }
	    }
    
        /**
         * Show  display settings for individual post
         * @since 0.20.0
         */
        public function cmb_page_display_settings()
        {
        
            $prefix = $this->meta_prefix;
            $option_prefix = 'lo_events_info_';
        
            // Define our metaboxes and fields.
            $cmb_page_settings = new_cmb2_box(array(
                'id'           => $prefix . 'display_settings',
                'title'        => esc_html__('Display Settings', 'liquid-outreach'),
                'object_types' => array('lo-event-partners'),
                'context'      => 'side',
                'priority'     => 'low',
                'show_names'   => true, // Show field names on the left
                'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
        
            $settings_arr = array(
                'location'    => [
                    'label' => 'Address',
                    'settings_key' => 'partner_address'
                ],
                'website'    => [
                    'label' => 'Website',
                    'settings_key' => 'partner_website'
                ],
                'team_leader'    => [
                    'label' => 'Team Leader',
                    'settings_key' => 'partner_team_leader'
                ],
                'phone'    => [
                    'label' => 'Phone',
                    'settings_key' => 'partner_phone'
                ],
                'email'    => [
                    'label' => 'Email',
                    'settings_key' => 'partner_email'
                ],
            );
        
            foreach ($settings_arr as $index => $item) {
                $cmb_page_settings->add_field( array(
                    'name' => $item['label'],
                    'desc' => '',
                    'id'   => $option_prefix . $item['settings_key'],
                    'type'    => 'radio_inline',
                    'options' => array(
                        '1' => __( 'Show', 'cmb2' ),
                        '0'   => __( 'Hide', 'cmb2' ),
                    ),
                    'default' => $this->global_details_page_setting($option_prefix . $item['settings_key'])
                ) );
            }
        }
        
        /**
         * Add custom fields to the CPT.
         *
         * @since  0.1.0
         */
        public function fields()
        {
            $this->cmb_page_display_settings();
            
            // Set our prefix.
            $prefix = $this->meta_prefix;
            
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

            //Event Image
            $cmb_additional->add_field(array(
                'name' => __('Image', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'image',
                'type' => 'file',
            ));
            
            //list_of_projects meta
            $cmb_additional->add_field(array(
                'name' => __('List of Related Events', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'list_of_projects',
                'type' => 'list_related_ccb_events',
            ));
            
            //group_id meta
//            $cmb_additional->add_field(array(
//                'name' => __('CCB Group ID', 'liquid-outreach'),
//                'desc' => __('', 'liquid-outreach'),
//                'id'   => $prefix . 'group_id',
//                'type' => 'text',
//                'attributes' => array(
//	                'readonly' => 'readonly',
//	                'disabled' => 'disabled',
//                ),
//            ));
        }
        
	    /**
	     * Retrieve lo-events-partners
	     *
	     * @since  0.2.7
	     *
	     * @return WP_Query|LO_Event_Partners_Post object
	     */
	    public function get_many($args)
	    {
		    $defaults = $this->query_args;
		    unset($defaults['posts_per_page']);
		    unset($defaults['no_found_rows']);
		    $args['augment_posts'] = true;
		    $args['orderby'] = 'title';
		    $args['order'] = 'ASC';
		    $args = apply_filters('lo_get_event_partners_args', wp_parse_args($args, $defaults));
		
		    $partners = new WP_Query($args);
		
		    if (
			    isset($args['augment_posts'])
			    && $args['augment_posts']
			    && $partners->have_posts()
			    // Don't augment for queries w/ greater than 100 posts, for perf. reasons.
			    && $partners->post_count < 100
		    ) {
			    foreach ($partners->posts as $key => $post) {
				    $partners->posts[$key] = new LO_Event_Partners_Post($post);
			    }
		    }
		    return $partners;
	    }

        /**
         * @param $term_slug
         * @param $args
         *
         * @return bool|WP_Query
         * @since 0.25.0
         */
	    public function get_similar($term_slug, $args)
	    {
            $term = get_term_by('slug', $term_slug, 'event-category');

            $posts_array = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'lo-events',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'event-category',
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        )
                    )
                )
            );

            $group_ids = [];
            foreach ($posts_array as $index => $item)
            {
                $group_ids[] = get_post_meta($item->ID, 'lo_ccb_events_group_id', true);
            }

            if(empty($group_ids)) {
                return false;
            }

            $meta_query = [];
            $meta_query['relation'] = 'OR';

            foreach ($group_ids as $index => $group_id)
            {
                $meta_query[] = array(
                    'key'     => 'lo_ccb_event_partner_group_id',
                    'value'   => $group_id,
                    'compare' => '='
                );

            }

            $defaults = $this->query_args;
		    $args['augment_posts'] = true;
		    $args['orderby'] = 'title';
		    $args['order'] = 'ASC';
		    $args['meta_query'] = $meta_query;
		    $args = apply_filters('lo_get_event_partners_similar_args', wp_parse_args($args, $defaults));

		    $partners = new WP_Query($args);

		    if (
			    isset($args['augment_posts'])
			    && $args['augment_posts']
			    && $partners->have_posts()
			    // Don't augment for queries w/ greater than 100 posts, for perf. reasons.
			    && $partners->post_count < 100
		    ) {
			    foreach ($partners->posts as $key => $post) {
				    $partners->posts[$key] = new LO_Event_Partners_Post($post);
			    }
		    }
		    return $partners;
	    }
    
        /**
         * Overriding get_args from parent
         *
         * @since  0.11.5
         * @return array
         */
        public function get_args()
        {
        
            if (!empty($this->cpt_args)) {
                return $this->cpt_args;
            }
        
            // Generate CPT labels
            $labels = array(
                'name' => $this->plural,
                'singular_name' => $this->singular,
                'add_new' => sprintf(__('Add New %s', 'cpt-core'), $this->singular),
                'add_new_item' => sprintf(__('Add New %s', 'cpt-core'), $this->singular),
                'edit_item' => sprintf(__('Edit %s', 'cpt-core'), $this->singular),
                'new_item' => sprintf(__('New %s', 'cpt-core'), $this->singular),
                'all_items' => sprintf(__('%s', 'cpt-core'), $this->plural),
                'view_item' => sprintf(__('View %s', 'cpt-core'), $this->singular),
                'search_items' => sprintf(__('Search %s', 'cpt-core'), $this->plural),
                'not_found' => sprintf(__('No %s', 'cpt-core'), $this->plural),
                'not_found_in_trash' => sprintf(__('No %s found in Trash', 'cpt-core'), $this->plural),
                'parent_item_colon' => isset($this->arg_overrides['hierarchical']) && $this->arg_overrides['hierarchical'] ? sprintf(__('Parent %s:', 'cpt-core'), $this->singular) : null,
                'menu_name' => $this->plural,
                'insert_into_item' => sprintf(__('Insert into %s', 'cpt-core'), strtolower($this->singular)),
                'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'cpt-core'), strtolower($this->singular)),
                'items_list' => sprintf(__('%s list', 'cpt-core'), $this->plural),
                'items_list_navigation' => sprintf(__('%s list navigation', 'cpt-core'), $this->plural),
                'filter_items_list' => sprintf(__('Filter %s list', 'cpt-core'), strtolower($this->plural))
            );
        
            // Set default CPT parameters
            $defaults = array(
                'labels' => array(),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'excerpt'),
            );
        
            $this->cpt_args = wp_parse_args($this->arg_overrides, $defaults);
            $this->cpt_args['labels'] = wp_parse_args($this->cpt_args['labels'], $labels);
        
            return $this->cpt_args;
        }
	
    }
