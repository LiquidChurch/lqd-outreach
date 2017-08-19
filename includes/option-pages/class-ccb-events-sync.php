<?php
/**
 * Liquid Outreach CCB Sync.
 *
 * Provides the options page used to sync events and partner organizations from CCB.
 * Handles much of the actual syncing from temp tables to actual table.
 *
 * @since   0.0.3
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach CCB Sync class.
 *
 * @since 0.0.3
 */
class LO_Ccb_Events_Sync extends Lo_Abstract
{

    /**
     * @since  0.0.6
     * @var string $lo_ccb_events_sync_form
     */
    public static $lo_ccb_events_sync_form = 'lo_ccb_events_sync_form';

    /**
     * Page title.
     *
     * @var    string $title
     * @since  0.0.3
     */
    protected $title = '';

    /**
     * page key, and page slug.
     *
     * @var    string $key
     * @since  0.0.3
     */
    protected $key = 'liquid_outreach_ccb_events_sync';

    /**
     * Options page metabox ID.
     *
     * @var    string $metabox_id
     * @since  0.0.3
     */
    protected $metabox_id = '_liquid_outreach_ccb_events_sync_metabox';

    /**
     * Options Page hook.
     *
     * @var string $options_page
     */
    protected $options_page = '';

    /**
     * Allowed Post Actions
     *
     * @since  0.0.6
     * @var array $acceptable_post_action
     */
    private $acceptable_post_action
        = array(
            'liquid_outreach_ccb_events_sync',
            'lo_admin_ajax_sync_ccb_events',
            'lo_admin_ajax_delete_ccb_events',
        );

    /**
     * Whether Form Has Been Submitted Variable
     *
     * @since  0.0.6
     * @var bool $form_submitted
     */
    private $form_submitted = FALSE;

    /**
     * Form Handle Status Variable
     *
     * @since  0.0.6
     * @var bool $form_handle_status
     */
    private $form_handle_status = FALSE;

    /**
     * Transient Key List Array
     *
     * For groups, group types, and departments
     *
     * @since 0.3.6
     * @var array $transient_key
     */
    private $transient_key
        = [
            'groups_list'     => 'ccb_groups_api_data_groups_list',
            'group_type_list' => 'ccb_groups_api_data_group_type_list',
            'department_list' => 'ccb_groups_api_data_departments_list',
        ];

    /**
     * Does upload directory exist? Variable
     *
     * @since 0.24.0
     * @var bool $upload_dir_exists
     */
    private $upload_dir_exists = FALSE;

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
        $this->title = esc_attr__('CCB Sync', 'liquid-outreach');

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
        add_action('wp_ajax_lo_admin_ajax_fetch_ccb_events', array($this, 'fetch_ccb_events_ccb_ajax_callback'));
        add_action('wp_ajax_lo_admin_ajax_sync_ccb_events', array($this, 'sync_ccb_events_ccb_ajax_callback'));
        add_action('wp_ajax_lo_admin_ajax_delete_ccb_events', array($this, 'delete_ccb_events_ccb_ajax_callback'));
        add_action('admin_menu', array($this, 'add_admin_menu_page'));
        add_action('cmb2_admin_init', array($this, 'add_options_page_metabox'));
        add_action('before_delete_post', array($this, 'update_api_data_table'));
        add_action('lo_ccb_cron_event_member_sync', array($this, 'cron_event_member_sync_func'));
    }

    /**
     * Check Post Action and Process Data for Fetch CCB Events
     *
     * Checks if the Post Action is valid and processes data fetched from CCB Events
     *
     * @since  0.0.6
     */
    public function fetch_ccb_events_ccb_ajax_callback()
    {
        // If no form submission, bail
        if (empty($_POST))
        {
            return FALSE;
        }

        // check required $_POST variables and security nonce
        if (
            ! isset($_POST['submit-cmb'], $_POST['object_id'], $_POST['nonce_CMB2php' .
                                                                      $this->metabox_id])
            || ! wp_verify_nonce($_POST['nonce_CMB2php' . $this->metabox_id],
                'nonce_CMB2php' . $this->metabox_id)
        )
        {
            return new WP_Error('security_fail', __('Security check failed.'));
        }

        $this->form_submitted = TRUE;
        $nonce                = sanitize_text_field($_POST['nonce_CMB2php' .
                                                           $this->metabox_id]);
        $action               = sanitize_text_field($_POST['object_id']);

        if ( ! in_array($action, $this->acceptable_post_action))
        {
            return new WP_Error('security_fail', __('Post action failed.'));
        }

        $method_key               = str_replace('-', '_', $action) . '_handler';
        $this->form_handle_status = $this->{$method_key}();
    }

    /**
     * Add Admin Menu Page and Enqueue CSS
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

        <div class="wrap ccb-options-page <?php echo esc_attr($this->key); ?>">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            <?php
            cmb2_metabox_form($this->metabox_id, $this->key);
            ?>
        </div>

        <script type="text/javascript">

            (function ($)
            {

                var blockui_msg = $('<h2>' +
                    '<img style="width: 25px; vertical-align: middle;" src="<?php echo Liquid_Outreach::$url .
                                                                                       '/assets/images/spinner.svg'?>" /> ' +
                    'Please Wait...</h2>' +
                    '<hr/>' +
                    '<h3 class="lo-page-det" style="color:blue;">Fetching Page <span>1</span></h3>' +
                    '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                $(document).ready(function ()
                {

                    $('#' + '<?php echo $this->metabox_id ?>').on('submit', function (e)
                    {
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

                var ccb_event_ajax_call = function (data)
                {

                    if (typeof data['page'] == 'undefined')
                    {
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
                    }).done(function (res)
                    {
                        if (res.error == false && res.success == true)
                        {
                            if (res.next_page != false)
                            {
                                data['page'] = res.next_page;
                                $(blockui_msg[3]).addClass('hide-obj');
                                $(blockui_msg[2]).find('span').html(data['page']);
                                ccb_event_ajax_call(data);
                            } else
                            {
                                $.unblockUI();
                                alert('All data has been fetched and saved to a table temporarily. This page will auto refresh after you click the OK button (if not then please refresh the page) and options to sync the data to WP Post will appear.');
                                location.reload();
                            }
                        } else
                        {
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
     * Show Sync Buttons with Sync Stats, Also Delete Buttons
     *
     * Displays the total number of records retrieved by CCB Sync,
     * the number that are already synced, those already synced who
     * have updated data, and those records which are new
     *
     * @since 0.0.9
     */
    protected function show_sync_details()
    {
        $sync_data       = $this->check_sync_data();
        $group_list      = $this->get_group_list();
        $group_type_list = $this->get_group_type_list();
        $department_list = $this->get_department_list();
        if (empty($sync_data['num_rows']))
        {
            return FALSE;
        }
        ?>
        <br/>
        <hr/>

        <div class="wrap ccb-options-page">
            <div class="cmb2-wrap form-table">
                <div class="cmb-row">
                    <div class="cmb-th">
                        <h2><?php echo esc_html__('Sync Type',
                                'liquid-outreach') ?></h2>
                    </div>
                </div>

                <div class="cmb-row ccb-sync-type-row">
                    <div class="cmb-th">
                        <label>Bulk</label>
                        <input type="radio" name="ccb-sync-type" class="ccb-sync-type" id="ccb-sync-type-bulk"
                               data-toggle="ccb-bulk" checked/>
                    </div>
                    <div class="cmb-th">
                        <label>Individual(s)</label>
                        <input type="radio" name="ccb-sync-type" class="ccb-sync-type"
                               id="ccb-sync-type-individuals" data-toggle="ccb-individuals"/>
                    </div>
                </div>
            </div>

            <div class="cmb2-wrap form-table">
                <div class="ccb-sync-bulk-form">
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
                                if ( ! empty($group_list))
                                {
                                    foreach ($group_list as $index => $item)
                                    {
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
                                if ( ! empty($group_type_list))
                                {
                                    foreach ($group_type_list as $index => $item)
                                    {
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
                                if ( ! empty($department_list))
                                {
                                    foreach ($department_list as $index => $item)
                                    {
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

                    <div class="sync-btns">
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
                                <button type="button" data-ccb-events="all"
                                        class="button-primary lo-sync-ccb-event"
                                        style="text-align: center;">
                                    Sync All
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="synced"
                                        class="button-primary lo-sync-ccb-event"
                                        style="text-align: center;"
                                    <?php echo(0 ==
                                               count($sync_data['data']['synced_data']) ? 'disabled' : '') ?>
                                >ReSync Existing
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="updated"
                                        class="button-primary lo-sync-ccb-event"
                                        style="text-align: center;"
                                    <?php echo(0 ==
                                               count($sync_data['data']['updated_data']) ? 'disabled' : '') ?>
                                >Sync Updated
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="new"
                                        class="button-primary lo-sync-ccb-event"
                                        style="text-align: center;"
                                    <?php echo(0 ==
                                               count($sync_data['data']['new_data']) ? 'disabled' : '') ?>
                                >Sync New
                                </button>
                            </div>
                        </div>
                        <div class="cmb-row" style="text-align: center;">
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="all"
                                        class="button-primary lo-delete-ccb-event lo-btn-danger">
                                    Delete All
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="synced"
                                        class="button-primary lo-delete-ccb-event lo-btn-danger"
                                    <?php echo(0 ==
                                               count($sync_data['data']['synced_data']) ? 'disabled' : '') ?>
                                >Delete Existing
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="updated"
                                        class="button-primary lo-delete-ccb-event lo-btn-danger"
                                    <?php echo(0 ==
                                               count($sync_data['data']['updated_data']) ? 'disabled' : '') ?>
                                >Delete Updated
                                </button>
                            </div>
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="new"
                                        class="button-primary lo-delete-ccb-event lo-btn-danger"
                                    <?php echo(0 ==
                                               count($sync_data['data']['new_data']) ? 'disabled' : '') ?>
                                >Delete New
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ccb-sync-individuals-form" style="display: none;">
                    <div class="cmb-row">
                        <div class="cmb-th">
                            <h2><?php echo esc_html__('Select Posts',
                                    'liquid-outreach') ?></h2>
                        </div>
                    </div>

                    <div class="ccb-form-elem">
                        <div class="cmb-row">
                            <div class="cmb-th" style="width: 400px;">
                                <select id="ccb-sync-select-posts" class="ccb-sync-select-posts" multiple>
                                    <?php
                                    $args   = [
                                        'post_type'      => "lo-events",
                                        'posts_per_page' => -1,
                                        'meta_key'       => "lo_ccb_events_start_date",
                                        'orderby'        => "meta_value_num",
                                        'order'          => "ASC",
                                    ];
                                    $events = new WP_Query($args);
                                    while ($events->have_posts())
                                    {
                                        $events->the_post();
                                        global $post;
                                        echo '<option value="' . $post->ID . '">' . $post->post_title .
                                             '</option>';
                                    }
                                    wp_reset_postdata();
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="cmb-row">
                            <div class="cmb-th">
                                <button type="button" data-ccb-events="all"
                                        class="button-primary lo-sync-ccb-individuals-event"
                                        style="text-align: center;">
                                    Sync
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            (function ($)
            {

                $(document).ready(function ()
                {

                    var blockui_msg_event_sync;

                    /***************sync button func******************/
                    $('.lo-sync-ccb-event').on('click', function (e)
                    {

                        e.preventDefault();

                        var confirm_sync = confirm('Are you sure want to process this sync ?');
                        if (!confirm_sync)
                        {
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

                    /***************delete button func******************/
                    $('.lo-delete-ccb-event').on('click', function (e)
                    {
                        e.preventDefault();

                        var confirm_del = confirm('Are you sure want to proceed with this request? ' +
                            'All related data from the wp_post and the plugin table will be deleted!');
                        if (!confirm_del)
                        {
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
                        var nonce = '<?php echo wp_create_nonce('nonce_lo_delete_ccb_event'); ?>';

                        blockui_msg_event_sync = $('<h2>' +
                            '<img style="width: 25px; vertical-align: middle;" src="<?php echo Liquid_Outreach::$url .
                                                                                               '/assets/images/spinner.svg'?>" /> ' +
                            'Please Wait...</h2>' +
                            '<hr/>' +
                            '<h3 class="lo-page-det" style="color:blue;">Syncing <span class="lo-sync-span">' + (offset + 1) + ' - ' + (offset + limit) + ' of ' + total_data + '</span></h3>' +
                            '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                        var data = {
                            'action': 'lo_admin_ajax_delete_ccb_events',
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

                        ccb_event_delete_ajax_call(data, blockui_msg_event_sync, ccb_data);
                    });

                    /***************individuals sync button func******************/
                    $('.lo-sync-ccb-individuals-event').on('click', function (e)
                    {

                        e.preventDefault();

                        if ($("#ccb-sync-select-posts").val() == null)
                        {
                            alert('Please select an event from the dropdown and continue.');
                            return;
                        }

                        var confirm_sync = confirm('Are you sure want to process this sync?');
                        if (!confirm_sync)
                        {
                            return;
                        }

                        var ccb_events_data = <?php echo json_encode($sync_data['data']['all_data']) ?>;
                        var ccb_data = [];
                        var selected_event_ids = $("#ccb-sync-select-posts").val();

                        $(ccb_events_data).each(function (i, v)
                        {
                            if ($.inArray(v.wp_post_id, selected_event_ids) != '-1')
                            {
                                ccb_data.push(v);
                            }
                        });

                        if (ccb_data.length == 0)
                        {
                            alert('Selected event doesn\'t exist in CCB API table');
                            return;
                        } else
                        {

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
                        }
                    });

                    /**********ajax handler for ccb post sync*************/
                    var ccb_event_sync_ajax_call = function (data, blockui_msg, ccb_data)
                    {

                        if (typeof data['offset'] == 'undefined' || data['offset'] == 0)
                        {
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
                        }).done(function (res)
                        {

                            var total_data = ccb_data.length;
                            data['offset'] = data['offset'] + data['limit'];
                            data['limit'] = data['limit'];

                            if (data['offset'] < total_data)
                            {

                                data['data'] = ccb_data.slice(data['offset'], (data['offset'] + data['limit']));

                                $(blockui_msg[2]).find('span.lo-sync-span').html((data['offset'] + 1) + ' - ' + (data['offset'] + data['limit']) + ' of ' + total_data);

                                $.blockUI({
                                    message: blockui_msg
                                });

                                ccb_event_sync_ajax_call(data, blockui_msg_event_sync, ccb_data);

                            } else
                            {

                                $.unblockUI();
                                alert('Selected data has been synced to WP.');
                                location.reload();
                            }

                        });
                    }

                    /**********ajax handler for ccb post delete**************/
                    var ccb_event_delete_ajax_call = function (data, blockui_msg, ccb_data)
                    {

                        if (typeof data['offset'] == 'undefined' || data['offset'] == 0)
                        {
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
                        }).done(function (res)
                        {

                            var total_data = ccb_data.length;
                            data['offset'] = data['offset'] + data['limit'];
                            data['limit'] = data['limit'];

                            if (data['offset'] < total_data)
                            {

                                data['data'] = ccb_data.slice(data['offset'], (data['offset'] + data['limit']));

                                $(blockui_msg[2]).find('span.lo-sync-span').html((data['offset'] + 1) + ' - ' + (data['offset'] + data['limit']) + ' of ' + total_data);

                                $.blockUI({
                                    message: blockui_msg
                                });

                                ccb_event_delete_ajax_call(data, blockui_msg_event_sync, ccb_data);

                            } else
                            {

                                $.unblockUI();
                                alert('Selected data has been delete.');
                                location.reload();
                            }

                        });
                    }

                    $("#ccb_event_sync_to_post_metabox").on('submit', function (e)
                    {
                        e.preventDefault();
                        return false;
                    });

                    $("#ccb_event_sync_to_post_metabox input[name='submit-cmb']").remove();

                    /*********ccb sync type toggle**********/
                    $(".ccb-sync-type").click(function ()
                    {
                        if ($(this).data("toggle") == 'ccb-bulk')
                        {
                            $(".ccb-sync-individuals-form").hide(400, function ()
                            {
                                $(".ccb-sync-bulk-form").show(400);
                            });

                        } else
                        {
                            $(".ccb-sync-bulk-form").hide(400, function ()
                            {
                                $(".ccb-sync-individuals-form").show();
                            });
                        }
                    });

                    /********select2 for select post***********/
                    $("#ccb-sync-select-posts").select2({width: '100%'});

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
    protected function check_sync_data($event_id = NULL)
    {
        global $wpdb;
        if ($event_id == NULL)
        {
            $results = $wpdb->get_results(
                'SELECT `id`, `ccb_event_id`, `ccb_group_id`, `ccb_dep_id`, `ccb_group_type_id`, `wp_post_id`, `data`, `md5_hash`,
                                  `last_modified`, `last_synced` FROM ' . $wpdb->prefix . 'lo_ccb_events_api_data',
                ARRAY_A);
        }
        else
        {
            $results = $wpdb->get_results(
                'SELECT `id`, `ccb_event_id`, `ccb_group_id`, `ccb_dep_id`, `ccb_group_type_id`, `wp_post_id`, `data`, `md5_hash`,
                                  `last_modified`, `last_synced` FROM ' . $wpdb->prefix . 'lo_ccb_events_api_data WHERE ccb_event_id="' . $event_id . '"',
                ARRAY_A);
        }

        $data = [
            'all_data'     => [],
            'synced_data'  => [],
            'updated_data' => [],
            'new_data'     => []
        ];

        if ( ! empty($results))
        {
            foreach ($results as $index => $result)
            {

                if (empty($result['data']))
                {
                    continue;
                }

                $api_data = json_decode($result['data'], 1);

                $val = [
                    'ccb_event_id'       => $result['ccb_event_id'],
                    'group_id'           => (isset($api_data['group']['id'])) ? $api_data['group']['id'] : NULL,
                    'department_id'      => (isset($result['ccb_dep_id'])) ? $result['ccb_dep_id'] : NULL,
                    'ccb_group_type_id'  => (isset($result['ccb_group_type_id'])) ? $result['ccb_group_type_id'] : NULL,
                    'wp_post_id'         => $result['wp_post_id'],
                    'title'              => $api_data['name'],
                    'description'        => (isset($api_data['description']) &&
                                             ! empty($api_data['description'])) ? $api_data['description'] : '',
                    'kid_friendly'       => (isset($api_data['registration']['event_type']['id']) &&
                                             ($api_data['registration']['event_type']['id'] ==
                                              '1')) ? 'yes' : 'no',
                    'organizer_id'       => (isset($api_data['organizer']['id'])) ? $api_data['organizer']['id'] : NULL,
                    'registration_limit' => (isset($api_data['registration']['limit'])) ? $api_data['registration']['limit'] : NULL,
                    'registration_url'   => (isset($api_data['registration']['forms']['registration_form']['url'])) ? $api_data['registration']['forms']['registration_form']['url'] : '',
                    'start_time'         => (isset($api_data['start_datetime'])) ? $api_data['start_datetime'] : NULL,
                    'end_time'           => (isset($api_data['end_datetime'])) ? $api_data['end_datetime'] : NULL,
                    'group_name'         => (isset($api_data['group']['value'])) ? $api_data['group']['value'] : NULL,
                    'address'            => (isset($api_data['location']) &&
                                             ! empty($api_data['location'])) ? $api_data['location'] : NULL,
                ];

                $data['all_data'][] = $val;

                if ( ! empty($result['wp_post_id']))
                {

                    $data['synced_data'][] = $val;

                    $last_modified = strtotime($result['last_modified']);
                    $last_synced   = strtotime($result['last_synced']);

                    if ($last_modified > $last_synced)
                    {
                        $data['updated_data'][] = $val;
                    }
                }
                else
                {

                    $data['new_data'][] = $val;
                }
            }

        }
        else
        {
            return NULL;
        }

        return [
            'num_rows' => $wpdb->num_rows,
            'data'     => $data,
        ];
    }

    /**
     * Return a list of the groups from CCB
     * to be used as a filter when syncing
     *
     * @return array|mixed|null|object|ø $data
     * @since 0.3.6
     */
    public function get_group_list()
    {
        $transient_key = $this->transient_key['groups_list'];
        $data          = get_transient($transient_key);

        if (empty($data))
        {

            global $wpdb;
            $data = $wpdb->get_results("SELECT `ccb_group_id`, `ccb_group_name` FROM `" .
                                       $wpdb->prefix . 'lo_ccb_groups_api_data`', ARRAY_A);
            $data = wp_list_pluck($data, 'ccb_group_name', 'ccb_group_id');
            $data = array_filter(array_unique($data));
            asort($data);

            set_transient($transient_key, $data, 60 * 60 * 24); // TODO: What are these magic numbers? Look like 60 secs * 60 mins * 24 hrs. Why?
        }

        return $data;
    }

    /**
     * Return a list of the group types from CCB
     * to be used as a filter when syncing
     *
     * @return array|mixed|null|object|ø $data
     * @since 0.5.0
     */
    public function get_group_type_list()
    {
        $transient_key = $this->transient_key['group_type_list'];
        $data          = get_transient($transient_key);

        if (empty($data))
        {

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
     * Return a list of the departments from CCB
     * to be used as a filter when syncing
     *
     * @return array|mixed|null|object|ø
     * @since 0.3.6
     */
    public function get_department_list()
    {
        $transient_key = $this->transient_key['department_list'];
        $data          = get_transient($transient_key);

        if (empty($data))
        {

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
        if (empty($_POST))
        {
            return FALSE;
        }

        // check required $_POST variables and security nonce
        if (
            ! isset($_POST['action'], $_POST['nonce'], $_POST['data'])
            || ! wp_verify_nonce($_POST['nonce'],
                'nonce_lo_sync_ccb_event')
        )
        {
            return new WP_Error('security_fail', __('Security check failed.'));
        }

        $this->form_submitted = TRUE;
        $nonce                = sanitize_text_field($_POST['nonce']);
        $action               = sanitize_text_field($_POST['action']);

        if ( ! in_array($action, $this->acceptable_post_action))
        {
            return new WP_Error('security_fail', __('Post action failed.'));
        }

        $method_key               = str_replace('-', '_', $action) . '_handler';
        $this->form_handle_status = $this->{$method_key}();
    }

    /**
     * check if post action is valid
     * and process data for ajax call delete ccb events
     *
     * @since  0.22.2
     */
    public function delete_ccb_events_ccb_ajax_callback()
    {
        // If no form submission, bail
        if (empty($_POST))
        {
            return FALSE;
        }

        // check required $_POST variables and security nonce
        if (
            ! isset($_POST['action'], $_POST['nonce'], $_POST['data'])
            || ! wp_verify_nonce($_POST['nonce'],
                'nonce_lo_delete_ccb_event')
        )
        {
            return new WP_Error('security_fail', __('Security check failed.'));
        }

        $this->form_submitted = TRUE;
        $nonce                = sanitize_text_field($_POST['nonce']);
        $action               = sanitize_text_field($_POST['action']);

        if ( ! in_array($action, $this->acceptable_post_action))
        {
            return new WP_Error('security_fail', __('Post action failed.'));
        }

        $method_key               = str_replace('-', '_', $action) . '_handler';
        $this->form_handle_status = $this->{$method_key}();
    }

    /**
     * ccb event delete handler method
     * deleting data from temp table and wp_posts
     *
     * @since  0.22.2
     */
    public function lo_admin_ajax_delete_ccb_events_handler()
    {

        global $wpdb;

        if ( ! empty($_POST['data']))
        {
            foreach ($_POST['data'] as $index => $datum)
            {

                if ( ! empty($datum['wp_post_id']))
                {

                    /********
                     * check if group has multiple event posts
                     * We determine this by checking for post_type lo-events and whether
                     * there is a meta_key called lo_ccb_events with a value of the group id
                     *********/
                    $event_query = new WP_Query("post_type=lo-events&meta_key=lo_ccb_events_group_id&meta_value={$datum['group_id']}");
                    if ($event_query->have_posts())
                    {
                        $count = $event_query->post_count;

                        if ($count < 2)
                        {
                            /*******deleting related partner post**********/
                            $eventPartner_post_meta_prefix = 'lo_ccb_event_partner_';
                            $partner_query                 = new WP_Query("post_type=lo-event-partners&meta_key=" .
                                                                          $eventPartner_post_meta_prefix .
                                                                          "group_id&meta_value=" .
                                                                          $datum['group_id']);

                            if ($partner_query->have_posts())
                            {
                                $partner_query->the_post();
                                global $post;
                                wp_delete_post($post->ID);
                                wp_reset_postdata();
                            }
                            /*******deleting related partner post**********/
                        }

                    }

                    /*******deleting event post**********/
                    wp_delete_post($datum['wp_post_id']);
                }

                // Deleting the event post form the temporary PAI data table.
                $wpdb->delete($wpdb->prefix . 'lo_ccb_events_api_data',
                    ['ccb_event_id' => $datum['ccb_event_id']]);
                $wpdb->delete($wpdb->prefix . 'lo_ccb_groups_api_data',
                    ['ccb_group_id' => $datum['group_id']]);
            }
        }
    }

    /**
     * ccb event sync handler method
     * syncing data from temp table to wp_posts
     *
     * @since 0.1.2
     */
    public function lo_admin_ajax_sync_ccb_events_handler()
    {
        global $wpdb;
        $inserted               = 0;
        $updated                = 0;
        $skipped                = 0;
        $event_post_meta_prefix = 'lo_ccb_events_';
        $ccb_event_data         = $_POST['data'];
        $filter_group           = ! empty($_POST['filter_group']) ? $_POST['filter_group'] : NULL;
        $filter_group_type
                                = ! empty($_POST['filter_group_type']) ? $_POST['filter_group_type'] : NULL;
        $filter_dep             = ! empty($_POST['filter_dep']) ? $_POST['filter_dep'] : NULL;
        $start_date_filter
                                = ! empty($_POST['start_date']) ? strtotime($_POST['start_date']) : NULL;
        $end_date_filter        = ! empty($_POST['end_date']) ? strtotime($_POST['end_date']) : NULL;

        $php_max_execution_time = ini_get('max_execution_time');
        if ($php_max_execution_time < 90)
        {
            set_time_limit(90);
        }

        if ( ! empty($ccb_event_data))
        {

            foreach ($ccb_event_data as $index => $ccb_event_datum)
            {

                $start_timestamp = strtotime($ccb_event_datum['start_time']);
                $end_timestamp   = strtotime($ccb_event_datum['end_time']);

                if ( ! empty($filter_group) && $ccb_event_datum['group_id'] != $filter_group)
                {
                    $skipped++;
                    continue;
                }
                if ( ! empty($filter_dep) && $ccb_event_datum['department_id'] != $filter_dep)
                {
                    $skipped++;
                    continue;
                }
                if ( ! empty($filter_group_type) &&
                     $ccb_event_datum['ccb_group_type_id'] != $filter_group_type
                )
                {
                    $skipped++;
                    continue;
                }

                if ( ! empty($start_date_filter) && ($start_timestamp < $start_date_filter))
                {
                    continue;
                }

                if ( ! empty($end_date_filter) && ($start_timestamp > $end_date_filter))
                {
                    continue;
                }

                $this->create_partner_post($ccb_event_datum);

                //create an event post
                $event_post_data = [
                    'title'      => $this->set_post_title($ccb_event_datum['title']),
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
                        'address' => ! empty($ccb_event_datum['address']) ? (is_array($ccb_event_datum['address']) ? (implode(PHP_EOL,
                            $ccb_event_datum['address'])) : $ccb_event_datum['address']) : '',

                        $event_post_meta_prefix .
                        'city' => ! isset($ccb_event_datum['address']['city']) ? '' : $ccb_event_datum['address']['city'],

                        $event_post_meta_prefix .
                        'team_lead_id' => $ccb_event_datum['organizer_id'],

                        $event_post_meta_prefix .
                        'group_id' => $ccb_event_datum['group_id'],

                        $event_post_meta_prefix .
                        'ccb_event_id' => $ccb_event_datum['ccb_event_id'],

                        $event_post_meta_prefix .
                        'register_url' => $ccb_event_datum['registration_url'],

                        $event_post_meta_prefix .
                        'image' => $this->get_event_image($ccb_event_datum),
                    ]
                ];

                $event_organizer_data
                    = $this->get_event_organizer_data($ccb_event_datum['ccb_event_id'],
                    $ccb_event_datum['organizer_id']);

                if ($event_organizer_data['error'] == FALSE)
                {
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

                if ($ccb_event_datum['registration_limit'] == 0)
                {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                        = 'no-limit';
                }
                else if (strtotime($ccb_event_datum['start_time']) > time())
                {
                    $event_member_data = $this->fetch_event_details_api($ccb_event_datum);

                    if (!empty($event_member_data) && isset($event_member_data['events']['event']['guest_list']['guest']))
                    {
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings'] = ($ccb_event_datum['registration_limit'] - count($event_member_data['events']['event']['guest_list']['guest']));
                    }
                    else
                    {
                        $event_post_data['meta_input'][$event_post_meta_prefix . 'openings'] = $ccb_event_datum['registration_limit'];
                    }

                }
                else
                {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                        = 'expired';
                }

                if (empty($ccb_event_datum['wp_post_id']))
                {

                    $new_post = NULL;
                    $new_post = wp_insert_post([
                        'post_title'   => $event_post_data['title'],
                        'post_content' => $event_post_data['content'],
                        'post_type'    => 'lo-events',
                        'meta_input'   => $event_post_data['meta_input'],
                    ]);

                    if ( ! empty($new_post))
                    {
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

                }
                else
                {

                    $update_post = wp_update_post([
                        'ID'           => $ccb_event_datum['wp_post_id'],
                        'post_title'   => $event_post_data['title'],
                        'post_content' => $event_post_data['content'],
                        'post_type'    => 'lo-events',
                        'meta_input'   => $event_post_data['meta_input'],
                    ]);

                    if ( ! empty($update_post))
                    {
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
     * Get Event Image
     *
     * If there is an image associated with the event, we grab it and
     * upload to our WP install, this is necessary because
     * the CCB API provides expiring URLs for images
     *
     * @param $event_details
     *
     * @return string $upload_dir_url
     *
     * @since 0.24.0
     */
    public function get_event_image($event_details)
    {
        $upload         = wp_upload_dir();
        $upload_dir     = $upload['basedir'] . '/lqd-outreach' . "/event-{$event_details['ccb_event_id']}-img.jpg";
        $upload_dir_url = $upload['baseurl'] . '/lqd-outreach' . "/event-{$event_details['ccb_event_id']}-img.jpg";

        if (file_exists($upload_dir))
        {
            return $upload_dir_url;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get Partner Image
     *
     * If there is an image associated with the partner, we grab it and
     * upload to our WP install, this is necessary because
     * the CCB API provides expiring URLs for images
     *
     * @param $partner_details
     *
     * @return string $upload_dir_url
     *
     * @since 0.24.0
     */
    public function get_partner_image($partner_details)
    {
        $upload         = wp_upload_dir();
        $upload_dir     = $upload['basedir'] . '/lqd-outreach' . "/group-{$partner_details['group']['id']}-img.jpg";
        $upload_dir_url = $upload['baseurl'] . '/lqd-outreach' . "/group-{$partner_details['group']['id']}-img.jpg";

        if (file_exists($upload_dir))
        {
            return $upload_dir_url;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Create Actual Partner Post
     *
     * This function takes data from the temp table and
     * copies as a partner post
     *
     * @param $ccb_event_datum
     *
     * @since 0.3.6
     */
    public function create_partner_post($ccb_event_datum)
    {
        global $wpdb;
        $eventPartner_post_meta_prefix = 'lo_ccb_event_partner_';
        $meta_input                    = [
            $eventPartner_post_meta_prefix .
            "group_id" => $ccb_event_datum['group_id']
        ];

        $partner_api_data = $wpdb->get_row(
            'SELECT * from wp_lo_ccb_groups_api_data
                        WHERE `ccb_group_id`=' . $ccb_event_datum['group_id'],
            ARRAY_A
        );
        $partner_data     = json_decode($partner_api_data['data'], 1);

        if (isset($partner_data['count']) && $partner_data['count'] > 0)
        {

            $meta_input[$eventPartner_post_meta_prefix . 'location'] = [];

            $partner_data_address
                = isset($partner_data['group']['addresses']['address']) ? $partner_data['group']['addresses']['address'] : [];

            isset($partner_data_address['street_address']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['street_address'] : NULL;

            isset($partner_data_address['city']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['city'] : NULL;

            isset($partner_data_address['state']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['state'] : NULL;

            isset($partner_data_address['zip']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['zip'] : NULL;

            isset($partner_data_address['line_1']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['line_1'] : NULL;

            isset($partner_data_address['line_2']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'location'][]
                    = $partner_data_address['line_2'] : NULL;

            $partner_data_main_lead
                = isset($partner_data['group']['main_leader']) ? $partner_data['group']['main_leader'] : [];

            isset($partner_data_main_lead['full_name']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'team_leader']
                    = $partner_data_main_lead['full_name'] : NULL;

            isset($partner_data_main_lead['id']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'team_leader_id']
                    = $partner_data_main_lead['id'] : NULL;

            isset($partner_data_main_lead['phones']['phone']['value']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'phone']
                    = $partner_data_main_lead['phones']['phone']['value'] : NULL;

            isset($partner_data_main_lead['email']) ?
                $meta_input[$eventPartner_post_meta_prefix . 'email']
                    = $partner_data_main_lead['email'] : NULL;

            $partner_data_campus = isset($partner_data['group']['campus']) ? $partner_data['group']['campus'] : [];

            $meta_input[$eventPartner_post_meta_prefix . 'campus']    = isset($partner_data_campus['value']) ? $partner_data_campus['value'] : '';
            $meta_input[$eventPartner_post_meta_prefix . 'campus_id'] = isset($partner_data_campus['id']) ? $partner_data_campus['id'] : '';

            $meta_input[$eventPartner_post_meta_prefix . 'image'] = $this->get_partner_image($partner_data);

        }

        //create events partners post
        // check if a partner already exists
        $partner_query = new WP_Query("post_type=lo-event-partners&meta_key=" .
                                      $eventPartner_post_meta_prefix .
                                      "group_id&meta_value=" .
                                      $ccb_event_datum['group_id']);
        // if no record exists
        if ( ! $partner_query->have_posts())
        {
            $new_partner_post = wp_insert_post([
                'post_title' => $ccb_event_datum['group_name'],
                'post_type'  => 'lo-event-partners',
                'meta_input' => $meta_input
            ]);
        }
        else // if record exists
        {
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
     * Save Partner Image
     *
     * @param $partner_details
     *
     * @return  array $this
     *
     * @since 0.24.0
     */
    public function save_partner_image($partner_details)
    {
        if ( ! empty($partner_details['group']['image']))
        {

            $upload         = wp_upload_dir();
            $upload_dir     = $upload['basedir'];
            $upload_dir     = $upload_dir . '/lqd-outreach';
            $upload_dir_url = $upload['baseurl'];
            $upload_dir_url = $upload_dir_url . '/lqd-outreach';

            return $this->save_image($partner_details['group']['image'], $upload = [
                'dir'  => $upload_dir,
                'url'  => $upload_dir_url,
                'file' => "group-{$partner_details['group']['id']}-img.jpg"
            ]);
        }

        return NULL;
    }

    /**
     * fetch event details using event_profile ccb_api
     *
     * @param $ccb_event_datum
     *
     * @return string $response
     *
     * @since 0.24.0
     */
    public function fetch_event_details_api($ccb_event_datum)
    {
        $this->plugin->lo_ccb_api_event_profile->api_map([
            'event_id' => $ccb_event_datum['ccb_event_id']
        ]);

        $api_error = $this->plugin->lo_ccb_api_event_profile->api_error;

        if (empty($api_error))
        {

            $response = $this->plugin->lo_ccb_api_event_profile->api_response_arr['ccb_api']['response'];

            return $response;
        }

        return NULL;
    }

    /**
     * save event image
     *
     * @param $event_details
     *
     * @return array $this
     *
     * @since 0.24.0
     */
    public function save_event_image($event_details)
    {
        if ( ! empty($event_details['image']))
        {

            $upload          = wp_upload_dir();
            $upload_dir      = $upload['basedir'];
            $upload_dir      = $upload_dir . '/lqd-outreach';
            $upload_dir_url  = $upload['baseurl'];
            $upload_dir_url  = $upload_dir_url . '/lqd-outreach';
            $upload_dir_file = $upload_dir . "/event-{$event_details['id']}-img.jpg";

            return $this->save_image($event_details['image'], $upload = [
                'dir'  => $upload_dir,
                'url'  => $upload_dir_url,
                'file' => "event-{$event_details['id']}-img.jpg"
            ]);
        }

        return NULL;
    }

    /**
     * save file
     *
     * @param $upload
     * @param $file_to_dwnld
     *
     * @return string $return
     * @since 0.24.0
     */
    public function save_image($file_to_dwnld, $upload)
    {
        $return = NULL;

        if ( ! $this->upload_dir_exists)
        {
            if ( ! is_dir($upload['dir']))
            {
                if (mkdir($upload['dir'], 0775))
                {
                    $this->upload_dir_exists = TRUE;
                }
            }
        }

        $ch = curl_init($file_to_dwnld);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $raw         = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($contentType != 'application/xml')
        {
            if (file_exists($upload['dir']))
            {
                unlink($upload['dir']);
            }

            $fp = fopen($upload['dir'] . "/{$upload['file']}", 'x');

            if (fwrite($fp, $raw))
            {
                $return = $upload['url'] . "/{$upload['file']}";
            }

            fclose($fp);
        }

        return $return;
    }

    /**
     * set post title from mapping
     *
     * @param $title
     *
     * @return $title
     *
     * @since  0.21.2
     */
    public function set_post_title($title)
    {
        $settings_key = $this->plugin->lo_ccb_events_name_map_settings->meta_prefix;
        $map          = lo_get_option('name-map', $settings_key . 'name_mapping');

        if (empty($map))
        {
            return $title;
        }

        foreach ($map as $index => $single_map)
        {
            if (strpos(strtolower($title), strtolower($single_map['title'])) !== FALSE)
            {
                $title = $single_map['replace_title'];
                break;
            }
        }

        return $title;
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
        $api_error       = FALSE;
        $transient_key   = "ccb_event_$ccb_event_id" . "_organizer_$organizer_id" .
                           "_data";
        $cache_data      = get_transient($transient_key);
        $individual_data = [];

        if (empty($cache_data))
        {

            $this->plugin->lo_ccb_api_individual_profile->api_map([
                'organizer_id' => $organizer_id
            ]);
            $api_error = $this->plugin->lo_ccb_api_individual_profile->api_error;

            if (empty($api_error))
            {

                $request
                    = $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['request'];

                $response
                    = $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['response'];

                if ( ! empty($response['individuals']['count']))
                {

                    $individual = $response['individuals']['individual'];

                    $individual_data = [
                        'first_name' => empty($individual['first_name']) ? '' : $individual['first_name'],
                        'last_name'  => empty($individual['last_name']) ? '' : $individual['last_name'],
                        'phone'      => empty($individual['phones']['phone'][0]['value']) ? '' : $individual['phones']['phone'][0]['value'],
                        'email'      => empty($individual['email']) ? '' : $individual['email'],
                    ];

                    set_transient($transient_key, $individual_data, (60 * 60 * 24));
                }
                else
                {
                    $api_error = TRUE;
                }

            }

        }
        else
        {
            $individual_data = $cache_data;
        }

        return [
            'error'           => ! empty($api_error),
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
        $api_error      = FALSE;
        $transient_key  = "ccb_event_$ccb_event_id" . "attendance_data";
        $cache_data     = get_transient($transient_key);
        $attendees_data = [];

        if (empty($cache_data))
        {

            $this->plugin->lo_ccb_api_attendance_profile->api_map([
                'event_id'   => $ccb_event_id,
                'occurrence' => $occurrence
            ]);
            $api_error = $this->plugin->lo_ccb_api_attendance_profile->api_error;

            if (empty($api_error))
            {

                $request
                    = $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['request'];

                $response
                    = isset($this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response']) ? $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response'] : [];

                if ( ! empty($response))
                {

                    $attendees = $response['events']['event']['attendees'];

                    $attendees_data = [
                        'count'     => count($attendees),
                        'attendees' => $attendees
                    ];

                    set_transient($transient_key, $attendees_data, (60 * 60));
                }
                else
                {
                    $api_error = TRUE;
                }

            }

        }
        else
        {
            $attendees_data = $cache_data;
        }

        return [
            'error'          => ! empty($api_error),
            'attendees_data' => $attendees_data
        ];
    }

    /**
     * set post terms for event category
     *
     * @since 0.10.0
     *
     * @param $postID
     * @param $postData
     *
     * @return bool
     */
    public function set_post_category($postID, $postData)
    {
        if (is_wp_error($postID))
        {
            return FALSE;
        }

        $settings_key = $this->plugin->lo_ccb_events_partner_cat_map_settings->meta_prefix;
        $category_map = lo_get_option('cat-map', $settings_key . 'category_mapping');

        if (empty($category_map))
        {
            return FALSE;
        }

        foreach ($category_map as $index => $map)
        {
            if (strpos(strtolower($postData['title']), strtolower($map['title'])) !== FALSE)
            {
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
            'hookup'      => FALSE,
            'save_fields' => FALSE,
            'cmb_styles'  => FALSE,
        ));

        // Add your fields here.
        $cmb->add_field(array(
            'name'       => __('Fetch Events and Groups From Date', 'liquid-outreach'),
            'desc'       => __('All events created or modified since the date will be synced',
                'liquid-outreach'),
            'id'         => 'modified_since', // No prefix needed.
            'type'       => 'text_date',
            'default'    => ! empty($_POST['modified_since']) ? $_POST['modified_since'] : '',
            'attributes' => [
                'required' => 'required'
            ]
        ));

        $cmb_box_sync_post_form = new_cmb2_box(array(
            'id'          => 'ccb_event_sync_to_post_metabox',
            'hookup'      => FALSE,
            'save_fields' => FALSE,
            'cmb_styles'  => FALSE,
        ));
        $cmb_box_sync_post_form->add_field(array(
            'name'    => __('Filter by Start Date', 'liquid-outreach'),
            'desc'    => __('',
                'liquid-outreach'),
            'id'      => 'start_date', // No prefix needed.
            'type'    => 'text_date',
            'default' => ! empty($_POST['start_date']) ? $_POST['start_date'] : '',
        ));
        $cmb_box_sync_post_form->add_field(array(
            'name'    => __('Filter by End Date', 'liquid-outreach'),
            'desc'    => __('',
                'liquid-outreach'),
            'id'      => 'end_date', // No prefix needed.
            'type'    => 'text_date',
            'default' => ! empty($_POST['end_date']) ? $_POST['end_date'] : '',
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
        if ('lo-events_page_liquid_outreach_ccb_events_sync' != $hook)
        {
            return;
        }

        wp_enqueue_style('select2-css', Liquid_Outreach::$url . '/assets/bower/select2/dist/css/select2.min.css');

        wp_enqueue_script('underscore');

        wp_enqueue_script('block-ui-js', Liquid_Outreach::$url . '/assets/bower/blockUI/jquery.blockUI.js');

        wp_enqueue_script('select2-js', Liquid_Outreach::$url . '/assets/bower/select2/dist/js/select2.js');
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
        $ccb_event_id = get_post_meta($pid, 'lo_ccb_events_ccb_event_id', TRUE);
        if ( ! empty($ccb_event_id))
        {
            $wpdb->update($wpdb->prefix . 'lo_ccb_events_api_data', [
                'wp_post_id'    => NULL,
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
    public function cron_event_member_sync_func($event_id = NULL)
    {
        $event_post_meta_prefix = 'lo_ccb_events_';

        $event_data = $this->check_sync_data($event_id);

        if ( ! isset($event_data['data']['synced_data']) || empty($event_data['data']['synced_data']))
        {
            return FALSE;
        }

        $synced_data_count      = count($event_data['data']['synced_data']);
        $php_max_execution_time = ini_get('max_execution_time');
        if ($synced_data_count > 10 || $php_max_execution_time < 30)
        {
            set_time_limit($php_max_execution_time + ($synced_data_count * 2));
        }

        foreach ($event_data['data']['synced_data'] as $index => $synced_datum)
        {
            if ($synced_datum['registration_limit'] == 0)
            {
                $event_post_data['meta_input'][$event_post_meta_prefix . 'openings']
                    = 'no-limit';
            }
            else if (strtotime($synced_datum['start_time']) > time())
            {
                $event_member_data = $this->fetch_event_details_api($synced_datum);

                if (!empty($event_member_data) && isset($event_member_data['events']['event']['guest_list']['guest']))
                {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings'] = ($synced_datum['registration_limit'] - count($event_member_data['events']['event']['guest_list']['guest']));
                }
                else
                {
                    $event_post_data['meta_input'][$event_post_meta_prefix . 'openings'] = $synced_datum['registration_limit'];
                }

            }
            else
            {
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
        if ($php_max_execution_time < 90)
        {
            set_time_limit(90);
        }

        $this->plugin->lo_ccb_api_event_profiles->api_map();
        $api_error = $this->plugin->lo_ccb_api_event_profiles->api_error;

        if (empty($api_error))
        {

            $request
                               = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['request'];
            $response
                               = $this->plugin->lo_ccb_api_event_profiles->api_response_arr['ccb_api']['response'];
            $request_arguments = $request['parameters']['argument'];
            $page_arguments    = $this->search_for_sub_arr('name', 'page',
                $request_arguments);
            $group_sync_status = [];

            if ( ! empty($response['events']['count']))
            {
                global $wpdb;
                $table_name = $wpdb->prefix . 'lo_ccb_events_api_data';

                foreach ($response['events']['event'] as $index => $event)
                {
                    $this->save_event_image($event);

                    $exist = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_event_id = " .
                                            $event['id'],
                        ARRAY_A);

                    $group_sync_result   = NULL;
                    $group_sync_status[] = $group_sync_result = $this->save_ccb_groups_temp_table($event);

                    if (NULL !== $exist)
                    {

                        //                        if ($exist['md5_hash'] != md5(json_encode($event))) {
                        $wpdb->update(
                            $table_name,
                            array(
                                'ccb_group_id'      => $event['group']['id'],
                                'ccb_dep_id'        => ! empty($group_sync_result['ccb_dep_id']) ? $group_sync_result['ccb_dep_id'] : NULL,
                                'ccb_group_type_id' => ! empty($group_sync_result['ccb_group_type_id']) ? $group_sync_result['ccb_group_type_id'] : NULL,
                                'data'              => $json_event = json_encode($event),
                                'md5_hash'          => md5($json_event),
                                'last_modified'     => date('Y-m-d H:i:s', time()),
                            ),
                            array(
                                'ccb_event_id' => $event['id']
                            )
                        );
                        //                        }

                    }
                    else
                    {

                        $wpdb->insert($table_name, array(
                            'ccb_event_id'  => $event['id'],
                            'ccb_group_id'  => $event['group']['id'],
                            'ccb_dep_id'    => ! empty($group_sync_result['ccb_dep_id']) ? $group_sync_result['ccb_dep_id'] : NULL,
                            'data'          => $json_event = json_encode($event),
                            'md5_hash'      => md5($json_event),
                            'created'       => date('Y-m-d H:i:s', time()),
                            'last_modified' => date('Y-m-d H:i:s', time()),
                        ));
                    }
                }
            }

            echo json_encode([
                'error'             => ! empty($api_error),
                'success'           => empty($api_error),
                'group_sync_status' => $group_sync_status,
                'current_page'      => $page_arguments['value'],
                'next_page'         => empty($response['events']['count']) ? FALSE : ($page_arguments['value'] +
                                                                                      1)
            ]);

        }
        else
        {

            echo json_encode([
                    'error'        => ! empty($api_error),
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
        foreach ($array as $key => $val)
        {
            if ($val[$id] === $value)
            {
                return $val;
            }
        }

        return NULL;
    }

    /**
     * save group api data to temp table
     *
     * @param $event
     *
     * @return array
     * @since 0.3.5
     */
    public function save_ccb_groups_temp_table($event)
    {

        if (empty($event['group']['id']))
        {
            return [
                'error'   => TRUE,
                'success' => FALSE,
                'details' => 'No group id found in event object.',
            ];
        }

        $cached_data = get_transient('ccb_group_' . $event['group']['id'] . '_profile');

        if (empty($cached_data))
        {

            $this->plugin->lo_ccb_api_group_profile_from_id->api_map(['group_id' => $event['group']['id']]);
            $api_error = $this->plugin->lo_ccb_api_group_profile_from_id->api_error;
        }

        if (empty($api_error) || ! empty($cached_data))
        {

            if (empty($cached_data))
            {
                $response
                    = $this->plugin->lo_ccb_api_group_profile_from_id->api_response_arr['ccb_api']['response'];
                set_transient('ccb_group_' . $event['group']['id'] . '_profile', $response,
                    60 * 60);
            }
            else
            {
                $response = $cached_data;
            }

            $response_groups = $response['groups'];

            if (empty($response_groups['count']))
            {
                return [
                    'error'   => TRUE,
                    'success' => FALSE,
                    'details' => 'No group data returned.',
                ];
            }

            if (empty($cached_data) && ! empty($response_groups['group']['image']))
            {
                $this->save_partner_image($response_groups);
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'lo_ccb_groups_api_data';
            $exist      = $wpdb->get_row("SELECT * FROM $table_name WHERE ccb_group_id = " .
                                         $response_groups['group']['id'],
                ARRAY_A);

            if (NULL !== $exist)
            {

                $wpdb->update(
                    $table_name,
                    array(
                        'ccb_group_name'      => $response_groups['group']['name'],
                        'ccb_group_type_id'   => ! empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : NULL,
                        'ccb_group_type_name' => ! empty($response_groups['group']['group_type']['value']) ? $response_groups['group']['group_type']['value'] : NULL,
                        'ccb_dep_id'          => ! empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : NULL,
                        'ccb_dep_name'        => ! empty($response_groups['group']['department']['value']) ? $response_groups['group']['department']['value'] : NULL,
                        'data'                => $json_group = json_encode($response_groups),
                        'md5_hash'            => md5($json_group),
                        'last_modified'       => date('Y-m-d H:i:s', time()),
                    ),
                    array(
                        'ccb_group_id' => $response_groups['group']['id']
                    )
                );

            }
            else
            {

                $wpdb->insert($table_name, array(
                    'ccb_group_id'        => $response_groups['group']['id'],
                    'ccb_group_name'      => $response_groups['group']['name'],
                    'ccb_group_type_id'   => ! empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : NULL,
                    'ccb_group_type_name' => ! empty($response_groups['group']['group_type']['value']) ? $response_groups['group']['group_type']['value'] : NULL,
                    'ccb_dep_id'          => ! empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : NULL,
                    'ccb_dep_name'        => ! empty($response_groups['group']['department']['value']) ? $response_groups['group']['department']['value'] : NULL,
                    'data'                => $json_group = json_encode($response_groups),
                    'md5_hash'            => md5($json_group),
                    'created'             => date('Y-m-d H:i:s', time()),
                    'last_modified'       => date('Y-m-d H:i:s', time()),
                ));
            }

            return [
                'error'             => ! empty($api_error),
                'success'           => empty($api_error),
                'ccb_group_id'      => $response_groups['group']['id'],
                'ccb_dep_id'        => ! empty($response_groups['group']['department']['id']) ? $response_groups['group']['department']['id'] : NULL,
                'ccb_group_type_id' => ! empty($response_groups['group']['group_type']['id']) ? $response_groups['group']['group_type']['id'] : NULL,
            ];

        }
        else
        {

            return [
                'error'   => ! empty($api_error),
                'success' => empty($api_error),
                'details' => $api_error,
            ];
        }
    }
}
