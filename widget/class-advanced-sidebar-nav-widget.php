<?php
/**
 * Advanced Sidebar Navigation Widget
 *
 * A WordPress widget that displays navigation menus in the sidebar with customizable
 * styling options including accent colors and themes.
 *
 * @package Advanced_Sidebar_Nav
 * @since 1.0
 * @author Nazmul Sabuz
 * @version 2.0.1
 * @license GPL-2.0
 */

/**
 * Advanced Sidebar Navigation Widget Class
 *
 * Extends WP_Widget to create a custom widget for displaying navigation menus
 * with advanced styling options and CSS custom property support.
 *
 * @package Advanced_Sidebar_Nav
 * @since 1.0
 */
class Advanced_Sidebar_Nav_Widget extends WP_Widget {

	/**
	 * Widget constructor.
	 *
	 * Initializes the widget with appropriate title and description based on
	 * WordPress version. Shows deprecation notice for WordPress 5.5+.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$current_wp_version = get_bloginfo( 'version' );
		$widget_title       = __( 'Advanced Sidebar Nav', 'advanced-sidebar-nav' );
		$widget_opts        = [
			'classname'             => 'advanced-sidebar-nav-widget',
			'description'           => __( 'Create beautiful vertical navigation menus with unlimited depth and advanced styling options!', 'advanced-sidebar-nav' ),
			'show_instance_in_rest' => true,
		];

		// Show deprecation notice for WordPress 5.8+.
		if ( version_compare( $current_wp_version, ADVANCED_SIDEBAR_NAV_MIN_WP_VERSION_FOR_BLOCKS, '>=' ) ) {
			$widget_title               = __( 'Advanced Sidebar Nav (Legacy)', 'advanced-sidebar-nav' );
			$widget_opts['description'] = __( 'This widget is deprecated. Please use our "Advanced Vertical Menu" block for better results.', 'advanced-sidebar-nav' );
		}

		parent::__construct( false, $widget_title, $widget_opts );
	}

	/**
	 * Allow CSS custom properties for widget styling.
	 *
	 * Adds CSS custom properties to the WordPress safe_style_css filter
	 * to enable custom styling via CSS variables.
	 *
	 * @since 1.0
	 * @param array $styles Array of allowed CSS properties.
	 * @return array Modified array with custom properties added.
	 */
	public function allow_css_custom_properties( $styles ) {
		$styles[] = '--accent-color';
		return $styles;
	}

	/**
	 * Outputs the widget content.
	 *
	 * Displays the widget on the frontend with proper styling, menu output,
	 * and CSS custom property support for accent colors.
	 *
	 * @since 1.0
	 * @param array $args     Widget arguments including before_widget, after_widget, etc.
	 * @param array $instance Widget instance settings including title, menu, color.
	 */
	public function widget( $args, $instance ) {
		// Enqueue required assets.
		wp_enqueue_script( 'advanced-sidebar-nav' );
		wp_enqueue_style( 'advanced-sidebar-nav' );

		// Prepare widget wrapper with custom styling.
		$before_widget = $args['before_widget'];

		// Add CSS custom property for accent color if not already present.
		if ( strpos( $before_widget, 'style=' ) === false && ! empty( $instance['color'] ) ) {
			$before_widget = preg_replace( '/(<[^>]+)(>)/', '$1 style="--accent-color: ' . esc_attr( $instance['color'] ) . '"$2', $before_widget, 1 );
		}

		// Temporarily allow CSS custom properties.
		add_filter( 'safe_style_css', [ $this, 'allow_css_custom_properties' ] );

		// Output widget wrapper.
		echo wp_kses_post( $before_widget );

		// Remove CSS custom properties filter.
		remove_filter( 'safe_style_css', [ $this, 'allow_css_custom_properties' ] );

		// Output widget title if set.
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . wp_kses_post( $args['after_title'] );
		}

		// Output navigation menu if selected.
		if ( ! empty( $instance['menu'] ) ) {
			$menu_output = wp_nav_menu(
				[
					'menu'            => esc_attr( $instance['menu'] ),
					'menu_class'      => 'advanced-sidebar-menu',
					'container_class' => 'advanced-sidebar-nav advanced-sidebar-nav-default',
					'echo'            => false,
				]
			);

			if ( $menu_output ) {
				echo wp_kses_post( $menu_output );
			}
		}

		// Close widget wrapper.
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Updates widget instance settings.
	 *
	 * Sanitizes and validates widget form data before saving to database.
	 * Ensures all input is properly cleaned and validated.
	 *
	 * @since 1.0
	 * @param array $new_instance New widget instance data from form submission.
	 * @param array $old_instance Previous widget instance data.
	 * @return array Updated and sanitized widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];

		// Sanitize title - strip all HTML tags.
		$instance['title'] = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		// Sanitize menu selection.
		$instance['menu'] = ! empty( $new_instance['menu'] ) ? sanitize_text_field( $new_instance['menu'] ) : '';

		// Sanitize and validate color - ensure it's a valid hex color.
		$instance['color'] = ! empty( $new_instance['color'] ) ? sanitize_hex_color( $new_instance['color'] ) : '#0f434f';

		return $instance;
	}

	/**
	 * Outputs widget form in admin.
	 *
	 * Creates the widget configuration form in the WordPress admin using
	 * the Kira Widget Options Framework for consistent styling.
	 *
	 * @since 1.0
	 * @param array $instance Current widget instance settings.
	 */
	public function form( $instance ) {
		global $kira_widget_options_framework;

		// Initialize framework if not already loaded.
		if ( ! $kira_widget_options_framework instanceof Kira_Widget_Options_Framework ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-kira-widget-options-framework.php';
			$kira_widget_options_framework = new Kira_Widget_Options_Framework();
		}

		// Title field.
		$kira_widget_options_framework->text(
			[
				'name'        => esc_attr( $this->get_field_name( 'title' ) ),
				'label'       => __( 'Title:', 'advanced-sidebar-nav' ),
				'description' => '',
				'value'       => isset( $instance['title'] ) ? $instance['title'] : '',
			]
		);

		// Menu selection field.
		$kira_widget_options_framework->select(
			[
				'name'        => esc_attr( $this->get_field_name( 'menu' ) ),
				'label'       => __( 'Select Menu:', 'advanced-sidebar-nav' ),
				'description' => '',
				'options'     => 'menu',
				'value'       => isset( $instance['menu'] ) ? $instance['menu'] : '',
			]
		);

		// Theme selection field.
		$kira_widget_options_framework->select(
			[
				'name'        => esc_attr( $this->get_field_name( 'theme' ) ),
				'label'       => __( 'Select Theme:', 'advanced-sidebar-nav' ),
				'description' => __( 'More themes are coming soon...', 'advanced-sidebar-nav' ),
				'options'     => [
					'default' => __( 'Default', 'advanced-sidebar-nav' ),
				],
				'value'       => isset( $instance['theme'] ) ? $instance['theme'] : 'default',
			]
		);

		// Color picker field.
		$kira_widget_options_framework->color(
			[
				'name'        => esc_attr( $this->get_field_name( 'color' ) ),
				'label'       => __( 'Accent Color:', 'advanced-sidebar-nav' ),
				'description' => '',
				'value'       => isset( $instance['color'] ) ? $instance['color'] : '#0F434F',
				'default'     => '#0F434F',
			]
		);
	}
}
