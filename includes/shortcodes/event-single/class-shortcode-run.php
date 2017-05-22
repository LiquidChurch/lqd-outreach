<?php
	
	/**
	 * Liquid Outreach Event Single Shortcode - Run
	 *
	 * @since   0.3.1
	 * @package Liquid Outreach
	 */
	class LO_Shortcodes_Event_Single_Run extends LO_Shortcodes_Run_Base {
		
		/**
		 * The Shortcode Tag
		 *
		 * @var string
		 * @since 0.3.1
		 */
		public $shortcode = 'lo_event_single';
		
		/**
		 * Default attributes applied to the shortcode.
		 *
		 * @var array
		 * @since 0.3.1
		 */
		public $atts_defaults
			= array(
				'event_id'          => 0,
			);
		
		/**
		 * Shortcode Output
		 *
		 * @since 0.3.1
		 */
		public function shortcode() {
			
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'lo-vandertable',
				Liquid_Outreach::$url . '/assets/js/vandertable.js' );
			wp_enqueue_script( 'lo-index', Liquid_Outreach::$url . '/assets/js/index.js' );
			
			$post = new LO_Events_Post(get_post($this->att('event_id')));
			
			$categories = liquid_outreach()->lo_ccb_event_categories->get_many( [
				'hide_empty' => false
			] );
			
			$cities     = liquid_outreach()->lo_ccb_events->get_all_city_list();
			
			$partners = liquid_outreach()->lo_ccb_event_partners->get_many( [
				'post_type'      => liquid_outreach()->lo_ccb_event_partners->post_type(),
				'posts_per_page' => - 1,
			] );
			
			$content  = '';
			$content  .= LO_Style_Loader::get_template( 'lc-plugin' );
			$content  .= LO_Template_Loader::get_template( 'event-details', array(
				'categories' => $categories,
				'cities'     => $cities,
				'partners'   => ! empty( $partners->posts ) ? $partners->posts : [],
				'post'     => $post,
			) );
			
			return $content;
		}
		
		/**
		 * @return array
		 * @since  0.3.1
		 */
		public function get_initial_query_args() {
			$paged  = (int) get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			$offset = ( ( $paged - 1 ) * 10 );
			
			return compact( 'paged', 'offset' );
		}
		
		/**
		 * Pagination links
		 *
		 * @since 0.3.1
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