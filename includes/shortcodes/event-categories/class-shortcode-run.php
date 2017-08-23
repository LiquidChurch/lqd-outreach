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
     * @var string  $shortcode
     * @since 0.4.0
     */
    public $shortcode = 'lo_event_categories';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array   $atts_defaults
     * @since 0.4.0
     */
    public $atts_defaults
        = array(
            'disable_header' => false,
            'disable_nav' => false,
            'disable_search' => false,
            'force_cat_slug'         => NULL,
        );

    /**
     * Shortcode Output
     *
     * @since 0.4.0
     */
    public function shortcode()
    {
        parent::shortcode();

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('lo-vandertable',
            Liquid_Outreach::$url . '/assets/js/vandertable.js');
        wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $content_arr['force_cat_page'] = $this->force_cat_page;
        $content_arr['menu_option_index'] = $this->menu['menu_option_index'];
        $content_arr['menu_option_search'] = $this->menu['menu_option_search'];
        $content_arr['menu_option_categories'] = $this->menu['menu_option_categories'];
        $content_arr['menu_option_city'] = $this->menu['menu_option_city'];
        $content_arr['menu_option_days'] = $this->menu['menu_option_days'];
        $content_arr['menu_option_partners'] = $this->menu['menu_option_partners'];
        $content_arr['menu_option_campus'] = $this->menu['menu_option_campus'];

        // Do we want to show a flip animation for categories when viewing categories page
        $lo_events_page_category_animation = lo_get_option( 'page', 'lo_events_page_category_animation' );

        $content_arr['disable'] = $disable = [
            'header' => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
            'nav' => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            'search' => (bool)$this->att('disable_search') == '1' || $this->att('disable_search') == 'true' ? 1 : 0,
            'animation' => !empty($lo_events_page_category_animation) ? 1 : 0
        ];

        $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
            'hide_empty' => true
        ]);

        if(!$disable['nav'] || !$disable['search']) {
            $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();
            $partners = liquid_outreach()->lo_ccb_event_partners->get_many([
                'post_type' => liquid_outreach()->lo_ccb_event_partners->post_type(),
                'posts_per_page' => -1,
            ]);
            $content_arr['partners'] = !empty($partners->posts) ? $partners->posts : [];
        }

        $content_arr = array_merge($content_arr, $this->get_base_pages());

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= LO_Template_Loader::get_template('event-category-page', $content_arr);

        return $content;
    }

}