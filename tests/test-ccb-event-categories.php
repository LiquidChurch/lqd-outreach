<?php
/**
 * Liquid Outreach Ccb_event_categories Tests.
 *
 * @since   0.0.0
 * @package Liquid_Outreach
 */
class LO_Ccb_event_categories_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'LO_Ccb_event_categories') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'LO_Ccb_event_categories', liquid_outreach()->ccb_event_categories );
	}

	/**
	 * Test that our taxonomy now exists.
	 *
	 * @since  0.0.0
	 */
	function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'lo-ccb-event-categor' ) );
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
