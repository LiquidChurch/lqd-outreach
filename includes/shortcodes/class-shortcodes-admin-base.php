<?php
/**
 * Liquid Outreach Shortcode Admin Base
 *
 * @since 0.2.0
 * @package Liquid_Outreach
 */

abstract class LO_Shortcodes_Admin_Base extends WDS_Shortcode_Admin {
	
	/**
	 * Parent plugin class
	 *
	 * @var   LO_Shortcodes_Run_Base    $run
	 * @since 0.2.0
	 */
	protected $run;

	/**
	 * Shortcode prefix for field ids.
	 *
	 * @var   string    $prefix
	 * @since 0.2.0
	 */
	protected $prefix = '';

	/**
	 * Constructor
	 *
	 * @since  0.2.0
	 * @param  object $run Main plugin object.
	 * @return void
	 */
	public function __construct( LO_Shortcodes_Run_Base $run ) {
		$this->run = $run;

		parent::__construct(
			$this->run->shortcode,
			Liquid_Outreach::VERSION,
			$this->run->atts_defaults
		);

		// Do this super late.
		add_filter( "{$this->shortcode}_shortcode_fields", array( $this, 'maybe_remove_prefixes' ), 99999 );
	}

	/**
	 * If the shortcode has a prefix property, we remove it from the shortcode attributes.
	 *
	 * @since  0.2.0
	 * @param  array  $updated Array of shortcode attributes.
	 * @return array  $updated Modified array of shortcode attributes.
	 */
	public function maybe_remove_prefixes( $updated ) {
		if ( $this->prefix ) {
			$prefix_length = strlen( $this->prefix );
			$new_updated = array();

			foreach ( $updated as $key => $value) {

				if ( $this->prefix === substr( $key, 0, $prefix_length ) ) {
				    $key = substr( $key, $prefix_length );
				}

				$new_updated[ $key ] = $value;
			}

			$updated = $new_updated;
		}

		return $updated;
	}
}
