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

final class Advanced_Sidebar_Nav {

	protected static $instance = null;

	protected function __construct() {
		// methods
		$this->load_files();

		// actions
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		add_action( 'init', [ $this, 'create_block_advanced_sidebar_nav_block_init' ] );
	}

	// create instance
	public static function instance() {
		if ( self::$instance == null ) {
			$instance = new self();
		}

		return $instance;
	}

	// load required files
	public function load_files() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/options.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/widget.php';
	}

	// register assets
	public function enqueue_scripts() {
		wp_register_style( 'advanced-sidebar-nav', plugin_dir_url( __FILE__ ) . 'assets/advanced-sidebar-nav.css' );
		wp_register_script( 'advanced-sidebar-nav', plugin_dir_url( __FILE__ ) . 'assets/advanced-sidebar-nav.js' );
	}

	// register wp widget
	public function register_widget() {
		register_widget( 'Advanced_Sidebar_Nav_Widget' );
	}

	// Block registration
	public function create_block_advanced_sidebar_nav_block_init() {
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			wp_register_block_types_from_metadata_collection(
				__DIR__ . '/blocks/advanced-sidebar-nav/build',
				__DIR__ . '/blocks/advanced-sidebar-nav/build/blocks-manifest.php'
			);
			return;
		}

		if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
			wp_register_block_metadata_collection(
				__DIR__ . '/blocks/advanced-sidebar-nav/build',
				__DIR__ . '/blocks/advanced-sidebar-nav/build/blocks-manifest.php'
			);
		}

		$manifest_data = require __DIR__ . '/blocks/advanced-sidebar-nav/build/blocks-manifest.php';
		foreach ( array_keys( $manifest_data ) as $block_type ) {
			register_block_type( __DIR__ . "/blocks/advanced-sidebar-nav/build/{$block_type}" );
		}
	}
}

Advanced_Sidebar_Nav::instance();
