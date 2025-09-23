<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Extract attributes
$menu        = $attributes['menu'] ?? '';
$theme       = $attributes['theme'] ?? 'default';
$accentColor = $attributes['accentColor'] ?? '#0f434f';

error_log( print_r( $attributes, true ) );

// Build CSS classes
$classes            = [ 'advanced-sidebar-nav', 'advanced-sidebar-nav-' . $theme ];
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class' => implode( ' ', $classes ),
		'style' => ! empty( $accentColor ) ? '--accent-color: ' . esc_attr( $accentColor ) . ';' : '',
	]
);
?>

<div <?php echo $wrapper_attributes; ?>>
	<?php if ( ! empty( $menu ) ) : ?>
		<?php
		$nav_menu = wp_nav_menu(
			[
				'menu'            => $menu,
				'menu_class'      => 'advanced-sidebar-menu',
				'container_class' => 'advanced-sidebar-nav-container',
				'echo'            => false,
			]
		);

		if ( $nav_menu ) {
			echo $nav_menu;
		} else {
			echo '<p>' . __( 'No menu found. Please select a valid menu.', 'advanced-sidebar-nav' ) . '</p>';
		}
		?>
	<?php else : ?>
		<p><?php _e( 'Please select a menu from the block settings.', 'advanced-sidebar-nav' ); ?></p>
	<?php endif; ?>
</div>
