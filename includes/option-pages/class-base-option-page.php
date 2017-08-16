<?php
/**
 * Liquid Outreach Base_Option_Page.
 *
 * @since   0.8.0
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach Base_Option_Page
 *
 * @since 0.8.0
 */
abstract class LO_Base_Option_Page {

    /**
     * Option key, and option page slug
     *
     * @var string
     * @since 0.8.0
     */
    protected $key = '';

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
     * Add menu options pages, enqueue CSS
     *
     * See https://developer.wordpress.org/reference/functions/add_submenu_page/
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

    public abstract function add_options_page_metabox();

    /**
     * Register settings notices for display
     *
     * @since  0.8.0
     *
     * @param  int   $object_id    Option key
     * @param  array $updated      Array of updated fields
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
     * Show WP Pages
     *
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

    /**
     * Public getter method for retrieving protected/private variables
     *
     * @since  0.3.4
     * @param  string   $field  Field to retrieve
     * @return mixed            Field value or exception is thrown
     *
     * @throws Exception
     */
    public function __get( $field ) {
        // Allowed fields to retrieve
        if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
            return $this->{$field};
        }

        throw new Exception( 'Invalid property: ' . $field );
    }

}

/**
 * Helper function to get/return the Myprefix_Admin object
 *
 * @param string $page
 *
 * @since  0.3.4
 * @return LO_Ccb_Events_Info_Settings object
 */
function lo_settings_admin($page = null) {
    if($page == 'page') {
        return LO_Ccb_Events_Page_Settings::get_instance();
    } else if($page == 'additional-info') {
        return LO_Ccb_Events_Info_Settings::get_instance();
    } else if($page == 'cat-map') {
        return LO_Ccb_Events_Partner_Cat_Map_Settings::get_instance();
    }  else if($page == 'name-map') {
        return LO_Ccb_Events_Name_Map_Settings::get_instance();
    } else {
        die(__('Invalid page setting key provided', 'liquid-outreach'));
    }
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  0.3.4
 *
 * @param  string #page
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 *
 * @return mixed           Option value
 */
function lo_get_option( $page = '', $key = '', $default = null ) {
    if ( function_exists( 'cmb2_get_option' ) ) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option( lo_settings_admin( $page )->key, $key, $default );
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option( lo_settings_admin( $page )->key, $key, $default );

    $val = $default;

    if ( 'all' == $key ) {
        $val = $opts;
    } elseif ( array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
        $val = $opts[ $key ];
    }

    return $val;
}