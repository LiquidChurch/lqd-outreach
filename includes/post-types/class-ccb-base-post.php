<?php
/**
 * Liquid Outreach Ccb post base.
 *
 * @since   0.11.5
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach Ccb post base class.
 *
 * @since   0.11.5
 * @extends CPT_Core
 *
 * @see     https://github.com/WebDevStudios/CPT_Core
 */
class LO_Ccb_Base_Post extends CPT_Core
{
    /**
     * Bypass temp. cache
     *
     * @var boolean
     * @since  0.2.4
     */
    public $flush = FALSE;

    /**
     * Set meta prefix
     *
     * @var string
     * @since  0.2.4
     */
    public $meta_prefix = '';

    /**
     * Parent plugin class.
     *
     * @var Liquid_Outreach
     * @since  0.0.1
     */
    protected $plugin = NULL;

    /**
     * Set overrides processed to false
     *
     * @var bool
     * @since  0.2.4
     */
    protected $overrides_processed = FALSE;

    /**
     * The identifier for this object
     *
     * Not currently utilized
     *
     * @var string
     * @since  0.2.4
     */
    //        protected $id = '';

    /**
     * Default WP_Query Args
     *
     * @var   array $query_args
     * @since 0.2.4
     */
    protected $query_args
        = array(
            'post_type'      => 'THIS(REPLACE)',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => TRUE,
        );

    /**
     * Initiate our hooks.
     *
     * @since  0.0.1
     */
    public function hooks()
    {
        add_action('cmb2_init', array($this, 'fields'));
        add_action('pre_get_posts', array($this, 'my_campus_orderby'));
        add_filter('manage_edit-lo-event-partners_sortable_columns', array($this, 'my_sortable_campus_column'));
    }

    /**
     * Provides access to protected class properties.
     *
     * @param  string $key Specific CPT parameter to return
     *
     * @return mixed        Specific CPT parameter or array of singular, plural and registered name
     *
     * @since  0.2.4
     */
    public function post_type($key = 'post_type')
    {
        if ( ! $this->overrides_processed)
        {
            $this->filter_values();
        }

        return parent::post_type($key);
    }

    /**
     * Filter for overriding class properties
     * which will be used as post arguments
     *
     * @since  0.2.4
     */
    public function filter_values()
    {
        if ($this->overrides_processed)
        {
            return;
        }

        $args = array(
            'singular'      => $this->singular,
            'plural'        => $this->plural,
            'post_type'     => $this->post_type,
            'arg_overrides' => $this->arg_overrides,
        );

        $filtered_args = apply_filters('lo_post_types_' . $this->id, $args, $this);

        if ($filtered_args !== $args)
        {
            foreach ($args as $arg => $val)
            {
                if (isset($filtered_args[$arg]))
                {
                    $this->{$arg} = $filtered_args[$arg];
                }
            }
        }

        $this->overrides_processed = TRUE;
    }

    /**
     * Actually registers our CPT with the merged arguments
     *
     * @since  0.1.0
     */
    public function register_post_type()
    {
        // Register our CPT
        $args = register_post_type($this->post_type, $this->get_args());
        // If error, yell about it.
        if (is_wp_error($args))
        {
            wp_die($args->get_error_message());
        }

        // Success. Set args to what WP returns
        $this->cpt_args = $args;

        // Add this post type to our custom_post_types array
        self::$custom_post_types[$this->post_type] = $this;

        $flush_base_rewrite = get_option('lo_ccb_flush_base_rewrite');

        if ($flush_base_rewrite == 'flush')
        {
            flush_rewrite_rules();
            update_option('lo_ccb_flush_base_rewrite', FALSE);
        }

    }

    /**
     * Registers admin columns to display. Hooked in via CPT_Core.
     *
     * @param  array $columns Array of registered column names/labels.
     *
     * @return array          Modified array.
     *
     * @since  0.0.1
     */
    public function columns($columns)
    {
        $last = [];
        if ($this->id == 'lo-events')
        {
            $last                           = array_splice($columns, 3);
            $columns['lo-event-start-date'] = __('Event Start Date', 'liquid-outreach');
        }

        if ($this->id == 'lo-event-partners')
        {
            $last                       = array_splice($columns, 2);
            $columns['lo-event-campus'] = __('Campus', 'liquid-outreach');
        }

        return array_merge($columns, $last);
    }

    /**
     *
     * To make the campus column sortable
     *
     * @param $columns
     *
     * @return mixed
     *
     * @since  0.26.2
     */
    public function my_sortable_campus_column($columns)
    {

        $columns['lo-event-campus'] = __('Campus', 'liquid-outreach');

        return $columns;
    }

    /**
     *
     * Sort Campus Column Alphabetically
     *
     * @param $query
     *
     * @since  0.26.2
     */
    function my_campus_orderby($query)
    {
        if ( ! is_admin())
        {
            return;
        }

        $screen = get_current_screen();
        if ($screen->id != 'edit-lo-event-partners')
        {
            return;
        }


        $orderby = $query->get('orderby');

        if ('Campus' == $orderby)
        {
            $query->set('meta_key', 'lo_ccb_event_partner_campus');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Handles admin column display. Hooked in via CPT_Core.
     *
     * @param array   $column  Column currently being rendered.
     * @param integer $post_id ID of post to display column for.
     *
     * @since  0.0.1
     */
    public function columns_display($column, $post_id)
    {
        if ($this->id == 'lo-events')
        {
            if ($column == 'lo-event-start-date')
            {
                $date = get_post_meta($post_id, 'lo_ccb_events_start_date', TRUE);
                $date = ! empty($date) ? date("Y-m-d H:i", $date) : '';
                echo $date;
            }
        }

        if ($this->id == 'lo-event-partners')
        {
            if ($column == 'lo-event-campus')
            {
                $campus = get_post_meta($post_id, 'lo_ccb_event_partner_campus', TRUE);

                echo $campus;
            }
        }
    }

    /**
     * Magic getter for our object. Allows getting but not setting.
     *
     * @param string $field
     *
     * @throws Exception Throws an exception if the field is invalid.
     * @return mixed
     * @since  0.2.4
     */
    public function __get($field)
    {
        switch ($field)
        {
            case 'id':
            case 'arg_overrides':
            case 'cpt_args':
                return $this->{$field};
            default:
                throw new Exception('Invalid ' . __CLASS__ . ' property: ' . $field);
        }
    }

    /**
     * Check common page option for default if no option is saved
     *
     * @param $key
     *
     * @return bool
     * @since 0.20.0
     */
    public function global_details_page_setting($key)
    {
        if ( ! isset($_GET['post']))
        {
            return NULL;
        }
        $post_config_val = get_post_meta($_GET['post'], $key, 1);
        if ($post_config_val == '')
        {
            $post_config_val = lo_get_option('additional-info', $key);
        }

        return $post_config_val;
    }

}
