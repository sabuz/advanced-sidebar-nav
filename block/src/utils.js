// Smooth submenu animations
const slideDown = (el, duration = 300) => {
	el.style.removeProperty("display");
	let display = window.getComputedStyle(el).display;
	if (display === "none") {
		el.style.display = "block";
	}
	const height = el.scrollHeight;
	el.style.overflow = "hidden";
	el.style.height = "0px";
	el.style.transition = `height ${duration}ms ease`;
	requestAnimationFrame(() => {
		el.style.height = height + "px";
	});
	const end = () => {
		el.removeEventListener("transitionend", end);
		el.style.removeProperty("height");
		el.style.removeProperty("overflow");
		el.style.removeProperty("transition");
	};
	el.addEventListener("transitionend", end);
};

const slideUp = (el, duration = 300) => {
	const height = el.scrollHeight;
	el.style.overflow = "hidden";
	el.style.height = height + "px";
	el.style.transition = `height ${duration}ms ease`;
	requestAnimationFrame(() => {
		el.style.height = "0px";
	});
	const end = () => {
		el.removeEventListener("transitionend", end);
		el.style.display = "none";
		el.style.removeProperty("height");
		el.style.removeProperty("overflow");
		el.style.removeProperty("transition");
	};
	el.addEventListener("transitionend", end);
};

const getDepth = (li) => {
	let depth = 0;
	let current = li.parentElement;
	while (current && !current.classList.contains("advanced-sidebar-nav")) {
		if (current.tagName === "UL") depth++;
		current = current.parentElement;
	}
	return Math.max(0, depth - 1);
};

const setIndentation = (root) => {
	const anchors = root.querySelectorAll("ul li a");
	anchors.forEach((a) => {
		const li = a.closest("li");
		if (!li) return;
		const depth = getDepth(li);
		if (depth > 0) {
			a.style.setProperty("padding-left", depth * 40 + "px", "important");
		}
	});
};

const initNav = (container) => {
	const items = container.querySelectorAll("li.menu-item-has-children");
	items.forEach((item) => {
		const link = item.querySelector(":scope > a");
		const submenu = item.querySelector(":scope > ul");
		if (!link || !submenu) return;

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

		const toggleSubmenu = () => {
			const isOpen = toggle.classList.toggle(
				"advanced-sidebar-nav-toggle-open",
			);
			if (isOpen) {
				link.classList.add("advanced-sidebar-nav-menu-open");
				slideDown(submenu, 300);
				toggle.setAttribute("aria-expanded", "true");
			} else {
				link.classList.remove("advanced-sidebar-nav-menu-open");
				slideUp(submenu, 300);
				toggle.setAttribute("aria-expanded", "false");
			}
		};

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
};

export { slideDown, slideUp, getDepth, setIndentation, initNav };
