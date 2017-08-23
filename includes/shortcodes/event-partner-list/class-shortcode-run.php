<?php

    /**
     * Liquid Outreach Event Partner_List Shortcode - Run
     *
     * @since   0.3.3
     * @package Liquid Outreach
     */
    class LO_Shortcodes_Event_Partner_List_Run extends LO_Shortcodes_Run_Base
    {

        /**
         * The Shortcode Tag
         *
         * @var string  $shortcode
         * @since 0.3.3
         */
        public $shortcode = 'lo_event_partner_list';

        /**
         * Default attributes applied to the shortcode.
         *
         * @var array   $atts_defaults
         * @since 0.3.3
         */
        public $atts_defaults
            = array(
                'disable_header' => FALSE,
                'disable_nav'    => FALSE,
            );

        /**
         * Shortcode Output
         *
         * @return string $content
         *
         * @since 0.3.3
         */
        public function shortcode()
        {
            parent::shortcode();

            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('lo-vandertable',
                Liquid_Outreach::$url . '/assets/js/vandertable.js');
            wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');

            $content_arr = [];

            $content_arr['menu_option_index']      = $this->menu['menu_option_index'];
            $content_arr['menu_option_search']     = $this->menu['menu_option_search'];
            $content_arr['menu_option_categories'] = $this->menu['menu_option_categories'];
            $content_arr['menu_option_city']       = $this->menu['menu_option_city'];
            $content_arr['menu_option_days']       = $this->menu['menu_option_days'];
            $content_arr['menu_option_partners']   = $this->menu['menu_option_partners'];
            $content_arr['menu_option_campus']     = $this->menu['menu_option_campus'];

            $content_arr['disable'] = $disable = [
                'header' => (bool)$this->att('disable_header') == '1' || $this->att('disable_header') == 'true' ? 1 : 0,
                'nav'    => (bool)$this->att('disable_nav') == '1' || $this->att('disable_nav') == 'true' ? 1 : 0,
            ];

            if ( ! $disable['nav'])
            {
                $content_arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
                    'hide_empty' => TRUE
                ]);

                $content_arr['cities'] = $cities = liquid_outreach()->lo_ccb_events->get_all_city_list();
            }

            $content_arr = array_merge($content_arr, $this->get_partner_list());

            $content_arr = array_merge($content_arr, $this->get_base_pages());

            $content = '';
            $content .= LO_Style_Loader::get_template('lc-plugin');
            $content .= LO_Style_Loader::get_template('vandertable');
            $content .= LO_Template_Loader::get_template('partner-list', $content_arr);

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
            $paged  = (int)get_query_var('paged') ? get_query_var('paged') : 1;
            $offset = (($paged - 1) * 10);

            return compact('paged', 'offset');
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