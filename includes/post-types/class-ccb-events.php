<?php
	/**
	 * Liquid Outreach Ccb Events.
	 *
	 * @since   0.0.1
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb Events post type class.
	 *
	 * @since 0.0.1
	 *
	 * @see   https://github.com/WebDevStudios/CPT_Core
	 */
	class LO_Ccb_Events extends CPT_Core {
		/**
		 * Bypass temp. cache
		 *
		 * @var boolean
		 * @since  0.2.4
		 */
		public $flush = false;
		/**
		 * @var string
		 * @since  0.2.4
		 */
		public $meta_prefix = 'lo_ccb_events_';
		/**
		 * Parent plugin class.
		 *
		 * @var Liquid_Outreach
		 * @since  0.0.1
		 */
		protected $plugin = null;
		/**
		 * @var bool
		 * @since  0.2.4
		 */
		protected $overrides_processed = false;
		/**
		 * The identifier for this object
		 *
		 * @var string
		 * @since  0.2.4
		 */
		protected $id = 'lo-events';
		/**
		 * Default WP_Query args
		 *
		 * @var   array
		 * @since 0.2.4
		 */
		protected $query_args
			= array(
				'post_type'      => 'THIS(REPLACE)',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			);
		
		/**
		 * Constructor.
		 *
		 * Register Custom Post Types.
		 *
		 * See documentation in CPT_Core, and in wp-includes/post.php.
		 *
		 * @since  0.0.1
		 *
		 * @param  Liquid_Outreach $plugin Main plugin object.
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
			
			// Register this cpt.
			// First parameter should be an array with Singular, Plural, and Registered name.
			parent::__construct(
				array(
					esc_html__( 'Event', 'liquid-outreach' ),
					esc_html__( 'Events', 'liquid-outreach' ),
					'lo-events',
				),
				array(
					'supports'     => array(
						'title',
						'editor',
						'excerpt',
						'thumbnail',
					),
					'menu_icon'    => 'dashicons-admin-post',
					// https://developer.wordpress.org/resource/dashicons/
					'public'       => true,
					'capabilities' => array(
						'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
					),
					'map_meta_cap' => true,
					'rewrite'      => array( 'slug' => 'events' ),
				)
			);
			
			$this->query_args['post_type'] = $this->post_type();
		}
		
		/**
		 * Initiate our hooks.
		 *
		 * @since  0.0.1
		 */
		public function hooks() {
			add_action( 'cmb2_init', array( $this, 'fields' ) );
		}
		
		/**
		 * Provides access to protected class properties.
		 *
		 * @since  0.2.4
		 *
		 * @param  boolean $key Specific CPT parameter to return
		 *
		 * @return mixed        Specific CPT parameter or array of singular, plural and registered name
		 */
		public function post_type( $key = 'post_type' ) {
			if ( ! $this->overrides_processed ) {
				$this->filter_values();
			}
			
			return parent::post_type( $key );
		}
		
		public function filter_values() {
			if ( $this->overrides_processed ) {
				return;
			}
			
			$args = array(
				'singular'      => $this->singular,
				'plural'        => $this->plural,
				'post_type'     => $this->post_type,
				'arg_overrides' => $this->arg_overrides,
			);
			
			$filtered_args = apply_filters( 'lo_post_types_' . $this->id, $args, $this );
			
			if ( $filtered_args !== $args ) {
				foreach ( $args as $arg => $val ) {
					if ( isset( $filtered_args[ $arg ] ) ) {
						$this->{$arg} = $filtered_args[ $arg ];
					}
				}
			}
			
			$this->overrides_processed = true;
		}
		
		/**
		 * Add custom fields to the CPT.
		 *
		 * @since  0.0.1
		 */
		public function fields() {
			
			// Set our prefix.
			$prefix = $this->meta_prefix;
			
			// Define our metaboxes and fields.
			$cmb_additional = new_cmb2_box( array(
				'id'           => $prefix . 'additional_meta',
				'title'        => esc_html__( 'Additional Details', 'liquid-outreach' ),
				'object_types' => array( 'lo-events' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left
				// 'cmb_styles' => false, // false to disable the CMB stylesheet
				// 'closed'     => true, // Keep the metabox closed by default
			) );
			
			//kid friendly meta
			$cmb_additional->add_field( array(
				'name'             => esc_html__( 'Kid Friendly', 'liquid-outreach' ),
				'desc'             => esc_html__( '', 'liquid-outreach' ),
				'id'               => $prefix . 'kid_friendly',
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => array(
					'yes' => esc_html__( 'Yes', 'liquid-outreach' ),
					'no'  => esc_html__( 'No', 'liquid-outreach' ),
				),
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//cost meta
			$cmb_additional->add_field( array(
				'name'            => __( 'Cost', 'liquid-outreach' ),
				'desc'            => __( '', 'liquid-outreach' ),
				'id'              => $prefix . 'cost',
				'type'            => 'text',
				'attributes'      => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
				'sanitization_cb' => 'absint',
				'escape_cb'       => 'absint',
			) );
			
			//openings meta
			$cmb_additional->add_field( array(
				'name' => __( 'Openings', 'liquid-outreach' ),
				'desc' => __( '', 'liquid-outreach' ),
				'id'   => $prefix . 'openings',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//start date meta
			$cmb_additional->add_field( array(
				'name'        => __( 'Start Date', 'liquid-outreach' ),
				'desc'        => __( '', 'liquid-outreach' ),
				'id'          => $prefix . 'start_date',
				'type'        => 'text_datetime_timestamp',
				'date_format' => 'Y-m-d',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//address meta
			$cmb_additional->add_field( array(
				'name' => __( 'Address', 'liquid-outreach' ),
				'desc' => __( '', 'liquid-outreach' ),
				'id'   => $prefix . 'address',
				'type' => 'textarea_small',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//Register Button meta
			$cmb_additional->add_field( array(
				'name' => __( 'Register Button', 'liquid-outreach' ),
				'desc' => __( '', 'liquid-outreach' ),
				'id'   => $prefix . 'regsiter_url',
				'type' => 'text',
			) );
			
			//Event City meta
			$cmb_additional->add_field( array(
				'name' => __( 'Event City', 'liquid-outreach' ),
				'desc' => __( '', 'liquid-outreach' ),
				'id'   => $prefix . 'city',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//Event ccb id meta
//			$cmb_additional->add_field( array(
//				'name'       => __( 'CCB Event ID', 'liquid-outreach' ),
//				'desc'       => __( '', 'liquid-outreach' ),
//				'id'         => $prefix . 'ccb_event_id',
//				'type'       => 'text',
//				'attributes' => array(
//					'readonly' => 'readonly',
//					'disabled' => 'disabled',
//				),
//			) );
			
			//Event group id meta
//			$cmb_additional->add_field( array(
//				'name'       => __( 'CCB Group ID', 'liquid-outreach' ),
//				'desc'       => __( '', 'liquid-outreach' ),
//				'id'         => $prefix . 'group_id',
//				'type'       => 'text',
//				'attributes' => array(
//					'readonly' => 'readonly',
//					'disabled' => 'disabled',
//				),
//			) );
			
			// Define our metaboxes and fields.
			$cmb_team_leader = new_cmb2_box( array(
				'id'           => $prefix . 'leader',
				'title'        => esc_html__( 'Team Leader', 'liquid-outreach' ),
				'object_types' => array( 'lo-events' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left
				// 'cmb_styles' => false, // false to disable the CMB stylesheet
				// 'closed'     => true, // Keep the metabox closed by default
			) );
			
			//team leader meta
//			$cmb_team_leader->add_field( array(
//				'name'       => esc_html__( 'ID', 'liquid-outreach' ),
//				'desc'       => esc_html__( '', 'liquid-outreach' ),
//				'id'         => $prefix . 'team_lead_id',
//				'type'       => 'text',
//				'attributes' => array(
//					'readonly' => 'readonly',
//					'disabled' => 'disabled',
//				),
//			) );
			
			//team leader fname meta
			$cmb_team_leader->add_field( array(
				'name' => esc_html__( 'First Name', 'liquid-outreach' ),
				'desc' => esc_html__( '', 'liquid-outreach' ),
				'id'   => $prefix . 'team_lead_fname',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//team leader lname meta
			$cmb_team_leader->add_field( array(
				'name' => esc_html__( 'Last Name', 'liquid-outreach' ),
				'desc' => esc_html__( '', 'liquid-outreach' ),
				'id'   => $prefix . 'team_lead_lname',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//team leader email meta
			$cmb_team_leader->add_field( array(
				'name' => esc_html__( 'Email', 'liquid-outreach' ),
				'desc' => esc_html__( '', 'liquid-outreach' ),
				'id'   => $prefix . 'team_lead_email',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
			
			//team leader phone meta
			$cmb_team_leader->add_field( array(
				'name' => esc_html__( 'Phone', 'liquid-outreach' ),
				'desc' => esc_html__( '', 'liquid-outreach' ),
				'id'   => $prefix . 'team_lead_phone',
				'type' => 'text',
				//                'attributes' => array(
				//                    'readonly' => 'readonly',
				//                    'disabled' => 'disabled',
				//                ),
			) );
		}
		
		/**
		 * Registers admin columns to display. Hooked in via CPT_Core.
		 *
		 * @since  0.0.1
		 *
		 * @param  array $columns Array of registered column names/labels.
		 *
		 * @return array          Modified array.
		 */
		public function columns( $columns ) {
			$new_column = array();
			
			return array_merge( $new_column, $columns );
		}
		
		/**
		 * Handles admin column display. Hooked in via CPT_Core.
		 *
		 * @since  0.0.1
		 *
		 * @param array   $column  Column currently being rendered.
		 * @param integer $post_id ID of post to display column for.
		 */
		public function columns_display( $column, $post_id ) {
			switch ( $column ) {
			}
		}
		
		/**
		 * Retrieve lo-events.
		 *
		 * @since  0.2.4
		 *
		 * @return WP_Query|LO_Events_Post object
		 */
		public function get_many( $args ) {
			$defaults = $this->query_args;
			unset( $defaults['posts_per_page'] );
			unset( $defaults['no_found_rows'] );
			$args['augment_posts'] = true;
			$args['meta_key']      = $this->meta_prefix . 'start_date';
			$args['orderby']       = 'meta_value_num';
			$args['order']         = 'ASC';
			$args['meta_query']    = [
				'key'     => $this->meta_prefix . 'start_date',
				'value'   => time(),
				'compare' => '>='
			];
			$args                  = apply_filters( 'lo_get_events_args',
				wp_parse_args( $args, $defaults ) );
			
			$events = new WP_Query( $args );
			
			if (
				isset( $args['augment_posts'] )
				&& $args['augment_posts']
				&& $events->have_posts()
				// Don't augment for queries w/ greater than 100 posts, for perf. reasons.
				&& $events->post_count < 100
			) {
				foreach ( $events->posts as $key => $post ) {
					$events->posts[ $key ] = new LO_Events_Post( $post );
				}
			}
			
			return $events;
		}
		
		/**
		 * @param string $status
		 *
		 * @since 0.2.7
		 * @return array
		 */
		public function get_all_city_list( $status = 'publish' ) {
			
			global $wpdb;
			
			$r = $wpdb->get_col( $wpdb->prepare( "
		        SELECT pm.meta_value FROM {$wpdb->postmeta} as pm
		        LEFT JOIN {$wpdb->posts} as p ON p.ID = pm.post_id
		        WHERE pm.meta_key = '%s'
		        AND p.post_status = '%s'
		        AND p.post_type = '%s'
		    ", $this->meta_prefix . 'city', $status, $this->post_type() ) );
			
			return array_filter( $r );
		}
		
		/**
		 * @param $args
		 *
		 * @return WP_Query
		 * @since 0.2.9
		 */
		public function get_search_result( $args ) {
			
			$defaults = $this->query_args;
			unset( $defaults['posts_per_page'] );
			unset( $defaults['no_found_rows'] );
			
			$search_query = [
				'key' => isset( $_GET['lo-event-s'] ) ? $_GET['lo-event-s'] : '',
				'cat' => isset( $_GET['lo-event-cat'] ) ? $_GET['lo-event-cat'] : '',
				'org' => isset( $_GET['lo-event-org'] ) ? $_GET['lo-event-org'] : '',
				'day' => isset( $_GET['lo-event-day'] ) ? $_GET['lo-event-day'] : '',
				'loc' => isset( $_GET['lo-event-loc'] ) ? $_GET['lo-event-loc'] : '',
			];
			
			if ( ! empty( $search_query['key'] ) ) {
				
				global $wpdb;
				$args['post__in']
					= $wpdb->get_col( "select ID from $wpdb->posts where post_title LIKE '" .
					                  $search_query['key'] . "%' " );
			}
			
			$args['augment_posts'] = true;
			
			if ( ! empty( $search_query['cat'] ) ) {
				$args['tax_query'] = [
					[
						'taxonomy' => liquid_outreach()->lo_ccb_event_categories->taxonomy(),
						'field'    => 'term_id',
						'terms'    => (int) $search_query['cat']
					]
				];
			}
			if ( ! empty( $search_query['org'] ) ) {
				$args['meta_query'][] = [
					'key'     => $this->meta_prefix . 'group_id',
					'value'   => $search_query['org'],
					'compare' => '='
				];
			}
			if ( ! empty( $search_query['day'] ) ) {
				$args['meta_query'][] = [
					'key'     => $this->meta_prefix . 'weekday_name',
					'value'   => $search_query['day'],
					'compare' => '='
				];
			}
			if ( ! empty( $search_query['loc'] ) ) {
				$args['meta_query'][] = [
					'key'     => $this->meta_prefix . 'city',
					'value'   => $search_query['loc'],
					'compare' => '='
				];
			}
			
			$args['meta_key'] = $this->meta_prefix . 'start_date';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'ASC';
			if ( ! empty( $args['meta_query'] ) ) {
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = [
				'key'     => $this->meta_prefix . 'start_date',
				'value'   => time(),
				'compare' => '>='
			];
			$args               = apply_filters( 'lo_get_events_args',
				wp_parse_args( $args, $defaults ) );
			
			$events = new WP_Query( $args );
			
			if (
				isset( $args['augment_posts'] )
				&& $args['augment_posts']
				&& $events->have_posts()
				// Don't augment for queries w/ greater than 100 posts, for perf. reasons.
				&& $events->post_count < 100
			) {
				foreach ( $events->posts as $key => $post ) {
					$events->posts[ $key ] = new LO_Events_Post( $post );
				}
			}
			
			return $events;
		}
		
		/**
		 * Magic getter for our object. Allows getting but not setting.
		 *
		 * @param string $field
		 *
		 * @throws Exception Throws an exception if the field is invalid.
		 * @return mixed
		 * @since  0.2.4
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'id':
				case 'arg_overrides':
				case 'cpt_args':
					return $this->{$field};
				default:
					throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
			}
		}
	}
