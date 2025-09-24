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
import {
	PanelBody,
	TextControl,
	SelectControl,
	ColorPicker,
	BaseControl,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState, useEffect } from "@wordpress/element";
import ServerSideRender from "@wordpress/server-side-render";

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
	const { menu, theme, accentColor } = attributes;

	const menus = useSelect((select) => {
		const terms = select("core").getEntityRecords("taxonomy", "nav_menu");
		return terms;
	}, []);

	const [menuOptions, setMenuOptions] = useState([]);

	useEffect(() => {
		if (menus) {
			const navOptions = menus.map((menu) => ({
				label: __(menu.name, "advanced-sidebar-nav"),
				value: menu.slug,
			}));
			navOptions.unshift({
				label: __("Select Menu", "advanced-sidebar-nav"),
				value: "",
			});

			setMenuOptions(navOptions);
		}
	}, [menus]);

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Settings", "advanced-sidebar-nav")}
					initialOpen={true}
				>
					{/* Menu Select */}
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Select menu", "advanced-sidebar-nav")}
						value={menu}
						options={menuOptions}
						onChange={(value) => setAttributes({ menu: value })}
					/>

					{/* Theme Select */}
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Select Theme", "advanced-sidebar-nav")}
						value={theme}
						options={[
							{
								label: __("Select theme", "advanced-sidebar-nav"),
								value: "",
							},
							{
								label: __("Default", "advanced-sidebar-nav"),
								value: "default",
							},
						]}
						onChange={(value) => setAttributes({ theme: value })}
					/>

					{/* Accent Color */}
					<BaseControl label={__("Accent Color", "advanced-sidebar-nav")}>
						<ColorPicker
							color={accentColor}
							onChange={(value) => setAttributes({ accentColor: value })}
							enableAlpha
							defaultValue="#000"
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			 {/* Block preview */}
			<div {...useBlockProps()}>
				{menu ? (
					<ServerSideRender
						block="advanced-sidebar-nav/advanced-sidebar-nav"
						attributes={attributes}
					/>
				) : (
					<div className="advanced-sidebar-nav-placeholder">
						<p>
							{__(
								"Please select a menu from the block settings.",
								"advanced-sidebar-nav",
							)}
						</p>
					</div>
				)}
			</div>
		</>
	);
}
