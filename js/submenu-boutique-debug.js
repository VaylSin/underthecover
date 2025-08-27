document.addEventListener("DOMContentLoaded", function () {
	console.clear();
	console.log("submenu-debug chargé");

	const link = document.querySelector(".menu-item-boutique");
	const submenu = document.getElementById("submenu-boutique");
	console.log("link ->", link);
	console.log("submenu ->", submenu);

	if (!link || !submenu) return;

	// retire un display inline résiduel
	try {
		submenu.style.removeProperty("display");
	} catch (e) {}

	// logs d'évènements
	["mouseenter", "mouseleave", "click"].forEach((evt) => {
		link.addEventListener(evt, (e) =>
			console.log("link", evt, e.type, {
				expanded: link.getAttribute("aria-expanded"),
			})
		);
	});
	["mouseenter", "mouseleave", "click"].forEach((evt) => {
		submenu.addEventListener(evt, (e) => console.log("submenu", evt));
	});

	// toggle au clic pour test
	link.addEventListener("click", function (e) {
		e.preventDefault();
		submenu.classList.toggle("active");
		const opened = submenu.classList.contains("active");
		link.setAttribute("aria-expanded", opened ? "true" : "false");
		console.log("toggle -> active?", opened);
		// bordure temporaire pour voir
		if (opened) submenu.style.border = "4px solid rgba(255,0,0,0.6)";
		else submenu.style.border = "";
	});

	// Test visuel automatique : ouvre 1s puis ferme
	console.log("force open test (1s)");
	submenu.classList.add("active");
	setTimeout(() => {
		submenu.classList.remove("active");
		console.log("force close test done");
	}, 1000);

	// raccourci clavier "t" pour toggle à la volée
	document.addEventListener("keydown", function (e) {
		if (e.key === "t") {
			submenu.classList.toggle("active");
			console.log(
				"keypress t -> toggle active:",
				submenu.classList.contains("active")
			);
		}
	});

	// montre computed styles quand on force
	window.showSubmenuComputed = function () {
		const cs = getComputedStyle(submenu);
		console.log("computed:", {
			display: cs.display,
			maxHeight: cs.maxHeight,
			opacity: cs.opacity,
			visibility: cs.visibility,
			offsetHeight: submenu.offsetHeight,
			scrollHeight: submenu.scrollHeight,
		});
	};
});
