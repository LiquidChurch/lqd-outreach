<?php

/**
 * Liquid Outreach Partner Single Shortcode - Run
 *
 * @since   0.11.2
 * @package Liquid Outreach
 */
class LO_Shortcodes_Partner_Single_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string  $shortcode
     * @since 0.11.2
     */
    public $shortcode = 'lo_partner_single';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array   $atts_defaults
     * @since 0.11.2
     */
    public $atts_defaults
        = array(
            'partner_id' => 0,
            'disable_header' => false,
            'disable_nav' => false,
            'disable_search' => false,
        );

    /**
     * Shortcode Output
     *
     * @return  string  $content
     * @since 0.11.2
     */
    public function shortcode()
    {
        parent::shortcode();

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('lo-vandertable',
            Liquid_Outreach::$url . '/assets/js/vandertable.js');
        wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $content_arr['disable'] = $disable = [
            'header' => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
            'nav' => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            'search' => (bool)$this->att('disable_search') == '1' || $this->att('disable_search') == 'true' ? 1 : 0
        ];

        $content_arr['post'] = $post = new LO_Event_Partners_Post(get_post($this->att('partner_id')));

        $args = $this->get_initial_query_args();

        $args = wp_parse_args($args, array(
            'post_type' => liquid_outreach()->lo_ccb_events->post_type(),
            'posts_per_page' => 10,
            'bypass_uri_query' => true,
            'event_org' => $post->get_meta('lo_ccb_event_partner_group_id'),
        ));

        $events = [];
        $event_empty_msg = '';

        $events = liquid_outreach()->lo_ccb_events->get_search_result($args);
        if (empty($events->posts)) {
            $event_empty_msg = 'Sorry, No data found for the search criteria.';
        }

        $max = !empty($events->max_num_pages) ? $events->max_num_pages : 0;
        $pagination = $this->get_pagination($max);

        $content_arr['events'] = !empty($events->posts) ? $events->posts : [];
        $content_arr['event_empty_msg'] = isset($event_empty_msg) ? $event_empty_msg : '';
        $content_arr['pagination'] = $pagination;


        if (!$disable['nav'] || !$disable['search']) {
            $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
                'hide_empty' => true
            ]);

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
        $content .= LO_Template_Loader::get_template('partner-details', $content_arr);

        return $content;
    }

    /**
     * Get Initial Query Arguments
     *
     * @return array
     * @since  0.11.2
     */
    public function get_initial_query_args()
    {
        $paged = (int)get_query_var('paged') ? get_query_var('paged') : 1;
        $offset = (($paged - 1) * 10);

        return compact('paged', 'offset');
    }

    /**
     * Pagination links
     *
     * @since 0.11.2
     *
     * @param $total_pages
     *
     * @return array    $nav
     */
    protected function get_pagination($total_pages)
    {
        $nav = array('prev_link' => '', 'next_link' => '');

        if (!$this->bool_att('remove_pagination')) {
            $nav['prev_link'] = get_previous_posts_link(__('<span>&larr;</span> Previous',
                'liquid-outreach'), $total_pages);
            $nav['next_link'] = get_next_posts_link(__('Next <span>&rarr;</span>',
                'liquid-outreach'), $total_pages);
        }

        return $nav;
    }

}