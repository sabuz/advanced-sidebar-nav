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

	/**
	 * Minimum WordPress version required for block editor widgets.
	 *
	 * @since 1.1
	 */
	const MIN_WP_VERSION_FOR_BLOCKS = '5.8';

	protected static $instance = null;

	protected function __construct() {
		// actions
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_scripts' ] );
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		add_action( 'init', [ $this, 'init_block_registration' ] );
	}

	// create instance
	public static function init() {
		if ( self::$instance == null ) {
			$instance = new self();
		}

		return $instance;
	}

	// register assets
	public function enqueue_scripts() {
		// Load asset file for proper dependency management
		$asset_file = plugin_dir_path( __FILE__ ) . 'assets/style.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : [
			'dependencies' => [],
			'version'      => '1.1.0',
		];

		wp_register_style(
			'advanced-sidebar-nav',
			plugin_dir_url( __FILE__ ) . 'assets/style.css',
			$asset['dependencies'],
			$asset['version']
		);

		wp_register_script(
			'advanced-sidebar-nav',
			plugin_dir_url( __FILE__ ) . 'assets/script.js',
			[ 'jquery' ],
			'1.1.0'
		);
	}

	// register wp widget
	public function register_widget() {
		require_once plugin_dir_path( __FILE__ ) . 'widget/class-advanced-sidebar-nav-widget.php';

		register_widget( 'Advanced_Sidebar_Nav_Widget' );
	}

	// Initialize block registration for WordPress 5.8+
	public function init_block_registration() {
		// Only load block registration for WordPress 5.8+.
		if ( version_compare( get_bloginfo( 'version' ), self::MIN_WP_VERSION_FOR_BLOCKS, '>=' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'block/class-advanced-sidebar-nav-block.php';
			new Advanced_Sidebar_Nav_Block();
		}
	}

}

Advanced_Sidebar_Nav::init();
