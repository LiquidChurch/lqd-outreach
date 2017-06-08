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

        $defaults = array(
            'labels' => array(),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'rewrite' => array('hierarchical' => $hierarchical, 'slug' => $this->taxonomy),
        );

        $this->taxonomy_args = wp_parse_args($this->arg_overrides, $defaults);
        $this->taxonomy_args['labels'] = wp_parse_args($this->taxonomy_args['labels'], $labels);

        if(!empty($this->taxonomy_capabilities)) {
            $this->taxonomy_args['capabilities'] = $this->taxonomy_capabilities;
        }

        return $this->taxonomy_args;
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
}
