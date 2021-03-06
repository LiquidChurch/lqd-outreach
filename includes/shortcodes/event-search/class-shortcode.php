<?php
	
	/**
	 * Liquid Outreach Event Search Shortcode
	 *
	 * @since   0.2.1
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Event_Search extends LO_Shortcodes_Base {
		
		/**
		 * Constructor
		 *
		 * @since  0.2.1
		 *
		 * @param  object $plugin Main plugin object.
		 *
		 * @return void
		 */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Event_Search_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Event_Search_Admin( $this->run );
			
			parent::hooks();
		}
		
	}