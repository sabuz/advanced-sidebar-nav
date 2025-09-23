<?php
/**
 * Server-side rendering for Advanced Sidebar Nav block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package Advanced_Sidebar_Nav
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

<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
	<?php if ( ! empty( $menu_slug ) ) : ?>
		<?php
		$nav_menu = wp_nav_menu(
			[
				'menu'       => $menu_slug,
				'menu_class' => 'advanced-sidebar-menu',
				'container'  => false,
				'echo'       => false,
			]
		);

		if ( $nav_menu ) {
			echo wp_kses_post( $nav_menu );
		} else {
			echo '<p>' . esc_html__( 'No menu found. Please select a valid menu.', 'advanced-sidebar-nav' ) . '</p>';
		}
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Please select a menu from the block settings.', 'advanced-sidebar-nav' ); ?></p>
	<?php endif; ?>
</div>
