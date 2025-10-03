/**
 * Advanced Sidebar Nav Block Editor Component
 *
 * This component handles the block editor interface for the Advanced Sidebar Nav block,
 * providing controls for menu selection, theme selection, and accent color customization.
 *
 * @since 1.1
 */

import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ColorPicker,
	BaseControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState, useEffect, useRef, useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { initNav } from './utils';

import './editor.scss';

/**
 * Edit component for the Advanced Sidebar Nav block.
 *
 * @param {Object}   props               - Component props.
 * @param {Object}   props.attributes    - Block attributes.
 * @param {Function} props.setAttributes - Function to update block attributes.
 * @return {JSX.Element} The edit component.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { menu, theme, accentColor } = attributes;

	// Get available navigation menus from WordPress.
	const menus = useSelect( ( select ) => {
		const terms = select( 'core' ).getEntityRecords(
			'taxonomy',
			'nav_menu'
		);
		return terms;
	}, [] );

	// Component state management.
	const [ menuOptions, setMenuOptions ] = useState( [] );
	const [ menuHtml, setMenuHtml ] = useState( '' );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ error, setError ] = useState( null );
	const originalMenuHtmlRef = useRef( '' );
	const wrapperRef = useRef( null );

	/**
	 * Update menu options when menus data is available.
	 */
	useEffect( () => {
		if ( menus ) {
			const navOptions = menus.map( ( menuItem ) => {
				// Use the menu name directly without translation for dynamic content.
				return {
					label: menuItem.name,
					value: menuItem.slug,
				};
			} );

			// Add default "Select Menu" option at the beginning.
			navOptions.unshift( {
				label: __( 'Select Menu', 'advanced-sidebar-nav' ),
				value: '',
			} );

			setMenuOptions( navOptions );
		}
	}, [ menus ] );

	/**
	 * Fetch menu HTML from the REST API.
	 *
	 * @return {void}
	 */
	const fetchMenuHtml = useCallback( () => {
		setIsLoading( true );
		setError( null );

		const params = new URLSearchParams( {
			theme: theme || 'default',
			accent_color: accentColor || '#0f434f',
		} );

		apiFetch( {
			path: `/advanced-sidebar-nav/v1/menu/${ menu }?${ params }`,
		} )
			.then( ( response ) => {
				originalMenuHtmlRef.current = response.html;
				setMenuHtml( response.html );
				setError( null );
			} )
			.catch( ( err ) => {
				setError( err.message || 'Failed to load menu' );
				setMenuHtml( '' );
				originalMenuHtmlRef.current = '';
			} )
			.finally( () => {
				setIsLoading( false );
			} );
	}, [ menu, theme ] );

	/**
	 * Fetch menu HTML when menu or theme changes.
	 */
	useEffect( () => {
		if ( ! menu ) {
			setMenuHtml( '' );
			setError( null );
			return;
		}

		fetchMenuHtml();
	}, [ menu, theme, fetchMenuHtml ] );

	/**
	 * Update menu HTML with accent color when accent color changes.
	 */
	useEffect( () => {
		if ( originalMenuHtmlRef.current && accentColor ) {
			// Instead of regenerating HTML, update the CSS custom property directly
			const wrapper = wrapperRef.current;
			
			if ( wrapper ) {
				const root = wrapper.querySelector( '.advanced-sidebar-nav' );
				
				if ( root ) {
					root.style.setProperty( '--accent-color', accentColor );
				}
			} else {
				// Retry after a short delay if wrapper is not ready
				setTimeout( () => {
					const retryWrapper = wrapperRef.current;
					if ( retryWrapper ) {
						const retryRoot = retryWrapper.querySelector( '.advanced-sidebar-nav' );
						if ( retryRoot ) {
							retryRoot.style.setProperty( '--accent-color', accentColor );
						}
					}
				}, 100 );
			}
		}
	}, [ accentColor ] );

	/**
	 * Initialize navigation functionality after menu HTML is rendered.
	 */
	useEffect( () => {
		if ( ! menu ) {
			return;
		}

		// Wait for the ref to be set and stable
		const checkAndInit = () => {
			const wrapper = wrapperRef.current;
			
			if ( ! wrapper ) {
				setTimeout( checkAndInit, 50 );
				return;
			}

			const root = wrapper.querySelector( '.advanced-sidebar-nav' );
			
			if ( root ) {
				initNav( root );
			}
		};

		// Start checking after a short delay
		setTimeout( checkAndInit, 100 );
	}, [ menu, theme ] );

	/**
	 * Render the appropriate content based on current state.
	 *
	 * @return {JSX.Element} The content to render.
	 */
	const renderContent = () => {
		if ( ! menu ) {
			return (
				<div className="advanced-sidebar-nav-placeholder">
					<p>
						{ __(
							'Please select a menu from the block settings.',
							'advanced-sidebar-nav'
						) }
					</p>
				</div>
			);
		}

		if ( isLoading ) {
			return (
				<div className="advanced-sidebar-nav-loading">
					<p>{ __( 'Loading menu…', 'advanced-sidebar-nav' ) }</p>
				</div>
			);
		}

		if ( error ) {
			return (
				<div className="advanced-sidebar-nav-error">
					<p>
						{ __( 'Error loading menu:', 'advanced-sidebar-nav' ) }
						{ error }
					</p>
				</div>
			);
		}

		return (
			<div
				dangerouslySetInnerHTML={ { __html: menuHtml } }
				ref={ wrapperRef }
			/>
		);
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'advanced-sidebar-nav' ) }
					initialOpen={ true }
				>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Select menu', 'advanced-sidebar-nav' ) }
						value={ menu }
						options={ menuOptions }
						onChange={ ( value ) =>
							setAttributes( { menu: value } )
						}
					/>

					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Select Theme', 'advanced-sidebar-nav' ) }
						value={ theme }
						options={ [
							{
								label: __(
									'Select theme',
									'advanced-sidebar-nav'
								),
								value: '',
							},
							{
								label: __( 'Default', 'advanced-sidebar-nav' ),
								value: 'default',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { theme: value } )
						}
					/>

					<BaseControl
						__nextHasNoMarginBottom
						id="accent-color-control"
						label={ __( 'Accent Color', 'advanced-sidebar-nav' ) }
					>
						<ColorPicker
							color={ accentColor }
							onChange={ ( value ) =>
								setAttributes( { accentColor: value } )
							}
							enableAlpha={ false }
							defaultValue="#0f434f"
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>{ renderContent() }</div>
		</>
	);
}
