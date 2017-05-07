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
		$this->run   = new LO_Shortcodes_Event_Search_Run( $plugin->lo_ccb_events, $plugin->lo_ccb_event_partners, $plugin->lo_ccb_event_categories );
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
		
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('lo-vandertable', Liquid_Outreach::$url . '/assets/js/vandertable.js');
		wp_enqueue_script('lo-index', Liquid_Outreach::$url . '/assets/js/index.js');
		
		$args = wp_parse_args( [], array(
			'post_type' => 'lo-events',
			'posts_per_page' => 10,
		) );
		
		$events = liquid_outreach()->lo_ccb_events->get_many($args);
		$max = $events->max_num_pages;
		$pagination = $this->get_pagination($max);
		
		$template = isset($_GET['template']) ? $_GET['template'] : 'search';
		$content = '';
		$content .= LO_Style_Loader::get_template('lc-plugin');
		$content .= LO_Style_Loader::get_template('vandertable');
		$content .= LO_Template_Loader::get_template( $template, array(
			'events' => $events,
			'pagination' => $pagination,
		) );
		return $content;
	}
	
	/**
	 * Pagination links
	 *
	 * @since 0.2.4
	 * @param $total_pages
	 *
	 * @return array
	 */
	protected function get_pagination($total_pages)
	{
		$nav = array('prev_link' => '', 'next_link' => '');
		
		if (!$this->bool_att('remove_pagination')) {
			$nav['prev_link'] = get_previous_posts_link(__('<span>&larr;</span> Newer', 'liquid-outreach'), $total_pages);
			$nav['next_link'] = get_next_posts_link(__('Older <span>&rarr;</span>', 'liquid-outreach'), $total_pages);
		}
		
		return $nav;
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
