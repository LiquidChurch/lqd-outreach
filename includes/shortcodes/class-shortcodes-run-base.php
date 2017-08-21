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
	 * Check If Shortcode Has Force Category Page Parameter
	 */
    public function shortcode()
    {
        $this->force_cat_page = ! empty($_GET['lo-cat-page']) ? $_GET['lo-cat-page'] : $this->att('force_cat_slug');
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

            $arr['page_link']['main']   = '';
            $arr['page_link']['search'] = get_permalink($this->page_settings['lo_events_page_lo_search_page']) . '?' . $page_query;
            $arr['page_link']['cat']    = get_permalink($this->page_settings['lo_events_page_lo_category_page']) . '?' . $page_query;

            foreach ($this->page_settings['lo_events_page_cat_base_page_mapping'] as $map_index => $map_value)
            {
                if ($this->force_cat_page == $map_value['category'])
                {
                    $arr['page_link']['main'] = get_permalink($map_value['page']);
                    break;
                }
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