<?php
/*
 * Plugin Name: Tour Operator Plugin
 * Plugin URI: https://www.lsdev.biz/product/tour-operator-plugin/
 * Description: The Tour Operator plugin core contains the Accommodation, Destination and Tour post types. Use these core post types to build day-by-day tour itineraries that map out of the progress of each tour through the various accommodations and destinations that are stayed at along the way.
 * Author: LightSpeed
 * Version: 1.0.0
 * Author URI: https://www.lsdev.biz/
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: tour-operator
 * Domain Path: /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('TO_PATH',  plugin_dir_path( __FILE__ ) );
define('TO_CORE',  __FILE__ );
define('TO_URL',  plugin_dir_url( __FILE__ ) );
define('TO_VER',  '1.1.0' );

require_once( TO_PATH . 'module.php' );