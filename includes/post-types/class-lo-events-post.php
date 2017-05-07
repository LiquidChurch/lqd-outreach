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
	 * Constructor
	 *
	 * @since  0.1.0
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
	 * @since  0.1.1
	 *
	 * @return string Sermon post permalink.
	 */
	public function permalink() {
		return get_permalink( $this->ID );
	}

	/**
	 * Wrapper for get_the_title.
	 *
	 * @since  0.1.1
	 *
	 * @return string Sermon post title.
	 */
	public function title() {
		return get_the_title( $this->ID );
	}

	/**
	 * Wrapper for the_excerpt. Returns value. Must be used in loop.
	 *
	 * @since  0.1.3
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
	 * @since  0.1.0
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

		if ( ! isset( $attr['series_image_fallback'] ) || false !== $attr['series_image_fallback'] ) {
			$series_image_fallback = true;
			if ( isset( $attr['series_image_fallback'] ) ) {
				unset( $attr['series_image_fallback'] );
			}
		}

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
		$this->images[ $size ][ $id ] = $img ? $img : $this->series_image( $size, $attr );

		return $this->images[ $size ][ $id ];
	}

	/**
	 * Wrapper for get_post_thumbnail_id
	 *
	 * @since  0.1.0
	 *
	 * @return string|int Post thumbnail ID or empty string.
	 */
	public function featured_image_id() {
		return get_post_thumbnail_id( $this->ID );
	}

	/**
	 * Wrapper for get_post_meta
	 *
	 * @since  0.1.1
	 *
	 * @param  string  $key Meta key
	 *
	 * @return mixed        Value of post meta
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->ID, $key, 1 );
	}

	/**
	 * Magic getter for our object.
	 *
	 * @param string $property
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
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
