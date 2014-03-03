<?php
/*
Plugin Name: Ad Manager
Description: A plugin for creating and managing ads.
Version: 0.1
Author: Kurt Karolenko
Author Email: kurtkarolenko@gmail.com
License:

	Copyright 2014  Kurt Karolenko  (email : kurtkarolenko@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('EasyAdManager')) {
    class EasyAdManager {

        public $eam_db_version = "1.0";

        /**
         * Initializes the plugin by setting localization, filters, and administration functions.
         */
        public function __construct() {

            // update the plugin
            add_action( 'plugins_loaded', array( $this, 'update_db_check') );

            // create custom plugin settings menu
            add_action('admin_init', array( $this, 'init_settings' ));
            add_action('admin_menu', array( $this, 'add_menu' ));
            
            //load_plugin_textdomain( 'easy_ad_manager', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

            // Register site styles and scripts
            add_action( 'admin_enqueue_scripts', array( &$this, 'register_plugin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );

            // Admin AJAX
            add_action( 'wp_ajax_eam_get_selected', array( &$this, 'get_selected' ) );
            add_action( 'wp_ajax_eam_create_new_ad', array( &$this, 'create_new_ad') );
            add_action( 'wp_ajax_eam_update_ad', array( &$this, 'update_ad' ) );

        }

        /**
         * Register necessary settings.
         */
        public function init_settings() {

            register_setting('eam_plugin', 'expire_to');

        }

        /**
         * Adds the sub-menu item "Easy Ad Manager" under the Settings section.
         */
        public function add_menu() {

            // create a new sub-menu for the plugin settings
            add_options_page('Easy Ad Manager', 'Easy Ad Manager', 'manage_options', 'eam_plugin', array( $this, 'settings_page') );

        }

        /**
         * Loads the settings page if the user has the correct permissions.
         */
        public function settings_page() {

            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Load the settings page file
            include(sprintf("%s/settings.php", dirname(__FILE__)));
        }

        /**
         * Registers and enqueues plugin-specific styles.
         */
        public function register_plugin_styles($hook) {

            if( 'settings_page_eam_plugin' != $hook)
                return;
            wp_enqueue_style( 'eam_styles', plugins_url( 'EasyAdManager/css/plugin.css'));
            
        }

        /**
         * Registers and enqueues plugin-specific scripts.
         */
        public function register_plugin_scripts($hook) {

            if( 'settings_page_eam_plugin' != $hook )
                return;
            wp_enqueue_script( 'eam_scripts', plugins_url( 'EasyAdManager/js/plugin.js'));
	    wp_localize_script( 'eam_scripts', 'ajax_object',
		    array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
        }

        /**
         * When activated, create a table or upgrade.
         */
        public function install() {

            global $wpdb;

            $table_name = $wpdb->prefix . "eam_ads";

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name tinytext,
                type tinytext NOT NULL,
                enabled tinyint(1) DEFAULT 0 NOT NULL,
                link VARCHAR(2048) DEFAULT '',
                data text NOT NULL,
                expires tinyint(1) DEFAULT 0 NOT NULL,
                exp_date datetime,
                PRIMARY KEY (id)
                );";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            dbDelta( $sql );

            add_option( "eam_db_version", $eam_db_version );
        }

        /**
         * Adds the new ad to the database.
         */
        public function create_new_ad() {

            global $wpdb;

    	    $ad_details = json_decode($_POST['ad_details'], true);

    	    file_put_contents('test.txt',$ad_details);
	
            $table_name = $wpdb->prefix . "eam_ads";

    	    $wpdb->query( 
                $wpdb->prepare(
            		"INSERT INTO $table_name
            		 (name, type, enabled, link, data, expires, exp_date)
            		 VALUES (
            			%s, %s, %s, %s, %s, %s, %s
            		 )",
            		$ad_details['name'],
            		$ad_details['type'],
            		$ad_details['enabled'],
            		$ad_details['link'],
            		$ad_details['data'],
            		$ad_details['expires'],
            		$ad_details['exp_date']	
                )
            );
    	
            die();
        }

        /**
         * Deactivate and uninstall the 
         */
        public function uninstall() {

            global $wpdb;

            /**
             * drop table
             */

            delete_option( "eam_db_version" );

        }

        /**
         * Called to place an ad
         * Also checks if the ad has expired or not
         */
        public function display($ad_ID) {

            global $wpdb;

            /**
             * if id exists
             *      check date
             * else
             *      no display
             */

        }

        /**
         * Export <option> tags for the settings page
         */
        public function get_select_options() {

            global $wpdb;

            $table_name = $wpdb->prefix . "eam_ads";

            // Get ads and export as <option id="id">name</option>
    	    $ads = $wpdb->get_results( 
        		"
        		SELECT id, name, type, enabled, link, data, expires, exp_date 
        		FROM $table_name
        		"
    	    );

    	    $options = "";

    	    if (sizeof($ads) > 0) {

        		foreach ( $ads as $ad ) {	
        			$options .= "<option ad_id='$ad->id'>$ad->name</option>";
        		}
    	    }

    	    return $options;

    	    die();
    	}

        /**
         * Deactivate
         */
        public function get_selected() {

            global $wpdb;

            /**
             * if id exists
             *      send contents
             * else
             *      send error
             */

        }

        /**
         * Is this ad's details valid for a single-type ad?
         */
        public function single_valid() {

        }

        /**
         * Is this ad's details valid for a rotating-type ad?
         */
        public function rotate_valid() {

        }

        /**
         * Is this ad's details valid for an alternate-type ad?
         */
        public function alt_valid() {

        }

        /**
         * Is this date valid?
         */
        public function isValidDate($datetime) {
            //return DateTime::createFromFormat('Y-m-d H:i:s');
        }

        /**
         * Removes current ad from the database
         */
        public function remove_ad() {

            global $wpdb;

            $table_name = $wpdb->prefix . "eam_ads";

    	    $ad_id = $_POST['ad_id'];

    	    $wpdb->query(
                $wpdb->prepare(
            		"DELETE from $table_name
            		 WHERE id = %d",
            		$ad_id
    	        )
            );
        }

        /**
         * Updates current ad settings
         */
        public function update_ad() {

            global $wpdb;

    	    $ad_details = json_decode($_POST['ad_details'], true);
    		
            $table_name = $wpdb->prefix . "eam_ads";

    	    $wpdb->query(
                $wpdb->prepare(
            		"UPDATE $table_name
            		 WHERE id=%d
            		 SET
            			name=%s,
            			type=%s,
            			enabled=%s,
            			link=%s,
            			data=%s,
            			expires=%s,
            			exp_date=%s",
            		$ad_details['id'],
            		$ad_details['name'],
            		$ad_details['type'],
            		$ad_details['enabled'],
            		$ad_details['link'],
            		$ad_details['data'],
            		$ad_details['expires'],
            		$ad_details['exp_date']	
        	    )
            );
    		
    	    die();
        }

        /**
         * If a new version of the plugin is activated, install the new db
         */
        public function update_db_check() {
            
            global $eam_db_version;

            if (get_site_option( 'eam_db_version' ) != $eam_db_version) {

                install();

            }
        }
    }

    // Run on install
    register_activation_hook( __FILE__, array( 'EasyAdManager', 'install' ));
    register_deactivation_hook(__FILE__, array('EasyAdManager', 'uninstall'));

    $eam = new EasyAdManager();

    // Add settings page link to plugin on plugin page
    if (isset($eam)) {

        function eam_plugin_settings_link($links) {
            $settings_link = '<a href="options-general.php?page="eam_plugin">Settings</a>';
            array_unshift($links, $settings_link);
            return links;
        }

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", "eam_plugin_settings_link");
    }
}
