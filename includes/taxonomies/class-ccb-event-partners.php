<?php
    /**
     * Liquid Outreach Ccb Event Partners.
     *
     * @since   0.0.2
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb Event Partners.
     *
     * @since 0.0.2
     *
     * @see   https://github.com/WebDevStudios/Taxonomy_Core
     */
    class LO_Ccb_Event_Partners extends Taxonomy_Core
    {
        
        /**
         * The identifier for this object
         *
         * @var string
         */
        protected $id = 'partners';
        
        /**
         * Parent plugin class.
         *
         * @var    Liquid_Outreach
         * @since  0.0.2
         */
        protected $plugin = null;
        
        /**
         * Constructor.
         *
         * Register Taxonomy.
         *
         * See documentation in Taxonomy_Core, and in wp-includes/taxonomy.php.
         *
         * @since  0.0.2
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
                    __('Event Partner', 'liquid-outreach'),
                    __('Event Partners', 'liquid-outreach'),
                    'lo-event-partner',
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
         * @since 0.0.2
         */
        public function hooks()
        {
            add_action('cmb2_admin_init', array($this, 'fields'));
        }
        
        /**
         * Add custom fields to the CPT
         *
         * @since  0.0.2
         * @return void
         */
        public function fields()
        {
            
            $prefix = 'lo_ccb_partners_';
            
            $cmb = $this->new_cmb2(array(
                'id'           => 'lo_event_partners_metabox',
                'taxonomies'   => array($this->taxonomy()),
                'object_types' => array('term'),
                'fields'       => array(
                    'partner_image'    => array(
                        'name' => __('Image', 'liquid-outreach'),
                        'desc' => __('', 'liquid-outreach'),
                        'id'   => $prefix . 'image',
                        'type' => 'file'
                    ),
                    'partner_location' => array(
                        'name'       => __('Location', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'location',
                        'type'       => 'text',
                        'repeatable' => true,
                    ),
                    'website' => array(
                        'name'       => __('Website', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'website',
                        'type'       => 'text_url',
                    ),
                    'team_leader' => array(
                        'name'       => __('Team Leader', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'team_leader',
                        'type'       => 'text',
                    ),
                    'phone' => array(
                        'name'       => __('Phone', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'phone',
                        'type'       => 'text',
                    ),
                    'email' => array(
                        'name'       => __('Email', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'email',
                        'type'       => 'text_email',
                    ),
                    'list_of_projects' => array(
                        'name'       => __('List of Projects', 'liquid-outreach'),
                        'desc'       => __('', 'liquid-outreach'),
                        'id'         => $prefix . 'list_of_projects',
                        'type'       => 'text',
                    ),
                ),
            ));
        }
        
        public function new_cmb2($args)
        {
            $cmb_id = $args['id'];
            
            return new_cmb2_box(apply_filters("lo_cmb2_box_args_{$this->id}_{$cmb_id}", $args));
        }
        
        /**
         * Magic getter for our object. Allows getting but not setting.
         *
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
