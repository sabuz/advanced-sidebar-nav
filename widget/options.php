<?php

class Advanced_Sidebar_Nav_Widget_Opts {

	protected static function _helper( $args ) {
		$arr = [];

		switch ( $args ) {
			case 'menu':
				if ( $menus = wp_get_nav_menus() ) {
					foreach ( $menus as $menu ) {
						$arr[ $menu->term_id ] = $menu->name;
					}
				}

				break;
		}

		return $arr;
	}

	public static function print_scripts() {
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

	public static function text( $args ) {
		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'value'       => '',
			'html_class'  => '',
			'html_id'     => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$html = '<p>
			<label for="' . $args['name'] . '" class="widefat">' . $args['label'] . '</label>
			<input type="text" name="' . $args['name'] . '" class="widefat" value="' . $args['value'] . '">
		</p>';

		return $html;
	}

	public static function select( $args ) {
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
			$args['options'] = self::_helper( $args['options'] );
		}

		$html = '<p>
			<label for="' . $args['name'] . '" class="widefat">' . $args['label'] . '</label>
			<select name="' . $args['name'] . '" class="widefat">';

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $value ) {
				$html .= '<option value="' . esc_html( $key ) . '" ' . ( esc_html( $args['value'] ) == esc_html( $key ) ? 'selected' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
		}

		$html .= '</select>';

		if ( ! empty( $args['description'] ) ) {
			$html .= '<span class="description">' . $args['description'] . '</span>';
		}

		$html .= '</p>';

		return $html;
	}

	public static function color( $args ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		add_action( 'admin_footer-widgets.php', [ new self(), 'print_scripts' ], 9999 );

		$defaults = [
			'name'        => '',
			'label'       => '',
			'description' => '',
			'value'       => '#ffffff',
			'default'     => '#ffffff',
		];

		$args = wp_parse_args( $args, $defaults );

		$html = '<p>
			<label for="' . $args['name'] . '" class="widefat">' . $args['label'] . '</label>
			<input type="text" name="' . $args['name'] . '" class="color-picker" value="' . $args['value'] . '" data-default-color="' . $args['default'] . '">
		</p>';

		return $html;
	}
}
