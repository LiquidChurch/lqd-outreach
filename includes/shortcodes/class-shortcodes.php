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
     */
	public function __construct( $plugin ) {
		$this->event_search    = new LO_Shortcodes_Event_Search( $plugin );
		$this->event_single    = new LO_Shortcodes_Event_Single( $plugin );
		$this->event_categories    = new LO_Shortcodes_Event_Categories( $plugin );
		$this->event_category_single    = new LO_Shortcodes_Event_Category_Single( $plugin );
		$this->event_partner_list    = new LO_Shortcodes_Event_Partner_List( $plugin );
		$this->header_element    = new LO_Shortcodes_Header_Element( $plugin );
		$this->nav_element    = new LO_Shortcodes_Nav_Element( $plugin );
		$this->categories_element    = new LO_Shortcodes_Categories_Element( $plugin );
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
