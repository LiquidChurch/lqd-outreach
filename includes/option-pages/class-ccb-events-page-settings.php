<?php
	/**
	 * Liquid Outreach Ccb Events Page Settings.
	 *
	 * @since   0.8.0
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb Events Page Settings class.
	 *
	 * @since 0.8.0
	 */
	class LO_Ccb_Events_Page_Settings extends LO_Base_Option_Page {
		
		/**
		 * Option key, and option page slug
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $key = 'liquid_outreach_ccb_events_page_settings';
		/**
		 * Options page metabox id
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $metabox_id = 'liquid_outreach_ccb_events_page_settings_metabox';
		/**
		 * Options Page title
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $title = '';
		/**
		 * Options Page hook
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $options_page = '';
		
		/**
		 * Constructor
		 *
		 * @since 0.8.0
		 */
		public function __construct() {
			// Set our title
			$this->title = __( 'Outreach Various Page Settings', 'liquid-outreach' );
			
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
		 * @since  0.8.0
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
			
			$prefix = 'lo_events_page_';
			
			$cmb->add_field( array(
				'name'    => 'Category Animation',
				'id'      => $prefix . 'category_animation',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Enable', 'cmb2' ),
					false => __( 'Disable', 'cmb2' ),
				),
			) );

			$cmb->add_field( array(
				'name'    => __('Default Header Image', 'liquid-outreach'),
				'id'      => $prefix . 'default_header_image',
				'type'    => 'file',
			) );

			$cmb->add_field( array(
				'name'    => __('Select Home Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_header_element]<br/>[lo_nav_element]<br/>[lo_categories_element]',
				'id'      => $prefix . 'lo_home_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

			$cmb->add_field( array(
				'name'    => __('Select Search Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_event_search]',
				'id'      => $prefix . 'lo_search_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

			$cmb->add_field( array(
				'name'    => __('Select Category Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_event_categories]',
				'id'      => $prefix . 'lo_category_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

		}
		
		/**
		 * Public getter method for retrieving protected/private variables
		 *
		 * @since  0.8.0
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