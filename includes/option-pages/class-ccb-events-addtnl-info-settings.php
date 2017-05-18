<?php
	/**
	 * Liquid Outreach Ccb Events Info Settings.
	 *
	 * @since   0.3.4
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb Events info settings class.
	 *
	 * @since 0.3.4
	 */
	class LO_Ccb_Events_Info_Setings {
		
		/**
		 * Holds an instance of the object
		 *
		 * @var LO_Ccb_Events_Info_Setings
		 * @since 0.3.4
		 */
		protected static $instance = null;
		/**
		 * Option key, and option page slug
		 *
		 * @var string
		 * @since 0.3.4
		 */
		protected $key = 'liquid_outreach_ccb_events_info_settings';
		/**
		 * Options page metabox id
		 *
		 * @var string
		 * @since 0.3.4
		 */
		protected $metabox_id = 'liquid_outreach_ccb_events_info_settings_metabox';
		/**
		 * Options Page title
		 *
		 * @var string
		 * @since 0.3.4
		 */
		protected $title = '';
		/**
		 * Options Page hook
		 *
		 * @var string
		 * @since 0.3.4
		 */
		protected $options_page = '';
		
		/**
		 * Constructor
		 *
		 * @since 0.3.4
		 */
		public function __construct() {
			// Set our title
			$this->title = __( 'Outreach Details Page Settings', 'liquid-outreach' );
			
			$this->hooks();
		}
		
		/**
		 * Returns the running object
		 *
		 * @return LO_Ccb_Events_Info_Setings
		 * @since 0.3.4
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->hooks();
			}
			
			return self::$instance;
		}
		
		/**
		 * Initiate our hooks
		 *
		 * @since 0.3.4
		 */
		public function hooks() {
			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		}
		
		
		/**
		 * Register our setting to WP
		 *
		 * @since  0.3.4
		 */
		public function init() {
			register_setting( $this->key, $this->key );
		}
		
		/**
		 * Add menu options page
		 *
		 * @since 0.3.4
		 */
		public function add_options_page() {
			$this->options_page = add_submenu_page(
				'edit.php?post_type=lo-events',
				$this->title,
				$this->title,
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' )
			);
			
			// Include CMB CSS in the head to avoid FOUC
			add_action( "admin_print_styles-{$this->options_page}",
				array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}
		
		/**
		 * Admin page markup. Mostly handled by CMB2
		 *
		 * @since  0.3.4
		 */
		public function admin_page_display() {
			?>
            <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
            </div>
			<?php
		}
		
		/**
		 * Add the options metabox to the array of metaboxes
		 *
		 * @since  0.3.4
		 */
		function add_options_page_metabox() {
			
			// hook in our save notices
			add_action( "cmb2_save_options-page_fields_{$this->metabox_id}",
				array( $this, 'settings_notices' ), 10, 2 );
			
			$cmb = new_cmb2_box( array(
				'id'         => $this->metabox_id,
				'hookup'     => false,
				'cmb_styles' => false,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $this->key, )
				),
			) );
			
			// Set our CMB2 fields
			
			$prefix = 'lo_events_info_';
			
			$cmb->add_field( array(
				'name'    => 'Date Time',
				'id'      => $prefix . 'date_time',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Cost',
				'id'      => $prefix . 'cost',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Openings',
				'id'      => $prefix . 'openings',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Categories',
				'id'      => $prefix . 'categories',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Kid Friendly',
				'id'      => $prefix . 'kid_friendly',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Team Leader',
				'id'      => $prefix . 'team_leader',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Team Leader Name',
				'id'      => $prefix . 'team_leader_name',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Team Leader Email',
				'id'      => $prefix . 'team_leader_email',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Team Leader Phone',
				'id'      => $prefix . 'team_leader_phone',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Partner organization',
				'id'      => $prefix . 'partner_organization',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			$cmb->add_field( array(
				'name'    => 'Address',
				'id'      => $prefix . 'address',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Show', 'cmb2' ),
					false => __( 'Hide', 'cmb2' ),
				),
			) );
			
		}
		
		/**
		 * Register settings notices for display
		 *
		 * @since  0.3.4
		 *
		 * @param  int   $object_id Option key
		 * @param  array $updated   Array of updated fields
		 *
		 * @return void
		 */
		public function settings_notices( $object_id, $updated ) {
			if ( $object_id !== $this->key || empty( $updated ) ) {
				return;
			}
			
			add_settings_error( $this->key . '-notices', '',
				__( 'Settings updated.', 'liquid-outreach' ), 'updated' );
			settings_errors( $this->key . '-notices' );
		}
		
		/**
		 * Public getter method for retrieving protected/private variables
		 *
		 * @since  0.3.4
		 *
		 * @param  string $field Field to retrieve
		 *
		 * @return mixed          Field value or exception is thrown
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve
			if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
				return $this->{$field};
			}
			
			throw new Exception( 'Invalid property: ' . $field );
		}
		
	}
	
	/**
	 * Helper function to get/return the Myprefix_Admin object
	 *
	 * @since  0.3.4
	 * @return LO_Ccb_Events_Info_Setings object
	 */
	function lo_settings_admin() {
		return LO_Ccb_Events_Info_Setings::get_instance();
	}
	
	/**
	 * Wrapper function around cmb2_get_option
	 *
	 * @since  0.3.4
	 *
	 * @param  string $key     Options array key
	 * @param  mixed  $default Optional default value
	 *
	 * @return mixed           Option value
	 */
	function lo_get_option( $key = '', $default = null ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( lo_settings_admin( null )->key, $key, $default );
		}
		
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( lo_settings_admin( null )->key, $key, $default );
		
		$val = $default;
		
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
		
		return $val;
	}