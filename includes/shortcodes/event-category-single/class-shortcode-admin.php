<?php
	/**
	 * Liquid Outreach Event Category Single Shortcode - Admin
	 *
	 * @since   0.3.2
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Category_Single_Admin extends LO_Shortcodes_Admin_Base {
		
		/**
		 * Shortcode prefix for field ids.
		 *
		 * @var   string
		 * @since 0.3.2
		 */
		protected $prefix = 'lo_event_category_single';
		
		/**
		 * Sets up the button
		 *
		 * @since 0.3.2
		 * @return array
		 */
		function js_button_data() {
			return array(
				'qt_button_text' => __( 'Event Category Single', 'liquid-outreach' ),
				'button_tooltip' => __( 'Event Category Single', 'liquid-outreach' ),
				'icon'           => 'dashicons-media-interactive',
				// 'mceView'        => true, // The future
			);
		}
		
		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @since 0.3.2
		 *
		 * @param $fields
		 * @param $button_data
		 *
		 * @return array
		 */
		function fields( $fields, $button_data ) {
			
			return $fields;
		}
	}
