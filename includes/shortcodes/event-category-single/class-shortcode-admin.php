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
		 * @var   string    $prefix
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
				'qt_button_text' => __( 'LO Event Category Single', 'liquid-outreach' ),
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
		 * @return array    $fields
		 */
		function fields( $fields, $button_data ) {

            $fields[] = array(
                'name'           => 'Select Event Category',
                'desc'           => 'Select event category',
                'id'             => 'event_cat_slug',
                'taxonomy'       => 'event-category', //Enter Taxonomy Slug
                'type'           => 'taxonomy_select',
                'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
            );

            $fields[] = array(
                'name' => 'Disable Header',
                'desc' => 'To disable header',
                'id'   => 'disable_header',
                'type' => 'checkbox',
            );

            $fields[] = array(
                'name' => 'Disable Nav Bar',
                'desc' => 'To disable Nav Bar',
                'id'   => 'disable_nav',
                'type' => 'checkbox',
            );

            $fields[] = array(
                'name' => 'Disable Search',
                'desc' => 'To disable search',
                'id'   => 'disable_search',
                'type' => 'checkbox',
            );

            $fields[] = array(
                'name' => 'Disable Mini Category List',
                'desc' => 'To disable mini category list',
                'id'   => 'disable_cateogy_list',
                'type' => 'checkbox',
            );
			
			return $fields;
		}

	}
