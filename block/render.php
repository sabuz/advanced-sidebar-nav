<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$menu_slug          = $attributes['menu'] ?? '';
$theme              = $attributes['theme'] ?? 'default';
$accent_color       = $attributes['accentColor'] ?? '#0f434f';
$classes            = [ 'advanced-sidebar-nav', 'advanced-sidebar-nav-' . $theme ];
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class' => implode( ' ', $classes ),
		'style' => ! empty( $accent_color ) ? '--accent-color: ' . esc_attr( $accent_color ) . ';' : '',
	]
);
?>

<div <?php echo $wrapper_attributes; ?>>
	<?php if ( ! empty( $menu_slug ) ) : ?>
		<?php
		$nav_menu = wp_nav_menu(
			[
				'menu'            => $menu_slug,
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
