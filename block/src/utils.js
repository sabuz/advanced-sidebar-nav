/**
 * Advanced Sidebar Nav Utility Functions
 *
 * This module contains utility functions for handling smooth animations,
 * depth calculations, and navigation initialization.
 *
 * @since 1.1
 */

/* global requestAnimationFrame */

/**
 * Smooth slide down animation for submenu elements.
 *
 * Creates a smooth height transition from 0 to the element's natural height.
 * Uses CSS transitions and requestAnimationFrame for optimal performance.
 *
 * @param {HTMLElement} el       - The element to animate.
 * @param {number}      duration - Animation duration in milliseconds (default: 300).
 * @return {void}
 */
const slideDown = ( el, duration = 300 ) => {
	// Remove any existing display property to get natural display value.
	el.style.removeProperty( 'display' );
	const display = window.getComputedStyle( el ).display;

	// Ensure element is visible for height calculation.
	if ( display === 'none' ) {
		el.style.display = 'block';
	}

	// Store the natural height before starting animation.
	const height = el.scrollHeight;

	// Set up initial animation state.
	el.style.overflow = 'hidden';
	el.style.height = '0px';
	el.style.transition = `height ${ duration }ms ease`;

	// Trigger the animation on the next frame.
	requestAnimationFrame( () => {
		el.style.height = `${ height }px`;
	} );

	// Clean up after animation completes.
	const end = () => {
		el.removeEventListener( 'transitionend', end );
		el.style.removeProperty( 'height' );
		el.style.removeProperty( 'overflow' );
		el.style.removeProperty( 'transition' );
	};

	el.addEventListener( 'transitionend', end );
};

/**
 * Smooth slide up animation for submenu elements.
 *
 * Creates a smooth height transition from the element's natural height to 0.
 * Uses CSS transitions and requestAnimationFrame for optimal performance.
 *
 * @param {HTMLElement} el       - The element to animate.
 * @param {number}      duration - Animation duration in milliseconds (default: 300).
 * @return {void}
 */
const slideUp = ( el, duration = 300 ) => {
	// Store the current height before starting animation.
	const height = el.scrollHeight;

	// Set up initial animation state.
	el.style.overflow = 'hidden';
	el.style.height = `${ height }px`;
	el.style.transition = `height ${ duration }ms ease`;

	// Trigger the animation on the next frame.
	requestAnimationFrame( () => {
		el.style.height = '0px';
	} );

	// Clean up after animation completes.
	const end = () => {
		el.removeEventListener( 'transitionend', end );
		el.style.display = 'none';
		el.style.removeProperty( 'height' );
		el.style.removeProperty( 'overflow' );
		el.style.removeProperty( 'transition' );
	};

	el.addEventListener( 'transitionend', end );
};

/**
 * Calculate the depth level of a menu item within the navigation hierarchy.
 *
 * Counts the number of nested UL elements from the given list item
 * up to the root navigation container.
 *
 * @param {HTMLElement} li - The list item element to calculate depth for.
 * @return {number} The depth level (0-based, where 0 is top level).
 */
const getDepth = ( li ) => {
	let depth = 0;
	let current = li.parentElement;

	// Traverse up the DOM tree until we reach the root navigation container.
	while (
		current &&
		! current.classList.contains( 'advanced-sidebar-nav' )
	) {
		if ( current.tagName === 'UL' ) {
			depth++;
		}
		current = current.parentElement;
	}

	// Return depth minus 1 to make it 0-based (top level = 0).
	return Math.max( 0, depth - 1 );
};

/**
 * Apply visual indentation to menu items based on their hierarchy depth.
 *
 * Calculates the depth of each menu item and applies appropriate
 * left padding to create a visual hierarchy.
 *
 * @param {HTMLElement} root - The root navigation container element.
 * @return {void}
 */
const setIndentation = ( root ) => {
	const anchors = root.querySelectorAll( 'ul li a' );

	anchors.forEach( ( anchor ) => {
		const li = anchor.closest( 'li' );
		if ( ! li ) {
			return;
		}

		const depth = getDepth( li );

		// Apply indentation for nested items (depth > 0).
		if ( depth > 0 ) {
			anchor.style.setProperty(
				'padding-left',
				`${ depth * 40 }px`,
				'important'
			);
		}
	} );
};

/**
 * Initialize interactive navigation functionality for a container.
 *
 * Sets up toggle buttons, event handlers, and initial state for all
 * menu items with children. Also applies proper indentation.
 *
 * @param {HTMLElement} container - The navigation container element.
 * @return {void}
 */
const initNav = ( container ) => {
	const items = container.querySelectorAll( 'li.menu-item-has-children' );

	items.forEach( ( item ) => {
		const link = item.querySelector( ':scope > a' );
		const submenu = item.querySelector( ':scope > ul' );

		// Skip items without proper structure.
		if ( ! link || ! submenu ) {
			return;
		}

		// Skip if toggle button already exists.
		if ( link.querySelector( '.advanced-sidebar-nav-toggle' ) ) {
			return;
		}

		// Create and configure toggle button.
		const toggle = document.createElement( 'span' );
		toggle.className = 'advanced-sidebar-nav-toggle';
		toggle.setAttribute( 'role', 'button' );
		toggle.setAttribute( 'tabindex', '0' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		link.appendChild( toggle );

		// Determine initial state based on CSS or current menu ancestor.
		const initiallyOpen =
			window.getComputedStyle( submenu ).display === 'block' ||
			item.classList.contains( 'current-menu-ancestor' );

		if ( initiallyOpen ) {
			toggle.classList.add( 'advanced-sidebar-nav-toggle-open' );
			link.classList.add( 'advanced-sidebar-nav-menu-open' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			submenu.style.display = 'block';
		} else {
			submenu.style.display = 'none';
		}

		/**
		 * Toggle submenu visibility and update ARIA attributes.
		 *
		 * @return {void}
		 */
		const toggleSubmenu = () => {
			const isOpen = toggle.classList.toggle(
				'advanced-sidebar-nav-toggle-open'
			);

			if ( isOpen ) {
				link.classList.add( 'advanced-sidebar-nav-menu-open' );
				slideDown( submenu, 300 );
				toggle.setAttribute( 'aria-expanded', 'true' );
			} else {
				link.classList.remove( 'advanced-sidebar-nav-menu-open' );
				slideUp( submenu, 300 );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		};

		// Add click event handler.
		toggle.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			toggleSubmenu();
		} );

		// Add keyboard event handler for accessibility.
		toggle.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				toggleSubmenu();
			}
		} );
	} );

	// Apply indentation to all menu items.
	setIndentation( container );
};

export { slideDown, slideUp, getDepth, setIndentation, initNav };
