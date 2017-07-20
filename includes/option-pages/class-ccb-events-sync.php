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
         * transient key list
         *
         * @since 0.3.6
         * @var array
         */
        private $transient_key
            = [
                'groups_list'     => 'ccb_groups_api_data_groups_list',
                'group_type_list' => 'ccb_groups_api_data_group_type_list',
                'department_list' => 'ccb_groups_api_data_departments_list',
            ];
        
        /**
         * Constructor.
         *
         * @since  0.0.3
         *
         * @param  Liquid_Outreach $plugin Main plugin object.
         */
        public function __construct($plugin)
        {
            // Set our title.
            $this->title = esc_attr__('Outreach Events Sync', 'liquid-outreach');
            
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
            add_action('before_delete_post', array($this, 'update_api_data_table'));
            add_action('lo_ccb_cron_event_attendance_sync',
                array($this, 'cron_event_attendance_sync_func'));
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
            $nonce = sanitize_text_field($_POST['nonce_CMB2php' .
                                                $this->metabox_id]);
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

                #ccb_event_sync_to_post_metabox input[name='submit-cmb'] {
                    display: none;
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
                        '<img style="width: 25px; vertical-align: middle;" src="<?php echo Liquid_Outreach::$url .
                                                                                           '/assets/images/spinner.svg'?>" /> ' +
                        'Please Wait...</h2>' +
                        '<hr/>' +
                        '<h3 class="lo-page-det" style="color:blue;">Fetching Page <span>1</span></h3>' +
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
                                data['page'] = res.current_page;
                                $(blockui_msg[3]).text(res.details.error_msg + '\r\n Please refresh the page and try again.');
                                $(blockui_msg[3]).removeClass('hide-obj');
//                            ccb_event_ajax_call(data);
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
            $group_list = $this->get_group_list();
            $group_type_list = $this->get_group_type_list();
            $department_list = $this->get_department_list();
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
                            <h2><?php echo esc_html__('Fetched Data Sync Filter',
                                    'liquid-outreach') ?></h2>
                        </div>
                    </div>

                    <div class="cmb-row ccb-sync-filter-by-group-row ccb-sync-filter-by-child">
                        <div class="cmb-th">
                            <label for=""><?php echo esc_html__('Select Group',
                                    'liquid-outreach') ?></label>
                        </div>
                        <div class="cmb-th">
                            <select id="ccb-sync-filter-by-group" class="ccb-sync-filter-by-select">
                                <option value=""> --- Any ---</option>
                                <?php
                                    if (!empty($group_list)) {
                                        foreach ($group_list as $index => $item) {
                                            echo '<option value="' . $index . '">' . $item .
                                                 '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="cmb-row ccb-sync-filter-by-group-type-row ccb-sync-filter-by-child">
                        <div class="cmb-th">
                            <label for=""><?php echo esc_html__('Select Group Type',
                                    'liquid-outreach') ?></label>
                        </div>
                        <div class="cmb-th">
                            <select id="ccb-sync-filter-by-group-type"
                                    class="ccb-sync-filter-by-select">
                                <option value=""> --- Any ---</option>
                                <?php
                                    if (!empty($group_type_list)) {
                                        foreach ($group_type_list as $index => $item) {
                                            echo '<option value="' . $index . '">' . $item .
                                                 '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="cmb-row ccb-sync-filter-by-dep-row ccb-sync-filter-by-child">
                        <div class="cmb-th">
                            <label for=""><?php echo esc_html__('Select Department',
                                    'liquid-outreach') ?></label>
                        </div>
                        <div class="cmb-th">
                            <select id="ccb-sync-filter-by-dep" class="ccb-sync-filter-by-select">
                                <option value=""> --- Any ---</option>
                                <?php
                                    if (!empty($department_list)) {
                                        foreach ($department_list as $index => $item) {
                                            echo '<option value="' . $index . '">' . $item .
                                                 '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <?php
                        cmb2_metabox_form('ccb_event_sync_to_post_metabox',
                            'ccb_event_sync_to_post_key');
                    ?>

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

                        $('.lo-sync-ccb-event').on('click', function (e) {

                            e.preventDefault();

                            var confirm_sync = confirm('Are you sure want to process this sync?');
                            if (!confirm_sync) {
                                return;
                            }

                            var ccb_events_data = {
                                'all': <?php echo json_encode($sync_data['data']['all_data']) ?>,
                                'synced': <?php echo json_encode($sync_data['data']['synced_data']) ?>,
                                'updated': <?php echo json_encode($sync_data['data']['updated_data']) ?>,
                                'new': <?php echo json_encode($sync_data['data']['new_data']) ?>
                            };

                            var ccb_data = ccb_events_data[$(this).data('ccb-events')];
                            var total_data = ccb_data.length, offset = 0, limit = 10;
                            var ccb_data_chunk = ccb_data.slice(offset, (offset + limit));
                            var nonce = '<?php echo wp_create_nonce('nonce_lo_sync_ccb_event'); ?>';

                            blockui_msg_event_sync = $('<h2>' +
                                '<img style="width: 25px; vertical-align: middle;" src="<?php echo Liquid_Outreach::$url .
                                                                                                   '/assets/images/spinner.svg'?>" /> ' +
                                'Please Wait...</h2>' +
                                '<hr/>' +
                                '<h3 class="lo-page-det" style="color:blue;">Syncing <span class="lo-sync-span">' + (offset + 1) + ' - ' + (offset + limit) + ' of ' + total_data + '</span></h3>' +
                                '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                            var data = {
                                'action': 'lo_admin_ajax_sync_ccb_events',
                                'nonce': nonce,
                                'offset': offset,
                                'limit': limit,
                                'data': ccb_data_chunk,
                                'filter_group': $("#ccb-sync-filter-by-group").val(),
                                'filter_group_type': $("#ccb-sync-filter-by-group-type").val(),
                                'filter_dep': $("#ccb-sync-filter-by-dep").val(),
                                'start_date': $("#start_date").val(),
                                'end_date': $("#end_date").val()
                            };

                            $(blockui_msg_event_sync[2]).find('span.lo-sync-span').html((offset + 1) + ' - ' + (offset + limit) + ' of ' + total_data);

                            ccb_event_sync_ajax_call(data, blockui_msg_event_sync, ccb_data);
                        });

                        var ccb_event_sync_ajax_call = function (data, blockui_msg, ccb_data) {

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
                                var total_data = ccb_data.length;
                                data['offset'] = data['offset'] + data['limit'];
                                data['limit'] = data['limit'];

                                if (data['offset'] < total_data) {

                                    data['data'] = ccb_data.slice(data['offset'], (data['offset'] + data['limit']));

                                    $(blockui_msg[2]).find('span.lo-sync-span').html((data['offset'] + 1) + ' - ' + (data['offset'] + data['limit']) + ' of ' + total_data);

                                    $.blockUI({
                                        message: blockui_msg
                                    });

                                    ccb_event_sync_ajax_call(data, blockui_msg_event_sync, ccb_data);

                                } else {

                                    $.unblockUI();
                                    alert('All data has been synced to WP.');
                                    location.reload();
                                }

                            });
                        }

                        $("#ccb_event_sync_to_post_metabox").on('submit', function (e) {
                            e.preventDefault();
                            return false;
                        });

                        $("#ccb_event_sync_to_post_metabox input[name='submit-cmb']").remove();

                    });

                })(jQuery);
            </script>
            <?php
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
                = $wpdb->get_results(
                'SELECT `id`, `ccb_event_id`, `ccb_group_id`, `ccb_dep_id`, `ccb_group_type_id`, `wp_post_id`, `data`, `md5_hash`,
                                  `last_modified`, `last_synced` FROM ' . $wpdb->prefix .
                'lo_ccb_events_api_data',
                ARRAY_A);
            
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
                        'ccb_event_id'       => $result['ccb_event_id'],
                        'group_id'           => (isset($api_data['group']['id'])) ? $api_data['group']['id'] : null,
                        'department_id'      => (isset($result['ccb_dep_id'])) ? $result['ccb_dep_id'] : null,
                        'ccb_group_type_id'  => (isset($result['ccb_group_type_id'])) ? $result['ccb_group_type_id'] : null,
                        'wp_post_id'         => $result['wp_post_id'],
                        'title'              => $api_data['name'],
                        'description'        => (isset($api_data['description']) &&
                                                 !empty($api_data['description'])) ? $api_data['description'] : '',
                        'kid_friendly'       => (isset($api_data['registration']['event_type']['id']) &&
                                                 ($api_data['registration']['event_type']['id'] ==
                                                  '1')) ? 'yes' : 'no',
                        'organizer_id'       => (isset($api_data['organizer']['id'])) ? $api_data['organizer']['id'] : null,
                        'registration_limit' => (isset($api_data['registration']['limit'])) ? $api_data['registration']['limit'] : null,
                        'registration_url'   => (isset($api_data['registration']['forms']['registration_form']['url'])) ? $api_data['registration']['forms']['registration_form']['url'] : '',
                        'start_time'         => (isset($api_data['start_datetime'])) ? $api_data['start_datetime'] : null,
                        'end_time'           => (isset($api_data['end_datetime'])) ? $api_data['end_datetime'] : null,
                        'group_name'         => (isset($api_data['group']['value'])) ? $api_data['group']['value'] : null,
                        'address'            => (isset($api_data['location']) &&
                                                 !empty($api_data['location'])) ? $api_data['location'] : null,
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
         * get group list for filter
         *
         * @return array|mixed|null|object|ø
         * @since 0.3.6
         */
        public function get_group_list()
        {
            $transient_key = $this->transient_key['groups_list'];
            $data = get_transient($transient_key);
            
            if (empty($data)) {
                
                global $wpdb;
                $data = $wpdb->get_results("SELECT `ccb_group_id`, `ccb_group_name` FROM `" .
                                           $wpdb->prefix . 'lo_ccb_groups_api_data`', ARRAY_A);
                $data = wp_list_pluck($data, 'ccb_group_name', 'ccb_group_id');
                $data = array_filter(array_unique($data));
                asort($data);
                
                set_transient($transient_key, $data, 60 * 60 * 24);
            }
            
            return $data;
        }
        
        /**
         * get group type list for filter
         *
         * @return array|mixed|null|object|ø
         * @since 0.5.0
         */
        public function get_group_type_list()
        {
            $transient_key = $this->transient_key['group_type_list'];
            $data = get_transient($transient_key);
            
            if (empty($data)) {
                
                global $wpdb;
                $data
                    = $wpdb->get_results("SELECT `ccb_group_type_id`, `ccb_group_type_name` FROM `" .
                                         $wpdb->prefix . 'lo_ccb_groups_api_data`', ARRAY_A);
                $data = wp_list_pluck($data, 'ccb_group_type_name', 'ccb_group_type_id');
                $data = array_filter(array_unique($data));
                asort($data);
                
                set_transient($transient_key, $data, 60 * 60 * 24);
            }
            
            return $data;
        }
        
        /**
         * get department list for filter
         *
         * @return array|mixed|null|object|ø
         * @since 0.3.6
         */
        public function get_department_list()
        {
            $transient_key = $this->transient_key['department_list'];
            $data = get_transient($transient_key);
            
            if (empty($data)) {
                
                global $wpdb;
                $data = $wpdb->get_results("SELECT `ccb_dep_id`, `ccb_dep_name` FROM `" .
                                           $wpdb->prefix . 'lo_ccb_groups_api_data`', ARRAY_A);
                $data = wp_list_pluck($data, 'ccb_dep_name', 'ccb_dep_id');
                $data = array_filter(array_unique($data));
                asort($data);
                
                set_transient($transient_key, $data, 60 * 60 * 24);
            }
            
            return $data;
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
         * syncing data from temple table wp_posts
         *
         * @since 0.1.2
         */
        public function lo_admin_ajax_sync_ccb_events_handler()
        {
            global $wpdb;
            $inserted = 0;
            $updated = 0;
            $skipped = 0;
            $event_post_meta_prefix = 'lo_ccb_events_';
            $ccb_event_data = $_POST['data'];
            $filter_group = !empty($_POST['filter_group']) ? $_POST['filter_group'] : null;
            $filter_group_type
                = !empty($_POST['filter_group_type']) ? $_POST['filter_group_type'] : null;
            $filter_dep = !empty($_POST['filter_dep']) ? $_POST['filter_dep'] : null;
            $start_date_filter
                = !empty($_POST['start_date']) ? strtotime($_POST['start_date']) : null;
            $end_date_filter = !empty($_POST['end_date']) ? strtotime($_POST['end_date']) : null;
            
            $php_max_execution_time = ini_get('max_execution_time');
            if ($php_max_execution_time < 90) {
                set_time_limit(90);
            }
            
            if (!empty($ccb_event_data)) {
                
                foreach ($ccb_event_data as $index => $ccb_event_datum) {
                    
                    $start_timestamp = strtotime($ccb_event_datum['start_time']);
                    $end_timestamp = strtotime($ccb_event_datum['end_time']);
                    
                    if (!empty($filter_group) && $ccb_event_datum['group_id'] != $filter_group) {
                        $skipped++;
                        continue;
                    }
                    if (!empty($filter_dep) && $ccb_event_datum['department_id'] != $filter_dep) {
                        $skipped++;
                        continue;
                    }
                    if (!empty($filter_group_type) &&
                        $ccb_event_datum['ccb_group_type_id'] != $filter_group_type
                    ) {
                        $skipped++;
                        continue;
                    }
                    
                    if (!empty($start_date_filter) && ($start_timestamp < $start_date_filter)) {
                        continue;
                    }
                    
                    if (!empty($end_date_filter) && ($start_timestamp > $end_date_filter)) {
                        continue;
                    }
                    
                    $this->create_partner_post($ccb_event_datum);
                    
                    //create events post
                    $event_post_data = [
                        'title'      => $ccb_event_datum['title'],
                        'content'    => $ccb_event_datum['description'],
                        'meta_input' => [
                            $event_post_meta_prefix .
                            'kid_friendly' => $ccb_event_datum['kid_friendly'],
                            
                            $event_post_meta_prefix .
                            'start_date' => strtotime($ccb_event_datum['start_time']),
                            
                            $event_post_meta_prefix .
                            'weekday_name' => strtolower(date('l',
                                strtotime($ccb_event_datum['start_time']))),
                            
                            $event_post_meta_prefix .
                            'address' => !empty($ccb_event_datum['address']) ? (is_array($ccb_event_datum['address']) ? (implode(PHP_EOL,
                                $ccb_event_datum['address'])) : $ccb_event_datum['address']) : '',
                            
                            $event_post_meta_prefix .
                            'city' => !isset($ccb_event_datum['address']['city']) ? '' : $ccb_event_datum['address']['city'],
                            
                            $event_post_meta_prefix .
                            'team_lead_id' => $ccb_event_datum['organizer_id'],
                            
                            $event_post_meta_prefix .
                            'group_id' => $ccb_event_datum['group_id'],
                            
                            $event_post_meta_prefix .
                            'ccb_event_id' => $ccb_event_datum['ccb_event_id'],
                            
                            $event_post_meta_prefix .
                            'register_url' => $ccb_event_datum['registration_url'],
                        ]
                    ];
                    
                    $event_organizer_data
                        = $this->get_event_organizer_data($ccb_event_datum['ccb_event_id'],
                        $ccb_event_datum['organizer_id']);
                    
                    if ($event_organizer_data['error'] == false) {
                        $event_post_data['meta_input'][$event_post_meta_prefix .
                                                       'team_lead_fname']
                            = $event_organizer_data['individual_data']['first_name'];
                        
                        $event_post_data['meta_input'][$event_post_meta_prefix .
                                                       'team_lead_lname']
                            = $event_organizer_data['individual_data']['last_name'];
                        
                        $event_post_data['meta_input'][$event_post_meta_prefix .
                                                       'team_lead_email']
                            = $event_organizer_data['individual_data']['phone'];
                        
                        $event_post_data['meta_input'][$event_post_meta_prefix .
                                                       'team_lead_phone']
                            = $event_organizer_data['individual_data']['email'];
                    }
                    
                    if ($ccb_event_datum['registration_limit'] == 0) {
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                            = 'no-limit';
                    } elseif (strtotime($ccb_event_datum['start_time']) > time()) {
                        $event_attendees_data
                            = $this->get_event_attendance_data($ccb_event_datum['ccb_event_id'],
                            date('Y-m-d', strtotime($ccb_event_datum['start_time'])));
                        
                        if (empty($event_attendees_data['error'])) {
                            
                            $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                                = ($ccb_event_datum['registration_limit'] -
                                   $event_attendees_data['attendees_data']['count']);
                        } else {
                            
                            $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                                = $ccb_event_datum['registration_limit'];
                        }
                        
                    } else {
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                            = 'expired';
                    }
                    
                    if (empty($ccb_event_datum['wp_post_id'])) {
                        
                        $new_post = null;
                        $new_post = wp_insert_post([
                            'post_title'   => $event_post_data['title'],
                            'post_content' => $event_post_data['content'],
                            'post_type'    => 'lo-events',
                            'meta_input'   => $event_post_data['meta_input'],
                        ]);
                        
                        if (!empty($new_post)) {
                            $inserted++;
                            
                            $wpdb->update(
                                $wpdb->prefix . 'lo_ccb_events_api_data',
                                [
                                    'wp_post_id'    => $new_post,
                                    'last_synced'   => date('Y-m-d H:i:s', time()),
                                    'last_modified' => date('Y-m-d H:i:s', time())
                                ],
                                [
                                    'ccb_event_id' => $ccb_event_datum['ccb_event_id']
                                ]);
                        }
                        
                        $this->set_post_category($new_post, $event_post_data);
                        
                    } else {
                        
                        $update_post = wp_update_post([
                            'ID'           => $ccb_event_datum['wp_post_id'],
                            'post_title'   => $event_post_data['title'],
                            'post_content' => $event_post_data['content'],
                            'post_type'    => 'lo-events',
                            'meta_input'   => $event_post_data['meta_input'],
                        ]);
                        
                        if (!empty($update_post)) {
                            $updated++;
                            
                            $wpdb->update(
                                $wpdb->prefix . 'lo_ccb_events_api_data',
                                [
                                    'last_synced'   => date('Y-m-d H:i:s', time()),
                                    'last_modified' => date('Y-m-d H:i:s', time())
                                ],
                                [
                                    'ccb_event_id' => $ccb_event_datum['ccb_event_id']
                                ]);
                        }
                        
                        $this->set_post_category($update_post, $event_post_data);
                    }
                    
                }
            }
            
            echo json_encode([
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
            ]);
            die();
        }
        
        /**
         * create partner post when syncing events
         *
         * @param $ccb_event_datum
         * @since 0.3.6
         */
        public function create_partner_post($ccb_event_datum)
        {
            global $wpdb;
            $eventPartner_post_meta_prefix = 'lo_ccb_event_partner_';
            $meta_input = [
                $eventPartner_post_meta_prefix .
                "group_id" => $ccb_event_datum['group_id']
            ];
            
            $partner_api_data = $wpdb->get_row(
                'SELECT * from wp_lo_ccb_groups_api_data
                        WHERE `ccb_group_id`=' . $ccb_event_datum['group_id'],
                ARRAY_A
            );
            $partner_data = json_decode($partner_api_data['data'], 1);
            
            if(isset($partner_data['count']) && $partner_data['count'] > 0) {
    
                $meta_input[$eventPartner_post_meta_prefix . 'location'] = [];
                
                $partner_data_address = isset($partner_data['group']['addresses']['address']) ? $partner_data['group']['addresses']['address'] : [];
                
                isset($partner_data_address['street_address']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['street_address'] : null;
                isset($partner_data_address['city']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['city'] : null;
                isset($partner_data_address['state']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['state'] : null;
                isset($partner_data_address['zip']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['zip'] : null;
                isset($partner_data_address['line_1']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['line_1'] : null;
                isset($partner_data_address['line_2']) ? $meta_input[$eventPartner_post_meta_prefix . 'location'][] = $partner_data_address['line_2'] : null;
                
                $partner_data_main_lead = isset($partner_data['group']['main_leader']) ? $partner_data['group']['main_leader'] : [];
                
                isset($partner_data_main_lead['full_name']) ? $meta_input[$eventPartner_post_meta_prefix . 'team_leader'] = $partner_data_main_lead['full_name'] : null;
                isset($partner_data_main_lead['id']) ? $meta_input[$eventPartner_post_meta_prefix . 'team_leader_id'] = $partner_data_main_lead['id'] : null;
                isset($partner_data_main_lead['phones']['phone']['value']) ? $meta_input[$eventPartner_post_meta_prefix . 'phone'] = $partner_data_main_lead['phones']['phone']['value'] : null;
                isset($partner_data_main_lead['email']) ? $meta_input[$eventPartner_post_meta_prefix . 'email'] = $partner_data_main_lead['email'] : null;
                
            }
            
            //create events partners post
            $partner_query = new WP_Query("post_type=lo-event-partners&meta_key=" .
                                          $eventPartner_post_meta_prefix .
                                          "group_id&meta_value=" .
                                          $ccb_event_datum['group_id']);
            if (!$partner_query->have_posts()) {
                $new_partner_post = wp_insert_post([
                    'post_title' => $ccb_event_datum['group_name'],
                    'post_type'  => 'lo-event-partners',
                    'meta_input' => $meta_input
                ]);
            } else {
                $partner_query->the_post();
                global $post;
                $update_partner_post = wp_update_post([
                    'ID'         => $post->ID,
                    'post_title' => $ccb_event_datum['group_name'],
                    'meta_input' => $meta_input
                ]);
            }
        }
        
        /**
         * get organizer data either from cache or api call
         *
         * @since 0.1.5
         *
         * @param $ccb_event_id
         * @param $organizer_id
         *
         * @return array
         */
        protected function get_event_organizer_data($ccb_event_id, $organizer_id)
        {
            $api_error = false;
            $transient_key = "ccb_event_$ccb_event_id" . "_organizer_$organizer_id" .
                             "_data";
            $cache_data = get_transient($transient_key);
            $individual_data = [];
            
            if (empty($cache_data)) {
                
                $this->plugin->lo_ccb_api_individual_profile->api_map([
                    'organizer_id' => $organizer_id
                ]);
                $api_error = $this->plugin->lo_ccb_api_individual_profile->api_error;
                
                if (empty($api_error)) {
                    
                    $request
                        = $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['request'];
                    
                    $response
                        = $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['response'];
                    
                    if (!empty($response['individuals']['count'])) {
                        
                        $individual = $response['individuals']['individual'];
                        
                        $individual_data = [
                            'first_name' => empty($individual['first_name']) ? '' : $individual['first_name'],
                            'last_name'  => empty($individual['last_name']) ? '' : $individual['last_name'],
                            'phone'      => empty($individual['phones']['phone'][0]['value']) ? '' : $individual['phones']['phone'][0]['value'],
                            'email'      => empty($individual['email']) ? '' : $individual['email'],
                        ];
                        
                        set_transient($transient_key, $individual_data, (60 * 60 * 24));
                    } else {
                        $api_error = true;
                    }
                    
                }
                
            } else {
                $individual_data = $cache_data;
            }
            
            return [
                'error'           => !empty($api_error),
                'individual_data' => $individual_data
            ];
        }
        
        /**
         * get attendance data either from cache or api call
         *
         * @since 0.1.6
         *
         * @param $ccb_event_id
         * @param $occurrence
         *
         * @return array
         */
        protected function get_event_attendance_data($ccb_event_id, $occurrence)
        {
            $api_error = false;
            $transient_key = "ccb_event_$ccb_event_id" . "attendance_data";
            $cache_data = get_transient($transient_key);
            $attendees_data = [];
            
            if (empty($cache_data)) {
                
                $this->plugin->lo_ccb_api_attendance_profile->api_map([
                    'event_id'   => $ccb_event_id,
                    'occurrence' => $occurrence
                ]);
                $api_error = $this->plugin->lo_ccb_api_attendance_profile->api_error;
                
                if (empty($api_error)) {
                    
                    $request
                        = $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['request'];
                    
                    $response
                        = isset($this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response']) ? $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response'] : [];
                    
                    if (!empty($response)) {
                        
                        $attendees = $response['events']['event']['attendees'];
                        
                        $attendees_data = [
                            'count'     => count($attendees),
                            'attendees' => $attendees
                        ];
                        
                        set_transient($transient_key, $attendees_data, (60 * 60));
                    } else {
                        $api_error = true;
                    }
                    
                }
                
            } else {
                $attendees_data = $cache_data;
            }
            
            return [
                'error'          => !empty($api_error),
                'attendees_data' => $attendees_data
            ];
        }
        
        /**
         * set post terms for event category
         *
         * @since 0.10.0
         * @param $postID
         * @param $postData
         * @return bool
         */
        public function set_post_category($postID, $postData)
        {
            if (is_wp_error($postID)) {
                return false;
            }
            
            $settings_key = $this->plugin->lo_ccb_events_partner_cat_map_settings->meta_prefix;
            $category_map = lo_get_option('cat-map', $settings_key . 'category_mapping');
            
            if (empty($category_map)) {
                return false;
            }
            
            foreach ($category_map as $index => $map) {
                if (strpos(strtolower($postData['title']), strtolower($map['title'])) !== false) {
                    wp_set_object_terms($postID, $map['event_categroy'], 'event-category');
                }
            }
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
                'id'          => $this->metabox_id,
                'hookup'      => false,
                'save_fields' => false,
                'cmb_styles'  => false,
            ));
            
            // Add your fields here.
            $cmb->add_field(array(
                'name'       => __('Fetch Events and Groups From Date', 'liquid-outreach'),
                'desc'       => __('All events created or modified since the date will be synced',
                    'liquid-outreach'),
                'id'         => 'modified_since', // No prefix needed.
                'type'       => 'text_date',
                'default'    => !empty($_POST['modified_since']) ? $_POST['modified_since'] : '',
                'attributes' => [
                    'required' => 'required'
                ]
            ));
            
            $cmb_box_sync_post_form = new_cmb2_box(array(
                'id'          => 'ccb_event_sync_to_post_metabox',
                'hookup'      => false,
                'save_fields' => false,
                'cmb_styles'  => false,
            ));
            $cmb_box_sync_post_form->add_field(array(
                'name'    => __('Filter by Start Date', 'liquid-outreach'),
                'desc'    => __('',
                    'liquid-outreach'),
                'id'      => 'start_date', // No prefix needed.
                'type'    => 'text_date',
                'default' => !empty($_POST['start_date']) ? $_POST['start_date'] : '',
            ));
            $cmb_box_sync_post_form->add_field(array(
                'name'    => __('Filter by End Date', 'liquid-outreach'),
                'desc'    => __('',
                    'liquid-outreach'),
                'id'      => 'end_date', // No prefix needed.
                'type'    => 'text_date',
                'default' => !empty($_POST['end_date']) ? $_POST['end_date'] : '',
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
                Liquid_Outreach::$url . '/assets/bower/blockUI/jquery.blockUI.js');
        }
        
        /**
         * update api data table when event post is deleted
         *
         * @since 0.1.7
         *
         * @param $pid
         */
        public function update_api_data_table($pid)
        {
            global $wpdb;
            $ccb_event_id = get_post_meta($pid, 'lo_ccb_events_ccb_event_id', true);
            if (!empty($ccb_event_id)) {
                $wpdb->update($wpdb->prefix . 'lo_ccb_events_api_data', [
                    'wp_post_id'    => null,
                    'last_synced'   => date('Y-m-d H:i:s', time()),
                    'last_modified' => date('Y-m-d H:i:s', time())
                ], [
                    'ccb_event_id' => $ccb_event_id
                ]);
            }
        }
        
        /**
         * attendance sync cron job handler
         *
         * @since 0.9.0
         */
        public function cron_event_attendance_sync_func()
        {
            $event_post_meta_prefix = 'lo_ccb_events_';
            
            $event_data = $this->check_sync_data();
            
            if (!isset($event_data['data']['synced_data']) ||
                empty($event_data['data']['synced_data'])
            ) {
                return false;
            }
            
            $synced_data_count = count($event_data['data']['synced_data']);
            $php_max_execution_time = ini_get('max_execution_time');
            if ($synced_data_count > 10 || $php_max_execution_time < 30) {
                set_time_limit($php_max_execution_time + ($synced_data_count * 2));
            }
            
            foreach ($event_data['data']['synced_data'] as $index => $synced_datum) {
                if ($synced_datum['registration_limit'] == 0) {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                        = 'no-limit';
                } elseif (strtotime($synced_datum['start_time']) > time()) {
                    $event_attendees_data
                        = $this->get_event_attendance_data($synced_datum['ccb_event_id'],
                        date('Y-m-d', strtotime($synced_datum['start_time'])));
                    
                    if (empty($event_attendees_data['error'])) {
                        
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                            = ($synced_datum['registration_limit'] -
                               $event_attendees_data['attendees_data']['count']);
                    } else {
                        
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                            = $synced_datum['registration_limit'];
                    }
                    
                } else {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                        = 'expired';
                }
                
                $update_post = wp_update_post([
                    'ID'         => $synced_datum['wp_post_id'],
                    'post_type'  => 'lo-events',
                    'meta_input' => $event_post_data['meta_input'],
                ]);
                
            }
        }
        
        /**
         * Option page form handler
         * syncing API data to temp table
         *
         * @since  0.0.6
         */
        protected function liquid_outreach_ccb_events_sync_handler()
        {
            $php_max_execution_time = ini_get('max_execution_time');
            if ($php_max_execution_time < 90) {
                set_time_limit(90);
            }
            
            $this->plugin->lo_ccb_api_event_profiles->api_map();
            $api_error = $this->plugin->lo_ccb_api_event_profiles->api_error;
            
            if (empty($api_error)) {
                
                $request
                    = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['request'];
                $response
                    = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['response'];
                $request_arguments = $request['parameters']['argument'];
                $page_arguments = $this->search_for_sub_arr('name', 'page',
                    $request_arguments);
                $group_sync_status = [];
                
                if (!empty($response['events']['count'])) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'lo_ccb_events_api_data';
                    
                    foreach ($response['events']['event'] as $index => $event) {
                        
                        $exist = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_event_id = " .
                                                $event['id'],
                            ARRAY_A);
                        
                        $group_sync_result = null;
                        $group_sync_status[]
                            = $group_sync_result = $this->save_ccb_groups_temp_table($event);
                        
                        if (null !== $exist) {
                            
                            //                        if ($exist['md5_hash'] != md5(json_encode($event))) {
                            $wpdb->update(
                                $table_name,
                                array(
                                    'ccb_group_id'      => $event['group']['id'],
                                    'ccb_dep_id'        => !empty($group_sync_result['ccb_dep_id']) ? $group_sync_result['ccb_dep_id'] : null,
                                    'ccb_group_type_id' => !empty($group_sync_result['ccb_group_type_id']) ? $group_sync_result['ccb_group_type_id'] : null,
                                    'data'              => $json_event = json_encode($event),
                                    'md5_hash'          => md5($json_event),
                                    'last_modified'     => date('Y-m-d H:i:s', time()),
                                ),
                                array(
                                    'ccb_event_id' => $event['id']
                                )
                            );
                            //                        }
                            
                        } else {
                            
                            $wpdb->insert($table_name, array(
                                'ccb_event_id'  => $event['id'],
                                'ccb_group_id'  => $event['group']['id'],
                                'ccb_dep_id'    => !empty($group_sync_result['ccb_dep_id']) ? $group_sync_result['ccb_dep_id'] : null,
                                'data'          => $json_event = json_encode($event),
                                'md5_hash'      => md5($json_event),
                                'created'       => date('Y-m-d H:i:s', time()),
                                'last_modified' => date('Y-m-d H:i:s', time()),
                            ));
                        }
                    }
                }
                
                echo json_encode([
                    'error'             => !empty($api_error),
                    'success'           => empty($api_error),
                    'group_sync_status' => $group_sync_status,
                    'current_page'      => $page_arguments['value'],
                    'next_page'         => empty($response['events']['count']) ? false : ($page_arguments['value'] +
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
            
            delete_transient($this->transient_key['groups_list']);
            delete_transient($this->transient_key['group_type_list']);
            delete_transient($this->transient_key['department_list']);
            die();
        }
        
        /**
         * @param $id
         * @param $value
         * @param $array
         *
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
        
        /**
         * save group api data to temp table
         *
         * @param $event
         * @return array
         * @since 0.3.5
         */
        public function save_ccb_groups_temp_table($event)
        {
            
            if (empty($event['group']['id'])) {
                return [
                    'error'   => true,
                    'success' => false,
                    'details' => 'No group id found in event object.',
                ];
            }
            
            $cached_data = get_transient('ccb_group_' . $event['group']['id'] . '_profile');
            
            if (empty($cached_data)) {
                
                $this->plugin->lo_ccb_api_group_profile_from_id->api_map(['group_id' => $event['group']['id']]);
                $api_error = $this->plugin->lo_ccb_api_group_profile_from_id->api_error;
            }
            
            if (empty($api_error) || !empty($cached_data)) {
                
                if (empty($cached_data)) {
                    $response
                        = $this->plugin->lo_ccb_api_group_profile_from_id->api_response_arr['ccb_api']['response'];
                    set_transient('ccb_group_' . $event['group']['id'] . '_profile', $response,
                        60 * 60);
                } else {
                    $response = $cached_data;
                }
                
                $response_groups = $response['groups'];
                
                if (empty($response_groups['count'])) {
                    return [
                        'error'   => true,
                        'success' => false,
                        'details' => 'No group data returned.',
                    ];
                }
                
                global $wpdb;
                $table_name = $wpdb->prefix . 'lo_ccb_groups_api_data';
                $exist = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_group_id = " .
                                        $response_groups['group']['id'],
                    ARRAY_A);
                
                if (null !== $exist) {
                    
                    $wpdb->update(
                        $table_name,
                        array(
                            'ccb_group_name'      => $response_groups['group']['name'],
                            'ccb_group_type_id'   => !empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : null,
                            'ccb_group_type_name' => !empty($response_groups['group']['group_type']['value']) ? $response_groups['group']['group_type']['value'] : null,
                            'ccb_dep_id'          => !empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : null,
                            'ccb_dep_name'        => !empty($response_groups['group']['department']['value']) ? $response_groups['group']['department']['value'] : null,
                            'data'                => $json_group = json_encode($response_groups),
                            'md5_hash'            => md5($json_group),
                            'last_modified'       => date('Y-m-d H:i:s', time()),
                        ),
                        array(
                            'ccb_group_id' => $response_groups['group']['id']
                        )
                    );
                    
                } else {
                    
                    $wpdb->insert($table_name, array(
                        'ccb_group_id'        => $response_groups['group']['id'],
                        'ccb_group_name'      => $response_groups['group']['name'],
                        'ccb_group_type_id'   => !empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : null,
                        'ccb_group_type_name' => !empty($response_groups['group']['group_type']['value']) ? $response_groups['group']['group_type']['value'] : null,
                        'ccb_dep_id'          => !empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : null,
                        'ccb_dep_name'        => !empty($response_groups['group']['department']['value']) ? $response_groups['group']['department']['value'] : null,
                        'data'                => $json_group = json_encode($response_groups),
                        'md5_hash'            => md5($json_group),
                        'created'             => date('Y-m-d H:i:s', time()),
                        'last_modified'       => date('Y-m-d H:i:s', time()),
                    ));
                }
                
                return [
                    'error'             => !empty($api_error),
                    'success'           => empty($api_error),
                    'ccb_group_id'      => $response_groups['group']['id'],
                    'ccb_dep_id'        => !empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : null,
                    'ccb_group_type_id' => !empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : null,
                ];
                
            } else {
                
                return [
                    'error'   => !empty($api_error),
                    'success' => empty($api_error),
                    'details' => $api_error,
                ];
            }
        }
    }
