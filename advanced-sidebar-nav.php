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
		// actions
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_scripts' ] );
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		add_action( 'init', [ $this, 'init_block_registration' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	// create instance
	public static function instance() {
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
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'block/class-block-registration.php';
			new Advanced_Sidebar_Nav_Block_Registration();
		}
	}

	// Register REST API routes
	public function register_rest_routes() {
		register_rest_route(
			'advanced-sidebar-nav/v1',
			'/menu/(?P<menu_slug>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_menu_html' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => [
					'menu_slug'    => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'theme'        => [
						'required'          => false,
						'type'              => 'string',
						'default'           => 'default',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'accent_color' => [
						'required'          => false,
						'type'              => 'string',
						'default'           => '#0f434f',
						'sanitize_callback' => 'sanitize_hex_color',
					],
				],
			]
		);
	}

	// Get menu HTML via REST API
	public function get_menu_html( $request ) {
		$menu_slug    = $request->get_param( 'menu_slug' );
		$theme        = $request->get_param( 'theme' );
		$accent_color = $request->get_param( 'accent_color' );

		// Build wrapper attributes
		$classes            = [ 'advanced-sidebar-nav', 'advanced-sidebar-nav-' . $theme ];
		$wrapper_attributes = get_block_wrapper_attributes(
			[
				'class' => implode( ' ', $classes ),
				'style' => ! empty( $accent_color ) ? '--accent-color: ' . esc_attr( $accent_color ) . ';' : '',
			]
		);

		// Get menu HTML
		$nav_menu = wp_nav_menu(
			[
				'menu'            => $menu_slug,
				'menu_class'      => 'advanced-sidebar-menu',
				'container_class' => 'advanced-sidebar-nav-container',
				'container'       => false,
				'echo'            => false,
			]
		);

		if ( ! $nav_menu ) {
			return new WP_Error( 'menu_not_found', 'Menu not found', [ 'status' => 404 ] );
		}

		$html  = '<div ' . wp_kses_data( $wrapper_attributes ) . '>';
		$html .= wp_kses_post( $nav_menu );
		$html .= '</div>';

		return [
			'html'         => $html,
			'menu_slug'    => $menu_slug,
			'theme'        => $theme,
			'accent_color' => $accent_color,
		];
	}
}

Advanced_Sidebar_Nav::instance();
