<?php
	
	/**
	 * Liquid Outreach Event Single Shortcode
	 *
	 * @since   0.3.1
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Event_Single extends LO_Shortcodes_Base {
		
		/**
		 * Constructor
		 *
		 * @since  0.3.1
		 *
		 * @param  object $plugin Main plugin object.
		 *
		 * @return void
		 */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Event_Single_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Event_Single_Admin( $this->run );
			
			parent::hooks();
		}
		
	}