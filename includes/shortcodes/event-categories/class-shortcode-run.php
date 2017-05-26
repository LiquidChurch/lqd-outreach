<?php

/**
 * Liquid Outreach Event Categories Shortcode - Run
 *
 * @since   0.4.0
 * @package Liquid Outreach
 */
class LO_Shortcodes_Event_Categories_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string
     * @since 0.4.0
     */
    public $shortcode = 'lo_event_categories';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array
     * @since 0.4.0
     */
    public $atts_defaults
        = array(
            'disable_header' => false,
            'disable_nav' => false,
            'disable_search' => false,
        );

    /**
     * Shortcode Output
     *
     * @since 0.4.0
     */
    public function shortcode()
    {

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('lo-vandertable',
            Liquid_Outreach::$url . '/assets/js/vandertable.js');
        wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $content_arr['cities'] = $disable = [
            'header' => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
            'nav' => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            'search' => (bool)$this->att('disable_search') == '1' || $this->att('disable_search') == 'true' ? 1 : 0
        ];

        $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
            'hide_empty' => false
        ]);

        if(!$disable['nav'] || !$disable['search']) {
            $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();
            $content_arr['partners'] = $partners = liquid_outreach()->lo_ccb_event_partners->get_many([
                'post_type' => liquid_outreach()->lo_ccb_event_partners->post_type(),
                'posts_per_page' => -1,
            ]);
        }

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= LO_Template_Loader::get_template('event-category-page', $content_arr);

        return $content;
    }

}