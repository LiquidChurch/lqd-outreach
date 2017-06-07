<?php
	/**
	 * Liquid Outreach Ccb Events Page Settings.
	 *
	 * @since   0.8.0
	 * @package Liquid_Outreach
	 */
	
	
	/**
	 * Liquid Outreach Ccb Events Page Settings class.
	 *
	 * @since 0.8.0
	 */
	class LO_Ccb_Events_Page_Settings {
		
		/**
		 * Holds an instance of the object
		 *
		 * @var LO_Ccb_Events_Page_Settings
		 * @since 0.8.0
		 */
		protected static $instance = null;
		/**
		 * Option key, and option page slug
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $key = 'liquid_outreach_ccb_events_page_settings';
		/**
		 * Options page metabox id
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $metabox_id = 'liquid_outreach_ccb_events_page_settings_metabox';
		/**
		 * Options Page title
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $title = '';
		/**
		 * Options Page hook
		 *
		 * @var string
		 * @since 0.8.0
		 */
		protected $options_page = '';
		
		/**
		 * Constructor
		 *
		 * @since 0.8.0
		 */
		public function __construct() {
			// Set our title
			$this->title = __( 'Outreach Various Page Settings', 'liquid-outreach' );
			
			$this->hooks();
		}
		
		/**
		 * Returns the running object
		 *
		 * @return LO_Ccb_Events_Page_Settings
		 * @since 0.8.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->hooks();
			}
			
			return self::$instance;
		}
		
		/**
		 * Initiate our hooks
		 *
		 * @since 0.8.0
		 */
		public function hooks() {
			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		}
		
		
		/**
		 * Register our setting to WP
		 *
		 * @since  0.8.0
		 */
		public function init() {
			register_setting( $this->key, $this->key );
		}
		
		/**
		 * Add menu options page
		 *
		 * @since 0.8.0
		 */
		public function add_options_page() {
			$this->options_page = add_submenu_page(
				'edit.php?post_type=lo-events',
				$this->title,
				$this->title,
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' )
			);
			
			// Include CMB CSS in the head to avoid FOUC
			add_action( "admin_print_styles-{$this->options_page}",
				array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}
		
		/**
		 * Admin page markup. Mostly handled by CMB2
		 *
		 * @since  0.8.0
		 */
		public function admin_page_display() {
			?>
            <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
            </div>
			<?php
		}
		
		/**
		 * Add the options metabox to the array of metaboxes
		 *
		 * @since  0.8.0
		 */
		function add_options_page_metabox() {
			
			// hook in our save notices
			add_action( "cmb2_save_options-page_fields_{$this->metabox_id}",
				array( $this, 'settings_notices' ), 10, 2 );
			
			$cmb = new_cmb2_box( array(
				'id'         => $this->metabox_id,
				'hookup'     => false,
				'cmb_styles' => false,
				'show_on'    => array(
					// These are important, don't remove
					'key'   => 'options-page',
					'value' => array( $this->key, )
				),
			) );
			
			// Set our CMB2 fields
			
			$prefix = 'lo_events_info_';
			
			$cmb->add_field( array(
				'name'    => 'Category Animation',
				'id'      => $prefix . 'category_animation',
				'type'    => 'radio_inline',
				'default' => true,
				'options' => array(
					true => __( 'Enable', 'cmb2' ),
					false => __( 'Disable', 'cmb2' ),
				),
			) );

			$cmb->add_field( array(
				'name'    => __('Default Header Image', 'liquid-outreach'),
				'id'      => $prefix . 'default_header_image',
				'type'    => 'file',
			) );

			$cmb->add_field( array(
				'name'    => __('Select Home Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_header_element]<br/>[lo_nav_element]<br/>[lo_categories_element]',
				'id'      => $prefix . 'lo_home_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

			$cmb->add_field( array(
				'name'    => __('Select Search Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_event_search]',
				'id'      => $prefix . 'lo_search_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

			$cmb->add_field( array(
				'name'    => __('Select Category Page', 'liquid-outreach'),
                'desc' => 'Place these shortcodes inside the page content area - ' .
                    '<br/>[lo_event_categories]',
				'id'      => $prefix . 'lo_category_page',
				'type'    => 'select',
                'options_cb' => ['LO_Ccb_Events_Page_Settings', 'show_wp_pages'],
			) );

		}
		
		/**
		 * Register settings notices for display
		 *
		 * @since  0.8.0
		 *
		 * @param  int   $object_id Option key
		 * @param  array $updated   Array of updated fields
		 *
		 * @return void
		 */
		public function settings_notices( $object_id, $updated ) {
			if ( $object_id !== $this->key || empty( $updated ) ) {
				return;
			}
			
			add_settings_error( $this->key . '-notices', '',
				__( 'Settings updated.', 'liquid-outreach' ), 'updated' );
			settings_errors( $this->key . '-notices' );
		}
		
		/**
		 * Public getter method for retrieving protected/private variables
		 *
		 * @since  0.8.0
		 *
		 * @param  string $field Field to retrieve
		 *
		 * @return mixed          Field value or exception is thrown
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve
			if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
				return $this->{$field};
			}
			
			throw new Exception( 'Invalid property: ' . $field );
		}

        /**
         * @since 0.8.0
         * @return mixed
         */
        public static function show_wp_pages()
        {
            $query = new WP_Query(
                $array = array(
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                )
            );

            $titles[''] = '';
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $titles[get_the_id()] = get_the_title();
                }
            }

            wp_reset_postdata();

            return $titles;
        }


    }