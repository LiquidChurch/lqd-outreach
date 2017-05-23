<?php
	/**
	 * Liquid Outreach Event Categories Shortcode - Admin
	 *
	 * @since   0.4.0
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Categories_Admin extends LO_Shortcodes_Admin_Base {
		
		/**
		 * Shortcode prefix for field ids.
		 *
		 * @var   string
		 * @since 0.4.0
		 */
		protected $prefix = 'lo_event_categories';
		
		/**
		 * Sets up the button
		 *
		 * @since 0.4.0
		 * @return array
		 */
		function js_button_data() {
			return array(
				'qt_button_text' => __( 'Event Categories', 'liquid-outreach' ),
				'button_tooltip' => __( 'Event Categories', 'liquid-outreach' ),
				'icon'           => 'dashicons-media-interactive',
				// 'mceView'        => true, // The future
			);
		}
		
		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @since 0.4.0
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
