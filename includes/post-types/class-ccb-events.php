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
    public $flush = FALSE;

    /**
     * @var string
     * @since  0.2.4
     */
    public $meta_prefix = 'lo_ccb_events_';

    /**
     * @var string
     * @since  0.2.5
     */
    public $groups_db_prefix = 'lo_ccb_groups_api_data';

    /**
     * Parent plugin class.
     *
     * @var Liquid_Outreach
     * @since  0.0.1
     */
    protected $plugin = NULL;

    /**
     * @var bool
     * @since  0.2.4
     */
    protected $overrides_processed = FALSE;

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
            'no_found_rows'  => TRUE,
        );

    /**
     * Constructor.
     *
     * Register Custom Post Types.
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
                       = ! empty($page_settings['lo_events_page_permalink_base']) ? $page_settings['lo_events_page_permalink_base'] : 'outreach';
        $event_base
                       = ! empty($page_settings['lo_events_page_permalink_base_events']) ? $page_settings['lo_events_page_permalink_base_events'] : 'events';
        $final_base    = $slug_base . '/' . $event_base;

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
                'public'       => TRUE,
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
                'map_meta_cap' => TRUE,
                'rewrite'      => array('slug' => $final_base),
            )
        );

        $this->query_args['post_type'] = $this->post_type();

        $this->plugin = $plugin;
        $this->hooks();


        /*edit 23. Aug */
        add_filter('manage_lo-events_posts_columns', array($this, 'edit_lo_events_columns'));
        add_action('manage_lo-events_posts_custom_column', array($this, 'lo_events_column'), 10, 2);
        add_filter('manage_edit-lo-events_sortable_columns', array($this, 'lo_events_sortable_columns'));
        add_action('pre_get_posts', array($this, 'lo_events_orderby'));
        /*end*/

        add_action('plugins_loaded', array($this, 'filter_values'), 4);
    }

    /**
     * Adding extra columns to the events list
     *
     * @since 0.27.0
     */
    public function edit_lo_events_columns($event_list_columns)
    {
        $date = $event_list_columns['date'];
        unset($event_list_columns['date']);
        $event_list_columns['group'] = __('Campus', 'lo-events');
        $event_list_columns['date']  = $date;

        return $event_list_columns;
    }

    /**
     * Getting data for custom coloumn
     *
     * @param $event_list_column
     * @param $post_id
     *
     * @since 0.27.0
     */
    public function lo_events_column($event_list_column, $post_id)
    {
        global $wpdb, $table_prefix;
        switch ($event_list_column)
        {
            case 'group' :
                $group_id   = get_post_meta($post_id, $this->meta_prefix . 'group_id', TRUE);
                $group_data = $wpdb->get_var('SELECT data FROM ' . $table_prefix . $this->groups_db_prefix . ' WHERE ccb_group_id = ' . $group_id);
                $group_name = json_decode($group_data);
                echo $group_name->group->campus->value;
                break;
        }
    }

    /**
     * Settings custom coloumn sortable
     *
     * @param $event_list_columns
     *
     * @return mixed
     * @since 0.27.0
     */
    public function lo_events_sortable_columns($event_list_columns)
    {
        $event_list_columns['group'] = 'group';

        return $event_list_columns;
    }

    /**
     * run order by custom coloumn query
     *
     * @param $lo_events_query
     *
     * @since 0.27.0
     */
    public function lo_events_orderby($lo_events_query)
    {
        if ( ! is_admin())
        {
            return;
        }
        $orderby = $lo_events_query->get('orderby');
        if ('group' == $orderby)
        {
            $lo_events_query->set('meta_key', $this->meta_prefix . 'group_id');
            $lo_events_query->set('orderby', 'meta_value_num');
        }
    }

    /**
     * Overriding get_args from parent
     *
     * @since 0.10.1
     * @return array
     */
    public function get_args()
    {

        if ( ! empty($this->cpt_args))
        {
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
                'cpt-core'), $this->singular) : NULL,
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
            'public'             => TRUE,
            'publicly_queryable' => TRUE,
            'show_ui'            => TRUE,
            'show_in_menu'       => TRUE,
            'has_archive'        => TRUE,
            'supports'           => array('title', 'editor', 'excerpt'),
        );

        $this->cpt_args           = wp_parse_args($this->arg_overrides, $defaults);
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
            'show_names'   => TRUE, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ));

        //kid friendly meta
        $cmb_additional->add_field(array(
            'name'             => esc_html__('Kid Friendly', 'liquid-outreach'),
            'desc'             => esc_html__('', 'liquid-outreach'),
            'id'               => $prefix . 'kid_friendly',
            'type'             => 'select',
            'show_option_none' => TRUE,
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

        if (Liquid_Outreach::$enable_ccb_gravity == TRUE)
        {
            //gravity form select option
            $cmb_additional->add_field(array(
                'name'       => __('Select Gravity Form', 'liquid-outreach'),
                'desc'       => __('When this is selected, above page url will not be used.', 'liquid-outreach'),
                'id'         => $prefix . 'gform',
                'type'       => 'select',
                'options_cb' => ['LO_Ccb_Events', 'get_gform_list'],
            ));
        }

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
            'show_names'   => TRUE, // Show field names on the left
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
     *
     * @since 0.20.0
     */
    public function cmb_page_display_settings()
    {

        $prefix        = $this->meta_prefix;
        $option_prefix = 'lo_events_info_';

        // Define our metaboxes and fields.
        $cmb_page_settings = new_cmb2_box(array(
            'id'           => $prefix . 'display_settings',
            'title'        => esc_html__('Display Settings', 'liquid-outreach'),
            'object_types' => array('lo-events'),
            'context'      => 'side',
            'priority'     => 'low',
            'show_names'   => TRUE, // Show field names on the left
            'cmb_styles'   => FALSE, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ));

        $settings_arr = array(
            'start_date'           => [
                'label'        => 'Date Time',
                'settings_key' => 'date_time'
            ],
            'cost'                 => [
                'label'        => 'Cost',
                'settings_key' => 'cost'
            ],
            'openings'             => [
                'label'        => 'Openings',
                'settings_key' => 'openings'
            ],
            'categories'           => [
                'label'        => 'Categories',
                'settings_key' => 'categories'
            ],
            'kid_friendly'         => [
                'label'        => 'Kid Friendly',
                'settings_key' => 'kid_friendly'
            ],
            'team_leader_name'     => [
                'label'        => 'Leader Name',
                'settings_key' => 'team_leader_name'
            ],
            'team_lead_email'      => [
                'label'        => 'Leader Email',
                'settings_key' => 'team_leader_email'
            ],
            'team_lead_phone'      => [
                'label'        => 'Leader Phone',
                'settings_key' => 'team_leader_phone'
            ],
            'address'              => [
                'label'        => 'Address',
                'settings_key' => 'address'
            ],
            'partner_organization' => [
                'label'        => 'Partner Organization',
                'settings_key' => 'partner_organization'
            ],
        );

        foreach ($settings_arr as $index => $item)
        {
            $cmb_page_settings->add_field(array(
                'name'    => $item['label'],
                'desc'    => '',
                'id'      => $option_prefix . $item['settings_key'],
                'type'    => 'radio_inline',
                'options' => array(
                    '1' => __('Show', 'cmb2'),
                    '0' => __('Hide', 'cmb2'),
                ),
                'default' => $this->global_details_page_setting($option_prefix . $item['settings_key'])
            ));
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
        $args['augment_posts'] = TRUE;
        $args['meta_key']      = $this->meta_prefix . 'start_date';
        $args['orderby']       = 'meta_value_num';
        $args['order']         = 'ASC';
        $args['meta_query']    = [
            'key'     => $this->meta_prefix . 'start_date',
            'value'   => time(),
            'compare' => '>='
        ];
        $args                  = apply_filters('lo_get_events_args',
            wp_parse_args($args, $defaults));

        $events = new WP_Query($args);

        if (
            isset($args['augment_posts'])
            && $args['augment_posts']
            && $events->have_posts()
            // Don't augment for queries w/ greater than 100 posts, for perf. reasons.
            && $events->post_count < 100
        )
        {
            foreach ($events->posts as $key => $post)
            {
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

        if (isset($args['bypass_uri_query']) && isset($args['event_org']))
        {
            $search_query = [
                'org' => $args['event_org'],
            ];
            unset($args['bypass_uri_query'], $args['event_org']);
        }
        else
        {
            $search_query = [
                'key'  => isset($_GET['lo-event-s']) ? $_GET['lo-event-s'] : '',
                'cat'  => isset($_GET['lo-event-cat']) ? $_GET['lo-event-cat'] : '',
                'org'  => isset($_GET['lo-event-org']) ? $_GET['lo-event-org'] : '',
                'day'  => isset($_GET['lo-event-day']) ? $_GET['lo-event-day'] : '',
                'loc'  => isset($_GET['lo-event-loc']) ? $_GET['lo-event-loc'] : '',
                'camp' => isset($_GET['lo-campus']) ? $_GET['lo-campus'] : '',
            ];
        }

        if ( ! empty($search_query['key']))
        {

            global $wpdb;
            $args['post__in']
                = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '" .
                                 $search_query['key'] . "%' ");
        }

        $args['augment_posts'] = TRUE;

        if ( ! empty($search_query['cat']))
        {
            $args['tax_query'] = [
                [
                    'taxonomy' => liquid_outreach()->lo_ccb_event_categories->taxonomy(),
                    'field'    => 'term_id',
                    'terms'    => (int)$search_query['cat']
                ]
            ];
        }

        if ( ! empty($search_query['org']))
        {
            $args['meta_query'][] = [
                'key'     => $this->meta_prefix . 'group_id',
                'value'   => $search_query['org'],
                'compare' => '='
            ];
        }

        if ( ! empty($search_query['day']))
        {
            $args['meta_query'][] = [
                'key'     => $this->meta_prefix . 'weekday_name',
                'value'   => $search_query['day'],
                'compare' => '='
            ];
        }

        if ( ! empty($search_query['loc']))
        {
            $args['meta_query'][] = [
                'key'     => $this->meta_prefix . 'city',
                'value'   => $search_query['loc'],
                'compare' => '='
            ];
        }

        if ( ! empty($search_query['camp']))
        {
            $args['meta_query'][] = [
                'key'     => $this->meta_prefix . 'campus_id',
                'value'   => $search_query['camp'],
                'compare' => '='
            ];
        }

        $args['meta_key'] = $this->meta_prefix . 'start_date';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'ASC';
        if ( ! empty($args['meta_query']))
        {
            $args['meta_query']['relation'] = 'AND';
        }
        $args['meta_query'][] = [
            'key'     => $this->meta_prefix . 'start_date',
            'value'   => time(),
            'compare' => '>='
        ];
        $args                 = apply_filters('lo_get_events_args',
            wp_parse_args($args, $defaults));

        $events = new WP_Query($args);

        if (
            isset($args['augment_posts'])
            && $args['augment_posts']
            && $events->have_posts()
            // Don't augment for queries w/ greater than 100 posts, for perf. reasons.
            && $events->post_count < 100
        )
        {
            foreach ($events->posts as $key => $post)
            {
                $events->posts[$key] = new LO_Events_Post($post);
            }
        }

        return $events;
    }

    public static function get_gform_list()
    {
        $form_array = array();

        // Gravity Form
        if (class_exists('RGFormsModel'))
        {
            $forms = RGFormsModel::get_forms(NULL, 'title');
            if ( ! empty($forms) && is_array($forms))
            {
                $form_array[''] = 'Select';
                foreach ($forms as $form)
                {
                    if (isset($form->title, $form->id))
                    {
                        $form_array[$form->id] = $form->title;
                    }
                }
            }
        }

        return $form_array;
    }

}
