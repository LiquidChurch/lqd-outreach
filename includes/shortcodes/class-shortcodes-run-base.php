<?php
/**
 * Liquid Outreach Shortcode Base
 *
 * @version 0.2.0
 * @package Liquid_Outreach
 */

abstract class LO_Shortcodes_Run_Base extends WDS_Shortcodes {

	/**
	 * LO_Ccb_Events object
	 *
	 * @var   LO_Ccb_Events
	 * @since 0.2.0
	 */
	public $ccb_event;
	
	/**
	 * LO_Ccb_Event_Partners object
	 *
	 * @var   LO_Ccb_Event_Partners
	 * @since 0.2.0
	 */
	public $ccb_event_partner;

	/**
	 * Constructor
	 *
	 * @since 0.2.0
	 *
	 * @param LO_Ccb_Events $ccb_event
	 * @param LO_Ccb_Event_Partners $ccb_event_partner
	 */
	public function __construct( LO_Ccb_Events $ccb_event, LO_Ccb_Event_Partners $ccb_event_partner ) {
		$this->ccb_event = $ccb_event;
		$this->ccb_event_partner = $ccb_event_partner;
		parent::__construct();
	}

	public function get_inline_styles() {
		$style = '';
		$has_icon_font_size = false;

		if ( $this->att( 'icon_color' ) || $this->att( 'icon_size' ) ) {
			$style = ' style="';
			// Get/check our text_color attribute
			if ( $this->att( 'icon_color' ) ) {
				$text_color = sanitize_text_field( $this->att( 'icon_color' ) );
				$style .= 'color: ' . $text_color .';';
			}
			if ( is_numeric( $this->att( 'icon_size' ) ) ) {
				$has_icon_font_size = absint( $this->att( 'icon_size' ) );
				$style .= 'font-size: ' . $has_icon_font_size .'em;';
			}
			$style .= '"';
		}

		return array( $style, $has_icon_font_size );
	}

}
