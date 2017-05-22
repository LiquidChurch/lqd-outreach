<?php
/**
 * Plugin Name: Liquid Outreach
 * Plugin URI:  https://liquidchurch.com
 * Description: Outreach and CCB API integration.
 * Version:     0.3.7
 * Author:      SurajPrGupta, Liquidchurch
 * Author URI:  https://twitter.com/surajprgupta
 * Donate link: https://liquidchurch.com
 * License:     GPLv2
 * Text Domain: liquid-outreach
 * Domain Path: /languages
 *
 * @link    https://liquidchurch.com
 *
 * @package Liquid_Outreach
 * @version 0.3.7
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

require __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://team.scripterz.in/products/liquidchurch/wp-plugin/lqd-outreach/meta.json',
    __FILE__,
    'liquid-outreach'
);

/**
 * Copyright (c) 2017 SurajPrGupta, Liquidchurch (email : suraj.gupta@scripterz.in)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// Use composer autoload.
require __DIR__ . '/vendor/autoload.php';

/**
 * Main initiation class.
 *
 * @since  0.0.0
 */
final class Liquid_Outreach
{

    /**
     * Current version.
     *
     * @var    string
     * @since  0.0.0
     */
    const VERSION = '0.3.7';
    const DB_VERSION = 2.0;
    /**
     * URL of plugin directory.
     *
     * @var    string
     * @since  0.0.0
     */
    public static $url = '';
    /**
     * Path of plugin directory.
     *
     * @var    string
     * @since  0.0.0
     */
    public static $path = '';
    /**
     * Plugin basename.
     *
     * @var    string
     * @since  0.0.0
     */
    public static $basename = '';
    /**
     * Singleton instance of plugin.
     *
     * @var    Liquid_Outreach
     * @since  0.0.0
     */
    protected static $single_instance = null;
    /**
     * Instance of Lo_Ccb_api_event_profiles
     *
     * @since 0.0.5
     * @var Lo_Ccb_api_event_profiles
     */
    protected $lo_ccb_api_event_profiles;
    /**
     * Instance of lo_ccb_api_group_profiles
     *
     * @since 0.3.5
     * @var lo_ccb_api_group_profiles
     */
    protected $lo_ccb_api_group_profile_from_id;
    /**
     * Instance of Lo_Ccb_api_individual_profile
     *
     * @since 0.1.3
     * @var Lo_Ccb_api_individual_profile
     */
    protected $lo_ccb_api_individual_profile;
    /**
     * Instance of Lo_Ccb_api_attendance_profile
     *
     * @since 0.1.4
     * @var Lo_Ccb_api_attendance_profile
     */
    protected $lo_ccb_api_attendance_profile;
    /**
     * Detailed activation error messages.
     *
     * @var    array
     * @since  0.0.0
     */
    protected $activation_errors = array();

    /**
     * Instance of LO_Ccb_Events
     *
     * @since 0.0.1
     * @var LO_Ccb_Events
     */
    protected $lo_ccb_events;

    /**
     * Instance of LO_Ccb_Event_Partners
     *
     * @since 0.0.2
     * @var LO_Ccb_Event_Partners
     */
    protected $lo_ccb_event_partners;

    /**
     * Instance of LO_Ccb_Events_Sync
     *
     * @since 0.0.3
     * @var LO_Ccb_Events_Sync
     */
    protected $lo_ccb_events_sync;

    /**
     * Instance of LO_Shortcodes
     *
     * @since 0.2.0
     * @var LO_Shortcodes
     */

    /**
     * Instance of LO_Ccb_Events_Info_Setings
     *
     * @since 0.3.4
     * @var LO_Ccb_Events_Info_Setings
     */
    protected $lo_ccb_events_info_setings;

    /**
     * Instance of LO_Ccb_Event_Categories
     *
     * @since 0.2.1
     * @var LO_Ccb_Event_Categories
     */
    protected $lo_ccb_event_categories;

    /**
     * Sets up our plugin.
     *
     * @since  0.0.0
     */
    protected function __construct()
    {
        self::$basename = plugin_basename(__FILE__);
        self::$url = plugin_dir_url(__FILE__);
        self::$path = plugin_dir_path(__FILE__);
    }

    /**
     * Creates or returns an instance of this class.
     *
     * @since   0.0.0
     * @return  Liquid_Outreach A single instance of this class.
     */
    public static function get_instance()
    {
        if (null === self::$single_instance) {
            self::$single_instance = new self();
        }

        return self::$single_instance;
    }

    /**
     * Add hooks and filters.
     * Priority needs to be
     * < 10 for CPT_Core,
     * < 5 for Taxonomy_Core,
     * and 0 for Widgets because widgets_init runs at init priority 1.
     *
     * @since  0.0.0
     */
    public function hooks()
    {
        add_action('init', array($this, 'init'), 0);
    } // END OF PLUGIN CLASSES FUNCTION

    /**
     * Activate the plugin.
     *
     * @since  0.0.0
     */
    public function _activate()
    {
        // Bail early if requirements aren't met.
        if (!$this->check_requirements()) {
            return;
        }

        $this->create_required_db_table();
        $this->update_required_user_role();

        // Make sure any rewrite functionality has been loaded.
        $this->init();
        flush_rewrite_rules();
    }

    public function update_required_user_role() {
        $role_class = new LO_Ccb_Outreach_Editor_Role();
        $role_class->add_role();
        $role_class->modify_existing_role();
    }

    /**
     * create required table
     *
     * @since 0.0.8
     */
    public function create_required_db_table()
    {
        global $wpdb;
        $event_table_name = $wpdb->prefix . 'lo_ccb_events_api_data';
        $group_table_name = $wpdb->prefix . 'lo_ccb_events_api_data';

        if ($wpdb->get_var("SHOW TABLES LIKE '$event_table_name'") != $event_table_name) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            $event_sql
                = "CREATE TABLE `$event_table_name` (  
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  
                        `ccb_event_id` bigint(20) unsigned NOT NULL,  
                        `ccb_group_id` bigint(20) unsigned DEFAULT NULL,  
                        `ccb_dep_id` bigint(20) unsigned DEFAULT NULL,  
                        `wp_post_id` bigint(20) unsigned DEFAULT NULL,  
                        `data` text COLLATE utf8mb4_unicode_520_ci NOT NULL,  
                        `md5_hash` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,  
                        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
                        `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
                        `last_synced` timestamp NULL DEFAULT NULL,  PRIMARY KEY (`id`)
                    ) $charset_collate;";

            dbDelta($event_sql);

        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$group_table_name'") != $group_table_name) {

            $group_sql
                = "CREATE TABLE `$group_table_name` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `ccb_group_id` bigint(20) unsigned NOT NULL,
                        `ccb_group_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
                        `ccb_dep_id` bigint(20) unsigned DEFAULT NULL,
                        `ccb_dep_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
                        `wp_post_id` bigint(20) unsigned DEFAULT NULL,
                        `data` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
                        `md5_hash` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
                        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `last_synced` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    ) $charset_collate;";

            dbDelta($group_sql);

        }

        update_option('liquid_outreach_db_version', SELF::DB_VERSION);
    }

    /**
     * Check if the plugin meets requirements and
     * disable it if they are not present.
     *
     * @since  0.0.0
     *
     * @return boolean True if requirements met, false if not.
     */
    public function check_requirements()
    {

        // Bail early if plugin meets requirements.
        if ($this->meets_requirements()) {
            return true;
        }

        // Add a dashboard notice.
        add_action('all_admin_notices', array($this, 'requirements_not_met_notice'));

        // Deactivate our plugin.
        add_action('admin_init', array($this, 'deactivate_me'));

        // Didn't meet the requirements.
        return false;
    }

    /**
     * Check that all plugin requirements are met.
     *
     * @since  0.0.0
     *
     * @return boolean True if requirements are met.
     */
    public function meets_requirements()
    {

        // Do checks for required classes / functions or similar.
        // Add detailed messages to $this->activation_errors array.
        return true;
    }

    /**
     * Init hooks
     *
     * @since  0.0.0
     */
    public function init()
    {

        // Bail early if requirements aren't met.
        if (!$this->check_requirements()) {
            return;
        }

        while ($this->update_table_structure()) {
            $this->update_table_structure();
        }

        // Load translated strings for plugin.
        load_plugin_textdomain('liquid-outreach', false,
            dirname(self::$basename) . '/languages/');

        // Initialize plugin classes.
        $this->plugin_classes();
    }

    /**
     * update old table structure
     * @return bool
     * @since 0.3.7
     */
    public function update_table_structure()
    {


        $current_db_version = get_option('liquid_outreach_db_version');

        if (empty($current_db_version) || $current_db_version < 2.0) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $event_table_name = $wpdb->prefix . 'lo_ccb_events_api_data';
            $group_table_name = $wpdb->prefix . 'lo_ccb_groups_api_data';

            if ($wpdb->get_var("SHOW TABLES LIKE '$event_table_name'") == $event_table_name) {

                $sql = "CREATE TABLE `$event_table_name` (  
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  
                        `ccb_event_id` bigint(20) unsigned NOT NULL,  
                        `ccb_group_id` bigint(20) unsigned DEFAULT NULL,  
                        `ccb_dep_id` bigint(20) unsigned DEFAULT NULL,  
                        `wp_post_id` bigint(20) unsigned DEFAULT NULL,  
                        `data` text COLLATE utf8mb4_unicode_520_ci NOT NULL,  
                        `md5_hash` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,  
                        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
                        `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
                        `last_synced` timestamp NULL DEFAULT NULL,  PRIMARY KEY (`id`)
                    ) $charset_collate;";
                dbDelta($sql);
            }

            if ($wpdb->get_var("SHOW TABLES LIKE '$group_table_name'") != $group_table_name) {

                $sql = "CREATE TABLE `$group_table_name`(
                            `id` bigint(20) unsigned NOT NULL  auto_increment , 
                            `ccb_group_id` bigint(20) unsigned NOT NULL  , 
                            `ccb_group_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL  , 
                            `ccb_dep_id` bigint(20) unsigned NULL  , 
                            `ccb_dep_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NULL  , 
                            `wp_post_id` bigint(20) unsigned NULL  , 
                            `data` text COLLATE utf8mb4_unicode_520_ci NOT NULL  , 
                            `md5_hash` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL  , 
                            `created` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP , 
                            `last_modified` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP , 
                            `last_synced` timestamp NULL  , 
                            PRIMARY KEY (`id`) 
                        ) $charset_collate;";
                dbDelta($sql);
            }

            return update_option('liquid_outreach_db_version', 2.0);
        }

        return false;
    }

    /**
     * Attach other plugin classes to the base plugin class.
     *
     * @since  0.0.0
     */
    public function plugin_classes()
    {

        $this->add_dev_classes();

        $this->lo_ccb_events = new LO_Ccb_Events($this);
        $this->lo_ccb_event_partners = new LO_Ccb_Event_Partners($this);
        $this->lo_ccb_event_categories = new LO_Ccb_Event_Categories($this);
        $this->lo_shortcodes = new LO_Shortcodes($this);
        $this->lo_ccb_events_info_setings = new LO_Ccb_Events_Info_Setings();

        if (is_admin()) {
            $this->lo_ccb_api_event_profiles = new Lo_Ccb_api_event_profiles($this);
            $this->lo_ccb_api_group_profile_from_id = new Lo_Ccb_api_group_profile_from_id($this);
            $this->lo_ccb_api_individual_profile = new Lo_Ccb_api_individual_profile($this);
            $this->lo_ccb_api_attendance_profile = new Lo_Ccb_api_attendance_profile($this);
            $this->lo_ccb_events_sync = new LO_Ccb_Events_Sync($this);
        } else {
            $this->lo_wp_template_loader = new LO_WP_Template_Loader();
        }

    }

    /**
     * log class include
     *
     * @since  0.0.6
     */
    public function add_dev_classes()
    {
        /*if (defined('CCB_ENV') && CCB_ENV == 'development') {*/
        if (file_exists(__DIR__ . '/dev/WP_Logging.php')) {
            include __DIR__ . '/dev/WP_Logging.php';
        }
        /*}*/
        include __DIR__ . '/dev/Logging_Mods.php';
    }

    /**
     * Deactivate the plugin.
     * Uninstall routines should be in uninstall.php.
     *
     * @since  0.0.0
     */
    public function _deactivate()
    {
        // Add deactivation cleanup functionality here.
        $role_class = new LO_Ccb_Outreach_Editor_Role();
        $role_class->delete_role();
    }

    /**
     * Deactivates this plugin, hook this function on admin_init.
     *
     * @since  0.0.0
     */
    public function deactivate_me()
    {

        // We do a check for deactivate_plugins before calling it, to protect
        // any developers from accidentally calling it too early and breaking things.
        if (function_exists('deactivate_plugins')) {
            deactivate_plugins(self::$basename);
        }
    }

    /**
     * Adds a notice to the dashboard if the plugin requirements are not met.
     *
     * @since  0.0.0
     */
    public function requirements_not_met_notice()
    {

        // Compile default message.
        $default_message
            = sprintf(__('Liquid Outreach is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.',
            'liquid-outreach'), admin_url('plugins.php'));

        // Default details to null.
        $details = null;

        // Add details if any exist.
        if ($this->activation_errors && is_array($this->activation_errors)) {
            $details = '<small>' . implode('</small><br /><small>', $this->activation_errors) .
                '</small>';
        }

        // Output errors.
        ?>
        <div id="message" class="error">
            <p><?php echo wp_kses_post($default_message); ?></p>
            <?php echo wp_kses_post($details); ?>
        </div>
        <?php
    }

    /**
     * Magic getter for our object.
     *
     * @since  0.0.0
     *
     * @param  string $field Field to get.
     * @throws Exception     Throws an exception if the field is invalid.
     * @return mixed         Value of the field.
     */
    public function __get($field)
    {
        switch ($field) {
            case 'version':
                return self::VERSION;
            case 'lo_ccb_events':
            case 'lo_ccb_event_partners':
            case 'lo_ccb_event_categories':
            case 'lo_ccb_events_sync':
            case 'lo_shortcodes':
            case 'lo_ccb_api_event_profiles':
            case 'lo_ccb_api_group_profile_from_id':
            case 'lo_ccb_api_individual_profile':
            case 'lo_ccb_api_attendance_profile':
            case 'lo_ccb_events_info_setings':
                return $this->$field;
            default:
                throw new Exception('Invalid ' . __CLASS__ . ' property: ' . $field);
        }
    }
}

/**
 * Grab the Liquid_Outreach object and return it.
 * Wrapper for Liquid_Outreach::get_instance().
 *
 * @since  0.0.0
 * @return Liquid_Outreach  Singleton instance of plugin class.
 */
function liquid_outreach()
{
    return Liquid_Outreach::get_instance();
}

// Kick it off.
add_action('plugins_loaded', array(liquid_outreach(), 'hooks'));

// Activation and deactivation.
register_activation_hook(__FILE__, array(liquid_outreach(), '_activate'));
register_deactivation_hook(__FILE__, array(liquid_outreach(), '_deactivate'));
