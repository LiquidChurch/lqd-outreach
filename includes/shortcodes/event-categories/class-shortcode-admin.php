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
		 * @var   string    $prefix
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
				'qt_button_text' => __( 'LO Event Categories', 'liquid-outreach' ),
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
		 * @return array    $fields
		 */
		function fields( $fields, $button_data ) {


            $fields[] = array(
                'name'           => 'Select Event Category',
                'desc'           => 'Select event category',
                'id'             => 'force_cat_slug',
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
                'name' => 'Select menu options below',
                'desc' => '',
                'type' => 'title',
                'id'   => 'menu_option_title'
            );

            $fields[] = array(
                'name'    => 'Index',
                'desc'    => '',
                'id'      => 'menu_option_index',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'Search',
                'desc'    => '',
                'id'      => 'menu_option_search',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'Categories',
                'desc'    => '',
                'id'      => 'menu_option_categories',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'City',
                'desc'    => '',
                'id'      => 'menu_option_city',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'Days',
                'desc'    => '',
                'id'      => 'menu_option_days',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'Partners',
                'desc'    => '',
                'id'      => 'menu_option_partners',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            $fields[] = array(
                'name'    => 'Campus',
                'desc'    => '',
                'id'      => 'menu_option_campus',
                'type'    => 'radio_inline',
                'options' => array(
                    'true' => __( 'Show', 'cmb2' ),
                    'false'   => __( 'Hide', 'cmb2' ),
                ),
                'default' => 'true',
            );

            return $fields;
		}
	}
