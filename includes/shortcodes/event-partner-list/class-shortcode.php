<?php
	
	/**
	 * Liquid Outreach Event Partner List Shortcode
	 *
	 * @since   0.3.3
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Event_Partner_List extends LO_Shortcodes_Base {
		
		/**
		 * Constructor
		 *
		 * @since  0.3.3
		 *
		 * @param  object $plugin Main plugin object.
		 *
		 * @return void
		 */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Event_Partner_List_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Event_Partner_List_Admin( $this->run );
			
			parent::hooks();
		}
		
	}