<?php
/**
 * Liquid Outreach General Settings Page.
 *
 * @since   0.8.0
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach General Settings Page class.
 *
 * @since 0.8.0
 */
class LO_Ccb_Events_Page_Settings extends LO_Base_Option_Page
{

    /**
     * Holds an instance of the object
     *
     * @var LO_Ccb_Events_Page_Settings
     * @since 0.8.0
     */
    protected static $instance = null;

    /**
     * Option key, and option page slug
     *
     * @var string
     * @since 0.8.0
     */
    protected $key = 'liquid_outreach_ccb_events_page_settings';
    /**
     * Options page metabox id
     *
     * @var string
     * @since 0.8.0
     */
    protected $metabox_id = 'liquid_outreach_ccb_events_page_settings_metabox';
    /**
     * Options page meta prefix
     *
     * @var string
     * @since 0.20.2
     */
    protected $meta_prefix = 'lo_events_page_';
    /**
     * Options Page title
     *
     * @var string
     * @since 0.8.0
     */
    protected $title = '';
    /**
     * Options Page hook
     *
     * @var string
     * @since 0.8.0
     */
    protected $options_page = '';

    /**
     * Constructor
     *
     * @since 0.8.0
     */
    public function __construct()
    {
        // Set our title
        $this->title = __('General Settings', 'liquid-outreach');

        $this->hooks();
    }

    /**
     * parent hook override
     * @since 0.10.1
     */
    public function hooks()
    {
        parent::hooks(); // TODO: Change the autogenerated stub

        add_action( "cmb2_save_field_lo_events_page_permalink_base", array( $this, 'after_permalink_settings_save' ), 10, 3 );
        add_action( "cmb2_save_field_lo_events_page_permalink_base_events", array( $this, 'after_permalink_settings_save' ), 10, 3 );
        add_action( "cmb2_save_field_lo_events_page_permalink_base_categories", array( $this, 'after_permalink_settings_save' ), 10, 3 );
        add_action( "cmb2_save_field_lo_events_page_permalink_base_partners", array( $this, 'after_permalink_settings_save' ), 10, 3 );
        add_action( "cmb2_save_field_lo_events_page_event_attendance_count_update", array( $this, 'clear_scheduled_hook' ), 10, 3 );
    }

    /**
     * remove wp_clear_scheduled_hook for cron interval
     * @since 0.11.7
     */
    public function clear_scheduled_hook($updated, $action, $settingsThis) {
        if(!empty($updated)) {
            wp_clear_scheduled_hook('lo_ccb_cron_event_attendance_sync');
        }
    }

    /**
     * for changes after permalink settings value is changed
     * @since 0.10.1
     */
    public function after_permalink_settings_save($updated, $action, $settingsThis) {
        if(!empty($updated)) {
            update_option('lo_ccb_flush_base_rewrite', 'flush');
        }
    }

    /**
     * Returns the running object
     *
     * @return LO_Ccb_Events_Info_Settings
     * @since 0.3.4
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Add the options metabox to the array of metaboxes
     *
     * @since  0.8.0
     */
    function add_options_page_metabox()
    {
        wp_enqueue_style('lc-plugin', Liquid_Outreach::$url . 'assets/css/lc-plugin.css');

        // hook in our save notices
        add_action("cmb2_save_options-page_fields_{$this->metabox_id}",
            array($this, 'settings_notices'), 10, 2);

        $cmb = new_cmb2_box(array(
            'id' => $this->metabox_id,
            'hookup' => false,
            'cmb_styles' => false,
            'show_on' => array(
                // These are important, don't remove
                'key' => 'options-page',
                'value' => array($this->key,)
            ),
        ));

        // Set our CMB2 fields

        $default_page = [
            'projects' => (get_page_by_path('projects')),
            'search' => (get_page_by_path('search-projects')),
            'categories' => (get_page_by_path('project-categories')),
        ];

        $cmb->add_field(array(
            'name' => __('CCB API Username', 'liquid-outreach'),
            'desc' => __('Please enter your username for accessing CCB API.', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'ccb_api_username',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'default' => '',
        ));

        $cmb->add_field(array(
            'name' => __('CCB API Password', 'liquid-outreach'),
            'desc' => __('Please enter your password for accessing CCB API.', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'ccb_api_password',
            'type' => 'text',
            'attributes' => [
                'required' => 'required',
                'type' => 'password'
            ],
            'default' => '',
        ));

        $cmb->add_field(array(
            'name' => __('Event Attendance Count Update Interval', 'liquid-outreach'),
            'desc' => '',
            'id' => $this->meta_prefix . 'event_attendance_count_update',
            'type' => 'select',
            'options' => [
                '5min' => '5 Min',
                '15min' => '15 Min',
                '30min' => '30 Min',
                '45min' => '45 Min',
                'hourly' => 'Hourly',
                'twicedaily' => 'Twice Daily',
                'daily' => 'Daily',
                ],
            'default' => '30min',
            'attributes' => [
                'required' => 'required',
            ]
        ));

        $cmb->add_field(array(
            'name' => 'Category Animation',
            'id' => $this->meta_prefix . 'category_animation',
            'type' => 'radio_inline',
            'default' => true,
            'options' => array(
                true => __('Enable', 'cmb2'),
                false => __('Disable', 'cmb2'),
            ),
        ));

        $cmb->add_field(array(
            'name' => __('Default Header Image', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'default_header_image',
            'type' => 'file',
        ));

        $cmb->add_field(array(
            'name' => __('Select Home Page', 'liquid-outreach'),
            'desc' => 'Place these shortcodes inside the page content area - ' .
                '<br/>[lo_header_element]<br/>[lo_nav_element]<br/>[lo_categories_element]',
            'id' => $this->meta_prefix . 'lo_home_page',
            'type' => 'select',
            'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
            'default' => !empty($default_page['projects']) ? $default_page['projects']->ID : ''
        ));

        $category_mapping_id = $cmb->add_field(array(
            'name' => __('Category Base Page Mapping', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'cat_base_page_mapping',
            'type' => 'group',
            'options' => array(
                'group_title' => esc_html__('Mapping {#}', 'liquid-outreach'),
                'add_button' => esc_html__('Add Another Mapping', 'liquid-outreach'),
                'remove_button' => esc_html__('Remove Mapping', 'liquid-outreach'),
            ),
        ));

        $cmb->add_group_field($category_mapping_id, array(
            'name' => 'Select Category',
            'desc' => '',
            'id' => 'category',
            'taxonomy' => 'event-category', //Enter Taxonomy Slug
            'type' => 'taxonomy_select',
            'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
        ));

        $cmb->add_group_field($category_mapping_id, array(
            'name' => 'Select Page',
            'desc' => '',
            'id' => 'page',
            'type' => 'select',
            'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_pages'],
        ));

        $cmb->add_field(array(
            'name' => __('Select Search Page', 'liquid-outreach'),
            'desc' => 'Place these shortcodes inside the page content area - ' .
                '<br/>[lo_event_search]',
            'id' => $this->meta_prefix . 'lo_search_page',
            'type' => 'select',
            'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
            'default' => !empty($default_page['search']) ? $default_page['projects']->ID : ''
        ));

        $cmb->add_field(array(
            'name' => __('Select Category Page', 'liquid-outreach'),
            'desc' => 'Place these shortcodes inside the page content area - ' .
                '<br/>[lo_event_categories]',
            'id' => $this->meta_prefix . 'lo_category_page',
            'type' => 'select',
            'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
            'default' => !empty($default_page['categories']) ? $default_page['projects']->ID : ''
        ));

        $cmb->add_field(array(
            'name' => __('Custom Permalink Base', 'liquid-outreach'),
            'desc' => __('Permalink base for outreach events, categories and partners.', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'permalink_base',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'default' => 'outreach',
        ));

        $cmb->add_field(array(
            'name' => __('Custom Permalink Events', 'liquid-outreach'),
            'desc' => __('Permalink base for outreach events.', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'permalink_base_events',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'default' => 'events',
        ));

        $cmb->add_field(array(
            'name' => __('Custom Permalink Categories', 'liquid-outreach'),
            'desc' => __('Permalink base for outreach categories', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'permalink_base_categories',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'default' => 'categories',
        ));

        $cmb->add_field(array(
            'name' => __('Custom Permalink Partners', 'liquid-outreach'),
            'desc' => __('Permalink base for outreach partners.', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'permalink_base_partners',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'default' => 'partners',
        ));

    }

    /**
     * Public getter method for retrieving protected/private variables
     *
     * @since  0.8.0
     *
     * @param  string $field Field to retrieve
     *
     * @return mixed          Field value or exception is thrown
     *
     * @throws Exception
     */
    public function __get($field)
    {
        // Allowed fields to retrieve
        if (in_array($field, array('key', 'metabox_id', 'meta_prefix', 'title', 'options_page'), true)) {
            return $this->{$field};
        }

        throw new Exception('Invalid property: ' . $field);
    }

    /**
     * get page list
     * \
     * @return mixed
     * @since 0.25.0
     */
    public static function show_pages()
    {
        $query = new WP_Query(
            $array = array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            )
        );

        $titles[''] = '';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $titles[get_the_id()] = get_the_title();
            }
        }

        wp_reset_postdata();

        return $titles;
    }


}