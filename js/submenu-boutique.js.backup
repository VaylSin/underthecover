document.addEventListener("DOMContentLoaded", function () {
	const link = document.querySelector(".menu-item-boutique");
	const submenu = document.getElementById("submenu-boutique");
	if (!link || !submenu) return;

	// Retirer immédiatement tout display inline
	submenu.style.removeProperty("display");
	submenu.removeAttribute("style"); // si safe (en dev), sinon au moins removeProperty('display')

	// Observer pour supprimer toute ré-application future de display:inline par un autre script
	const obs = new MutationObserver((mutations) => {
		for (const m of mutations) {
			if (m.type === "attributes" && m.attributeName === "style") {
				if (
					submenu.style &&
					submenu.style.display &&
					submenu.style.display !== ""
				) {
					submenu.style.removeProperty("display");
				}
			}
		}
	});
	obs.observe(submenu, { attributes: true, attributeFilter: ["style"] });

	// debug helper (optionnel) : expose une fonction pour forcer l'ouverture depuis console
	window.__submenuForceOpen = function (open) {
		if (open) submenu.classList.add("force-open");
		else submenu.classList.remove("force-open");
	};

	// accessibility
	link.setAttribute("aria-haspopup", "true");
	link.setAttribute("aria-expanded", "false");

	let leaveTimer = null;
	const leaveDelay = 120;

	const open = () => {
		clearTimeout(leaveTimer);
		submenu.classList.add("active");
		link.setAttribute("aria-expanded", "true");
	};
	const close = () => {
		clearTimeout(leaveTimer);
		submenu.classList.remove("active");
		link.setAttribute("aria-expanded", "false");
	};

	const isHoverCapable = window.matchMedia(
		"(hover: hover) and (pointer: fine)"
	).matches;

	if (isHoverCapable) {
		link.addEventListener("mouseenter", open);
		link.addEventListener("mouseleave", () => {
			leaveTimer = setTimeout(() => {
				if (!submenu.matches(":hover")) close();
			}, leaveDelay);
		});
		submenu.addEventListener("mouseenter", () => {
			clearTimeout(leaveTimer);
			open();
		});
		submenu.addEventListener("mouseleave", () => {
			leaveTimer = setTimeout(close, leaveDelay);
		});
	} else {
		link.addEventListener("click", function (e) {
			e.preventDefault();
			submenu.classList.toggle("active");
			const opened = submenu.classList.contains("active");
			link.setAttribute("aria-expanded", opened ? "true" : "false");
		});
		document.addEventListener("click", function (e) {
			if (!submenu.contains(e.target) && e.target !== link) close();
		});
		window.addEventListener("scroll", close);
	}
});
