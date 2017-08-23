<?php

/**
 * Liquid Outreach Shortcode Base
 *
 * @version 0.2.0
 * @package Liquid_Outreach
 */
abstract class LO_Shortcodes_Run_Base extends WDS_Shortcodes
{

    /**
     * LO_Ccb_Events object
     *
     * @var   LO_Ccb_Events $ccb_event
     * @since 0.2.0
     */
    public $ccb_event;

    /**
     * LO_Ccb_Event_Partners object
     *
     * @var   LO_Ccb_Event_Partners $ccb_event_partner
     * @since 0.2.0
     */
    public $ccb_event_partner;

    /**
     * LO_Ccb_Event_Categories object
     *
     * @var   LO_Ccb_Event_Categories $ccb_event_categories
     * @since 0.2.0
     */
    public $ccb_event_categories;

    /**
     * Page Settings Variable
     *
     * @var mixed $page_settings
     * @since 0.25.0
     */
    public $page_settings;

    /**
     * Force Category Page Variable
     *
     * @var string $force_cat_page
     * @since 0.25.0
     */
    public $force_cat_page = NULL;

    /**
     * Plugin menu option
     *
     * @var
     * @since 0.27.0
     */
    public $menu;

    /**
     * Constructor
     *
     * @since 0.2.0
     *
     * @param LO_Ccb_Events           $ccb_event
     * @param LO_Ccb_Event_Partners   $ccb_event_partner
     * @param LO_Ccb_Event_Categories $ccb_event_categories
     */
    public function __construct(LO_Ccb_Events $ccb_event, LO_Ccb_Event_Partners $ccb_event_partner, LO_Ccb_Event_Categories $ccb_event_categories)
    {

        $this->ccb_event            = $ccb_event;
        $this->ccb_event_partner    = $ccb_event_partner;
        $this->ccb_event_categories = $ccb_event_categories;

        parent::__construct();

        $this->page_settings = lo_get_option('page', 'all');
    }

    /**
     * Set plugin $this->menu var
     *
     * @since 0.27.0
     */
    public function set_plugin_menu()
    {
        if (empty($this->force_cat_page))
        {
            $this->menu = [
                'menu_option_index'      => $this->page_settings['menu_option_index'],
                'menu_option_search'     => $this->page_settings['menu_option_search'],
                'menu_option_categories' => $this->page_settings['menu_option_categories'],
                'menu_option_city'       => $this->page_settings['menu_option_city'],
                'menu_option_days'       => $this->page_settings['menu_option_days'],
                'menu_option_partners'   => $this->page_settings['menu_option_partners'],
                'menu_option_campus'     => $this->page_settings['menu_option_campus'],
            ];
        }
    }

    /**
     * Check If Shortcode Has Force Category Page Parameter
     */
    public function shortcode()
    {
        $this->force_cat_page = ! empty($_GET['lo-cat-page']) ? $_GET['lo-cat-page'] : $this->att('force_cat_slug');
        $this->set_plugin_menu();
    }

    /**
     * Get Inline CSS Styles
     *
     * @return array
     */
    public function get_inline_styles()
    {
        $style              = '';
        $has_icon_font_size = FALSE;

        if ($this->att('icon_color') || $this->att('icon_size'))
        {
            $style = ' style="';
            // Get/check our text_color attribute
            if ($this->att('icon_color'))
            {
                $text_color = sanitize_text_field($this->att('icon_color'));
                $style      .= 'color: ' . $text_color . ';';
            }
            if (is_numeric($this->att('icon_size')))
            {
                $has_icon_font_size = absint($this->att('icon_size'));
                $style              .= 'font-size: ' . $has_icon_font_size . 'em;';
            }
            $style .= '"';
        }

        return array($style, $has_icon_font_size);
    }

    /**
     * Get Base Pages
     *
     * @return array
     */
    public function get_base_pages()
    {
        $arr                                = [];
        $arr['page_link']                   = [];
        $arr['page_link']['page_query_arr'] = [];

        if ($this->force_cat_page != NULL)
        {
            $arr['page_link']['page_query_arr'] = [
                'lo-cat-page' => empty($this->force_cat_page) ? '' : $this->force_cat_page
            ];

            $page_query = http_build_query($arr['page_link']['page_query_arr']);

            $lo_page_settings = $this->page_settings['lo_events_page_cat_base_page_mapping'];
            $page_mapping     = [];

            foreach ($lo_page_settings as $index => $lo_page_setting)
            {
                if (isset($lo_page_setting['category']) && ($lo_page_setting['category'] == $this->force_cat_page))
                {
                    $page_mapping = $lo_page_setting;
                    break;
                }
            }

            if ( ! empty($page_mapping))
            {

                $arr['page_link']['main']   = get_permalink($page_mapping['page']);
                $arr['page_link']['search'] = get_permalink($page_mapping['page_browse']);
                $arr['page_link']['cat']    = get_permalink($page_mapping['page_category']);
            }
            else
            {

                $arr['page_link']['main']   = get_permalink($this->page_settings['lo_events_page_lo_home_page']) . '?' . $page_query;
                $arr['page_link']['search'] = get_permalink($this->page_settings['lo_events_page_lo_search_page']) . '?' . $page_query;
                $arr['page_link']['cat']    = get_permalink($this->page_settings['lo_events_page_lo_category_page']) . '?' . $page_query;
            }
        }
        else
        {
            $arr['page_link']['main']   = get_permalink($this->page_settings['lo_events_page_lo_home_page']);
            $arr['page_link']['search'] = get_permalink($this->page_settings['lo_events_page_lo_search_page']);
            $arr['page_link']['cat']    = get_permalink($this->page_settings['lo_events_page_lo_category_page']);
        }

        return $arr;
    }

    /**
     * Get Category List
     *
     * @return mixed
     * @since 0.25.0
     */
    public function get_category_list()
    {
        if ($this->force_cat_page != NULL)
        {
            $arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_similar($this->force_cat_page);
        }
        else
        {
            $arr['categories'] = $categories = liquid_outreach()->lo_ccb_event_categories->get_many([
                'hide_empty' => TRUE
            ]);
        }

        return $arr;
    }

    /**
     * Get Category Partner List
     *
     * @return array
     */
    public function get_partner_list()
    {
        if ($this->force_cat_page != NULL)
        {
            $partners = liquid_outreach()->lo_ccb_event_partners->get_similar($this->force_cat_page, [
                'post_type'      => liquid_outreach()->lo_ccb_event_partners->post_type(),
                'posts_per_page' => -1,
            ]);
        }
        else
        {
            $partners = liquid_outreach()->lo_ccb_event_partners->get_many([
                'post_type'      => liquid_outreach()->lo_ccb_event_partners->post_type(),
                'posts_per_page' => -1,
            ]);
        }

        return ! empty($partners) ? ['partners' => $partners->posts] : [];
    }

}