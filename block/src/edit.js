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
	SelectControl,
	ColorPicker,
	BaseControl,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState, useEffect, useRef } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

function getDepth(li) {
	let depth = 0;
	let current = li.parentElement;
	while (current && !current.classList.contains("advanced-sidebar-nav")) {
		if (current.tagName === "UL") depth++;
		current = current.parentElement;
	}
	return Math.max(0, depth - 1);
}

function setIndentation(root) {
	const anchors = root.querySelectorAll("ul li a");
	anchors.forEach((a) => {
		const li = a.closest("li");
		if (!li) return;
		const depth = getDepth(li);
		if (depth > 0) {
			a.style.setProperty("padding-left", depth * 40 + "px", "important");
		}
	});
}

function initNav(container) {
	const items = container.querySelectorAll("li.menu-item-has-children");
	items.forEach((item) => {
		const link = item.querySelector(":scope > a");
		const submenu = item.querySelector(":scope > ul");
		if (!link || !submenu) return;

		// Avoid duplicates
		if (link.querySelector(".advanced-sidebar-nav-toggle")) return;

		const toggle = document.createElement("span");
		toggle.className = "advanced-sidebar-nav-toggle";
		toggle.setAttribute("role", "button");
		toggle.setAttribute("tabindex", "0");
		toggle.setAttribute("aria-expanded", "false");
		link.appendChild(toggle);

		const initiallyOpen =
			window.getComputedStyle(submenu).display === "block" ||
			item.classList.contains("current-menu-ancestor");
		if (initiallyOpen) {
			toggle.classList.add("advanced-sidebar-nav-toggle-open");
			link.classList.add("advanced-sidebar-nav-menu-open");
			toggle.setAttribute("aria-expanded", "true");
			submenu.style.display = "block";
		} else {
			submenu.style.display = "none";
		}

		function toggleSubmenu() {
			const isOpen = toggle.classList.toggle(
				"advanced-sidebar-nav-toggle-open",
			);
			if (isOpen) {
				link.classList.add("advanced-sidebar-nav-menu-open");
				submenu.style.display = "block";
				toggle.setAttribute("aria-expanded", "true");
			} else {
				link.classList.remove("advanced-sidebar-nav-menu-open");
				submenu.style.display = "none";
				toggle.setAttribute("aria-expanded", "false");
			}
		}

		toggle.addEventListener("click", (e) => {
			e.preventDefault();
			toggleSubmenu();
		});
		toggle.addEventListener("keydown", (e) => {
			if (e.key === "Enter" || e.key === " ") {
				e.preventDefault();
				toggleSubmenu();
			}
		});
	});

	setIndentation(container);
}

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

	// Fetch menu HTML when menu or theme changes
	useEffect(() => {
		if (!menu) {
			setMenuHtml("");
			setError(null);
			return;
		}

		fetchMenuHtml();
	}, [menu, theme]);

	// Update accent color by modifying the HTML directly
	useEffect(() => {
		if (originalMenuHtmlRef.current && accentColor) {
			// Update the style attribute in the HTML string
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
				// Store original HTML in ref
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

	// Initialize submenu toggle behavior inside the editor preview
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
					<BaseControl
						__nextHasNoMarginBottom
						label={__("Accent Color", "advanced-sidebar-nav")}
					>
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
