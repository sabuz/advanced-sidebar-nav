/**
 * Block editor JavaScript for Advanced Sidebar Nav.
 * 
 * This file is loaded by the block editor but the block registration
 * is handled in PHP via the Advanced_Sidebar_Nav_Block class.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';

/**
 * Note: Block registration is handled in PHP via Advanced_Sidebar_Nav_Block class.
 * This file only contains the editor interface components.
 */
