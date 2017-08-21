<?php

/**
 * Liquid Outreach Shortcode Base
 *
 *
 * @since   0.2.0
 * @package Liquid_Outreach
 */
abstract class LO_Shortcodes_Base
{

    /**
     * Instance of LO_Shortcodes_Run_Base
     *
     * @since 0.2.0
     * @var LO_Shortcodes_Run_Base  $run
     */
    public $run;

    /**
     * Instance of LO_Shortcodes_Admin_Base
     *
     * @since 0.2.0
     * @var LO_Shortcodes_Admin_Base    $admin
     */
    public $admin;

    /**
     * Constructor
     *
     * @since  0.2.0
     *
     * @param  object $plugin Main plugin object.
     *
     * @return void
     */
    public function __construct($plugin)
    {
        $this->hooks();
    }

    /**
     * Will be called when class is initialized
     *
     * @since 0.2.0
     */
    public function hooks()
    {
        if ( ! is_admin())
        {
            $this->run->hooks();
        }
        $this->admin->hooks();
    }

}
