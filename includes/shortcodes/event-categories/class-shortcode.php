<?php
	
	/**
	 * Liquid Outreach Event Categories Shortcode
	 *
	 * @since   0.4.0
	 * @package Liquid_Outreach
	 */
	class LO_Shortcodes_Event_Categories extends LO_Shortcodes_Base {

        /**
         * Constructor
         *
         * @since  0.4.0
         *
         * @param  object $plugin Main plugin object.
         *
         */
		public function __construct( $plugin ) {
			$this->run   = new LO_Shortcodes_Event_Categories_Run( $plugin->lo_ccb_events,
				$plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
			$this->admin = new LO_Shortcodes_Event_Categories_Admin( $this->run );
			
			parent::hooks();
		}
		
	}