// Script de débogage temporaire pour tester les fonctionnalités
console.log("=== DEBUT DEBUG TEST ===");

// Test 1: Vérifier si les éléments existent
setTimeout(() => {
	console.log("1. Eléments présents:");
	console.log(
		"- .menu-item-boutique:",
		document.querySelector(".menu-item-boutique")
	);
	console.log(
		"- .boutique-container:",
		document.querySelector(".boutique-container")
	);
	console.log(
		"- #submenu-boutique:",
		document.getElementById("submenu-boutique")
	);
	console.log("- .search-toggle:", document.querySelector(".search-toggle"));
	console.log("- #searchDropdown:", document.getElementById("searchDropdown"));
	console.log(
		"- .search-dropdown:",
		document.querySelector(".search-dropdown")
	);
	console.log(
		"- .smooth-scroll-wrapper:",
		document.querySelector(".smooth-scroll-wrapper")
	);
}, 1000);

// Test 2: Vérifier les initialisations
setTimeout(() => {
	console.log("2. Variables globales:");
	console.log("- __siklane_js_initialized:", window.__siklane_js_initialized);
	console.log(
		"- __siklane_initScroller:",
		typeof window.__siklane_initScroller
	);

	// Test manual du sous-menu
	const boutiqueContainer = document.querySelector(".boutique-container");
	if (boutiqueContainer) {
		console.log("3. Test manuel sous-menu:");
		boutiqueContainer.classList.add("open");
		console.log("- Classe 'open' ajoutée à boutique-container");

		setTimeout(() => {
			boutiqueContainer.classList.remove("open");
			console.log("- Classe 'open' supprimée");
		}, 2000);
	}

	// Test manuel de la recherche
	const searchDropdown =
		document.getElementById("searchDropdown") ||
		document.querySelector(".search-dropdown");
	if (searchDropdown) {
		console.log("4. Test manuel recherche:");
		searchDropdown.classList.add("open");
		console.log("- Classe 'open' ajoutée à search-dropdown");

		setTimeout(() => {
			searchDropdown.classList.remove("open");
			console.log("- Classe 'open' supprimée");
		}, 3000);
	}
}, 2000);

console.log("=== FIN DEBUG TEST ===");
