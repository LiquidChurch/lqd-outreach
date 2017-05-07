<?php
/**
 * Liquid Outreach Event Search Shortcode
 * @since 0.2.1
 * @package Liquid_Outreach
 */
class LO_Shortcodes_Event_Search extends LO_Shortcodes_Base {

	/**
	 * Constructor
	 *
	 * @since  0.2.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->run   = new LO_Shortcodes_Event_Search_Run( $plugin->ccb_events, $plugin->ccb_event_partners, $plugin->ccb_event_categories );
		$this->admin = new LO_Shortcodes_Event_Search_Admin( $this->run );

		parent::hooks();
	}

}

/**
 * Liquid Outreach Event Search Shortcode - Run
 *
 * @since 0.2.1
 * @package Liquid Outreach
 */
class LO_Shortcodes_Event_Search_Run extends LO_Shortcodes_Run_Base {

	/**
	 * The Shortcode Tag
	 * @var string
	 * @since 0.2.1
	 */
	public $shortcode = 'lo_event_search';

	/**
	 * Default attributes applied to the shortcode.
	 * @var array
	 * @since 0.2.1
	 */
	public $atts_defaults = array(
	);

	/**
	 * Shortcode Output
	 *
	 * @since 0.2.1
	 */
	public function shortcode() {
		return 'worked';
	}

}


/**
 * Liquid Outreach Event Search Shortcode - Admin
 * @since 0.2.1
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
	 * @param $fields
	 * @param $button_data
	 *
	 * @return array
	 */
	function fields( $fields, $button_data ) {

		return $fields;
	}
}
