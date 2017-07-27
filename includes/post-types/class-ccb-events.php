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
    class LO_Ccb_Events extends LO_Ccb_Base_Post
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
        public $meta_prefix = 'lo_ccb_events_';
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
        protected $id = 'lo-events';
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
            $page_settings = get_option('liquid_outreach_ccb_events_page_settings');
            $slug_base
                = !empty($page_settings['lo_events_page_permalink_base']) ? $page_settings['lo_events_page_permalink_base'] : 'outreach';
            $event_base
                = !empty($page_settings['lo_events_page_permalink_base_events']) ? $page_settings['lo_events_page_permalink_base_events'] : 'events';
            $final_base = $slug_base . '/' . $event_base;
            
            // Register this cpt.
            // First parameter should be an array with Singular, Plural, and Registered name.
            parent::__construct(
                array(
                    esc_html__('Outreach', 'liquid-outreach'),
                    esc_html__('Outreach', 'liquid-outreach'),
                    'lo-events',
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
                        'create_posts'           => 'do_not_allow', // false < WP 4.5, credit @Ewout
                        'edit_others_posts'      => 'edit_others_outreach_events',
                        'delete_others_posts'    => 'delete_others_outreach_events',
                        'delete_private_posts'   => 'delete_private_outreach_events',
                        'edit_private_posts'     => 'edit_private_outreach_events',
                        'read_private_posts'     => 'read_private_outreach_events',
                        'edit_published_posts'   => 'edit_published_outreach_events',
                        'publish_posts'          => 'publish_outreach_events',
                        'delete_published_posts' => 'delete_published_outreach_events',
                        'edit_posts'             => 'edit_outreach_events',
                        'delete_posts'           => 'delete_outreach_events',
                        'edit_post'              => 'edit_outreach_event',
                        'read_post'              => 'read_outreach_event',
                        'delete_post'            => 'delete_outreach_event',
                        'read'                   => 'read_outreach_event',
                    ),
                    'map_meta_cap' => true,
                    'rewrite'      => array('slug' => $final_base),
                )
            );
            
            $this->query_args['post_type'] = $this->post_type();
            
            $this->plugin = $plugin;
            $this->hooks();
            
            add_action('plugins_loaded', array($this, 'filter_values'), 4);
        }
        
        /**
         * Overriding get_args from parent
         *
         * @since 0.10.1
         * @return array
         */
        public function get_args()
        {
            
            if (!empty($this->cpt_args)) {
                return $this->cpt_args;
            }
            
            // Generate CPT labels
            $labels = array(
                'name'                  => $this->plural,
                'singular_name'         => $this->singular,
                'add_new'               => sprintf(__('Add New %s', 'cpt-core'), $this->singular),
                'add_new_item'          => sprintf(__('Add New %s', 'cpt-core'), $this->singular),
                'edit_item'             => sprintf(__('Edit %s', 'cpt-core'), $this->singular),
                'new_item'              => sprintf(__('New %s', 'cpt-core'), $this->singular),
                'all_items'             => sprintf(__('%s Events', 'cpt-core'), $this->plural),
                'view_item'             => sprintf(__('View %s', 'cpt-core'), $this->singular),
                'search_items'          => sprintf(__('Search %s', 'cpt-core'), $this->plural),
                'not_found'             => sprintf(__('No %s', 'cpt-core'), $this->plural),
                'not_found_in_trash'    => sprintf(__('No %s found in Trash', 'cpt-core'),
                    $this->plural),
                'parent_item_colon'     => isset($this->arg_overrides['hierarchical']) &&
                                           $this->arg_overrides['hierarchical'] ? sprintf(__('Parent %s:',
                    'cpt-core'), $this->singular) : null,
                'menu_name'             => $this->plural,
                'insert_into_item'      => sprintf(__('Insert into %s', 'cpt-core'),
                    strtolower($this->singular)),
                'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'cpt-core'),
                    strtolower($this->singular)),
                'items_list'            => sprintf(__('%s list', 'cpt-core'), $this->plural),
                'items_list_navigation' => sprintf(__('%s list navigation', 'cpt-core'),
                    $this->plural),
                'filter_items_list'     => sprintf(__('Filter %s list', 'cpt-core'),
                    strtolower($this->plural))
            );
            
            // Set default CPT parameters
            $defaults = array(
                'labels'             => array(),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'has_archive'        => true,
                'supports'           => array('title', 'editor', 'excerpt'),
            );
            
            $this->cpt_args = wp_parse_args($this->arg_overrides, $defaults);
            $this->cpt_args['labels'] = wp_parse_args($this->cpt_args['labels'], $labels);
            
            return $this->cpt_args;
        }
        
        /**
         * Add custom fields to the CPT.
         *
         * @since  0.0.1
         */
        public function fields()
        {
            wp_enqueue_style('lc-plugin', Liquid_Outreach::$url . 'assets/css/lc-plugin.css');
    
            $this->cmb_page_display_settings();
            
            // Set our prefix.
            $prefix = $this->meta_prefix;
            
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
                    'no'  => esc_html__('No', 'liquid-outreach'),
                ),
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
                'name' => __('Openings', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'openings',
                'type' => 'text',
            ));
            
            //start date meta
            $cmb_additional->add_field(array(
                'name'        => __('Start Date', 'liquid-outreach'),
                'desc'        => __('', 'liquid-outreach'),
                'id'          => $prefix . 'start_date',
                'type'        => 'text_datetime_timestamp',
                'date_format' => 'Y-m-d',
            ));
            
            //address meta
            $cmb_additional->add_field(array(
                'name' => __('Address', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'address',
                'type' => 'textarea_small',
            ));
            
            //Register Button meta
            $cmb_additional->add_field(array(
                'name' => __('Register Button URL', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'register_url',
                'type' => 'text',
            ));
            
            //Event City meta
            $cmb_additional->add_field(array(
                'name' => __('Event City', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'city',
                'type' => 'text',
            ));

            //Event Image
            $cmb_additional->add_field(array(
                'name' => __('Event Image', 'liquid-outreach'),
                'desc' => __('', 'liquid-outreach'),
                'id'   => $prefix . 'image',
                'type' => 'file',
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
            
            //team leader fname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('First Name', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'team_lead_fname',
                'type' => 'text',
            ));
            
            //team leader lname meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Last Name', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'team_lead_lname',
                'type' => 'text',
            ));
            
            //team leader email meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Email', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'team_lead_email',
                'type' => 'text',
            ));
            
            //team leader phone meta
            $cmb_team_leader->add_field(array(
                'name' => esc_html__('Phone', 'liquid-outreach'),
                'desc' => esc_html__('', 'liquid-outreach'),
                'id'   => $prefix . 'team_lead_phone',
                'type' => 'text',
            ));
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
                'object_types' => array('lo-events'),
                'context'      => 'side',
                'priority'     => 'low',
                'show_names'   => true, // Show field names on the left
                 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
            ));
            
            $settings_arr = array(
                'start_date'    => [
                    'label' => 'Date Time',
                    'settings_key' => 'date_time'
                ],
                'cost'    => [
                    'label' => 'Cost',
                    'settings_key' => 'cost'
                ],
                'openings'    => [
                    'label' => 'Openings',
                    'settings_key' => 'openings'
                ],
                'categories'    => [
                    'label' => 'Categories',
                    'settings_key' => 'categories'
                ],
                'kid_friendly'    => [
                    'label' => 'Kid Friendly',
                    'settings_key' => 'kid_friendly'
                ],
                'team_leader_name'    => [
                    'label' => 'Leader Name',
                    'settings_key' => 'team_leader_name'
                ],
                'team_lead_email'    => [
                    'label' => 'Leader Email',
                    'settings_key' => 'team_leader_email'
                ],
                'team_lead_phone'    => [
                    'label' => 'Leader Phone',
                    'settings_key' => 'team_leader_phone'
                ],
                'address'    => [
                    'label' => 'Address',
                    'settings_key' => 'address'
                ],
                'partner_organization'    => [
                    'label' => 'Partner organization',
                    'settings_key' => 'partner_organization'
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
            $args['meta_key'] = $this->meta_prefix . 'start_date';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            $args['meta_query'] = [
                'key'     => $this->meta_prefix . 'start_date',
                'value'   => time(),
                'compare' => '>='
            ];
            $args = apply_filters('lo_get_events_args',
                wp_parse_args($args, $defaults));
            
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
        
        /**
         * @param string $status
         *
         * @since 0.2.7
         * @return array
         */
        public function get_all_city_list($status = 'publish')
        {
            
            global $wpdb;
            
            $r = $wpdb->get_col($wpdb->prepare("
		        SELECT pm.meta_value FROM {$wpdb->postmeta} as pm
		        LEFT JOIN {$wpdb->posts} as p ON p.ID = pm.post_id
		        WHERE pm.meta_key = '%s'
		        AND p.post_status = '%s'
		        AND p.post_type = '%s'
		    ", $this->meta_prefix . 'city', $status, $this->post_type()));
            
            $filter_r = array_filter(array_unique($r));
            sort($filter_r);
            
            return array_filter(array_unique($filter_r));
        }
        
        /**
         * @param $args
         *
         * @return WP_Query
         * @since 0.2.9
         */
        public function get_search_result($args)
        {
            
            $defaults = $this->query_args;
            unset($defaults['posts_per_page']);
            unset($defaults['no_found_rows']);
            
            if (isset($args['bypass_uri_query']) && isset($args['event_org'])) {
                $search_query = [
                    'org' => $args['event_org'],
                ];
                unset($args['bypass_uri_query'], $args['event_org']);
            } else {
                $search_query = [
                    'key' => isset($_GET['lo-event-s']) ? $_GET['lo-event-s'] : '',
                    'cat' => isset($_GET['lo-event-cat']) ? $_GET['lo-event-cat'] : '',
                    'org' => isset($_GET['lo-event-org']) ? $_GET['lo-event-org'] : '',
                    'day' => isset($_GET['lo-event-day']) ? $_GET['lo-event-day'] : '',
                    'loc' => isset($_GET['lo-event-loc']) ? $_GET['lo-event-loc'] : '',
                ];
            }
            
            if (!empty($search_query['key'])) {
                
                global $wpdb;
                $args['post__in']
                    = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '" .
                                     $search_query['key'] . "%' ");
            }
            
            $args['augment_posts'] = true;
            
            if (!empty($search_query['cat'])) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => liquid_outreach()->lo_ccb_event_categories->taxonomy(),
                        'field'    => 'term_id',
                        'terms'    => (int)$search_query['cat']
                    ]
                ];
            }
            if (!empty($search_query['org'])) {
                $args['meta_query'][] = [
                    'key'     => $this->meta_prefix . 'group_id',
                    'value'   => $search_query['org'],
                    'compare' => '='
                ];
            }
            if (!empty($search_query['day'])) {
                $args['meta_query'][] = [
                    'key'     => $this->meta_prefix . 'weekday_name',
                    'value'   => $search_query['day'],
                    'compare' => '='
                ];
            }
            if (!empty($search_query['loc'])) {
                $args['meta_query'][] = [
                    'key'     => $this->meta_prefix . 'city',
                    'value'   => $search_query['loc'],
                    'compare' => '='
                ];
            }
            
            $args['meta_key'] = $this->meta_prefix . 'start_date';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            if (!empty($args['meta_query'])) {
                $args['meta_query']['relation'] = 'AND';
            }
            $args['meta_query'][] = [
                'key'     => $this->meta_prefix . 'start_date',
                'value'   => time(),
                'compare' => '>='
            ];
            $args = apply_filters('lo_get_events_args',
                wp_parse_args($args, $defaults));
            
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
