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
    class LO_Ccb_Events_Sync extends Lo_Abstract
    {
        
        /**
         * @since  0.0.6
         * @var string
         */
        public static $lo_ccb_events_sync_form = 'lo_ccb_events_sync_form';
        /**
         * Page title.
         *
         * @var    string
         * @since  0.0.3
         */
        protected $title = '';
        /**
         * page key, and page slug.
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
        protected $metabox_id = '_liquid_outreach_ccb_events_sync_metabox';
        /**
         * Options Page hook.
         *
         * @var string
         */
        protected $options_page = '';
        /**
         * allowed post action
         *
         * @since  0.0.6
         * @var array
         */
        private $acceptable_post_action
            = array(
                'liquid_outreach_ccb_events_sync',
            );
        /**
         * @since  0.0.6
         * @var bool
         */
        private $form_submitted = false;
        
        /**
         * @since  0.0.6
         * @var bool
         */
        private $form_handle_status = false;
        
        /**
         * Constructor.
         *
         * @since  0.0.3
         * @param  Liquid_Outreach $plugin Main plugin object.
         */
        public function __construct($plugin)
        {
            parent::__construct($plugin);
            
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
            $this->check_post_action();
            add_action('admin_menu', array($this, 'add_admin_menu_page'));
            add_action('cmb2_admin_init', array($this, 'add_options_page_metabox'));
        }
        
        /**
         * check if post action is valid
         *
         * @since  0.0.6
         */
        private function check_post_action()
        {
            // If no form submission, bail
            if (empty($_POST)) {
                return false;
            }
            
            // check required $_POST variables and security nonce
            if (
                !isset($_POST['submit-cmb'], $_POST['object_id'], $_POST['nonce_CMB2php' .
                                                                         $this->metabox_id])
                || !wp_verify_nonce($_POST['nonce_CMB2php' . $this->metabox_id],
                    'nonce_CMB2php' . $this->metabox_id)
            ) {
                return new WP_Error('security_fail', __('Security check failed.'));
            }
            
            $this->form_submitted = true;
            $nonce = sanitize_text_field($_POST['nonce_CMB2php' . $this->metabox_id]);
            $action = sanitize_text_field($_POST['object_id']);
            
            if (!in_array($action, $this->acceptable_post_action)) {
                return new WP_Error('security_fail', __('Post action failed.'));
            }
    
            $method_key = str_replace('-', '_', $action) . '_handler';
            $this->form_handle_status = $this->{$method_key}();
        }
        
        /**
         * add admin menu page
         *
         * @since  0.0.6
         */
        public function add_admin_menu_page()
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
                <?php
                    cmb2_metabox_form($this->metabox_id, $this->key);
                ?>
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
                'id'           => $this->metabox_id,
                'object_types' => array('post'),
                'hookup'       => false,
                'save_fields'  => false,
                'cmb_styles'   => false,
            ));
            
            // Add your fields here.
            $cmb->add_field(array(
                'name' => __('Fetch Events From Date', 'liquid-outreach'),
                'desc' => __('All events created or modified since the date will be synced',
                    'liquid-outreach'),
                'id'   => 'modified_since', // No prefix needed.
                'type' => 'text_date',
            ));
            
        }
    
        /**
         * Option page form handler
         *
         * @since  0.0.6
         */
        protected function liquid_outreach_ccb_events_sync_handler() {
            p($_POST);
        }
    }
