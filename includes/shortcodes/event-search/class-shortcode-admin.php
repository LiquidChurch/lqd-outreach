<?php
	/**
	 * Liquid Outreach Event Search Shortcode - Admin
	 *
	 * @since   0.2.1
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Search_Admin extends LO_Shortcodes_Admin_Base {
		
		/**
		 * Shortcode prefix for field ids.
		 *
		 * @var   string
		 * @since 0.2.1
		 */
		protected $prefix = 'lo_event_search';
		
		/**
		 * Sets up the button
		 *
		 * @since 0.2.1
		 * @return array
		 */
		function js_button_data() {
			return array(
				'qt_button_text' => __( 'Event Search', 'liquid-outreach' ),
				'button_tooltip' => __( 'Event Search', 'liquid-outreach' ),
				'icon'           => 'dashicons-media-interactive',
				// 'mceView'        => true, // The future
			);
		}
		
		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @since 0.2.1
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
