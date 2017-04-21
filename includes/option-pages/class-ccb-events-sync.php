<?php
    /**
     * Liquid Outreach Ccb Events Sync.
     *
     * @since   0.0.3
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb Events Sync class.
     *
     * @since 0.0.3
     */
    class LO_Ccb_Events_Sync
    {
        /**
         * Parent plugin class.
         *
         * @var    Liquid_Outreach
         * @since  0.0.3
         */
        protected $plugin = null;
        
        /**
         * Option key, and option page slug.
         *
         * @var    string
         * @since  0.0.3
         */
        protected $key = 'liquid_outreach_ccb_events_sync';
        
        /**
         * Options page metabox ID.
         *
         * @var    string
         * @since  0.0.3
         */
        protected $metabox_id = 'liquid_outreach_ccb_events_sync_metabox';
        
        /**
         * Options Page title.
         *
         * @var    string
         * @since  0.0.3
         */
        protected $title = '';
        
        /**
         * Options Page hook.
         *
         * @var string
         */
        protected $options_page = '';
        
        /**
         * Constructor.
         *
         * @since  0.0.3
         *
         * @param  Liquid_Outreach $plugin Main plugin object.
         */
        public function __construct($plugin)
        {
            $this->plugin = $plugin;
            $this->hooks();
            
            // Set our title.
            $this->title = esc_attr__('Liquid Outreach Ccb Events Sync', 'liquid-outreach');
        }
        
        /**
         * Initiate our hooks.
         *
         * @since  0.0.3
         */
        public function hooks()
        {
            
            // Hook in our actions to the admin.
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'add_options_page'));
            
            add_action('cmb2_admin_init', array($this, 'add_options_page_metabox'));
            
        }
        
        /**
         * Register our setting to WP.
         *
         * @since  0.0.3
         */
        public function admin_init()
        {
            register_setting($this->key, $this->key);
        }
        
        /**
         * Add menu options page.
         *
         * @since  0.0.3
         */
        public function add_options_page()
        {
            $this->options_page = add_submenu_page(
                'edit.php?post_type=lo-events',
                $this->title,
                $this->title,
                'manage_options',
                $this->key,
                array($this, 'admin_page_display')
            );
            
            // Include CMB CSS in the head to avoid FOUC.
            add_action("admin_print_styles-{$this->options_page}",
                array('CMB2_hookup', 'enqueue_cmb_css'));
        }
        
        /**
         * Admin page markup. Mostly handled by CMB2.
         *
         * @since  0.0.3
         */
        public function admin_page_display()
        {
            ?>
            <div class="wrap cmb2-options-page <?php echo esc_attr($this->key); ?>">
                <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
                <?php cmb2_metabox_form($this->metabox_id, $this->key); ?>
            </div>
            <?php
        }
        
        /**
         * Add custom fields to the options page.
         *
         * @since  0.0.3
         */
        public function add_options_page_metabox()
        {
            
            // Add our CMB2 metabox.
            $cmb = new_cmb2_box(array(
                'id'         => $this->metabox_id,
                'hookup'     => false,
                'cmb_styles' => false,
                'show_on'    => array(
                    // These are important, don't remove.
                    'key'   => 'options-page',
                    'value' => array($this->key),
                ),
            ));
            
            // Add your fields here.
            $cmb->add_field(array(
                'name'    => __('Test Text', 'liquid-outreach'),
                'desc'    => __('field description (optional)', 'liquid-outreach'),
                'id'      => 'test_text', // No prefix needed.
                'type'    => 'text',
                'default' => __('Default Text', 'liquid-outreach'),
            ));
            
        }
    }
