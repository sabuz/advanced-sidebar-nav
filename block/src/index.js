/**
 * Block editor JavaScript for Advanced Sidebar Nav.
 * 
 * This file handles the block editor interface while block registration
 * and server-side rendering are handled in PHP via the Advanced_Sidebar_Nav_Block class.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

import Edit from './edit';

/**
 * Register the block editor component.
 * 
 * This registers only the editor interface component. The block itself is registered
 * in PHP with server-side rendering capabilities.
 */
const { registerBlockType } = wp.blocks;

registerBlockType('advanced-sidebar-nav/advanced-sidebar-nav', {
	edit: Edit,
});
