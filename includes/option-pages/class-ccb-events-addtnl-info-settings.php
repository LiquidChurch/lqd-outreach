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
	class LO_Ccb_Events_Info_Setings extends LO_Base_Option_Page{

        /**
         * Holds an instance of the object
         *
         * @var LO_Ccb_Events_Page_Settings
         * @since 0.8.0
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
                'name' => 'Settings for Events <hr/>',
                'desc' => '',
                'type' => 'title',
                'id'   => 'events_title'
            ) );
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

            $cmb->add_field( array(
                'name' => '<hr/>Settings for Partners <hr/>',
                'desc' => '',
                'type' => 'title',
                'id'   => 'partner_title'
            ) );
            $cmb->add_field( array(
                'name'    => 'Address',
                'id'      => $prefix . 'partner_address',
                'type'    => 'radio_inline',
                'default' => true,
                'options' => array(
                    true => __( 'Show', 'cmb2' ),
                    false => __( 'Hide', 'cmb2' ),
                ),
            ) );
            $cmb->add_field( array(
                'name'    => 'Website',
                'id'      => $prefix . 'partner_website',
                'type'    => 'radio_inline',
                'default' => true,
                'options' => array(
                    true => __( 'Show', 'cmb2' ),
                    false => __( 'Hide', 'cmb2' ),
                ),
            ) );
            $cmb->add_field( array(
                'name'    => 'Team Leader',
                'id'      => $prefix . 'partner_team_leader',
                'type'    => 'radio_inline',
                'default' => true,
                'options' => array(
                    true => __( 'Show', 'cmb2' ),
                    false => __( 'Hide', 'cmb2' ),
                ),
            ) );
            $cmb->add_field( array(
                'name'    => 'Phone',
                'id'      => $prefix . 'partner_phone',
                'type'    => 'radio_inline',
                'default' => true,
                'options' => array(
                    true => __( 'Show', 'cmb2' ),
                    false => __( 'Hide', 'cmb2' ),
                ),
            ) );
            $cmb->add_field( array(
                'name'    => 'Email',
                'id'      => $prefix . 'partner_email',
                'type'    => 'radio_inline',
                'default' => true,
                'options' => array(
                    true => __( 'Show', 'cmb2' ),
                    false => __( 'Hide', 'cmb2' ),
                ),
            ) );
			
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