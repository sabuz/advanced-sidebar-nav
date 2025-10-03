<?php
/**
 * Kira Widget Options Framework
 *
 * A comprehensive framework for creating custom widget options with various field types.
 * This class provides methods to generate HTML form fields for WordPress widgets,
 * including text, textarea, select, radio, checkbox, and color picker fields.
 *
 * @package Kira_Widget_Options_Framework
 * @since 1.0
 * @author Nazmul Sabuz
 * @version 1.1
 * @license GPL-2.0
 * @link https://github.com/sabuz/kira-widget-options-framework
 * @see https://github.com/sabuz/kira-widget-options-framework/blob/master/README.md
 */

/**
 * Main framework class for widget options
 *
 * This class handles the initialization and provides methods for creating
 * various types of form fields for WordPress widgets. It includes CSS and
 * JavaScript for enhanced functionality like color pickers.
 *
 * @package Kira_Widget_Options_Framework
 * @since 1.0
 */
class Kira_Widget_Options_Framework {

	/**
	 * Constructor
	 *
	 * Initializes the framework by hooking into WordPress admin actions
	 * to add necessary CSS and JavaScript for widget functionality.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_head-widgets.php', [ $this, 'header_scripts' ], 99 );
		add_action( 'admin_footer-widgets.php', [ $this, 'footer_scripts' ], 99 );
	}

	/**
	 * Output CSS styles for widget controls
	 *
	 * Adds custom CSS styles to the admin head specifically for the widgets page.
	 * These styles ensure proper layout and appearance of widget control groups.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function header_scripts() {
		echo '<style>
            .kira-widget-control-group-wrap {
                display: block;
                width: 100%;
                clear: both;
                margin-bottom: 5px;
            }
            .kira-widget-control-group-wrap label {
                display: block;
                clear: both;
            }
        </style>';
	}

	/**
	 * Output JavaScript for enhanced widget functionality
	 *
	 * Adds JavaScript to initialize color picker functionality for widget controls.
	 * The script handles both existing widgets and dynamically added/updated widgets.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function footer_scripts() {
		echo '<script>
            (function($) {
                function initColorPicker(widget) {
                    $(".color-picker", widget).wpColorPicker({
                        change: function(e, ui) {
                            $(e.target).val(ui.color.toString());
                            $(e.target).trigger("change");
                        },
                        clear: function(e, ui) {
                            $(e.target).trigger("change");
                        }
                    });
                }

                $(document).ready(function() {
                    $("#widgets-right .widget:has(.color-picker)").each(function() {
                        initColorPicker($(this));
                    });
                });

                $(document).on("widget-added widget-updated", function(event, widget) {
                    initColorPicker(widget);
                });
            })(jQuery);
		</script>';
	}

	/**
	 * Helper method to generate option arrays for select fields
	 *
	 * Creates associative arrays of options for select, radio, and checkbox fields
	 * based on WordPress data sources like pages, posts, menus, and users.
	 *
	 * @since 1.0
	 * @param string $args The type of data to retrieve ('page', 'post', 'menu', 'user').
	 * @return array Associative array of options (ID => Name/Title)
	 */
	protected function helper( $args ) {
		$arr = [];

		switch ( $args ) {
			case 'page':
				$pages = get_posts(
					[
						'post_type'      => 'page',
						'orderby'        => 'date',
						'order'          => 'DESC',
						'posts_per_page' => -1,
					]
				);
				if ( $pages ) {
					foreach ( $pages as $page ) {
						$arr[ $page->ID ] = $page->post_title;
					}
				}

				break;

			case 'post':
				$posts = get_posts(
					[
						'post_type'      => 'post',
						'orderby'        => 'date',
						'order'          => 'DESC',
						'posts_per_page' => -1,
					]
				);
				if ( $posts ) {
					foreach ( $posts as $post ) {
						$arr[ $post->ID ] = $post->post_title;
					}
				}

				break;

			case 'menu':
				$menus = wp_get_nav_menus();
				if ( $menus ) {
					foreach ( $menus as $menu ) {
						$arr[ $menu->term_id ] = $menu->name;
					}
				}

				break;

			case 'user':
				$users = get_users();
				if ( $users ) {
					foreach ( $users as $user ) {
						$arr[ $user->ID ] = $user->display_name;
					}
				}

				break;
		}

		return $arr;
	}

	/**
	 * Get allowed HTML tags for widget form fields
	 *
	 * @since 1.0
	 * @return array Allowed HTML tags and attributes
	 */
	private function get_allowed_html() {
		return [
			'p'        => [],
			'label'    => [
				'for'   => [],
				'class' => [],
			],
			'input'    => [
				'type'               => [],
				'name'               => [],
				'class'              => [],
				'value'              => [],
				'id'                 => [],
				'data-default-color' => [],
			],
			'textarea' => [
				'name'  => [],
				'class' => [],
				'id'    => [],
			],
			'select'   => [
				'name'  => [],
				'class' => [],
				'id'    => [],
			],
			'option'   => [
				'value'    => [],
				'selected' => [],
			],
			'span'     => [
				'class' => [],
			],
		];
	}

	/**
	 * Generate a text input field
	 *
	 * Creates a single-line text input field with label and optional description.
	 * Perfect for simple text inputs like titles, names, or short values.
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute.
	 *     @type string $label       Field label text.
	 *     @type string $description Optional description text.
	 *     @type string $value       Current field value.
	 *     @type string $html_class  Additional CSS classes.
	 *     @type string $html_id     HTML ID attribute.
	 * }
	 * @return void
	 */
	public function text( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'value'       => '',
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$value       = esc_attr( $args['value'] );
		$description = esc_html( $args['description'] );
		$html_class  = esc_attr( $args['html_class'] );
		$html_id     = esc_attr( $args['html_id'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<input type="text" name="' . $name . '" class="widefat ' . $html_class . '" value="' . $value . '"' . ( ! empty( $html_id ) ? ' id="' . $html_id . '"' : '' ) . '>';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}

	/**
	 * Generate a textarea field
	 *
	 * Creates a multi-line textarea field with label and optional description.
	 * Ideal for longer text content like descriptions, content blocks, or notes.
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute.
	 *     @type string $label       Field label text.
	 *     @type string $description Optional description text.
	 *     @type string $value       Current field value.
	 *     @type string $html_class  Additional CSS classes.
	 *     @type string $html_id     HTML ID attribute.
	 * }
	 * @return void
	 */
	public function textarea( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'value'       => '',
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$value       = esc_textarea( $args['value'] );
		$description = esc_html( $args['description'] );
		$html_class  = esc_attr( $args['html_class'] );
		$html_id     = esc_attr( $args['html_id'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<textarea name="' . $name . '" class="widefat ' . $html_class . '"' . ( ! empty( $html_id ) ? ' id="' . $html_id . '"' : '' ) . '>' . $value . '</textarea>';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}

	/**
	 * Generate a select dropdown field
	 *
	 * Creates a dropdown select field with options. Options can be provided as
	 * an array or as a string to use helper data (page, post, menu, user).
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute
	 *     @type string $label       Field label text
	 *     @type string $description Optional description text
	 *     @type array|string $options Array of options or helper type ('page', 'post', 'menu', 'user').
	 *     @type string $value       Current field value
	 *     @type string $html_class  Additional CSS classes
	 *     @type string $html_id     HTML ID attribute
	 * }
	 * @return void
	 */
	public function select( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'options'     => [],
			'value'       => '',
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_string( $args['options'] ) ) {
			$args['options'] = $this->helper( $args['options'] );
		}

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$value       = esc_attr( $args['value'] );
		$description = esc_html( $args['description'] );
		$html_class  = esc_attr( $args['html_class'] );
		$html_id     = esc_attr( $args['html_id'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<select name="' . $name . '" class="widefat ' . $html_class . '"' . ( ! empty( $html_id ) ? ' id="' . $html_id . '"' : '' ) . '>';

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $option_value ) {
				$key_escaped   = esc_attr( $key );
				$value_escaped = esc_html( $option_value );
				$selected      = ( $value === $key_escaped ) ? ' selected' : '';
				$html         .= '<option value="' . $key_escaped . '"' . $selected . '>' . $value_escaped . '</option>';
			}
		}

		$html .= '</select>';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}

	/**
	 * Generate radio button fields
	 *
	 * Creates a group of radio buttons with options. Options can be provided as
	 * an array or as a string to use helper data (page, post, menu, user).
	 * Only one option can be selected at a time.
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute
	 *     @type string $label       Field label text
	 *     @type string $description Optional description text
	 *     @type array|string $options Array of options or helper type ('page', 'post', 'menu', 'user').
	 *     @type string $value       Current field value
	 *     @type string $html_class  Additional CSS classes
	 *     @type string $html_id     HTML ID attribute
	 * }
	 * @return void
	 */
	public function radio( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'options'     => [],
			'value'       => '',
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_string( $args['options'] ) ) {
			$args['options'] = $this->helper( $args['options'] );
		}

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$value       = esc_attr( $args['value'] );
		$description = esc_html( $args['description'] );
		$html_class  = esc_attr( $args['html_class'] );
		$html_id     = esc_attr( $args['html_id'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<span class="kira-widget-control-group-wrap">';

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $option_value ) {
				$uid           = uniqid( null, $name );
				$key_escaped   = esc_attr( $key );
				$value_escaped = esc_html( $option_value );
				$checked       = ( $value === $key_escaped ) ? ' checked' : '';
				$html         .= '<label for="' . $uid . '"><input type="radio" name="' . $name . '" id="' . $uid . '" value="' . $key_escaped . '"' . $checked . '>' . $value_escaped . '</label>';
			}
		}

		$html .= '</span>';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}

	/**
	 * Generate checkbox fields
	 *
	 * Creates a group of checkbox fields with options. Options can be provided as
	 * an array or as a string to use helper data (page, post, menu, user).
	 * Multiple options can be selected at the same time.
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute
	 *     @type string $label       Field label text
	 *     @type string $description Optional description text
	 *     @type array|string $options Array of options or helper type ('page', 'post', 'menu', 'user').
	 *     @type array $value       Current field values (array of selected values).
	 *     @type string $html_class  Additional CSS classes
	 *     @type string $html_id     HTML ID attribute
	 * }
	 * @return void
	 */
	public function checkbox( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'options'     => [],
			'value'       => [],
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_string( $args['options'] ) ) {
			$args['options'] = $this->helper( $args['options'] );
		}

		// Ensure value is an array.
		if ( ! is_array( $args['value'] ) ) {
			$args['value'] = [];
		}

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$description = esc_html( $args['description'] );
		$html_class  = esc_attr( $args['html_class'] );
		$html_id     = esc_attr( $args['html_id'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<span class="kira-widget-control-group-wrap">';

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $option_value ) {
				$uid           = uniqid( null, $name );
				$key_escaped   = esc_attr( $key );
				$value_escaped = esc_html( $option_value );
				$checked       = in_array( $key_escaped, $args['value'], true ) ? ' checked' : '';
				$html         .= '<label for="' . $uid . '"><input type="checkbox" name="' . $name . '[]" id="' . $uid . '" value="' . $key_escaped . '"' . $checked . '>' . $value_escaped . '</label>';
			}
		}

		$html .= '</span>';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}

	/**
	 * Generate a color picker field
	 *
	 * Creates a color picker input field using WordPress's built-in color picker.
	 * Automatically enqueues the required WordPress color picker scripts and styles.
	 * Includes a default color option and proper initialization.
	 *
	 * @since 1.0
	 * @param array $args {
	 *     Field configuration arguments.
	 *
	 *     @type string $name        Field name attribute
	 *     @type string $label       Field label text
	 *     @type string $description Optional description text
	 *     @type string $value       Current field value (hex color code).
	 *     @type string $default     Default color value (hex color code).
	 * }
	 * @return void
	 */
	public function color( $args ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'value'       => '#ffffff',
			'default'     => '#ffffff',
		];

		$args = wp_parse_args( $args, $defaults );

		// Sanitize values.
		$name        = esc_attr( $args['name'] );
		$label       = esc_html( $args['label'] );
		$value       = esc_attr( $args['value'] );
		$default     = esc_attr( $args['default'] );
		$description = esc_html( $args['description'] );

		$html  = '<p>';
		$html .= '<label for="' . $name . '" class="widefat">' . $label . '</label>';
		$html .= '<input type="text" name="' . $name . '" class="color-picker" value="' . $value . '" data-default-color="' . $default . '">';

		if ( ! empty( $description ) ) {
			$html .= '<span class="description">' . $description . '</span>';
		}

		$html .= '</p>';

		echo wp_kses( $html, $this->get_allowed_html() );
	}
}
