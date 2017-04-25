<?php
    /**
     * Liquid Outreach Ccb Events Sync to Post.
     *
     * @since   0.0.9
     * @package Liquid_Outreach
     */
    
    
    /**
     * Liquid Outreach Ccb Events Sync to Post class.
     *
     * @since 0.0.9
     */
    class LO_Ccb_Events_Sync_Post extends Lo_Abstract
    {
        
        /**
         * Page title.
         *
         * @var    string
         * @since  0.0.9
         */
        protected $title = '';
        
        /**
         * page key, and page slug.
         *
         * @var    string
         * @since  0.0.9
         */
        protected $key = 'liquid_outreach_ccb_events_sync_post';
        
        /**
         * Options Page hook.
         *
         * @var string
         * @since  0.0.9
         */
        protected $options_page = '';
        
        /**
         * allowed post action
         *
         * @since  0.0.9
         * @var array
         */
        private $acceptable_post_action
            = array(
                'liquid_outreach_ccb_events_sync_post',
            );
        
        /**
         * @since  0.0.9
         * @var bool
         */
        private $form_submitted = false;
        
        /**
         * @since  0.0.9
         * @var bool
         */
        private $form_handle_status = false;
        
        /**
         * Constructor.
         *
         * @since  0.0.9
         * @param  Liquid_Outreach $plugin Main plugin object.
         */
        public function __construct($plugin)
        {
            // Set our title.
            $this->title = esc_attr__('Liquid Outreach Ccb Events Sync 2', 'liquid-outreach');
            
            parent::__construct($plugin);
        }
        
        /**
         * Initiate our hooks.
         *
         * @since  0.0.9
         */
        public function hooks()
        {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_js'));
            add_action('wp_ajax_lo_admin_ajax_save_ccb_events_post', array($this, 'check_post_action'));
            add_action('admin_menu', array($this, 'add_admin_menu_page'));
        }
        
        /**
         * check if post action is valid
         *
         * @since  0.0.9
         */
        public function check_post_action()
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
         * @since  0.0.9
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
         * @since  0.0.9
         */
        public function admin_page_display()
        {
            ?>
            
            <div class="wrap cmb2-options-page <?php echo esc_attr($this->key); ?>">
                <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            </div>
            
            <?php
        }
        
        /**
         * include page specific js
         *
         * @param $hook
         *
         * @since 0.0.9
         */
        public function admin_enqueue_js($hook)
        {
            if ('lo-events_page_liquid_outreach_ccb_events_sync_post' != $hook) {
                return;
            }
            
            wp_enqueue_script('block-ui-js',
                $this->plugin->url . '/assets/bower/blockUI/jquery.blockUI.js');
        }
        
        /**
         * Option page form handler
         *
         * @since  0.0.9
         */
        protected function liquid_outreach_ccb_events_sync_post_handler()
        {
            $this->plugin->lo_ccb_api_event_profiles->api_map();
            $api_error = $this->plugin->lo_ccb_api_event_profiles->api_error;
            
            if (empty($api_error)) {
                
                $request
                    = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['request'];
                $response
                    = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['response'];
                $request_arguments = $request['parameters']['argument'];
                $page_arguments = $this->search_for_sub_arr('name', 'page', $request_arguments);
                
                if (!empty($response['events']['count'])) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'lo_ccb_events_api_data';
                    
                    foreach ($response['events']['event'] as $index => $event) {
                        $exist = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_event_id = ".$event['id'],
                            ARRAY_A);
    
                        if ( null !== $exist ) {
                            
                            if($exist['md5_hash'] != md5(json_encode($event))) {
                                $wpdb->replace(
                                    'table',
                                    array(
                                        'data' => $json_event = json_encode($event),
                                        'md5_hash' => md5($json_event),
                                        'last_modified' => date('Y-m-d H:i:s', time()),
                                    )
                                );
                            }
                        
                        } else {
    
                            $wpdb->insert( $table_name, array(
                                'ccb_event_id' => $event['id'],
                                'data' => $json_event = json_encode($event),
                                'md5_hash' => md5($json_event),
                                'created' => date('Y-m-d H:i:s', time()),
                                'last_modified' => date('Y-m-d H:i:s', time()),
                            )  );
                        }
                    }
                }
                
                echo json_encode([
                    'error'        => !empty($api_error),
                    'success'      => empty($api_error),
                    'current_page' => $page_arguments['value'],
                    'next_page'    => empty($response['events']['count']) ? false : ($page_arguments['value'] +
                                                                                     1)
                ]);
                
            } else {
                
                echo json_encode([
                        'error'        => !empty($api_error),
                        'success'      => empty($api_error),
                        'details'      => $api_error,
                        'current_page' => empty($_POST['page']) ? 1 : $_POST['page'],
                    ]
                );
            }
            
            die();
        }
        
    }
