<?php

/**
 * Liquid Outreach Event Category Single Shortcode - Run
 *
 * @since   0.3.2
 * @package Liquid Outreach
 */
class LO_Shortcodes_Event_Category_Single_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string
     * @since 0.3.2
     */
    public $shortcode = 'lo_event_category_single';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array
     * @since 0.3.2
     */
    public $atts_defaults
        = array(
            'event_cat_slug' => '',
            'disable_header' => false,
            'disable_nav' => false,
            'disable_search' => false,
            'disable_cateogy_list' => false,
        );

    /**
     * Shortcode Output
     *
     * @since 0.3.2
     */
    public function shortcode()
    {

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('lo-vandertable',
            Liquid_Outreach::$url . '/assets/js/vandertable.js');
        wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $content_arr['disable'] = $disable = [
            'header' => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
            'nav' => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            'search' => (bool)$this->att('disable_search') == '1' || $this->att('disable_search') == 'true' ? 1 : 0,
            'cateogy_list' => (bool)$this->att('disable_cateogy_list') == '1' || $this->att('disable_cateogy_list') == 'true' ? 1 : 0
        ];

        $args = $this->get_initial_query_args();

        $args = wp_parse_args($args, array(
            'post_type' => liquid_outreach()->lo_ccb_events->post_type(),
            'posts_per_page' => 10,
        ));

        $events = liquid_outreach()->lo_ccb_events->get_many($args);
        $content_arr['events'] = !empty($events->posts) ? $events->posts : [];

        if (empty($events->posts)) {
            $event_empty_msg = 'Sorry, No data found.';
        }
        $content_arr['event_empty_msg'] = isset($event_empty_msg) ? $event_empty_msg : '';

        $max = !empty($events->max_num_pages) ? $events->max_num_pages : 0;
        $content_arr['pagination'] = $this->get_pagination($max);

        $categories_required = false;
        if(!$disable['nav'] || !$disable['search']) {
            $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();

            $partners = liquid_outreach()->lo_ccb_event_partners->get_many([
                'post_type' => liquid_outreach()->lo_ccb_event_partners->post_type(),
                'posts_per_page' => -1,
            ]);
            $content_arr['partners'] = !empty($partners->posts) ? $partners->posts : [];

            $categories_required = true;
        }

        if($categories_required || !$disable['cateogy_list']) {
            $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
                'hide_empty' => false
            ]);
        }

        $content_arr = array_merge($content_arr, $this->get_base_pages());

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= LO_Template_Loader::get_template('search', $content_arr);

        return $content;
    }

    /**
     * @return array
     * @since  0.3.2
     */
    public function get_initial_query_args()
    {
        $paged = (int)get_query_var('paged') ? get_query_var('paged') : 1;
        $offset = (($paged - 1) * 10);
        $event_cat_slug = $this->att('event_cat_slug');
        $tax_query = array(
            array(
                'taxonomy' => liquid_outreach()->lo_ccb_event_categories->taxonomy(),
                'field' => 'slug',
                'terms' => $event_cat_slug,
            ),
        );
        return compact('paged', 'offset', 'tax_query');
    }

    /**
     * Pagination links
     *
     * @since 0.3.2
     *
     * @param $total_pages
     *
     * @return array
     */
    protected function get_pagination($total_pages)
    {
        $nav = array('prev_link' => '', 'next_link' => '');

        if (!$this->bool_att('remove_pagination')) {
            $nav['prev_link'] = get_previous_posts_link(__('<span>&larr;</span> Previous',
                'liquid-outreach'));
            $nav['next_link'] = get_next_posts_link(__('Next <span>&rarr;</span>',
                'liquid-outreach'), $total_pages);
        }

        return $nav;
    }

}