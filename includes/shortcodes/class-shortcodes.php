<?php
/**
 * Liquid Outreach Shortcodes
 * @since 0.2.0
 * @package Liquid_Outreach
 */

class LO_Shortcodes {

	/**
	 * Instance of LO_Shortcodes_Event_Search
	 *
	 * @var LO_Shortcodes_Event_Search
	 * @since 0.2.0
	 */
	protected $event_search;

	/**
	 * Constructor
	 *
	 * @since  0.2.0
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->event_search    = new LO_Shortcodes_Event_Search( $plugin );
		$this->event_single    = new LO_Shortcodes_Event_Single( $plugin );
		$this->event_category_single    = new LO_Shortcodes_Event_Category_Single( $plugin );
	}

	/**
	 * Magic getter for our object. Allows getting but not setting.
	 *
	 * @since  0.2.0
	 * @param string $field
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		return $this->{$field};
	}
}
