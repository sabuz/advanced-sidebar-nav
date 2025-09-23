/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { PanelBody, TextControl, SelectControl } from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { title, selectMenu, selectTheme, accentColor } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Settings", "advanced-sidebar-nav")}
					initialOpen={true}
				>
					{/* Title */}
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Title", "advanced-sidebar-nav")}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>

					{/* Menu Select */}
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Select Menu", "advanced-sidebar-nav")}
						value={selectMenu}
						options={[
							{ label: __("Select a menu", "advanced-sidebar-nav"), value: "" },
							{ label: __("Menu 1", "advanced-sidebar-nav"), value: "menu1" },
							{ label: __("Menu 2", "advanced-sidebar-nav"), value: "menu2" },
							{ label: __("Menu 3", "advanced-sidebar-nav"), value: "menu3" },
						]}
						onChange={(value) => setAttributes({ selectMenu: value })}
					/>

					{/* Theme Select */}
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Select Theme", "advanced-sidebar-nav")}
						value={selectTheme || "default"}
						options={[
							{
								label: __("Default", "advanced-sidebar-nav"),
								value: "default",
							},
						]}
						onChange={(value) => setAttributes({ selectTheme: value })}
					/>

					{/* Accent Color */}
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Accent Color", "advanced-sidebar-nav")}
						type="text"
						value={accentColor || "#0f434f"}
						onChange={(value) => setAttributes({ accentColor: value })}
					/>
				</PanelBody>
			</InspectorControls>

			{/* Example block content output */}
			<div {...useBlockProps()}>
				<h3>{title}</h3>
				<p>Menu: {selectMenu}</p>
				<p>Theme: {selectTheme || "Default"}</p>
				<p>Accent Color: {accentColor || "#0f434f"}</p>
			</div>
		</>
	);
}
