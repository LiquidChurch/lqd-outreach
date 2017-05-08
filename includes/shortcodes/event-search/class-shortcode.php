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
		
		$args = $this->get_initial_query_args();
		
		$args = wp_parse_args( $args, array(
			'post_type' => liquid_outreach()->lo_ccb_events->post_type(),
			'posts_per_page' => 10,
		) );
		
		$events = liquid_outreach()->lo_ccb_events->get_many($args);
		
		$max = !empty($events->max_num_pages) ? $events->max_num_pages : 0;
		$pagination = $this->get_pagination($max);
		
		$categories = liquid_outreach()->lo_ccb_event_categories->get_many([
			'hide_empty' => false
		]);
		$cities = liquid_outreach()->lo_ccb_events->get_all_city_list();
		
		$partners = liquid_outreach()->lo_ccb_event_partners->get_many([
			'post_type' => liquid_outreach()->lo_ccb_event_partners->post_type(),
			'posts_per_page' => -1,
		]);
		
		$template = isset($_GET['template']) ? $_GET['template'] : 'search';
		$content = '';
		$content .= LO_Style_Loader::get_template('lc-plugin');
		$content .= LO_Style_Loader::get_template('vandertable');
		$content .= LO_Template_Loader::get_template( $template, array(
			'events' => !empty($events->posts) ? $events->posts : [],
			'pagination' => $pagination,
			'categories' => $categories,
			'partners' => !empty($partners->posts) ? $partners->posts : [],
			'cities' => $cities,
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
			$nav['prev_link'] = get_previous_posts_link(__('<span>&larr;</span> Previous', 'liquid-outreach'), $total_pages);
			$nav['next_link'] = get_next_posts_link(__('Next <span>&rarr;</span>', 'liquid-outreach'), $total_pages);
		}
		
		return $nav;
	}
	
	/**
	 * @return array
	 * @since  0.2.4
	 */
	public function get_initial_query_args()
	{
		$paged = (int)get_query_var('paged') ? get_query_var('paged') : 1;
		$offset = (($paged - 1) * 10);
		
		return compact('paged', 'offset');
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
