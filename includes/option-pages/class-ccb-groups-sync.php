<?php
	/**
	 * Liquid Outreach Ccb Groups Sync.
	 *
	 * @since   0.3.5
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb Groups Sync class.
	 *
	 * @since 0.3.5
	 */
	class LO_Ccb_Groups_Sync extends Lo_Abstract {
		
		/**
		 * @since  0.3.5
		 * @var string
		 */
		public static $lo_ccb_groups_sync_form = 'lo_ccb_groups_sync_form';
		
		/**
		 * Page title.
		 *
		 * @var    string
		 * @since  0.3.5
		 */
		protected $title = '';
		
		/**
		 * page key, and page slug.
		 *
		 * @var    string
		 * @since  0.3.5
		 */
		protected $key = 'liquid_outreach_ccb_groups_sync';
		
		/**
		 * Options page metabox ID.
		 *
		 * @var    string
		 * @since  0.3.5
		 */
		protected $metabox_id = '_liquid_outreach_ccb_groups_sync_metabox';
		
		/**
		 * Options Page hook.
		 *
		 * @var string
		 */
		protected $options_page = '';
		
		/**
		 * allowed post action
		 *
		 * @since  0.3.5
		 * @var array
		 */
		private $acceptable_post_action
			= array(
				'liquid_outreach_ccb_groups_sync',
				'lo_admin_ajax_sync_ccb_groups',
			);
		
		/**
		 * @since  0.3.5
		 * @var bool
		 */
		private $form_submitted = false;
		
		/**
		 * @since  0.3.5
		 * @var bool
		 */
		private $form_handle_status = false;
		
		/**
		 * Constructor.
		 *
		 * @since  0.3.5
		 *
		 * @param  Liquid_Outreach $plugin Main plugin object.
		 */
		public function __construct( $plugin ) {
			// Set our title.
			$this->title = esc_attr__( 'Outreach Groups Sync', 'liquid-outreach' );
			
			parent::__construct( $plugin );
		}
		
		/**
		 * Initiate our hooks.
		 *
		 * @since  0.3.5
		 */
		public function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_js' ) );
			add_action( 'wp_ajax_lo_admin_ajax_fetch_ccb_groups',
				array( $this, 'fetch_ccb_groups_ccb_ajax_callback' ) );
			add_action( 'wp_ajax_lo_admin_ajax_sync_ccb_groups',
				array( $this, 'sync_ccb_groups_ccb_ajax_callback' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
			add_action( 'before_delete_post', array( $this, 'update_api_data_table' ) );
		}
		
		/**
		 * check if post action is valid
		 * and process data for fetch ccb groups
		 *
		 * @since  0.3.5
		 */
		public function fetch_ccb_groups_ccb_ajax_callback() {
			// If no form submission, bail
			if ( empty( $_POST ) ) {
				return false;
			}
			
			// check required $_POST variables and security nonce
			if (
				! isset( $_POST['submit-cmb'], $_POST['object_id'], $_POST[ 'nonce_CMB2php' .
				                                                            $this->metabox_id ] )
				|| ! wp_verify_nonce( $_POST[ 'nonce_CMB2php' . $this->metabox_id ],
					'nonce_CMB2php' . $this->metabox_id )
			) {
				return new WP_Error( 'security_fail', __( 'Security check failed.' ) );
			}
			
			$this->form_submitted = true;
			$nonce                = sanitize_text_field( $_POST[ 'nonce_CMB2php' .
			                                                     $this->metabox_id ] );
			$action               = sanitize_text_field( $_POST['object_id'] );
			
			if ( ! in_array( $action, $this->acceptable_post_action ) ) {
				return new WP_Error( 'security_fail', __( 'Post action failed.' ) );
			}
			
			$method_key               = str_replace( '-', '_', $action ) . '_handler';
			$this->form_handle_status = $this->{$method_key}();
		}
		
		/**
		 * add admin menu page
		 *
		 * @since  0.3.5
		 */
		public function add_admin_menu_page() {
			$this->options_page = add_submenu_page(
				'edit.php?post_type=lo-groups',
				$this->title,
				$this->title,
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' )
			);
			
			// Include CMB CSS in the head to avoid FOUC.
			add_action( "admin_print_styles-{$this->options_page}",
				array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}
		
		/**
		 * Admin page markup. Mostly handled by CMB2.
		 *
		 * @since  0.3.5
		 */
		public function admin_page_display() {
			?>
            <style>
                .hide-obj {
                    display: none !important;
                }
            </style>

            <div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php
					cmb2_metabox_form( $this->metabox_id, $this->key );
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
                                'action': 'lo_admin_ajax_fetch_ccb_groups',
                                'submit-cmb': $('[name="submit-cmb"]').attr('value'),
                                'object_id': $('[name="object_id"]').val(),
                                'modified_since': $('#modified_since').val()
                            };
                            data[nonce['key']] = nonce['value'];

                            $(blockui_msg[2]).find('span').html('1');
                            ccb_group_ajax_call(data);
                        });

                    });

                    var ccb_group_ajax_call = function (data) {

                        if (typeof data['page'] == 'undefined') {
                            $.blockUI({
                                message: blockui_msg
                            });
                        }

                        var ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
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
                                    ccb_group_ajax_call(data);
                                } else {
                                    $.unblockUI();
                                    alert('All data has been fetched and saved to table temporarily, This page will auto refresh after clicking the OK button (if not then please refresh the page) and options to sync the data to WP Post will appear.');
                                    location.reload();
                                }
                            } else {
                                $(blockui_msg[3]).addClass('hide-obj');
                                data['page'] = res.current_page;
                                ccb_group_ajax_call(data);
                            }
                        });
                    }

                })(jQuery);

            </script>
			
			<?php
			$this->show_sync_details();
		}
		
		/**
		 * Show sync details for groups
		 *
		 * @since 0.3.5
		 */
		protected function show_sync_details() {
			$sync_data = $this->check_sync_data();
			if ( empty( $sync_data['num_rows'] ) ) {
				return false;
			}
			?>
            <br/>
            <hr/>

            <div class="wrap cmb2-options-page-2">
                <div class="cmb2-wrap form-table">
                    <div class="cmb-row">
                        <div class="cmb-th">
                            <label for=""><?php echo esc_html__( 'Fetched Data Details',
									'liquid-outreach' ) ?></label>
                        </div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th">
                            <label for="">
								<?php echo esc_html__( 'Total Data',
									'liquid-outreach' ) ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
								<?php echo esc_html__( 'Synced Data',
									'liquid-outreach' ) ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
								<?php echo esc_html__( 'Updated Data',
									'liquid-outreach' ) ?>
                            </label>
                        </div>
                        <div class="cmb-th">
                            <label for="">
								<?php echo esc_html__( 'New Data',
									'liquid-outreach' ) ?>
                            </label>
                        </div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th"><?php echo $sync_data['num_rows'] ?></div>
                        <div class="cmb-th"><?php echo count( $sync_data['data']['synced_data'] ) ?></div>
                        <div class="cmb-th"><?php echo count( $sync_data['data']['updated_data'] ) ?></div>
                        <div class="cmb-th"><?php echo count( $sync_data['data']['new_data'] ) ?></div>
                    </div>
                    <div class="cmb-row" style="text-align: center;">
                        <div class="cmb-th">
                            <button type="buton" data-ccb-groups="all"
                                    class="button-primary lo-sync-ccb-group"
                                    style="text-align: center;">
                                Sync All
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-groups="synced"
                                    class="button-primary lo-sync-ccb-group"
                                    style="text-align: center;"
								<?php echo( 0 ==
								            count( $sync_data['data']['synced_data'] ) ? 'disabled' : '' ) ?>
                            >ReSync Existing
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-groups="updated"
                                    class="button-primary lo-sync-ccb-group"
                                    style="text-align: center;"
								<?php echo( 0 ==
								            count( $sync_data['data']['updated_data'] ) ? 'disabled' : '' ) ?>
                            >Sync Updated
                            </button>
                        </div>
                        <div class="cmb-th">
                            <button type="buton" data-ccb-groups="new"
                                    class="button-primary lo-sync-ccb-group"
                                    style="text-align: center;"
								<?php echo( 0 ==
								            count( $sync_data['data']['new_data'] ) ? 'disabled' : '' ) ?>
                            >Sync New
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                (function ($) {

                    $(document).ready(function () {

                        var blockui_msg_group_sync;

                        $('.lo-sync-ccb-group').on('click', function (e) {

                            e.preventDefault();

                            var ccb_groups_data = {
                                'all': <?php echo json_encode( $sync_data['data']['all_data'] ) ?>,
                                'synced': <?php echo json_encode( $sync_data['data']['synced_data'] ) ?>,
                                'updated': <?php echo json_encode( $sync_data['data']['updated_data'] ) ?>,
                                'new': <?php echo json_encode( $sync_data['data']['new_data'] ) ?>
                            };

                            var ccb_data = ccb_groups_data[$(this).data('ccb-groups')];
                            var total_data = ccb_data.length, offset = 0, limit = 50;
                            var ccb_data_chunk = ccb_data.slice(offset, (offset + limit));
                            var nonce = '<?php echo wp_create_nonce( 'nonce_lo_sync_ccb_group' ); ?>';

                            blockui_msg_group_sync = $('<h2>' +
                                '<img style="width: 25px; vertical-align: middle;" src="<?php echo Liquid_Outreach::$url .
								                                                                   '/assets/images/spinner.svg'?>" /> ' +
                                'Please Wait...</h2>' +
                                '<hr/>' +
                                '<h3 class="lo-page-det" style="color:blue;">Syncing <span class="lo-sync-span">' + (offset + 1) + ' - ' + (offset + limit) + ' of ' + total_data + '</span></h3>' +
                                '<h3 class="lo-page-error hide-obj" style="display:none; color:red;">Error!!! Trying again.</h3>');

                            var data = {
                                'action': 'lo_admin_ajax_sync_ccb_groups',
                                'nonce': nonce,
                                'offset': offset,
                                'limit': limit,
                                'data': ccb_data_chunk
                            };

                            $(blockui_msg_group_sync[2]).find('span.lo-sync-span').html((offset + 1) + ' - ' + (offset + limit) + ' of ' + total_data);

                            ccb_group_sync_ajax_call(data, blockui_msg_group_sync, ccb_data);
                        });

                        var ccb_group_sync_ajax_call = function (data, blockui_msg, ccb_data) {

                            if (typeof data['offset'] == 'undefined' || data['offset'] == 0) {
                                $.blockUI({
                                    message: blockui_msg
                                });
                            }

                            var ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
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

                                    ccb_group_sync_ajax_call(data, blockui_msg_group_sync, ccb_data);

                                } else {

                                    $.unblockUI();
                                    alert('All data has been synced to WP.');
                                    location.reload();
                                }

                            });
                        }

                    });

                })(jQuery);
            </script>
			<?php
		}
		
		/**
		 * Check group sync data
		 *
		 * @since 0.3.5
		 */
		protected function check_sync_data() {
			global $wpdb;
			$results
				= $wpdb->get_results( 'SELECT `id`, `ccb_group_id`, `wp_post_id`, `data`, `md5_hash`, `last_modified`, `last_synced` FROM ' .
				                      $wpdb->prefix . 'lo_ccb_groups_api_data', ARRAY_A );
			
			$data = [
				'all_data'     => [],
				'synced_data'  => [],
				'updated_data' => [],
				'new_data'     => []
			];
			
			if ( ! empty( $results ) ) {
				foreach ( $results as $index => $result ) {
					
					if ( empty( $result['data'] ) ) {
						continue;
					}
					
					$api_data = json_decode( $result['data'], 1 );
					
					$val = [
						'ccb_group_id'       => $result['ccb_group_id'],
						'wp_post_id'         => $result['wp_post_id'],
						'title'              => $api_data['name'],
						'description'        => ( isset( $api_data['description'] ) &&
						                          ! empty( $api_data['description'] ) ) ? $api_data['description'] : '',
						'kid_friendly'       => ( isset( $api_data['registration']['group_type']['id'] ) &&
						                          ( $api_data['registration']['group_type']['id'] ==
						                            '1' ) ) ? 'yes' : 'no',
						'organizer_id'       => ( isset( $api_data['organizer']['id'] ) ) ? $api_data['organizer']['id'] : null,
						'registration_limit' => ( isset( $api_data['registration']['limit'] ) ) ? $api_data['registration']['limit'] : null,
						'start_time'         => ( isset( $api_data['start_datetime'] ) ) ? $api_data['start_datetime'] : null,
						'end_time'           => ( isset( $api_data['end_datetime'] ) ) ? $api_data['end_datetime'] : null,
						'group_id'           => ( isset( $api_data['group']['id'] ) ) ? $api_data['group']['id'] : null,
						'group_name'         => ( isset( $api_data['group']['value'] ) ) ? $api_data['group']['value'] : null,
						'address'            => ( isset( $api_data['location'] ) &&
						                          ! empty( $api_data['location'] ) ) ? $api_data['location'] : null,
					];
					
					$data['all_data'][] = $val;
					
					if ( ! empty( $result['wp_post_id'] ) ) {
						
						$data['synced_data'][] = $val;
						
						$last_modified = strtotime( $result['last_modified'] );
						$last_synced   = strtotime( $result['last_synced'] );
						
						if ( $last_modified > $last_synced ) {
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
		 * check if post action is valid
		 * and process data for ajax call sync ccb groups
		 *
		 * @since  0.3.5
		 */
		public function sync_ccb_groups_ccb_ajax_callback() {
			// If no form submission, bail
			if ( empty( $_POST ) ) {
				return false;
			}
			
			// check required $_POST variables and security nonce
			if (
				! isset( $_POST['action'], $_POST['nonce'], $_POST['data'] )
				|| ! wp_verify_nonce( $_POST['nonce'],
					'nonce_lo_sync_ccb_group' )
			) {
				return new WP_Error( 'security_fail', __( 'Security check failed.' ) );
			}
			
			$this->form_submitted = true;
			$nonce                = sanitize_text_field( $_POST['nonce'] );
			$action               = sanitize_text_field( $_POST['action'] );
			
			if ( ! in_array( $action, $this->acceptable_post_action ) ) {
				return new WP_Error( 'security_fail', __( 'Post action failed.' ) );
			}
			
			$method_key               = str_replace( '-', '_', $action ) . '_handler';
			$this->form_handle_status = $this->{$method_key}();
		}
		
		/**
		 * ccb group sync handler method
		 *
		 * @since 0.3.5
		 */
		public function lo_admin_ajax_sync_ccb_groups_handler() {
			global $wpdb;
			$inserted                      = 0;
			$updated                       = 0;
			$group_post_meta_prefix        = 'lo_ccb_groups_';
			$groupPartner_post_meta_prefix = 'lo_ccb_group_partner_';
			$ccb_group_data                = $_POST['data'];
			
			if ( ! empty( $ccb_group_data ) ) {
				
				foreach ( $ccb_group_data as $index => $ccb_group_datum ) {
					
					//create groups partners post
					$partner_query = new WP_Query( "post_type=lo-group-partners&meta_key=" .
					                               $groupPartner_post_meta_prefix .
					                               "group_id&meta_value=" .
					                               $ccb_group_datum['group_id'] );
					if ( ! $partner_query->have_posts() ) {
						$new_partner_post = wp_insert_post( [
							'post_title' => $ccb_group_datum['group_name'],
							'post_type'  => 'lo-group-partners',
							'meta_input' => [
								$groupPartner_post_meta_prefix .
								"group_id" => $ccb_group_datum['group_id']
							]
						] );
					}
					
					//create groups post
					$group_post_data = [
						'title'      => $ccb_group_datum['title'],
						'content'    => $ccb_group_datum['description'],
						'meta_input' => [
							$group_post_meta_prefix .
							'kid_friendly' => $ccb_group_datum['kid_friendly'],
							
							$group_post_meta_prefix .
							'start_date' => strtotime( $ccb_group_datum['start_time'] ),
							
							$group_post_meta_prefix .
							'weekday_name' => strtolower(date('l', strtotime( $ccb_group_datum['start_time'] ))),
							
							$group_post_meta_prefix .
							'address' => ! empty( $ccb_group_datum['address'] ) ? ( is_array( $ccb_group_datum['address'] ) ? ( implode( PHP_EOL,
								$ccb_group_datum['address'] ) ) : $ccb_group_datum['address'] ) : '',
							
							$group_post_meta_prefix .
							'city' => ! isset( $ccb_group_datum['address']['city'] ) ? '' : $ccb_group_datum['address']['city'],
							
							$group_post_meta_prefix .
							'team_lead_id' => $ccb_group_datum['organizer_id'],
							
							$group_post_meta_prefix .
							'group_id' => $ccb_group_datum['group_id'],
							
							$group_post_meta_prefix .
							'ccb_group_id' => $ccb_group_datum['ccb_group_id'],
						]
					];
					
					$group_organizer_data
						= $this->get_group_organizer_data( $ccb_group_datum['ccb_group_id'],
						$ccb_group_datum['organizer_id'] );
					
					if ( $group_organizer_data['error'] == false ) {
						$group_post_data['meta_input'][ $group_post_meta_prefix .
						                                'team_lead_fname' ]
							= $group_organizer_data['individual_data']['first_name'];
						
						$group_post_data['meta_input'][ $group_post_meta_prefix .
						                                'team_lead_lname' ]
							= $group_organizer_data['individual_data']['last_name'];
						
						$group_post_data['meta_input'][ $group_post_meta_prefix .
						                                'team_lead_email' ]
							= $group_organizer_data['individual_data']['phone'];
						
						$group_post_data['meta_input'][ $group_post_meta_prefix .
						                                'team_lead_phone' ]
							= $group_organizer_data['individual_data']['email'];
					}
					
					if ( $ccb_group_datum['registration_limit'] == 0 ) {
						$group_post_data['meta_input'][ $group_post_meta_prefix . 'openings' ]
							= 'no-limit';
					} elseif ( strtotime( $ccb_group_datum['start_time'] ) > time() ) {
						$group_attendees_data
							= $this->get_group_attendance_data( $ccb_group_datum['ccb_group_id'],
							date( 'Y-m-d', strtotime( $ccb_group_datum['start_time'] ) ) );
						
						if ( empty( $group_attendees_data['error'] ) ) {
							
							$group_post_data['meta_input'][ $group_post_meta_prefix . 'openings' ]
								= ( $ccb_group_datum['registration_limit'] -
								    $group_attendees_data['attendees_data']['count'] );
						} else {
							
							$group_post_data['meta_input'][ $group_post_meta_prefix . 'openings' ]
								= $ccb_group_datum['registration_limit'];
						}
						
					} else {
						$group_post_data['meta_input'][ $group_post_meta_prefix . 'openings' ]
							= 'expired';
					}
					
					if ( empty( $ccb_group_datum['wp_post_id'] ) ) {
						
						$new_post = null;
						$new_post = wp_insert_post( [
							'post_title'   => $group_post_data['title'],
							'post_content' => $group_post_data['content'],
							'post_type'    => 'lo-groups',
							'meta_input'   => $group_post_data['meta_input'],
						] );
						
						if ( ! empty( $new_post ) ) {
							$inserted ++;
							
							$wpdb->update(
								$wpdb->prefix . 'lo_ccb_groups_api_data',
								[
									'wp_post_id'    => $new_post,
									'last_synced'   => date( 'Y-m-d H:i:s', time() ),
									'last_modified' => date( 'Y-m-d H:i:s', time() )
								],
								[
									'ccb_group_id' => $ccb_group_datum['ccb_group_id']
								] );
						}
						
					} else {
						
						$update_post = wp_update_post( [
							'ID'           => $ccb_group_datum['wp_post_id'],
							'post_title'   => $group_post_data['title'],
							'post_content' => $group_post_data['content'],
							'post_type'    => 'lo-groups',
							'meta_input'   => $group_post_data['meta_input'],
						] );
						
						if ( ! empty( $update_post ) ) {
							$updated ++;
							
							$wpdb->update(
								$wpdb->prefix . 'lo_ccb_groups_api_data',
								[
									'last_synced'   => date( 'Y-m-d H:i:s', time() ),
									'last_modified' => date( 'Y-m-d H:i:s', time() )
								],
								[
									'ccb_group_id' => $ccb_group_datum['ccb_group_id']
								] );
						}
					}
					
				}
			}
			
			echo json_encode( [
				'inserted' => $inserted,
				'updated'  => $updated,
			] );
			die();
		}
		
		/**
		 * get organizer data either from cache or api call
		 *
		 * @since 0.3.5
		 *
		 * @param $ccb_group_id
		 * @param $organizer_id
		 *
		 * @return array
		 */
		protected function get_group_organizer_data( $ccb_group_id, $organizer_id ) {
			$api_error       = false;
			$transient_key   = "ccb_group_$ccb_group_id" . "_organizer_$organizer_id" .
			                   "_data";
			$cache_data      = get_transient( $transient_key );
			$individual_data = [];
			
			if ( empty( $cache_data ) ) {
				
				$this->plugin->lo_ccb_api_individual_profile->api_map( [
					'organizer_id' => $organizer_id
				] );
				$api_error = $this->plugin->lo_ccb_api_individual_profile->api_error;
				
				if ( empty( $api_error ) ) {
					
					$request
						= $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['request'];
					
					$response
						= $this->plugin->lo_ccb_api_individual_profile->api_response_arr['ccb_api']['response'];
					
					if ( ! empty( $response['individuals']['count'] ) ) {
						
						$individual = $response['individuals']['individual'];
						
						$individual_data = [
							'first_name' => empty( $individual['first_name'] ) ? '' : $individual['first_name'],
							'last_name'  => empty( $individual['last_name'] ) ? '' : $individual['last_name'],
							'phone'      => empty( $individual['phones']['phone'][0]['value'] ) ? '' : $individual['phones']['phone'][0]['value'],
							'email'      => empty( $individual['email'] ) ? '' : $individual['email'],
						];
						
						set_transient( $transient_key, $individual_data, ( 60 * 60 * 24 ) );
					} else {
						$api_error = true;
					}
					
				}
				
			} else {
				$individual_data = $cache_data;
			}
			
			return [
				'error'           => ! empty( $api_error ),
				'individual_data' => $individual_data
			];
		}
		
		/**
		 * get attendance data either from cache or api call
		 *
		 * @since 0.3.5
		 *
		 * @param $ccb_group_id
		 * @param $occurrence
		 *
		 * @return array
		 */
		protected function get_group_attendance_data( $ccb_group_id, $occurrence ) {
			$api_error      = false;
			$transient_key  = "ccb_group_$ccb_group_id" . "attendance_data";
			$cache_data     = get_transient( $transient_key );
			$attendees_data = [];
			
			if ( empty( $cache_data ) ) {
				
				$this->plugin->lo_ccb_api_attendance_profile->api_map( [
					'group_id'   => $ccb_group_id,
					'occurrence' => $occurrence
				] );
				$api_error = $this->plugin->lo_ccb_api_attendance_profile->api_error;
				
				if ( empty( $api_error ) ) {
					
					$request
						= $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['request'];
					
					$response
						= isset( $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response'] ) ? $this->plugin->lo_ccb_api_attendance_profile->api_response_arr['ccb_api']['response'] : [];
					
					if ( ! empty( $response ) ) {
						
						$attendees = $response['groups']['group']['attendees'];
						
						$attendees_data = [
							'count'     => count( $attendees ),
							'attendees' => $attendees
						];
						
						set_transient( $transient_key, $attendees_data, ( 60 * 60 ) );
					} else {
						$api_error = true;
					}
					
				}
				
			} else {
				$attendees_data = $cache_data;
			}
			
			return [
				'error'          => ! empty( $api_error ),
				'attendees_data' => $attendees_data
			];
		}
		
		/**
		 * Add custom fields to the options page.
		 *
		 * @since  0.3.5
		 */
		public function add_options_page_metabox() {
			
			// Add our CMB2 metabox.
			$cmb = new_cmb2_box( array(
				'id'           => $this->metabox_id,
				'object_types' => array( 'post' ),
				'hookup'       => false,
				'save_fields'  => false,
				'cmb_styles'   => false,
			) );
			
			// Add your fields here.
			$cmb->add_field( array(
				'name'    => __( 'Fetch Groups From Date', 'liquid-outreach' ),
				'desc'    => __( 'All groups created or modified since the date will be synced',
					'liquid-outreach' ),
				'id'      => 'modified_since', // No prefix needed.
				'type'    => 'text_date',
				'default' => ! empty( $_POST['modified_since'] ) ? $_POST['modified_since'] : ''
			) );
			
		}
		
		/**
		 * include page specific js
		 *
		 * @param $hook
		 *
		 * @since 0.3.5
		 */
		public function admin_enqueue_js( $hook ) {
			if ( 'lo-groups_page_liquid_outreach_ccb_groups_sync' != $hook ) {
				return;
			}
			
			wp_enqueue_script( 'underscore' );
			
			wp_enqueue_script( 'block-ui-js',
				Liquid_Outreach::$url . '/assets/bower/blockUI/jquery.blockUI.js' );
		}
		
		/**
		 * update api data table when group post is deleted
		 *
		 * @since 0.3.5
		 *
		 * @param $pid
		 */
		public function update_api_data_table( $pid ) {
			global $wpdb;
			$ccb_group_id = get_post_meta( $pid, 'lo_ccb_groups_ccb_group_id', true );
			if ( ! empty( $ccb_group_id ) ) {
				$wpdb->update( $wpdb->prefix . 'lo_ccb_groups_api_data', [
					'wp_post_id'    => null,
					'last_synced'   => date( 'Y-m-d H:i:s', time() ),
					'last_modified' => date( 'Y-m-d H:i:s', time() )
				], [
					'ccb_group_id' => $ccb_group_id
				] );
			}
		}
		
		/**
		 * Option page form handler
		 *
		 * @since  0.3.5
		 */
		protected function liquid_outreach_ccb_groups_sync_handler() {
			$this->plugin->lo_ccb_api_group_profiles->api_map();
			$api_error = $this->plugin->lo_ccb_api_group_profiles->api_error;
			
			if ( empty( $api_error ) ) {
				
				$request
					               = $this->plugin->lo_ccb_api_group_profiles->api_response_arr['ccb_api']['request'];
				$response
					               = $this->plugin->lo_ccb_api_group_profiles->api_response_arr['ccb_api']['response'];
				$request_arguments = $request['parameters']['argument'];
				$page_arguments    = $this->search_for_sub_arr( 'name', 'page',
					$request_arguments );
				
				if ( ! empty( $response['groups']['count'] ) ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'lo_ccb_groups_api_data';
					
					foreach ( $response['groups']['group'] as $index => $group ) {
						$exist = $wpdb->get_row( "SELECT * FROM $table_name WHERE ccb_group_id = " .
						                         $group['id'],
							ARRAY_A );
						
						if ( null !== $exist ) {
							
							if ( $exist['md5_hash'] != md5( json_encode( $group ) ) ) {
								$wpdb->replace(
									'table',
									array(
										'data'          => $json_group = json_encode( $group ),
										'md5_hash'      => md5( $json_group ),
										'last_modified' => date( 'Y-m-d H:i:s', time() ),
									)
								);
							}
							
						} else {
							
							$wpdb->insert( $table_name, array(
								'ccb_group_id'  => $group['id'],
								'data'          => $json_group = json_encode( $group ),
								'md5_hash'      => md5( $json_group ),
								'created'       => date( 'Y-m-d H:i:s', time() ),
								'last_modified' => date( 'Y-m-d H:i:s', time() ),
							) );
						}
					}
				}
				
				echo json_encode( [
					'error'        => ! empty( $api_error ),
					'success'      => empty( $api_error ),
					'current_page' => $page_arguments['value'],
					'next_page'    => empty( $response['groups']['count'] ) ? false : ( $page_arguments['value'] +
					                                                                    1 )
				] );
				
			} else {
				
				echo json_encode( [
						'error'        => ! empty( $api_error ),
						'success'      => empty( $api_error ),
						'details'      => $api_error,
						'current_page' => empty( $_POST['page'] ) ? 1 : $_POST['page'],
					]
				);
			}
			
			die();
		}
		
		/**
		 * @param $id
		 * @param $value
		 * @param $array
		 *
		 * @return int|null|string
		 *
		 * @since  0.3.5
		 */
		function search_for_sub_arr( $id, $value, $array ) {
			foreach ( $array as $key => $val ) {
				if ( $val[ $id ] === $value ) {
					return $val;
				}
			}
			
			return null;
		}
	}
