<?php

/**
 * Liquid Outreach Event Single Shortcode - Admin
 *
 * @since   0.3.1
 * @package Liquid Outreach
 */
class LO_Shortcodes_Event_Single_Admin extends LO_Shortcodes_Admin_Base
{

    /**
     * Shortcode prefix for field ids.
     *
     * @var   string
     * @since 0.3.1
     */
    protected $prefix = 'lo_event_single';

    public static function show_event_posts()
    {
        $query = new WP_Query(
            $array = array(
                'post_type' => 'lo-events',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            )
        );

        $titles[''] = '';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $titles[get_the_id()] = get_the_title();
            }
        }

        wp_reset_postdata();

        return $titles;
    }

    /**
     * Sets up the button (for TinyMCE?)
     *
     * @since 0.3.1
     * @return array
     */
    function js_button_data()
    {
        return array(
            'qt_button_text' => __('LO Event Single', 'liquid-outreach'),
            'button_tooltip' => __('Event Single', 'liquid-outreach'),
            'icon' => 'dashicons-media-interactive',
            // 'mceView'        => true, // The future
        );
    }

    /**
     * Adds fields to the button modal using CMB2
     *
     * @since 0.3.1
     *
     * @param $fields
     * @param $button_data
     *
     * @return array
     */
    function fields($fields, $button_data)
    {

        $fields[] = array(
            'name' => 'Select Event',
            'desc' => 'Select Event',
            'id' => 'event_id',
            'type' => 'select',
            'options_cb' => ['LO_Shortcodes_Event_Single_Admin', 'show_event_posts'],
        );

        $fields[] = array(
            'name' => 'Disable Header',
            'desc' => 'To disable header',
            'id' => 'disable_header',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'name' => 'Disable Nav Bar',
            'desc' => 'To disable Nav Bar',
            'id' => 'disable_nav',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'name' => 'Disable Search',
            'desc' => 'To disable search',
            'id' => 'disable_search',
            'type' => 'checkbox',
        );

        return $fields;
    }
}
