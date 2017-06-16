<?php
	
	/**
	 * Liquid Outreach Partner Single Shortcode
	 *
	 * @since   0.11.2
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Partner_Single extends LO_Shortcodes_Base {
		
		/**
		 * Constructor
		 *
		 * @since  0.11.2
		 *
		 * @param  object $plugin Main plugin object.
		 *
		 * @return void
		 */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Partner_Single_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Partner_Single_Admin( $this->run );
			
			parent::hooks();
		}
		
	}