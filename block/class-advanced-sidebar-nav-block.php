<?php
/**
 * Block Registration Class
 *
 * Handles the registration of the Advanced Sidebar Nav block for WordPress 5.8+
 * where the block editor widget functionality is available.
 *
 * @package Advanced_Sidebar_Nav
 * @since 1.1
 * @author Nazmul Sabuz
 * @version 1.1
 * @license GPL-2.0
 */

/**
 * Advanced Sidebar Nav Block Class
 *
 * This class handles the conditional registration of the Advanced Sidebar Nav block
 * based on WordPress version and block editor widget availability.
 *
 * @package Advanced_Sidebar_Nav
 * @since 1.1
 */
class Advanced_Sidebar_Nav_Block {


	/**
	 * Block registration constructor.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Register the Advanced Sidebar Nav block.
	 *
	 * Registers the block with WordPress and sets up all necessary
	 * assets and functionality.
	 *
	 * @since 1.1
	 * @return void
	 */
	public function register_block() {
		// Register block type.
		register_block_type(
			'advanced-sidebar-nav/advanced-sidebar-nav',
			[
				'api_version'     => 3,
				'title'           => __( 'Advanced Sidebar Nav', 'advanced-sidebar-nav' ),
				'category'        => 'widgets',
				'icon'            => 'menu',
				'description'     => __( 'Display navigation menus in sidebar with advanced styling options.', 'advanced-sidebar-nav' ),
				'render_callback' => [ $this, 'render_block' ],
				'attributes'      => [
					'menu'        => [
						'type'    => 'string',
						'default' => '',
					],
					'theme'       => [
						'type'    => 'string',
						'default' => 'default',
					],
					'accentColor' => [
						'type'    => 'string',
						'default' => '#0f434f',
					],
				],
				'supports'        => [
					'html'    => false,
					'spacing' => [
						'margin'  => true,
						'padding' => true,
					],
				],
				'editor_script'   => 'advanced-sidebar-nav-block-editor',
				'editor_style'    => [
					'advanced-sidebar-nav-block-editor',
					'advanced-sidebar-nav',
				],
				'style'           => [
					'advanced-sidebar-nav-block',
					'advanced-sidebar-nav',
				],
			]
		);

		// Enqueue block assets.
		$this->enqueue_block_assets();
	}

	/**
	 * Render the Advanced Sidebar Nav block.
	 *
	 * Handles the server-side rendering of the block with proper
	 * styling and menu output.
	 *
	 * @since 1.1
	 * @param array $attributes Block attributes.
	 * @return string Rendered block HTML.
	 */
	public function render_block( $attributes ) {
		// Extract attributes with defaults.
		$menu         = isset( $attributes['menu'] ) ? $attributes['menu'] : '';
		$theme        = isset( $attributes['theme'] ) ? $attributes['theme'] : 'default';
		$accent_color = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#0f434f';

		// Return early if no menu is selected.
		if ( empty( $menu ) ) {
			return '<div class="advanced-sidebar-nav-block-placeholder">' .
				__( 'Please select a menu to display.', 'advanced-sidebar-nav' ) .
				'</div>';
		}

		// Build wrapper attributes.
		$classes = [
			'advanced-sidebar-nav',
			'advanced-sidebar-nav-' . esc_attr( $theme ),
			'advanced-sidebar-nav-block',
		];

		$wrapper_attributes = [
			'class' => implode( ' ', $classes ),
		];

		// Add CSS custom property for accent color.
		if ( ! empty( $accent_color ) ) {
			$wrapper_attributes['style'] = '--accent-color: ' . esc_attr( $accent_color ) . ';';
		}

		// Get menu HTML.
		$menu_output = wp_nav_menu(
			[
				'menu'            => esc_attr( $menu ),
				'menu_class'      => 'advanced-sidebar-menu',
				'container_class' => 'advanced-sidebar-nav-container',
				'container'       => false,
				'echo'            => false,
			]
		);

		// Return early if menu not found.
		if ( ! $menu_output ) {
			return '<div class="advanced-sidebar-nav-block-error">' .
				__( 'Menu not found. Please check your menu selection.', 'advanced-sidebar-nav' ) .
				'</div>';
		}

		// Build final HTML.
		$html  = '<div ' . get_block_wrapper_attributes( $wrapper_attributes ) . '>';
		$html .= wp_kses_post( $menu_output );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * Enqueues the necessary CSS and JavaScript files for the block editor.
	 *
	 * @since 1.1
	 * @return void
	 */
	private function enqueue_block_assets() {
		// Register block editor styles.
		wp_register_style(
			'advanced-sidebar-nav-block-editor',
			ADVANCED_SIDEBAR_NAV_PLUGIN_URL . 'block/build/index.css',
			[ 'advanced-sidebar-nav' ],
			ADVANCED_SIDEBAR_NAV_VERSION
		);

		// Register block editor scripts.
		wp_register_script(
			'advanced-sidebar-nav-block-editor',
			ADVANCED_SIDEBAR_NAV_PLUGIN_URL . 'block/build/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
			ADVANCED_SIDEBAR_NAV_VERSION,
			true
		);

		// Register frontend styles.
		wp_register_style(
			'advanced-sidebar-nav-block',
			ADVANCED_SIDEBAR_NAV_PLUGIN_URL . 'block/build/style-index.css',
			[ 'advanced-sidebar-nav' ],
			ADVANCED_SIDEBAR_NAV_VERSION
		);
	}

	/**
	 * Register REST API routes for block functionality.
	 *
	 * Registers the REST endpoint used by the block editor to fetch menu HTML.
	 *
	 * @since 1.1
	 * @return void
	 */
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

	/**
	 * Get menu HTML via REST API.
	 *
	 * Handles REST API requests to fetch menu HTML for the block editor.
	 *
	 * @since 1.1
	 * @param WP_REST_Request $request The REST request object.
	 * @return array|WP_Error Menu HTML data or error.
	 */
	public function get_menu_html( $request ) {
		$menu_slug    = $request->get_param( 'menu_slug' );
		$theme        = $request->get_param( 'theme' );
		$accent_color = $request->get_param( 'accent_color' );

		// Build wrapper attributes.
		$classes            = [ 'advanced-sidebar-nav', 'advanced-sidebar-nav-' . $theme ];
		$wrapper_attributes = get_block_wrapper_attributes(
			[
				'class' => implode( ' ', $classes ),
				'style' => ! empty( $accent_color ) ? '--accent-color: ' . esc_attr( $accent_color ) . ';' : '',
			]
		);

		// Get menu HTML.
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
