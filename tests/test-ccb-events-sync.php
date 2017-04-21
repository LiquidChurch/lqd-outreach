<?php
/**
 * Liquid Outreach Ccb Events Sync Tests.
 *
 * @since   0.0.0
 * @package Liquid_Outreach
 */
class LO_Ccb_Events_Sync_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'LO_Ccb_Events_Sync') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'LO_Ccb_Events_Sync', liquid_outreach()->lo_ccb_events_sync );
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
