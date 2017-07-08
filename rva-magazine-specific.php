<?php
/*
Plugin Name: RVA Magazine Specific Functions
Plugin URI: 
Description: Custom functions required by RVA Magazine site
Author: Matthew Ringer
Text Domain: rva_magazine_specific
Domain Path: 
Author URI: http://unfork.me
Version: 1.0.0
License: GPLv2
*/

global $wpdb;
/* define the Root for URL and Document */

define('RVA_MAG_DOCROOT',	dirname(__FILE__));
define('RVA_MAG_PLUGURL',	plugin_dir_url(__FILE__));
define('RVA_MAG_WEBROOT',	str_replace(getcwd(), home_url(), dirname(__FILE__)));
define('RVA_MAG_DOMAIN',	'rva_magazine_specific');

/* load all files  */
require_once RVA_MAG_DOCROOT . '/lib/activate_deactivate.php';      // Activation and deactivation code 
require_once RVA_MAG_DOCROOT . '/lib/functions.php';                // Globally available utility functions 
require_once RVA_MAG_DOCROOT . '/lib/Globals_Container.php';
require_once RVA_MAG_DOCROOT . '/lib/Magazine_Post_Type.php';

// Admin
//require_once RVA_MAG_DOCROOT . '/lib/admin/Input.php';
require_once RVA_MAG_DOCROOT . '/lib/admin/admin-posts.php';        // For adding/editing custom post options to RVA
require_once RVA_MAG_DOCROOT . '/lib/admin/admin-functions.php';    // 
require_once RVA_MAG_DOCROOT . '/lib/admin/admin-settings.php';     // RVA Magazine Theme settings
require_once RVA_MAG_DOCROOT . '/lib/admin/admin-stats.php';        // RVA Magazine Theme settings

// Structure
require_once RVA_MAG_DOCROOT . '/lib/structure/advertising.php';    // DFP Ad short code
require_once RVA_MAG_DOCROOT . '/lib/structure/components.php';    // DFP Ad short code

//Single Category selection
require_once RVA_MAG_DOCROOT . '/vendor/taxonomy-single-term/class.taxonomy-single-term.php';
require_once RVA_MAG_DOCROOT . '/vendor/taxonomy-single-term/walker.taxonomy-single-term.php';

use RVAMag\Magazine_Post_Type as Magazine_Post_Type;

/* plugin install and uninstall hooks */ 
register_activation_hook(__FILE__, 'rva_activation' );
register_deactivation_hook(__FILE__, 'rva_deactivation');
//register_uninstall_hook(__FILE__, 'rva_unistall_plugin');

//Register Custom Types
Magazine_Post_Type::init_post_type();

/*Plugin version setup*/
// if(!get_option('rva_plugin_version') || get_option('rva_plugin_version') < 1.0)
// {
// 	add_action("init", "rva_update_plugin");
// }

/**
 *
 */
function load_custom_admin_assets($hook) {
	wp_enqueue_script( 'jquery_datetimepicker_js', plugins_url('assets/js/jquery.datetimepicker.full.min.js', __FILE__) );
	wp_enqueue_style( 'jquery_datetimepicker_js', plugins_url('assets/css/jquery.datetimepicker.min.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'load_custom_admin_assets' );

//sanitizing values
function rva_string_sanitize($s) {
    $result = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($s, ENT_QUOTES));
    return $result;
}

add_action('plugins_loaded', 
//'rva_load_domain'
    (function() {
        $plugin_dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( RVA_MAG_DOMAIN, false, $plugin_dir );
    })
);

// @ini_set( 'upload_max_size' , '64M' );
// @ini_set( 'post_max_size', '64M');
// @ini_set( 'max_execution_time', '300' );

?>