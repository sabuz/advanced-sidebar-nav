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

final class Advanced_Sidebar_Nav
{
    protected static $instance = null;

    protected function __construct()
    {
        // methods
        $this->load_files();

        // actions
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('widgets_init', array($this, 'register_widget'));
    }

    // create instance
    public static function instance()
    {
        if (self::$instance == null) {
            $instance = new self;
        }

        return $instance;
    }

    // load required files
    public function load_files()
    {
        require_once plugin_dir_path(__FILE__) . 'inc/options.php';
        require_once plugin_dir_path(__FILE__) . 'inc/widget.php';
    }

    // register assets
    public function enqueue_scripts()
    {
        wp_register_style('advanced-sidebar-nav', plugin_dir_url(__FILE__) . 'assets/advanced-sidebar-nav.css');
        wp_register_script('advanced-sidebar-nav', plugin_dir_url(__FILE__) . 'assets/advanced-sidebar-nav.js');
    }

    // register wp widget
    public function register_widget()
    {
        register_widget('Advanced_Sidebar_Nav_Widget');
    }
}

add_action('plugins_loaded', function () {
    Advanced_Sidebar_Nav::instance();
});

function create_block_advanced_sidebar_nav_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_advanced_sidebar_nav_block_init' );
