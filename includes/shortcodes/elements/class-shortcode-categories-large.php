<?php

/**
 * Liquid Outreach Categories Element Shortcode
 *
 * @since   0.7.0
 * @package Liquid_Outreach
 */
class LO_Shortcodes_Categories_Element extends LO_Shortcodes_Base
{

    /**
     * Constructor
     *
     * @since  0.7.0
     *
     * @param  object $plugin Main plugin object.
     *
     */
    public function __construct($plugin)
    {
        $this->run = new LO_Shortcodes_Categories_Element_Run($plugin->lo_ccb_events,
            $plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories);
        $this->admin = new LO_Shortcodes_Categories_Element_Admin($this->run);

        parent::hooks();
    }

}


class LO_Shortcodes_Categories_Element_Admin extends LO_Shortcodes_Admin_Base
{

    /**
     * Shortcode prefix for field ids.
     *
     * @var   string    $prefix
     * @since 0.7.0
     */
    protected $prefix = 'lo_categories_element';

    /**
     * Sets up the button
     *
     * @since 0.7.0
     * @return array
     */
    function js_button_data()
    {
        return array(
            'qt_button_text' => __('LO Categories Element', 'liquid-outreach'),
            'button_tooltip' => __('Categories Element', 'liquid-outreach'),
            'icon' => 'dashicons-media-interactive',
            // 'mceView'        => true, // The future
        );
    }

    /**
     * Adds fields to the button modal using CMB2
     *
     * @since 0.7.0
     *
     * @param $fields
     * @param $button_data
     *
     * @return array    $fields
     */
    function fields($fields, $button_data)
    {
        $fields[] = array(
            'name'           => 'Select Event Category',
            'desc'           => 'Select event category',
            'id'             => 'force_cat_slug',
            'taxonomy'       => 'event-category', //Enter Taxonomy Slug
            'type'           => 'taxonomy_select',
            'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
        );

        return $fields;
    }
}


class LO_Shortcodes_Categories_Element_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string  $shortcode
     * @since 0.7.0
     */
    public $shortcode = 'lo_categories_element';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array   $atts_defaults
     * @since 0.7.0
     */
    public $atts_defaults
        = array(
            'force_cat_slug' => NULL
        );

    /**
     * Shortcode Output
     *
     * @since 0.7.0
     */
    public function shortcode()
    {
        parent::shortcode();

        if (!wp_script_is('jquery-ui-sortable', $list = 'enqueued'))
            wp_enqueue_script('jquery-ui-sortable');

        if (!wp_script_is('lo-vandertable', $list = 'enqueued'))
            wp_enqueue_script('lo-vandertable',
                Liquid_Outreach::$url . '/assets/js/vandertable.js');

        if (!wp_script_is('lo-index', $list = 'enqueued'))
            wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $lo_events_page_category_animation = lo_get_option( 'page', 'lo_events_page_category_animation' );

        $content_arr['disable'] = $disable = [
            'animation' => !empty($lo_events_page_category_animation) ? 1 : 0
        ];

        if ($this->force_cat_page != NULL)
        {
            $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_similar($this->force_cat_page);
        } else
        {
            $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
                'hide_empty' => true
            ]);
        }

        $content_arr = array_merge($content_arr, $this->get_base_pages());

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= '<div class="lo-full">';
        $content .= '<div id="lo-filter-panel" class="lo-filter-panel">';
        $content .= LO_Template_Loader::output_template('event-category-list', $content_arr);
        $content .= '</div>';
        $content .= '</div>';

        return $content;
    }

}