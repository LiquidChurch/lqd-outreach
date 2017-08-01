<?php
/**
 * Liquid Outreach Ccb_event_categories.
 *
 * @since   0.1.1
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach Ccb_event_categories.
 *
 * @since 0.1.1
 *
 * @see   https://github.com/WebDevStudios/Taxonomy_Core
 */
class LO_Ccb_Event_Categories extends Taxonomy_Core
{
    /**
     * The identifier for this object
     *
     * @since  0.1.1
     * @var string
     */
    protected $id = 'event_category';

    /**
     * Parent plugin class.
     *
     * @var    Liquid_Outreach
     * @since  0.1.1
     */
    protected $plugin = null;

    /**
     * The image meta key for this taxonomy, if applicable
     *
     * @var string
     * @since  0.2.5
     */
    protected $image_meta_key = 'lo_ccb_event_category_image';
    protected $btn_color_meta_key = 'lo_ccb_event_category_btn_color';

    /**
     * @var string
     * @since 0.8.1
     */
    protected $header_image_meta_key = 'lo_ccb_event_category_header_image';

    /**
     * The default args array for self::get()
     *
     * @var array
     * @since  0.2.5
     */
    protected $term_get_args_defaults
        = array(
            'image_size' => 25,
        );

    /**
     * The default args array for self::get_many()
     *
     * @var array
     * @since  0.2.6
     */
    protected $term_get_many_args_defaults
        = array(
            'orderby' => 'name',
            'augment_terms' => true,
        );

    /**
     * @var array
     * @since 0.3.8
     */
    protected $taxonomy_capabilities = [
        'manage_terms' => 'manage_event_categories',
        'edit_terms' => 'edit_event_categories',
        'delete_terms' => 'delete_event_categories',
        'assign_terms' => 'assign_event_categories'
    ];

    /**
     * Constructor.
     *
     * Register Taxonomy.
     *
     * See documentation in Taxonomy_Core, and in wp-includes/taxonomy.php.
     *
     * @since  0.1.1
     *
     * @param  Liquid_Outreach $plugin Main plugin object.
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->hooks();

        parent::__construct(
        // Should be an array with Singular, Plural, and Registered name.
            array(
                __('Outreach Category', 'liquid-outreach'),
                __('Outreach Categories', 'liquid-outreach'),
                'event-category',
            ),
            // Register taxonomy arguments.
            array(
                'hierarchical' => false,
            ),
            // Post types to attach to.
            array(
                'lo-events',
            )
        );
    }

    /**
     * Initiate our hooks.
     *
     * @since 0.1.1
     */
    public function hooks()
    {
        add_action('cmb2_admin_init', array($this, 'fields'));
    }

    /**
     * overrides get_args because fo taxonomy capabilities functionaloity
     * @return array
     *
     * @since 0.3.8
     */
    public function get_args()
    {
        if (!empty($this->taxonomy_args)) {
            return $this->taxonomy_args;
        }

        // Hierarchical check that will be used multiple times below
        $hierarchical = true;
        if (isset($this->arg_overrides['hierarchical'])) {
            $hierarchical = (bool)$this->arg_overrides['hierarchical'];
        }

        // Generate CPT labels
        $labels = array(
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'search_items' => sprintf(__('Search %s', 'taxonomy-core'), $this->plural),
            'all_items' => sprintf(__('All %s', 'taxonomy-core'), $this->plural),
            'edit_item' => sprintf(__('Edit %s', 'taxonomy-core'), $this->singular),
            'view_item' => sprintf(__('View %s', 'taxonomy-core'), $this->singular),
            'update_item' => sprintf(__('Update %s', 'taxonomy-core'), $this->singular),
            'add_new_item' => sprintf(__('Add New %s', 'taxonomy-core'), $this->singular),
            'new_item_name' => sprintf(__('New %s Name', 'taxonomy-core'), $this->singular),
            'not_found' => sprintf(__('No %s found.', 'taxonomy-core'), $this->plural),
            'no_terms' => sprintf(__('No %s', 'taxonomy-core'), $this->plural),

            // Hierarchical stuff
            'parent_item' => $hierarchical ? sprintf(__('Parent %s', 'taxonomy-core'), $this->singular) : null,
            'parent_item_colon' => $hierarchical ? sprintf(__('Parent %s:', 'taxonomy-core'), $this->singular) : null,

            // Non-hierarchical stuff
            'popular_items' => $hierarchical ? null : sprintf(__('Popular %s', 'taxonomy-core'), $this->plural),
            'separate_items_with_commas' => $hierarchical ? null : sprintf(__('Separate %s with commas', 'taxonomy-core'), $this->plural),
            'add_or_remove_items' => $hierarchical ? null : sprintf(__('Add or remove %s', 'taxonomy-core'), $this->plural),
            'choose_from_most_used' => $hierarchical ? null : sprintf(__('Choose from the most used %s', 'taxonomy-core'), $this->plural),
        );

        $page_settings = get_option('liquid_outreach_ccb_events_page_settings');
        $slug_base = !empty($page_settings['lo_events_page_permalink_base']) ? $page_settings['lo_events_page_permalink_base'] : 'outreach';
        $categories_base = !empty($page_settings['lo_events_page_permalink_base_categories']) ? $page_settings['lo_events_page_permalink_base_categories'] : 'categories';
        $final_base = $slug_base . '/' . $categories_base;

        $defaults = array(
            'labels' => array(),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'rewrite' => array('hierarchical' => $hierarchical, 'slug' => $final_base),
        );

        $this->taxonomy_args = wp_parse_args($this->arg_overrides, $defaults);
        $this->taxonomy_args['labels'] = wp_parse_args($this->taxonomy_args['labels'], $labels);

        if(!empty($this->taxonomy_capabilities)) {
            $this->taxonomy_args['capabilities'] = $this->taxonomy_capabilities;
        }

        return $this->taxonomy_args;
    }

    /**
     * Actually registers our Taxonomy with the merged arguments
     * @since  0.1.0
     */
    public function register_taxonomy() {
        global $wp_taxonomies;

        // Register our Taxonomy
        $args = register_taxonomy( $this->taxonomy, $this->object_types, $this->get_args() );
        // If error, yell about it.
        if ( is_wp_error( $args ) ) {
            wp_die( $args->get_error_message() );
        }

        // Success. Set args to what WP returns
        $this->taxonomy_args = $wp_taxonomies[ $this->taxonomy ];

        // Add this taxonomy to our taxonomies array
        self::$taxonomies[ $this->taxonomy ] = $this;

        $flush_base_rewrite = get_option('lo_ccb_flush_base_rewrite');

        if ($flush_base_rewrite == 'flush') {
            flush_rewrite_rules();
            update_option('lo_ccb_flush_base_rewrite', FALSE);
        }
    }

    /**
     * Add custom fields to the CPT
     *
     * @since  0.1.1
     * @return void
     */
    public function fields()
    {

        $prefix = 'lo_ccb_event_category_';

        $cmb = $this->new_cmb2(array(
            'id' => 'lo_event_category_metabox',
            'taxonomies' => array($this->taxonomy()),
            'object_types' => array('term'),
            'fields' => array(
                'image' => array(
                    'name' => __('Image', 'liquid-outreach'),
                    'desc' => __('', 'liquid-outreach'),
                    'id' => $prefix . 'image',
                    'type' => 'file'
                ),
                'header_image' => array(
                    'name' => __('Header Image', 'liquid-outreach'),
                    'desc' => __('', 'liquid-outreach'),
                    'id' => $prefix . 'header_image',
                    'type' => 'file'
                ),
                'btn_color' => array(
                    'id' => $prefix . 'btn_color',
                    'name' => __('Button Color', 'liquid-outreach'),
                    'desc' => __('', 'liquid-outreach'),
                    'type' => 'colorpicker',
                    'default' => '#A5CD66'
                )
            ),
        ));
    
        $this->add_image_column( __( 'Categories Image', 'liquid-outreach' ) );

    }

    /**
     * @since 0.1.1
     *
     * @param $args
     *
     * @return CMB2
     */
    public function new_cmb2($args)
    {
        $cmb_id = $args['id'];

        return new_cmb2_box(apply_filters("lo_cmb2_box_args_{$this->id}_{$cmb_id}", $args));
    }

    /**
     * Wrapper for get_terms
     *
     * @since  0.2.6
     *
     * @param  array $args Array of arguments (passed to get_terms).
     * @param  array $single_term_args Array of arguments for LO_Ccb_Event_Categories::get().
     *
     * @return array|false Array of term objects or false
     */
    public function get_many($args = array(), $single_term_args = array())
    {
        $args = wp_parse_args($args, $this->term_get_many_args_defaults);
        $args = apply_filters("lo_get_{$this->id}_args", $args);

        $terms = self::get_terms($this->taxonomy(), $args);

        if (!$terms || is_wp_error($terms)) {
            return false;
        }

        if (
            isset($args['augment_terms'])
            && $args['augment_terms']
            && !empty($terms)
            // Don't augment for queries w/ greater than 100 terms, for perf. reasons.
            && 100 > count($terms)
        ) {
            foreach ($terms as $key => $term) {
                $terms[$key] = $this->get($term, $single_term_args);
            }
        }

        return $terms;
    }

    /**
     * @param       $term_slug
     * @param array $args
     * @param array $single_term_args
     *
     * @return array|bool|WP_Error
     * @since 0.25.0
     */
    public function get_similar_terms($term_slug, $args = array(), $single_term_args = array()) {

        $args = wp_parse_args($args, $this->term_get_many_args_defaults);
        $args = apply_filters("lo_get_{$this->id}_args", $args);

        $term = get_term_by('slug', $term_slug, $this->taxonomy());

        $posts_array = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'lo-events',
                'tax_query' => array(
                    array(
                        'taxonomy' => $this->taxonomy(),
                        'field' => 'term_id',
                        'terms' => $term->term_id,
                    )
                )
            )
        );

        if(!empty($posts_array)) {
            $posts_array = wp_list_pluck( $posts_array, 'ID' );

            $terms = wp_get_object_terms( $posts_array, 'event-category' );
        } else {
            return false;
        }

        if(empty($terms)) {
            return false;
        }

        if (
            isset($args['augment_terms'])
            && $args['augment_terms']
            && !empty($terms)
            // Don't augment for queries w/ greater than 100 terms, for perf. reasons.
            && 100 > count($terms)
        ) {
            foreach ($terms as $key => $term) {
                $terms[$key] = $this->get($term, $single_term_args);
            }
        }

        return $terms;
    }

    /**
     * Wrapper for `get_terms` to account for changes in WP 4.5 where taxonomy
     * is expected as part of the arguments.
     *
     * @since  0.2.6
     *
     * @return mixed Array of terms on success
     */
    protected static function get_terms($taxonomy, $args = array())
    {
        unset($args['augment_terms']);
        if (version_compare($GLOBALS['wp_version'], '4.5.0', '>=')) {
            $args['taxonomy'] = $taxonomy;
            $terms = get_terms($args);
        } else {
            $terms = get_terms($taxonomy, $args);
        }

        return $terms;
    }

    /**
     * Get a single term object
     *
     * @since  0.2.5
     *
     * @param  object|int $term Term id or object
     * @param  array $args Array of arguments.
     *
     * @return WP_Term|false  Term object or false
     */
    public function get($term, $args = array())
    {
        $term = isset($term->term_id) ? $term : get_term_by('id', $term, $this->taxonomy());
        if (!isset($term->term_id)) {
            return false;
        }

        $args = wp_parse_args($args, $this->term_get_args_defaults);
        $args = apply_filters("lo_get_{$this->id}_single_args", $args, $term, $this);

        $term->term_link = get_term_link($term);
        $term = $this->extra_term_data($term, $args);

        return $term;
    }

    /**
     * Sets extra term data on the the term object, including the image, if applicable
     *
     * @since  0.2.5
     *
     * @param  WP_Term $term Term object
     * @param  array $args Array of arguments.
     *
     * @return WP_Term|false
     */
    protected function extra_term_data($term, $args)
    {
        if ($this->image_meta_key) {
            $term = $this->add_image($term, $this->image_meta_key, $args['image_size']);
        }
        if ($this->header_image_meta_key) {
            $term = $this->add_image($term, $this->header_image_meta_key, 'full', 'header_');
        }
        if ($this->btn_color_meta_key) {
            $term = $this->add_btn_color($term);
        }

        return $term;
    }

    /**
     * Add term's image
     *
     * @since  0.2.8
     *
     * @param  WP_Term $term Term object
     * @param  string $size Size of the image to retrieve
     *
     * @return mixed         URL if successful or set
     */
    protected function add_image($term, $key, $size = '', $prefix = '')
    {
        if (!$key) {
            return $term;
        }

        $term->{$prefix . 'image_id'} = get_term_meta($term->term_id, $key . '_id', 1);

        if (!$term->{$prefix . 'image_id'}) {

            $term->{$prefix . 'image_url'} = get_term_meta($term->term_id, $key, 1);

            $term->{$prefix . 'image'} = $term->{$prefix . 'image_url'} ? '<img src="' . esc_url($term->{$prefix . 'image_url'}) . '" alt="' . $term->name . '"/>' : '';

            return $term;
        }

        if ($size) {
            $size = is_numeric($size) ? array($size, $size) : $size;
        }

        $term->{$prefix . 'image'} = wp_get_attachment_image($term->{$prefix . 'image_id'}, $size ? $size : 'thumbnail');

        $src = wp_get_attachment_image_src($term->{$prefix . 'image_id'}, $size ? $size : 'thumbnail');
        $term->{$prefix . 'image_url'} = isset($src[0]) ? $src[0] : '';

        return $term;
    }

    /**
     * @param $term
     *
     * @return mixed
     * @since 0.2.8
     */
    protected function add_btn_color($term)
    {
        if (!$this->btn_color_meta_key) {
            return $term;
        }

        $term->btn_color = get_term_meta($term->term_id, $this->btn_color_meta_key, 1);
        $term->btn_color = empty($term->btn_color) ? '#A5CD66' : $term->btn_color;

        return $term;
    }

    /**
     * Magic getter for our object. Allows getting but not setting.
     *
     * @since 0.1.1
     *
     * @param string $field
     *
     * @throws Exception Throws an exception if the field is invalid.
     * @return mixed
     */
    public function __get($field)
    {
        switch ($field) {
            case 'id':
                return $this->id;
            default:
                throw new Exception('Invalid ' . __CLASS__ . ' property: ' . $field);
        }
    }
    
    /**
     * Register image columns for $this->taxonomy().
     *
     * @since 0.11.4
     *
     * @param string  $img_col_title The title for the Image column.
     */
    protected function add_image_column( $img_col_title ) {
        $this->img_col_title = $img_col_title ? $img_col_title : __( 'Image', 'gc-sermons' );
        
        $tax = $this->taxonomy();
        
        add_filter( "manage_edit-{$tax}_columns", array( $this, 'add_column_header' ) );
        add_filter( "manage_{$tax}_custom_column", array( $this, 'add_column_value'  ), 10, 3 );
    }
    
    /**
     * Add the "tax-image" column to taxonomy terms list-tables.
     *
     * @since 0.11.4
     *
     * @param array $columns
     *
     * @return array
     */
    public function add_column_header( $columns = array() ) {
        $columns['tax-image'] = $this->img_col_title;
        
        return $columns;
    }
    
    /**
     * Output the value for the custom column.
     *
     * @since 0.11.4
     *
     * @param string $empty
     * @param string $custom_column
     * @param int    $term_id
     *
     * @return mixed
     */
    public function add_column_value( $empty = '', $custom_column = '', $term_id = 0 ) {
        
        // Bail if no taxonomy passed or not on the `tax-image` column
        if ( empty( $_REQUEST['taxonomy'] ) || ( 'tax-image' !== $custom_column ) || ! empty( $empty ) ) {
            return;
        }
        
        $retval = '&#8212;';
        
        // Get the term data.
        $term = $this->get( $term_id, array( 'image_size' => 'thumb' ) );
        
        // Output image if not empty.
        if ( isset( $term->image_id ) && $term->image_id ) {
            $retval = wp_get_attachment_image( $term->image_id, 'thumb', false, array(
                'style' => 'max-width:100%;height: auto;',
            ) );
            
            $link = get_edit_term_link( $term->term_id, $this->taxonomy() );
            
            if ( $link ) {
                $retval = '<a href="'. $link .'">'. $retval .'</a>';
            }
        }
        
        echo $retval;
    }
}
