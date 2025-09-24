/* =============================================
   GRILLES FORCÉES 2 COLONNES - MOBILE
   ============================================= */

/* Injection de CSS pour forcer les grilles 2 colonnes partout */
document.addEventListener("DOMContentLoaded", function () {
	// Fonction pour appliquer la grille 2-colonnes
	function forceMobileGrid() {
		if (window.innerWidth <= 767) {
			// Sélecteurs multiples pour toutes les grilles de produits
			const gridSelectors = [
				".row.g-3",
				".row.g-4",
				".row.gy-4",
				".popular-products-row",
				".products-grid",
				".woocommerce ul.products",
				".woocommerce-page ul.products",
				".archive.woocommerce .site-main .row",
				".woocommerce .products",
				".products.columns-3",
				".products.columns-4",
				".related.products ul.products",
				".cross-sells ul.products",
				".up-sells ul.products",
			];

			gridSelectors.forEach((selector) => {
				const elements = document.querySelectorAll(selector);
				elements.forEach((element) => {
					// Force CSS Grid sur le conteneur
					element.style.display = "grid";
					element.style.gridTemplateColumns = "repeat(2, 1fr)";
					element.style.gap = "1rem";
					element.style.width = "100%";

					// Reset les enfants
					const children = element.children;
					Array.from(children).forEach((child) => {
						child.style.width = "100%";
						child.style.float = "none";
						child.style.margin = "0";
						child.style.flex = "none";
						child.style.maxWidth = "100%";
					});
				});
			});

			// Forcer aussi via les classes Bootstrap
			const bootstrapCols = document.querySelectorAll(
				".col-md-3, .col-md-4, .col-lg-3, .col-lg-4, .col-xl-3"
			);
			bootstrapCols.forEach((col) => {
				if (col.closest(".row")) {
					col.style.flex = "0 0 calc(50% - 0.5rem)";
					col.style.maxWidth = "calc(50% - 0.5rem)";
				}
			});
		}
	}

	// Applique au chargement
	forceMobileGrid();

	// Applique au redimensionnement
	let resizeTimer;
	window.addEventListener("resize", function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(forceMobileGrid, 250);
	});

	// Observer les changements DOM (pour AJAX/filtres)
	const observer = new MutationObserver(function (mutations) {
		mutations.forEach(function (mutation) {
			if (mutation.type === "childList") {
				setTimeout(forceMobileGrid, 100);
			}
		});
	});

	// Observer le body pour les nouveaux produits
	observer.observe(document.body, {
		childList: true,
		subtree: true,
	});

	// WooCommerce AJAX events
	document.addEventListener("wc_fragments_loaded", forceMobileGrid);
	document.addEventListener("wc_fragments_refreshed", forceMobileGrid);
});

/* Debug function */
function debugMobileGrid() {
	console.log("=== DEBUG MOBILE GRID ===");
	console.log("Screen width:", window.innerWidth);
	console.log("Is mobile:", window.innerWidth <= 767);

	const grids = document.querySelectorAll(".row, .products, ul.products");
	console.log("Found grids:", grids.length);

	grids.forEach((grid, index) => {
		console.log(`Grid ${index}:`, {
			element: grid,
			display: getComputedStyle(grid).display,
			gridTemplateColumns: getComputedStyle(grid).gridTemplateColumns,
			children: grid.children.length,
		});
	});
}
