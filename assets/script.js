/**
 * Advanced Sidebar Nav Frontend Script
 *
 * This script handles the interactive functionality for the Advanced Sidebar Nav
 * widget/block on the frontend, including toggle buttons and indentation.
 *
 * @since 1.0
 */

/* global jQuery */

jQuery( document ).ready( function ( $ ) {
	/**
	 * Initialize navigation functionality for all Advanced Sidebar Nav instances.
	 */
	$( '.advanced-sidebar-nav' ).each( function () {
		const $nav = $( this );

		// Process each menu item that has children.
		$( '.menu-item-has-children', $nav ).each( function () {
			const $item = $( this );

			// Insert toggle button into the menu item link.
			$( '> a', $item ).append(
				'<span class="advanced-sidebar-nav-toggle"></span>'
			);

			// Calculate and apply indentation based on menu depth.
			const depth = $item.parents( '.menu-item-has-children' ).length;
			$( 'ul li a', $item ).attr(
				'style',
				`padding-left:${ ( depth + 2 ) * 20 }px !important`
			);

			// Set initial state for toggle button if submenu is open.
			if ( $( 'ul', $item ).css( 'display' ) === 'block' ) {
				$( '.advanced-sidebar-nav-toggle', $item ).addClass(
					'advanced-sidebar-nav-toggle-open'
				);
			}
		} );
	} );

	/**
	 * Handle toggle button clicks for menu expansion/collapse.
	 */
	$( '.advanced-sidebar-nav' ).on(
		'click',
		'.advanced-sidebar-nav-toggle',
		function ( e ) {
			e.preventDefault();

			const $toggle = $( this );
			const $link = $toggle.parent( 'a' );
			const $submenu = $link.siblings( 'ul' );

			// Toggle the open state.
			$toggle.toggleClass( 'advanced-sidebar-nav-toggle-open' );

			if ( $toggle.hasClass( 'advanced-sidebar-nav-toggle-open' ) ) {
				// Open the submenu.
				$link.addClass( 'advanced-sidebar-nav-menu-open' );
				$submenu.slideDown( 300 );
			} else {
				// Close the submenu.
				$link.removeClass( 'advanced-sidebar-nav-menu-open' );
				$submenu.slideUp( 300 );
			}
		}
	);
} );
