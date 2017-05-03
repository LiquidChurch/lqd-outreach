<?php
/**
 * Liquid Outreach Ccb_event_partners Tests.
 *
 * @since   0.0.0
 * @package Liquid_Outreach
 */
class LO_Ccb_Event_Partners_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'LO_Ccb_Event_Partners') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'LO_Ccb_Event_Partners', liquid_outreach()->ccb_event_partners );
	}

	/**
	 * Test to make sure the CPT now exists.
	 *
	 * @since  0.0.0
	 */
	function test_cpt_exists() {
		$this->assertTrue( post_type_exists( 'lo-ccb-event-partner' ) );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.0.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
