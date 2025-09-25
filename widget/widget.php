<?php

class Advanced_Sidebar_Nav_Widget extends WP_Widget {


	public function __construct() {
		$widget_opts = [
			'classname'   => 'advanced-sidebar-nav-widget',
			'description' => __( 'The best way to display navigation menus on sidebar, no matter how many depth!' ),
		];

		parent::__construct( false, __( 'Advanced Sidebar Nav' ), $widget_opts );
	}

	public function widget( $args, $instance ) {
		// asset output
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'advanced-sidebar-nav' );
		wp_enqueue_style( 'advanced-sidebar-nav' );

		// html output
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		if ( ! empty( $instance['menu'] ) ) {
			echo wp_nav_menu(
				[
					'menu'            => $instance['menu'],
					'menu_class'      => 'advanced-sidebar-menu',
					'container_class' => 'advanced-sidebar-nav advanced-sidebar-nav-default',
					'echo'            => false,
				]
			);
		}

		if ( ! empty( $instance['color'] ) ) {
			$css = "#{$this->id} .advanced-sidebar-nav > ul {background-color: {$instance['color']}}";
			wp_add_inline_style( 'advanced-sidebar-nav', $css );
		}

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['menu']  = ! empty( $new_instance['menu'] ) ? $new_instance['menu'] : null;
		$instance['color'] = ! empty( $new_instance['color'] ) ? $new_instance['color'] : '#0f434f';

		return $instance;
	}

	public function form( $instance ) {
		global $kira_widget_options_framework;

		$kira_widget_options_framework->text(
			[
				'name'        => esc_attr( $this->get_field_name( 'title' ) ),
				'label'       => 'Title:',
				'description' => '',
				'value'       => @$instance['title'],
			]
		);

		$kira_widget_options_framework->select(
			[
				'name'        => esc_attr( $this->get_field_name( 'menu' ) ),
				'label'       => 'Select Menu:',
				'description' => '',
				'options'     => 'menu',
				'value'       => @$instance['menu'],
			]
		);

		$kira_widget_options_framework->select(
			[
				'name'        => esc_attr( $this->get_field_name( 'theme' ) ),
				'label'       => 'Select Theme:',
				'description' => __( 'More themes are comming soon...' ),
				'options'     => [
					'default' => 'Default',
				],
				'value'       => @$instance['menu'],
			]
		);

		$kira_widget_options_framework->color(
			[
				'name'        => esc_attr( $this->get_field_name( 'color' ) ),
				'label'       => 'Accent Color:',
				'description' => '',
				'value'       => @$instance['color'],
				'default'     => '#0F434F',
			]
		);
	}
}
