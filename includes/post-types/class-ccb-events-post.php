<?php
/**
 * Liquid Outreach Sermon Post
 * @since 0.2.3
 * @package Liquid Outreach
 */

class LO_Events_Post {

	/**
	 * Post object to wrap
	 *
	 * @var   WP_Post
	 * @since 0.2.3
	 */
	protected $post;
	
	/**
	 * event categoroes object
	 *
	 * @since 0.2.5
	 */
	protected $event_categories;
	
	/**
	 * Image data for the post.
	 *
	 * @var array
	 */
	protected $images = array();

	/**
	 * Constructor
	 *
	 * @since  0.2.3
	 * @param  mixed $post Post object to wrap
	 * @return void
	 */
	public function __construct( $post ) {
		if ( ! ( $post instanceof WP_Post ) ) {
			throw new Exception( 'Sorry, '. __CLASS__ .' expects a WP_Post object.' );
		}

		$post_type = liquid_outreach()->lo_ccb_events->post_type();

		if ( $post->post_type !== $post_type ) {
			throw new Exception( 'Sorry, '. __CLASS__ .' expects a '. $post_type .' object.' );
		}

		$this->post = $post;
	}

	/**
	 * Wrapper for get_permalink.
	 *
	 * @since  0.2.3
	 *
	 * @return string Sermon post permalink.
	 */
	public function permalink() {
		return get_permalink( $this->ID );
	}

	/**
	 * Wrapper for get_the_title.
	 *
	 * @since  0.2.3
	 *
	 * @return string Sermon post title.
	 */
	public function title() {
		return get_the_title( $this->ID );
	}

	/**
	 * Wrapper for the_excerpt. Returns value. Must be used in loop.
	 *
	 * @since  0.2.3
	 *
	 * @return string Sermon post excerpt.
	 */
	public function loop_excerpt() {
		ob_start();
		the_excerpt();
		// grab the data from the output buffer and add it to our $content variable
		$excerpt = ob_get_clean();

		return $excerpt;
	}

	/**
	 * Wrapper for get_the_post_thumbnail which stores the results to the object
	 *
	 * @since  0.2.3
	 *
	 * @param  string|array $size  Optional. Image size to use. Accepts any valid image size, or
	 *	                            an array of width and height values in pixels (in that order).
	 *	                            Default 'full'.
	 * @param  string|array $attr Optional. Query string or array of attributes. Default empty.
	 * @return string             The post thumbnail image tag.
	 */
	public function featured_image( $size = 'full', $attr = '' ) {
		// Unique id for the passed-in attributes.
		$id = md5( $attr );

		if ( isset( $this->images[ $size ] ) ) {
			// If we got it already, then send it back
			if ( isset( $this->images[ $size ][ $id ] ) ) {
				return $this->images[ $size ][ $id ];
			} else {
				$this->images[ $size ][ $id ] = array();
			}
		} else {
			$this->images[ $size ][ $id ] = array();
		}

		$img = get_the_post_thumbnail( $this->ID, $size, $attr );
		$this->images[ $size ][ $id ] = $img ? $img : null;

		return $this->images[ $size ][ $id ];
	}

	/**
	 * Wrapper for get_post_thumbnail_id
	 *
	 * @since  0.2.3
	 *
	 * @return string|int Post thumbnail ID or empty string.
	 */
	public function featured_image_id() {
		return get_post_thumbnail_id( $this->ID );
	}

	/**
	 * Wrapper for get_post_meta
	 *
	 * @since  0.2.3
	 *
	 * @param  string  $key Meta key
	 *
	 * @return mixed        Value of post meta
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->ID, $key, 1 );
	}
	
	/**
	 * Get all categories for this event
	 *
	 * @since  0.2.5
	 *
	 * @param  array  Args to pass to LO_Ccb_Event_Categories::get()
	 *
	 * @return WP_Term|false Event_Category term object.
	 */
	public function get_event_categories( $args = array() ) {
		$categories = null;
		if ( empty( $this->event_categories ) ) {
			$categories = $this->event_categories = $this->init_taxonomy( 'lo_ccb_event_categories' );
		}
		if ( empty( $categories ) ) {
			return false;
		}
		$category = array();
		foreach($categories as $key => $val) {
			$category[] = liquid_outreach()->lo_ccb_event_categories->get( $val, $args );
		}
		
		return $category;
	}
	
	/**
	 * Initate the taxonomy.
	 *
	 * @since  0.2.5
	 *
	 * @param  string  $taxonomy Taxonomy to initiate
	 *
	 * @return array  Array of terms for this taxonomy.
	 */
	protected function init_taxonomy( $taxonomy_class ) {
		$tax_slug = liquid_outreach()->{$taxonomy_class}->taxonomy();
		return get_the_terms( $this->ID, $tax_slug );
	}

	/**
	 * Magic getter for our object.
	 *
	 * @param string $property
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 * @since  0.2.5
	 */
	public function __get( $property ) {
		// Automate
		switch ( $property ) {
			case 'post':
				return $this->{$property};
			default:
				// Check post object for property
				// In general, we'll avoid using same-named properties,
				// so the post object properties are always available.
				if ( isset( $this->post->{$property} ) ) {
					return $this->post->{$property};
				}
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $property );
		}
	}

	/**
	 * Magic isset checker for our object.
	 *
	 * @param string $property
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 * @since  0.2.5
	 */
	public function __isset( $property ) {
		// Automate
		switch ( $property ) {
			default:
				// Check post object for property
				// In general, we'll avoid using same-named properties,
				// so the post object properties are always available.
				return isset( $this->post->{$property} );
		}
	}

}
