<?php
	/**
	 * Liquid Outreach Ccb_event_categories.
	 *
	 * @since   0.1.1
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb_event_categories.
	 *
	 * @since 0.1.1
	 *
	 * @see   https://github.com/WebDevStudios/Taxonomy_Core
	 */
	class LO_Ccb_Event_Categories extends Taxonomy_Core {
		/**
		 * The identifier for this object
		 *
		 * @since  0.1.1
		 * @var string
		 */
		protected $id = 'event_category';
		
		/**
		 * Parent plugin class.
		 *
		 * @var    Liquid_Outreach
		 * @since  0.1.1
		 */
		protected $plugin = null;
		
		/**
		 * The image meta key for this taxonomy, if applicable
		 *
		 * @var string
		 * @since  0.2.5
		 */
		protected $image_meta_key = '';
		
		/**
		 * The default args array for self::get()
		 *
		 * @var array
		 * @since  0.2.5
		 */
		protected $term_get_args_defaults
			= array(
				'image_size' => 64,
			);
		
		/**
		 * The default args array for self::get_many()
		 *
		 * @var array
		 * @since  0.2.6
		 */
		protected $term_get_many_args_defaults
			= array(
				'orderby'       => 'name',
				'augment_terms' => true,
			);
		
		/**
		 * Constructor.
		 *
		 * Register Taxonomy.
		 *
		 * See documentation in Taxonomy_Core, and in wp-includes/taxonomy.php.
		 *
		 * @since  0.1.1
		 *
		 * @param  Liquid_Outreach $plugin Main plugin object.
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
			
			parent::__construct(
			// Should be an array with Singular, Plural, and Registered name.
				array(
					__( 'Event Category', 'liquid-outreach' ),
					__( 'Event Categories', 'liquid-outreach' ),
					'event-category',
				),
				// Register taxonomy arguments.
				array(
					'hierarchical' => false,
				),
				// Post types to attach to.
				array(
					'lo-events',
				)
			);
		}
		
		/**
		 * Initiate our hooks.
		 *
		 * @since 0.1.1
		 */
		public function hooks() {
			add_action( 'cmb2_admin_init', array( $this, 'fields' ) );
		}
		
		/**
		 * Add custom fields to the CPT
		 *
		 * @since  0.1.1
		 * @return void
		 */
		public function fields() {
			
			$prefix = 'lo_ccb_event_category_';
			
			$cmb = $this->new_cmb2( array(
				'id'           => 'lo_event_category_metabox',
				'taxonomies'   => array( $this->taxonomy() ),
				'object_types' => array( 'term' ),
				'fields'       => array(
					'image' => array(
						'name' => __( 'Image', 'liquid-outreach' ),
						'desc' => __( '', 'liquid-outreach' ),
						'id'   => $prefix . 'image',
						'type' => 'file'
					),
				),
			) );
		}
		
		/**
		 * @since 0.1.1
		 *
		 * @param $args
		 *
		 * @return CMB2
		 */
		public function new_cmb2( $args ) {
			$cmb_id = $args['id'];
			
			return new_cmb2_box( apply_filters( "lo_cmb2_box_args_{$this->id}_{$cmb_id}", $args ) );
		}
		
		/**
		 * Wrapper for get_terms
		 *
		 * @since  0.2.6
		 *
		 * @param  array $args             Array of arguments (passed to get_terms).
		 * @param  array $single_term_args Array of arguments for LO_Ccb_Event_Categories::get().
		 *
		 * @return array|false Array of term objects or false
		 */
		public function get_many( $args = array(), $single_term_args = array() ) {
			$args = wp_parse_args( $args, $this->term_get_many_args_defaults );
			$args = apply_filters( "lo_get_{$this->id}_args", $args );
			
			$terms = self::get_terms( $this->taxonomy(), $args );
			
			if ( ! $terms || is_wp_error( $terms ) ) {
				return false;
			}
			
			if (
				isset( $args['augment_terms'] )
				&& $args['augment_terms']
				&& ! empty( $terms )
				// Don't augment for queries w/ greater than 100 terms, for perf. reasons.
				&& 100 > count( $terms )
			) {
				foreach ( $terms as $key => $term ) {
					$terms[ $key ] = $this->get( $term, $single_term_args );
				}
			}
			
			return $terms;
		}
		
		/**
		 * Wrapper for `get_terms` to account for changes in WP 4.5 where taxonomy
		 * is expected as part of the arguments.
		 *
		 * @since  0.2.6
		 *
		 * @return mixed Array of terms on success
		 */
		protected static function get_terms( $taxonomy, $args = array() ) {
			unset( $args['augment_terms'] );
			if ( version_compare( $GLOBALS['wp_version'], '4.5.0', '>=' ) ) {
				$args['taxonomy'] = $taxonomy;
				$terms            = get_terms( $args );
			} else {
				$terms = get_terms( $taxonomy, $args );
			}
			
			return $terms;
		}
		
		/**
		 * Get a single term object
		 *
		 * @since  0.2.5
		 *
		 * @param  object|int $term Term id or object
		 * @param  array      $args Array of arguments.
		 *
		 * @return WP_Term|false  Term object or false
		 */
		public function get( $term, $args = array() ) {
			$term = isset( $term->term_id ) ? $term : get_term_by( 'id', $term, $this->taxonomy() );
			if ( ! isset( $term->term_id ) ) {
				return false;
			}
			
			$args = wp_parse_args( $args, $this->term_get_args_defaults );
			$args = apply_filters( "lo_get_{$this->id}_single_args", $args, $term, $this );
			
			$term->term_link = get_term_link( $term );
			$term            = $this->extra_term_data( $term, $args );
			
			return $term;
		}
		
		/**
		 * Sets extra term data on the the term object, including the image, if applicable
		 *
		 * @since  0.2.5
		 *
		 * @param  WP_Term $term Term object
		 * @param  array   $args Array of arguments.
		 *
		 * @return WP_Term|false
		 */
		protected function extra_term_data( $term, $args ) {
			if ( $this->image_meta_key ) {
				$term = $this->add_image( $term, $args['image_size'] );
			}
			
			return $term;
		}
		
		/**
		 * Magic getter for our object. Allows getting but not setting.
		 *
		 * @since 0.1.1
		 *
		 * @param string $field
		 *
		 * @throws Exception Throws an exception if the field is invalid.
		 * @return mixed
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'id':
					return $this->id;
				default:
					throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
			}
		}
	}
