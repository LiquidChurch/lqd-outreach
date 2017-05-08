<?php
	
	/**
	 * Liquid Outreach Event Category Single Shortcode
	 *
	 * @since   0.3.2
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Event_Category_Single extends LO_Shortcodes_Base {
		
		/**
		 * Constructor
		 *
		 * @since  0.3.2
		 *
		 * @param  object $plugin Main plugin object.
		 *
		 * @return void
		 */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Event_Category_Single_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Event_Category_Single_Admin( $this->run );
			
			parent::hooks();
		}
		
	}