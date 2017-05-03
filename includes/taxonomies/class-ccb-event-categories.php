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
                    __('Event Category', 'liquid-outreach'),
                    __('Event Categories', 'liquid-outreach'),
                    'lo-event-category',
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
         * Add custom fields to the CPT
         *
         * @since  0.1.1
         * @return void
         */
        public function fields()
        {
            
            $prefix = 'lo_ccb_event_category_';
            
            $cmb = $this->new_cmb2(array(
                'id'           => 'lo_event_category_metabox',
                'taxonomies'   => array($this->taxonomy()),
                'object_types' => array('term'),
                'fields'       => array(
                    'image' => array(
                        'name' => __('Image', 'liquid-outreach'),
                        'desc' => __('', 'liquid-outreach'),
                        'id'   => $prefix . 'image',
                        'type' => 'file'
                    ),
                ),
            ));
        }
        
        /**
         * @since 0.1.1
         * @param $args
         * @return CMB2
         */
        public function new_cmb2($args)
        {
            $cmb_id = $args['id'];
            
            return new_cmb2_box(apply_filters("lo_cmb2_box_args_{$this->id}_{$cmb_id}", $args));
        }
        
        /**
         * Magic getter for our object. Allows getting but not setting.
         *
         * @since 0.1.1
         * @param string $field
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
