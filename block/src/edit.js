import { __ } from "@wordpress/i18n";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	ColorPicker,
	BaseControl,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState, useEffect, useRef } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import { initNav } from "./utils";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { menu, theme, accentColor } = attributes;

	const menus = useSelect((select) => {
		const terms = select("core").getEntityRecords("taxonomy", "nav_menu");
		return terms;
	}, []);

	const [menuOptions, setMenuOptions] = useState([]);
	const [menuHtml, setMenuHtml] = useState("");
	const [isLoading, setIsLoading] = useState(false);
	const [error, setError] = useState(null);
	const originalMenuHtmlRef = useRef("");
	const wrapperRef = useRef(null);

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

	useEffect(() => {
		if (!menu) {
			setMenuHtml("");
			setError(null);
			return;
		}

		fetchMenuHtml();
	}, [menu, theme]);

	useEffect(() => {
		if (originalMenuHtmlRef.current && accentColor) {
			const updatedHtml = originalMenuHtmlRef.current.replace(
				/style="[^"]*"/,
				`style="--accent-color: ${accentColor};"`,
			);
			setMenuHtml(updatedHtml);
		}
	}, [accentColor]);

	const fetchMenuHtml = () => {
		setIsLoading(true);
		setError(null);

		const params = new URLSearchParams({
			theme: theme || "default",
			accent_color: accentColor || "#0f434f",
		});

		apiFetch({
			path: `/advanced-sidebar-nav/v1/menu/${menu}?${params}`,
		})
			.then((response) => {
				originalMenuHtmlRef.current = response.html;
				setMenuHtml(response.html);
				setError(null);
			})
			.catch((err) => {
				setError(err.message || "Failed to load menu");
				setMenuHtml("");
				originalMenuHtmlRef.current = "";
			})
			.finally(() => {
				setIsLoading(false);
			});
	};

	useEffect(() => {
		if (!menu) return;

		const timer = setTimeout(() => {
			const wrapper = wrapperRef.current;
			if (!wrapper) return;
			const root = wrapper.querySelector(".advanced-sidebar-nav");
			if (root) initNav(root);
		}, 100);

		return () => clearTimeout(timer);
	}, [menu, theme, accentColor]);

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Settings", "advanced-sidebar-nav")}
					initialOpen={true}
				>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__("Select menu", "advanced-sidebar-nav")}
						value={menu}
						options={menuOptions}
						onChange={(value) => setAttributes({ menu: value })}
					/>

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

					<BaseControl
						__nextHasNoMarginBottom
						label={__("Accent Color", "advanced-sidebar-nav")}
					>
						<ColorPicker
							color={accentColor}
							onChange={(value) => setAttributes({ accentColor: value })}
							enableAlpha={false}
							defaultValue="#0f434f"
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				{!menu ? (
					<div className="advanced-sidebar-nav-placeholder">
						<p>
							{__(
								"Please select a menu from the block settings.",
								"advanced-sidebar-nav",
							)}
						</p>
					</div>
				) : isLoading ? (
					<div className="advanced-sidebar-nav-loading">
						<p>{__("Loading menu...", "advanced-sidebar-nav")}</p>
					</div>
				) : error ? (
					<div className="advanced-sidebar-nav-error">
						<p>
							{__("Error loading menu: ", "advanced-sidebar-nav")}
							{error}
						</p>
					</div>
				) : (
					<div
						dangerouslySetInnerHTML={{ __html: menuHtml }}
						ref={wrapperRef}
					/>
				)}
			</div>
		</>
	);
}
