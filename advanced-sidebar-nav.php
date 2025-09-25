<?php
/**
 * Plugin Name: Advanced Sidebar Nav
 * Description: The best way to display navigation menus on sidebar, no matter how many depth!
 * Version: 1.1
 * Author: Nazmul Sabuz
 * Author URI: https://profiles.wordpress.org/nazsabuz/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'ADVANCED_SIDEBAR_NAV_VERSION', '1.1' );
define( 'ADVANCED_SIDEBAR_NAV_PLUGIN_FILE', __FILE__ );
define( 'ADVANCED_SIDEBAR_NAV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_NAV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_NAV_MIN_WP_VERSION_FOR_BLOCKS', '5.8' );

require_once ADVANCED_SIDEBAR_NAV_PLUGIN_DIR . 'includes/class-advanced-sidebar-nav.php';

Advanced_Sidebar_Nav::init();
