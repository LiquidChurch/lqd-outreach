<?php

/**
 * Liquid Outreach Event Search Shortcode - Run
 *
 * @since   0.2.1
 * @package Liquid Outreach
 */
class LO_Shortcodes_Event_Search_Run extends LO_Shortcodes_Run_Base
{

    /**
     * The Shortcode Tag
     *
     * @var string $shortcode
     * @since 0.2.1
     */
    public $shortcode = 'lo_event_search';

    /**
     * Default attributes applied to the shortcode.
     *
     * @var array $atts_defaults
     * @since 0.2.1
     */
    public $atts_defaults
        = array(
            'disable_header'       => FALSE,
            'disable_nav'          => FALSE,
            'disable_cateogy_list' => FALSE,
            'force_cat_slug'       => NULL,
        );

    /**
     * Shortcode Output
     *
     * @return  string  $content
     * @since 0.2.1
     */
    public function shortcode()
    {
        parent::shortcode();

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('lo-vandertable',
            Liquid_Outreach::$url . '/assets/js/vandertable.js');
        wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

        $content_arr = [];

        $content_arr['force_cat_page']         = $this->force_cat_page;
        $content_arr['menu_option_index']      = $this->menu['menu_option_index'];
        $content_arr['menu_option_search']     = $this->menu['menu_option_search'];
        $content_arr['menu_option_categories'] = $this->menu['menu_option_categories'];
        $content_arr['menu_option_city']       = $this->menu['menu_option_city'];
        $content_arr['menu_option_days']       = $this->menu['menu_option_days'];
        $content_arr['menu_option_partners']   = $this->menu['menu_option_partners'];
        $content_arr['menu_option_campus']     = $this->menu['menu_option_campus'];

        $content_arr['disable'] = $disable = [
            'header'       => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
            'nav'          => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            'cateogy_list' => (bool)$this->att('disable_cateogy_list') == '1' || $this->att('disable_cateogy_list') == 'true' ? 1 : 0
        ];

        $args = $this->get_initial_query_args();

        $args = wp_parse_args($args, array(
            'post_type'      => liquid_outreach()->lo_ccb_events->post_type(),
            'posts_per_page' => 10,
        ));

        if (isset($_GET['lo-event-s']) || isset($_GET['lo-event-loc']) || isset($_GET['lo-event-day']) || isset($_GET['lo-event-org']) || isset($_GET['lo-campus']))
        {
            $events = liquid_outreach()->lo_ccb_events->get_search_result($args);
            if (empty($events->posts))
            {
                $event_empty_msg = 'Sorry, No data found for the search criteria.';
            }
        }
        else
        {
            $events = liquid_outreach()->lo_ccb_events->get_many($args);
            if (empty($events->posts))
            {
                $event_empty_msg = 'Sorry, No data found.';
            }
        }

        $max        = ! empty($events->max_num_pages) ? $events->max_num_pages : 0;
        $pagination = $this->get_pagination($max);

        $content_arr['events']          = ! empty($events->posts) ? $events->posts : [];
        $content_arr['event_empty_msg'] = isset($event_empty_msg) ? $event_empty_msg : '';
        $content_arr['pagination']      = $pagination;

        if ( ! $disable['nav'] || ! $disable['cateogy_list'])
        {

            $content_arr = array_merge($content_arr, $this->get_category_list());

            $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();

            $content_arr = array_merge($content_arr, $this->get_partner_list());
        }

        $content_arr = array_merge($content_arr, $this->get_base_pages());

        $content = '';
        $content .= LO_Style_Loader::get_template('lc-plugin');
        $content .= LO_Style_Loader::get_template('vandertable');
        $content .= LO_Template_Loader::get_template('search', $content_arr);

        return $content;
    }

    /**
     * Get Initial Query Arguments
     *
     * @return array
     * @since  0.2.4
     */
    public function get_initial_query_args()
    {
        $paged     = (int)get_query_var('paged') ? get_query_var('paged') : 1;
        $offset    = (($paged - 1) * 10);
        $tax_query = [];
        if ($this->force_cat_page != NULL)
        {
            $tax_query[] = array(
                'taxonomy' => liquid_outreach()->lo_ccb_event_categories->taxonomy(),
                'field'    => 'slug',
                'terms'    => $this->force_cat_page,
            );
        }

        return compact('paged', 'offset', 'tax_query');
    }

    /**
     * Pagination links
     *
     * @since 0.2.4
     *
     * @param $total_pages
     *
     * @return array
     */
    protected function get_pagination($total_pages)
    {
        $nav = array('prev_link' => '', 'next_link' => '');

        if ( ! $this->bool_att('remove_pagination'))
        {
            $nav['prev_link'] = get_previous_posts_link(__('<span>&larr;</span> Previous',
                'liquid-outreach'), $total_pages);
            $nav['next_link'] = get_next_posts_link(__('Next <span>&rarr;</span>',
                'liquid-outreach'), $total_pages);
        }

        return $nav;
    }

}