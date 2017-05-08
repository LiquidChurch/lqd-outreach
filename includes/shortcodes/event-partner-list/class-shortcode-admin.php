<?php
	/**
	 * Liquid Outreach Event Partner List Shortcode - Admin
	 *
	 * @since   0.3.3
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Partner_List_Admin extends LO_Shortcodes_Admin_Base {
		
		/**
		 * Shortcode prefix for field ids.
		 *
		 * @var   string
		 * @since 0.3.3
		 */
		protected $prefix = 'lo_event_partner_list';
		
		/**
		 * Sets up the button
		 *
		 * @since 0.3.3
		 * @return array
		 */
		function js_button_data() {
			return array(
				'qt_button_text' => __( 'Event Partner List', 'liquid-outreach' ),
				'button_tooltip' => __( 'Event Partner List', 'liquid-outreach' ),
				'icon'           => 'dashicons-media-interactive',
				// 'mceView'        => true, // The future
			);
		}
		
		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @since 0.3.3
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
