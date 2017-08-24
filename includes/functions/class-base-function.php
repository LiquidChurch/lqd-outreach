<?php
/**
 * Liquid Outreach Base Function.
 *
 * @since   0.11.5
 * @package Liquid_Outreach
 */

/**
 * Liquid Outreach Base Function Class.
 *
 * @since 0.11.5
 *
 */
class LO_Ccb_Base_Function
{
    /**
     * Parent plugin class.
     *
     * @var Liquid_Outreach
     * @since  0.11.5
     */
    protected $plugin;

    /**
     * LO_Ccb_Base_Function constructor.
     *
     * @param $plugin
     *
     * @since  0.11.5
     */
    public function __construct($plugin)
    {

        $this->plugin = $plugin;

        $this->lo_ccb_post_action_handler = new LO_Ccb_Post_Action_Handler($this->plugin);
    }

    /**
     * Check Details Display Enabled
     *
     * @param $postID
     * @param $key
     *
     * @return mixed|string $settings|$show
     */
    public static function check_details_display_enabled($postID, $key)
    {
        $lo_events_info_global_settings = get_option('liquid_outreach_ccb_events_info_settings');
        if ($lo_events_info_global_settings[$key])
        {
            $show = get_post_meta($postID, $key, TRUE);
            if ($show == '')
            {

                $settings = lo_get_option('additional-info', $key);

                return ! empty($settings) ? '1' : '0';
            }
            else
            {
                return $show;
            }
        }
        else
        {
            return 0;
        }
    }

    public static function get_campus_list($cat_slug = NULL)
    {
        global $wpdb;
        $return = [];

        if ($cat_slug)
        {

            $post_ids = get_posts(array(
                'numberposts' => -1, // get all posts.
                'post_type'   => 'lo-events', // get all posts.
                'tax_query'   => array(
                    array(
                        'taxonomy' => 'event-category',
                        'field'    => 'slug',
                        'terms'    => $cat_slug,
                    ),
                ),
                'fields'      => 'ids', // Only get post IDs
            ));

            if (empty($post_ids))
            {
                $post_ids = [];
            }

            $post_ids = implode(',', $post_ids);

            $return = $wpdb->get_col($wpdb->prepare("
                                                            SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                                                            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                                                            WHERE pm.meta_key = '%s' 
                                                            AND p.post_status = '%s' 
                                                            AND p.post_type = '%s'
                                                            AND p.ID IN ($post_ids)
                                                        ", 'lo_ccb_events_campus', 'publish', 'lo-events'));

        }
        else
        {
            $return = $wpdb->get_col($wpdb->prepare("
                                                            SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                                                            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                                                            WHERE pm.meta_key = '%s' 
                                                            AND p.post_status = '%s' 
                                                            AND p.post_type = '%s'
                                                        ", 'lo_ccb_events_campus', 'publish', 'lo-events'));
        }

        return $return;
    }
}