<?php
/**
 * Plugin Name:       Advanced Vertical Menu
 * Plugin URI:        https://wordpress.org/plugins/advanced-sidebar-nav/
 * Description:       Create beautiful vertical navigation menus anywhere on your site! Features both modern block editor support and legacy widget compatibility. Perfect for sidebars, footers, or any content area.
 * Version:           2.0
 * Requires at least: 4.0
 * Requires PHP:      5.6.20
 * Author:            Nazmul Sabuz
 * Author URI:        https://profiles.wordpress.org/nazsabuz/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-sidebar-nav
 * Domain Path:       /languages
 *
 * @package           Advanced_Sidebar_Nav
 * @version           2.0
 * @link              https://wordpress.org/plugins/advanced-sidebar-nav/
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'ADVANCED_SIDEBAR_NAV_VERSION', '2.0' );
define( 'ADVANCED_SIDEBAR_NAV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_NAV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ADVANCED_SIDEBAR_NAV_MIN_WP_VERSION_FOR_BLOCKS', '5.8' );

/**
 * Load the main plugin class.
 *
 * @since 1.0.0
 */
require_once ADVANCED_SIDEBAR_NAV_PLUGIN_DIR . 'includes/class-advanced-sidebar-nav.php';

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
Advanced_Sidebar_Nav::init();
