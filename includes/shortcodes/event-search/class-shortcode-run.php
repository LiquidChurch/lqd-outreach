<?php
	
	/**
	 * Liquid Outreach Event Search Shortcode - Run
	 *
	 * @since   0.2.1
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Search_Run extends LO_Shortcodes_Run_Base {
		
		/**
		 * The Shortcode Tag
		 *
		 * @var string
		 * @since 0.2.1
		 */
		public $shortcode = 'lo_event_search';
		
		/**
		 * Default attributes applied to the shortcode.
		 *
		 * @var array
		 * @since 0.2.1
		 */
		public $atts_defaults
			= array();
		
		/**
		 * Shortcode Output
		 *
		 * @since 0.2.1
		 */
		public function shortcode() {
			
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'lo-vandertable',
				Liquid_Outreach::$url . '/assets/js/vandertable.js' );
			wp_enqueue_script( 'lo-index', Liquid_Outreach::$url . '/assets/js/index.js' );
			
			$args = $this->get_initial_query_args();
			
			$args = wp_parse_args( $args, array(
				'post_type'      => liquid_outreach()->lo_ccb_events->post_type(),
				'posts_per_page' => 10,
			) );
			
			if ( isset( $_GET['lo-event-s'] ) || isset( $_GET['lo-event-loc'] ) || isset( $_GET['lo-event-day'] ) ) {
				$events = liquid_outreach()->lo_ccb_events->get_search_result( $args );
				if ( empty( $events->posts ) ) {
					$event_empty_msg = 'Sorry, No data found for the search criteria.';
				}
			} else {
				$events = liquid_outreach()->lo_ccb_events->get_many( $args );
				if ( empty( $events->posts ) ) {
					$event_empty_msg = 'Sorry, No data found.';
				}
			}
			
			$max        = ! empty( $events->max_num_pages ) ? $events->max_num_pages : 0;
			$pagination = $this->get_pagination( $max );
			
			$categories = liquid_outreach()->lo_ccb_event_categories->get_many( [
				'hide_empty' => false
			] );
			$cities     = liquid_outreach()->lo_ccb_events->get_all_city_list();
			
			$partners = liquid_outreach()->lo_ccb_event_partners->get_many( [
				'post_type'      => liquid_outreach()->lo_ccb_event_partners->post_type(),
				'posts_per_page' => - 1,
			] );
			
			$template = isset( $_GET['template'] ) ? $_GET['template'] : 'search';
			$content  = '';
			$content  .= LO_Style_Loader::get_template( 'lc-plugin' );
			$content  .= LO_Style_Loader::get_template( 'vandertable' );
			$content  .= LO_Template_Loader::get_template( $template, array(
				'events'     => !empty( $events->posts ) ? $events->posts : [],
				'event_empty_msg'     => isset( $event_empty_msg ) ? $event_empty_msg : '',
				'pagination' => $pagination,
				'categories' => $categories,
				'partners'   => ! empty( $partners->posts ) ? $partners->posts : [],
				'cities'     => $cities,
			) );
			
			return $content;
		}
		
		/**
		 * @return array
		 * @since  0.2.4
		 */
		public function get_initial_query_args() {
			$paged  = (int) get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			$offset = ( ( $paged - 1 ) * 10 );
			
			return compact( 'paged', 'offset' );
		}
		
		/**
		 * Pagination links
		 *
		 * @since 0.2.4
		 *
		 * @param $total_pages
		 *
		 * @return array
		 */
		protected function get_pagination( $total_pages ) {
			$nav = array( 'prev_link' => '', 'next_link' => '' );
			
			if ( ! $this->bool_att( 'remove_pagination' ) ) {
				$nav['prev_link'] = get_previous_posts_link( __( '<span>&larr;</span> Previous',
					'liquid-outreach' ), $total_pages );
				$nav['next_link'] = get_next_posts_link( __( 'Next <span>&rarr;</span>',
					'liquid-outreach' ), $total_pages );
			}
			
			return $nav;
		}
		
	}