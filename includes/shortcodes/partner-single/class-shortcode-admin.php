<?php

/**
 * Liquid Outreach Partner Single Shortcode - Admin
 *
 * @since   0.11.2
 * @package Liquid Outreach
 */
class LO_Shortcodes_Partner_Single_Admin extends LO_Shortcodes_Admin_Base
{

    /**
     * Shortcode prefix for field ids.
     *
     * @var   string
     * @since 0.11.2
     */
    protected $prefix = 'lo_partner_single';

    public static function show_partner_posts()
    {
        $query = new WP_Query(
            $array = array(
                'post_type' => 'lo-event-partners',
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
     * Sets up the button
     *
     * @since 0.11.2
     * @return array
     */
    function js_button_data()
    {
        return array(
            'qt_button_text' => __('LO Partner Single', 'liquid-outreach'),
            'button_tooltip' => __('Partner Single', 'liquid-outreach'),
            'icon' => 'dashicons-media-interactive',
            // 'mceView'        => true, // The future
        );
    }

    /**
     * Adds fields to the button modal using CMB2
     *
     * @since 0.11.2
     *
     * @param $fields
     * @param $button_data
     *
     * @return array
     */
    function fields($fields, $button_data)
    {

        $fields[] = array(
            'name' => 'Select Partner',
            'desc' => 'Select Partner',
            'id' => 'partner_id',
            'type' => 'select',
            'options_cb' => ['LO_Shortcodes_Partner_Single_Admin', 'show_partner_posts'],
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
