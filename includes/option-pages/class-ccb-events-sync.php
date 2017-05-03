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
                'lo_admin_ajax_sync_ccb_events',
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
            // Set our title.
            $this->title = esc_attr__('Liquid Outreach Ccb Events Sync', 'liquid-outreach');
            
            parent::__construct($plugin);
        }
        
        /**
         * Initiate our hooks.
         *
         * @since  0.0.3
         */
        public function hooks()
        {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_js'));
            add_action('wp_ajax_lo_admin_ajax_fetch_ccb_events',
                array($this, 'fetch_ccb_events_ccb_ajax_callback'));
            add_action('wp_ajax_lo_admin_ajax_sync_ccb_events',
                array($this, 'sync_ccb_events_ccb_ajax_callback'));
            add_action('admin_menu', array($this, 'add_admin_menu_page'));
            add_action('cmb2_admin_init', array($this, 'add_options_page_metabox'));
        }
        
        /**
         * check if post action is valid
         * and process data for fetch ccb events
         *
         * @since  0.0.6
         */
        public function fetch_ccb_events_ccb_ajax_callback()
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
            <style>
                .hide-obj {
                    display: none !important;
                }
            </style>

            <div class="wrap cmb2-options-page <?php echo esc_attr($this->key); ?>">
                <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
                <?php
                    cmb2_metabox_form($this->metabox_id, $this->key);
                ?>
            </div>

            <script type="text/javascript">

                (function ($) {

                    var blockui_msg = $('<h2>' +
                        '<img style="width: 25px; vertical-align: middle;" src="<?php echo $this->plugin->url .
                                                                                           '/assets/images/spinner.svg'?>" /> ' +
                        'Please Wait...</h2>' +
                        '<hr/>' +
                        '<h3 class="lo-page-det" style="color:blue;">Fetching page <span>1</span></h3>' +
                        '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                    $(document).ready(function () {

                        $('#' + '<?php echo $this->metabox_id ?>').on('submit', function (e) {
                            e.preventDefault();

                            var nonce = {
                                key: 'nonce_CMB2php' + '<?php echo $this->metabox_id ?>',
                                value: $('#' + 'nonce_CMB2php' + '<?php echo $this->metabox_id ?>').val()
                            };

                            var data = {
                                'action': 'lo_admin_ajax_fetch_ccb_events',
                                'submit-cmb': $('[name="submit-cmb"]').attr('value'),
                                'object_id': $('[name="object_id"]').val(),
                                'modified_since': $('#modified_since').val()
                            };
                            data[nonce['key']] = nonce['value'];

                            $(blockui_msg[2]).find('span').html('1');
                            ccb_event_ajax_call(data);
                        });

                    });

                    var ccb_event_ajax_call = function (data) {

                        if (typeof data['page'] == 'undefined') {
                            $.blockUI({
                                message: blockui_msg
                            });
                        }

                        var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
                        $.ajax({
                            url: ajax_url,
                            method: 'POST',
                            data: data,
                            dataType: "json"
                        }).done(function (res) {
                            if (res.error == false && res.success == true) {
                                if (res.next_page != false) {
                                    data['page'] = res.next_page;
                                    $(blockui_msg[3]).addClass('hide-obj');
                                    $(blockui_msg[2]).find('span').html(data['page']);
                                    ccb_event_ajax_call(data);
                                } else {
                                    $.unblockUI();
                                    alert('All data has been fetched and saved to table temporarily, This page will auto refresh after clicking the OK button (if not then please refresh the page) and options to sync the data to WP Post will appear.');
                                    location.reload();
                                }
                            } else {
                                $(blockui_msg[3]).addClass('hide-obj');
                                data['page'] = res.current_page;
                                ccb_event_ajax_call(data);
                            }
                        });
                    }

                })(jQuery);

            </script>
            
            <?php
            $this->show_sync_details();
        }
        
        /**
         * Show sync details for events
         *
         * @since 0.0.9
         */
        protected function show_sync_details()
        {
            $sync_data = $this->check_sync_data();
            if (empty($sync_data['num_rows'])) {
                return false;
            }
            ?>
            <br/>
            <hr/>

            <div class="wrap cmb2-options-page-2">
                <div class="cmb2-wrap form-table">
                    <div class="cmb-row">
                        <div class="cmb-th">
                            <label for=""><?php echo esc_html__('Fetched Data Details',
                                    'liquid-outreach') ?></label>
                        </div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th">
                            <label for="">
                                <?php echo esc_html__('Total Data',
                                    'liquid-outreach') ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
                                <?php echo esc_html__('Synced Data',
                                    'liquid-outreach') ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
                                <?php echo esc_html__('Updated Data',
                                    'liquid-outreach') ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
                                <?php echo esc_html__('New Data',
                                    'liquid-outreach') ?>
                            </label>
                        </div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th"><?php echo $sync_data['num_rows'] ?></div>
                        <div class="cmb-th"><?php echo count($sync_data['data']['synced_data']) ?></div>
                        <div class="cmb-th"><?php echo count($sync_data['data']['updated_data']) ?></div>
                        <div class="cmb-th"><?php echo count($sync_data['data']['new_data']) ?></div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th">
                            <button type="buton" data-ccb-events="all"
                                    class="button-primary lo-sync-ccb-event"
                                    style="text-align: center;">
                                Sync All
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-events="synced"
                                    class="button-primary lo-sync-ccb-event"
                                    style="text-align: center;"
                                <?php echo(0 ==
                                           count($sync_data['data']['synced_data']) ? 'disabled' : '') ?>
                            >ReSync Existing
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-events="updated"
                                    class="button-primary lo-sync-ccb-event"
                                    style="text-align: center;"
                                <?php echo(0 ==
                                           count($sync_data['data']['updated_data']) ? 'disabled' : '') ?>
                            >Sync Updated
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-events="new"
                                    class="button-primary lo-sync-ccb-event"
                                    style="text-align: center;"
                                <?php echo(0 ==
                                           count($sync_data['data']['new_data']) ? 'disabled' : '') ?>
                            >Sync New
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                (function ($) {

                    $(document).ready(function () {

                        var blockui_msg_event_sync;

                        var ccb_events_data = {
                            'all': <?php echo json_encode($sync_data['data']['all_data']) ?>,
                            'synced': <?php echo json_encode($sync_data['data']['synced_data']) ?>,
                            'updated': <?php echo json_encode($sync_data['data']['updated_data']) ?>,
                            'new': <?php echo json_encode($sync_data['data']['new_data']) ?>
                        };

                        $('.lo-sync-ccb-event').on('click', function (e) {

                            e.preventDefault();

                            var ccb_data = ccb_events_data[$(this).data('ccb-events')];
                            var total_data = ccb_data.length, offset = 0, limit = 100;
                            var ccb_data_chunk = ccb_data.slice(offset, (offset + limit));
                            var nonce = '<?php echo wp_create_nonce('nonce_lo_sync_ccb_event'); ?>';

                            blockui_msg_event_sync = $('<h2>' +
                                '<img style="width: 25px; vertical-align: middle;" src="<?php echo $this->plugin->url .
                                                                                                   '/assets/images/spinner.svg'?>" /> ' +
                                'Please Wait...</h2>' +
                                '<hr/>' +
                                '<h3 class="lo-page-det" style="color:blue;">Syncing <span class="lo-sync-span">' + (offset + 1) + ' - ' + (offset + 100) + ' of ' + total_data + '</span></h3>' +
                                '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                            var data = {
                                'action': 'lo_admin_ajax_sync_ccb_events',
                                'nonce': nonce,
                                'data': ccb_data_chunk,
                                'offset': offset,
                                'limit': limit
                            };
                            
                            $(blockui_msg_event_sync[2]).find('span.lo-sync-span').html((offset + 1) + ' - ' + (offset + 100) + ' of ' + total_data);

                            ccb_event_sync_ajax_call(data, blockui_msg_event_sync);
                        });

                        var ccb_event_sync_ajax_call = function (data, blockui_msg) {

                            if (typeof data['offset'] == 'undefined' || data['offset'] == 0) {
                                $.blockUI({
                                    message: blockui_msg
                                });
                            }

                            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
                            $.ajax({
                                url: ajax_url,
                                method: 'POST',
                                data: data,
                                dataType: "json"
                            }).done(function (res) {
                                
                                console.log(res);
                                return;
                                
                                if (res.error == false && res.success == true) {
                                    if (res.next_page != false) {
                                        data['page'] = res.next_page;
                                        $(blockui_msg[3]).addClass('hide-obj');
                                        $(blockui_msg[2]).find('span').html(data['page']);
                                        ccb_event_ajax_call(data);
                                    } else {
                                        $.unblockUI();
                                        alert('All data has been fetched and saved to table temporarily, This page will auto refresh after clicking the OK button (if not then please refresh the page) and options to sync the data to WP Post will appear.');
                                        location.reload();
                                    }
                                } else {
                                    $(blockui_msg[3]).addClass('hide-obj');
                                    data['page'] = res.current_page;
                                    ccb_event_ajax_call(data);
                                }
                            });
                        }

                    });

                })(jQuery);
            </script>
            <?php
        }
    
        /**
         * check if post action is valid
         * and process data for ajax call sync ccb events
         *
         * @since  0.1.2
         */
        public function sync_ccb_events_ccb_ajax_callback()
        {
            // If no form submission, bail
            if (empty($_POST)) {
                return false;
            }
        
            // check required $_POST variables and security nonce
            if (
                !isset($_POST['action'], $_POST['nonce'], $_POST['data'])
                || !wp_verify_nonce($_POST['nonce'],
                    'nonce_lo_sync_ccb_event')
            ) {
                return new WP_Error('security_fail', __('Security check failed.'));
            }
        
            $this->form_submitted = true;
            $nonce = sanitize_text_field($_POST['nonce']);
            $action = sanitize_text_field($_POST['action']);
        
            if (!in_array($action, $this->acceptable_post_action)) {
                return new WP_Error('security_fail', __('Post action failed.'));
            }
        
            $method_key = str_replace('-', '_', $action) . '_handler';
            $this->form_handle_status = $this->{$method_key}();
        }
    
        /**
         * ccb event sync handler method
         *
         * @since 0.1.2
         */
        public function lo_admin_ajax_sync_ccb_events_handler() {
        
        }
        
        /**
         * Check event sync data
         *
         * @since 0.0.9
         */
        protected function check_sync_data()
        {
            global $wpdb;
            $results
                = $wpdb->get_results('SELECT `id`, `ccb_event_id`, `wp_post_id`, `data`, `md5_hash`, `last_modified`, `last_synced` FROM ' .
                                     $wpdb->prefix . 'lo_ccb_events_api_data', ARRAY_A);
            
            $data = [
                'all_data'     => [],
                'synced_data'  => [],
                'updated_data' => [],
                'new_data'     => []
            ];
            
            if (!empty($results)) {
                foreach ($results as $index => $result) {
                    
                    if (empty($result['data'])) {
                        continue;
                    }
                    
                    $api_data = json_decode($result['data'], 1);
                    
                    $val = [
                        'title'              => $api_data['name'],
                        'description'        => (isset($api_data['description']) && !empty($api_data['description'])) ? $api_data['description'] : '',
                        'kid_friendly'       => (isset($api_data['registration']['event_type']['id']) &&
                                                 ($api_data['registration']['event_type']['id'] ==
                                                  '1')) ? true : false,
                        'organizer_id'       => (isset($api_data['organizer']['id'])) ? $api_data['organizer']['id'] : null,
                        'registration_limit' => (isset($api_data['registration']['limit'])) ? $api_data['registration']['limit'] : null,
                        'start_time'         => (isset($api_data['start_datetime'])) ? $api_data['start_datetime'] : null,
                        'group_id'           => (isset($api_data['group']['id'])) ? $api_data['group']['id'] : null,
                        'address'            => (isset($api_data['location']) && !empty($api_data['location'])) ? $api_data['location'] : null,
                    ];
                    
                    $data['all_data'][] = $val;
                    
                    if (!empty($result['wp_post_id'])) {
                        
                        $data['synced_data'][] = $val;
                        
                        $last_modified = strtotime($result['last_modified']);
                        $last_synced = strtotime($result['last_synced']);
                        
                        if ($last_modified > $last_synced) {
                            $data['updated_data'][] = $val;
                        }
                    } else {
                        
                        $data['new_data'][] = $val;
                    }
                }
                
            } else {
                return null;
            }
            
            return [
                'num_rows' => $wpdb->num_rows,
                'data'     => $data,
            ];
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
                'name'    => __('Fetch Events From Date', 'liquid-outreach'),
                'desc'    => __('All events created or modified since the date will be synced',
                    'liquid-outreach'),
                'id'      => 'modified_since', // No prefix needed.
                'type'    => 'text_date',
                'default' => !empty($_POST['modified_since']) ? $_POST['modified_since'] : ''
            ));
            
        }
        
        /**
         * include page specific js
         *
         * @param $hook
         *
         * @since 0.0.7
         */
        public function admin_enqueue_js($hook)
        {
            if ('lo-events_page_liquid_outreach_ccb_events_sync' != $hook) {
                return;
            }
            
            wp_enqueue_script('underscore');
            
            wp_enqueue_script('block-ui-js',
                $this->plugin->url . '/assets/bower/blockUI/jquery.blockUI.js');
        }
        
        /**
         * Option page form handler
         *
         * @since  0.0.6
         */
        protected function liquid_outreach_ccb_events_sync_handler()
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
                        $exist = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_event_id = " .
                                                $event['id'],
                            ARRAY_A);
                        
                        if (null !== $exist) {
                            
                            if ($exist['md5_hash'] != md5(json_encode($event))) {
                                $wpdb->replace(
                                    'table',
                                    array(
                                        'data'          => $json_event = json_encode($event),
                                        'md5_hash'      => md5($json_event),
                                        'last_modified' => date('Y-m-d H:i:s', time()),
                                    )
                                );
                            }
                            
                        } else {
                            
                            $wpdb->insert($table_name, array(
                                'ccb_event_id'  => $event['id'],
                                'data'          => $json_event = json_encode($event),
                                'md5_hash'      => md5($json_event),
                                'created'       => date('Y-m-d H:i:s', time()),
                                'last_modified' => date('Y-m-d H:i:s', time()),
                            ));
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
        
        /**
         * @param $id
         * @param $value
         * @param $array
         * @return int|null|string
         *
         * @since  0.0.6
         */
        function search_for_sub_arr($id, $value, $array)
        {
            foreach ($array as $key => $val) {
                if ($val[$id] === $value) {
                    return $val;
                }
            }
            
            return null;
        }
    }
