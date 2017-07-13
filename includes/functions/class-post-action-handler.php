<?php
    /**
     * Liquid Outreach post action handler.
     *
     * @since   0.11.5
     * @package Liquid_Outreach
     */
    
    /**
     * Liquid Outreach post action handler class.
     *
     * @since 0.11.5
     *
     */
    class LO_Ccb_Post_Action_Handler
    {
        /**
         * Parent plugin class.
         *
         * @var Liquid_Outreach
         * @since  0.11.5
         */
        protected $plugin;
    
        /**
         * LO_Ccb_Post_Action_Handler constructor.
         *
         * @param $plugin
         * @since  0.11.5
         */
        public function __construct($plugin) {
    
            $this->plugin =  $plugin;
            
            add_filter('bulk_actions-edit-' . $this->plugin->lo_ccb_events->post_type(), array($this, 'add_custom_bulk_actions'));
            add_filter('bulk_actions-edit-' . $this->plugin->lo_ccb_event_partners->post_type(), array($this, 'add_custom_bulk_actions'));
            add_action('load-edit.php', array($this, 'do_custom_bulk_actions'));
        }
    
        /**
         * Add custom bulk action
         *
         * @since  0.11.5
         *
         * @param $actions
         * @return mixed
         */
        public function add_custom_bulk_actions($actions)
        {
            $actions['publish'] = 'Publish';
        
            return $actions;
        }
    
        /**
         * Excecute custom bulk action
         *
         * @since  0.11.5
         *
         * @param $actions
         * @return mixed
         */
        public function do_custom_bulk_actions()
        {
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();
            $sendback = admin_url() . 'edit.php?post_type=' . $_REQUEST['post_type'];
        
            if (!empty($action)) {
            
                check_admin_referer('bulk-posts');
            
                switch ($action) {
                    case 'publish':
                        // if we set up user permissions/capabilities, the code might look like:
                        //if ( !current_user_can($post_type_object->cap->export_post, $post_id) )
                        //  pp_die( __('You are not allowed to export this post.') );
                    
                        $published = 0;
                    
                        if (empty($_REQUEST['post'])) {
                            break;
                        }
                    
                        foreach ($_REQUEST['post'] as $post_id) {
                        
                            $current_post = get_post($post_id, 'ARRAY_A');
                            $current_post['post_status'] = 'publish';
                        
                            wp_update_post($current_post);
                        
                            $published++;
                        }
                    
                        // build the redirect url
                        $sendback = add_query_arg(array(
                            'published' => $published,
                            'ids'      => join(',', $_REQUEST['post'])
                        ), $sendback);
                    
                        break;
                    default:
                        return;
                }
            
                wp_redirect($sendback);
            
                exit();
            }
        }
    }