<?php
/**
 * Main plugin class for Advanced Sidebar Nav.
 *
 * This class handles the initialization and registration of widgets and blocks,
 * as well as asset management for the Advanced Sidebar Nav plugin.
 *
 * @package Advanced_Sidebar_Nav
 * @since   2.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Advanced Sidebar Nav class.
 *
 * @since 2.0
 */
final class Advanced_Sidebar_Nav {

	/**
	 * Plugin instance.
	 *
	 * @since 2.0
	 * @var Advanced_Sidebar_Nav|null
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * Sets up hooks for widget and block registration, and asset enqueuing.
	 *
	 * @since 2.0
	 */
	protected function __construct() {
		add_action( 'init', [ $this, 'maybe_register_block' ] );
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_assets' ] );
	}

	/**
	 * Initialize the plugin instance.
	 *
	 * @since 2.0
	 * @return Advanced_Sidebar_Nav Plugin instance.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the legacy widget.
	 *
	 * @since 2.0
	 */
	public function register_widget() {
		require_once ADVANCED_SIDEBAR_NAV_PLUGIN_DIR . 'widget/class-advanced-sidebar-nav-widget.php';
		register_widget( 'Advanced_Sidebar_Nav_Widget' );
	}

	/**
	 * Conditionally register the block for WordPress 5.8+.
	 *
	 * @since 2.0
	 */
	public function maybe_register_block() {
		// Only load block registration for WordPress 5.8+.
		if ( version_compare( get_bloginfo( 'version' ), ADVANCED_SIDEBAR_NAV_MIN_WP_VERSION_FOR_BLOCKS, '>=' ) ) {
			require_once ADVANCED_SIDEBAR_NAV_PLUGIN_DIR . 'block/class-advanced-sidebar-nav-block.php';
			$block_instance = new Advanced_Sidebar_Nav_Block();
			$block_instance->register_block();
		}
	}

	/**
	 * Register plugin assets.
	 *
	 * Registers CSS and JavaScript files for both frontend and block editor.
	 *
	 * @since 2.0
	 */
	public function register_assets() {
		wp_register_style(
			'advanced-sidebar-nav',
			ADVANCED_SIDEBAR_NAV_PLUGIN_URL . 'assets/style.css',
			[],
			ADVANCED_SIDEBAR_NAV_VERSION
		);

		wp_register_script(
			'advanced-sidebar-nav',
			ADVANCED_SIDEBAR_NAV_PLUGIN_URL . 'assets/script.js',
			[ 'jquery' ],
			ADVANCED_SIDEBAR_NAV_VERSION,
			true
		);
	}
}
