<?php
/**
 * Liquid Outreach Ccb Partner Organizations Auto Categorize Options Page
 *
 * @since   0.20.2
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach Ccb Events Partner Auto Categorize Options Class
 *
 * @since 0.20.2
 */
class LO_Ccb_Events_Partner_Cat_Map_Settings extends LO_Base_Option_Page
{

    /**
     * Holds an instance of the object
     *
     * @var LO_Ccb_Events_Partner_Cat_Map_Settings
     * @since 0.20.2
     */
    protected static $instance = null;

    /**
     * Option key, and option page slug
     *
     * @var string
     * @since 0.20.2
     */
    protected $key = 'liquid_outreach_ccb_events_partner_cat_map_settings';

    /**
     * Options page metabox id
     *
     * @var string
     * @since 0.20.2
     */
    protected $metabox_id = 'liquid_outreach_ccb_events_partner_cat_map_settings_metabox';

    /**
     * Options page meta prefix
     *
     * @var string
     * @since 0.20.2
     */
    protected $meta_prefix = 'lo_events_partner_cat_map_';

    /**
     * Options Page title
     *
     * @var string
     * @since 0.20.2
     */
    protected $title = '';

    /**
     * Options Page hook
     *
     * @var string
     * @since 0.20.2
     */
    protected $options_page = '';

    /**
     * Constructor
     *
     * @since 0.20.2
     */
    public function __construct()
    {
        // Set our title
        $this->title = __('Outreach Partner Auto Categorize', 'liquid-outreach');

        $this->hooks();
    }

    /**
     * parent hook override
     * @since 0.20.2
     */
    public function hooks()
    {
        parent::hooks(); // TODO: Change the autogenerated stub
    }

    /**
     * Returns the running object
     *
     * @return LO_Ccb_Events_Partner_Cat_Map_Settings
     * @since 0.20.2
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
     * @since  0.20.2
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

        $category_mapping_id = $cmb->add_field(array(
            'name' => __('Category Mapping', 'liquid-outreach'),
            'id' => $this->meta_prefix . 'category_mapping',
            'type' => 'group',
            'options' => array(
                'group_title' => esc_html__('Mapping {#}', 'liquid-outreach'),
                'add_button' => esc_html__('Add Another Mapping', 'liquid-outreach'),
                'remove_button' => esc_html__('Remove Mapping', 'liquid-outreach'),
            ),
        ));

        $cmb->add_group_field($category_mapping_id, array(
            'name' => esc_html__('Name of Organization to Match', 'liquid-outreach'),
            'desc' => '',
            'id' => 'title',
            'type' => 'text',
        ));

        $cmb->add_group_field($category_mapping_id, array(
            'name' => 'Outreach Category',
            'desc' => 'Matched title post will be mapped to this outreach category',
            'id' => 'event_categroy',
            'taxonomy' => 'event-category', //Enter Taxonomy Slug
            'type' => 'taxonomy_select',
            'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
        ));

    }

    /**
     * Public getter method for retrieving protected/private variables
     *
     * @since  0.20.2
     *
     * @param  string $field Field to retrieve
     * @return mixed          Field value or exception is thrown
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

}