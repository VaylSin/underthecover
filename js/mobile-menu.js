/**
 * Menu Mobile Slide-in
 * Gère l'ouverture/fermeture du menu et les sous-menus déroulants
 */

(function () {
	"use strict";

	let mobileMenu, mobileMenuOverlay, mobileMenuBurger, mobileMenuClose;
	let isInitialized = false;

	function initMobileMenu() {
		if (isInitialized) return;

		console.log("Initialisation du menu mobile...");

		// Sélection des éléments
		mobileMenu = document.getElementById("mobileMenu");
		mobileMenuOverlay = document.getElementById("mobileMenuOverlay");
		mobileMenuBurger = document.getElementById("mobileMenuBurger");
		mobileMenuClose = document.getElementById("mobileMenuClose");

		console.log("Éléments trouvés:", {
			mobileMenu: !!mobileMenu,
			mobileMenuOverlay: !!mobileMenuOverlay,
			mobileMenuBurger: !!mobileMenuBurger,
			mobileMenuClose: !!mobileMenuClose,
		});

		if (
			!mobileMenu ||
			!mobileMenuOverlay ||
			!mobileMenuBurger ||
			!mobileMenuClose
		) {
			console.error("Menu mobile: Éléments manquants");
			return;
		}

		// Event listeners avec logs
		mobileMenuBurger.addEventListener("click", function (e) {
			console.log("Clic sur burger");
			e.preventDefault();
			openMobileMenu();
		});

		mobileMenuClose.addEventListener("click", function (e) {
			console.log("Clic sur fermer");
			e.preventDefault();
			closeMobileMenu();
		});

		mobileMenuOverlay.addEventListener("click", function (e) {
			console.log("Clic sur overlay");
			e.preventDefault();
			closeMobileMenu();
		});

		// Gestion des sous-menus
		initSubmenus();

		// Gestion de l'icône de recherche mobile (même comportement que desktop)
		initMobileSearchToggle();

		// Gestion du bouton de fermeture de la recherche mobile
		initMobileSearchClose();

		// Réessayer l'initialisation de la recherche mobile après 500ms au cas où les éléments ne sont pas encore disponibles
		setTimeout(() => {
			if (!document.getElementById("mobileSearchToggle")?.onclick) {
				console.log("Réinitialisation de la recherche mobile...");
				initMobileSearchToggle();
				initMobileSearchClose();
			}
		}, 500);

		// Gestion du panier mobile
		initMobileCart();

		isInitialized = true;
		console.log("Menu mobile initialisé");
	}

	function openMobileMenu() {
		console.log("Ouverture du menu mobile");
		mobileMenu.classList.add("active");
		mobileMenuOverlay.classList.add("active");
		mobileMenuBurger.classList.add("active");
		mobileMenu.setAttribute("aria-hidden", "false");
		mobileMenuBurger.setAttribute("aria-expanded", "true");
		document.body.classList.add("mobile-menu-open");

		// Focus sur le premier élément focusable
		const firstFocusable = mobileMenu.querySelector(
			'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
		);
		if (firstFocusable) {
			setTimeout(() => firstFocusable.focus(), 100);
		}
	}

	function closeMobileMenu() {
		console.log("Fermeture du menu mobile");
		mobileMenu.classList.remove("active");
		mobileMenuOverlay.classList.remove("active");
		mobileMenuBurger.classList.remove("active");
		mobileMenu.setAttribute("aria-hidden", "true");
		mobileMenuBurger.setAttribute("aria-expanded", "false");
		document.body.classList.remove("mobile-menu-open");

		// Fermer tous les sous-menus
		closeAllSubmenus();

		// Redonner le focus au bouton burger
		mobileMenuBurger.focus();
	}

	function initSubmenus() {
		// Gestion des sous-menus boutique
		const boutiqueSubmenu = document.getElementById("boutique-submenu");
		const boutiqueTrigger = document.querySelector(
			'[data-target="boutique-submenu"]'
		);

		if (boutiqueTrigger && boutiqueSubmenu) {
			boutiqueTrigger.addEventListener("click", function () {
				toggleSubmenu(this, boutiqueSubmenu);
			});
		}

		// Gestion des catégories
		const categoryTriggers = document.querySelectorAll(
			".mobile-category-trigger"
		);
		categoryTriggers.forEach((trigger) => {
			trigger.addEventListener("click", function () {
				const targetId = this.getAttribute("data-target");
				const targetElement = document.getElementById(targetId);
				if (targetElement) {
					toggleSubmenu(this, targetElement);
				}
			});
		});

		// Gestion des autres sous-menus du menu principal
		const menuTriggers = document.querySelectorAll(
			'.mobile-menu-trigger:not([data-target="boutique-submenu"])'
		);
		menuTriggers.forEach((trigger) => {
			trigger.addEventListener("click", function () {
				const targetId = this.getAttribute("data-target");
				const targetElement = document.getElementById(targetId);
				if (targetElement) {
					toggleSubmenu(this, targetElement);
				}
			});
		});
	}

	function toggleSubmenu(trigger, submenu) {
		console.log("Toggle submenu:", trigger, submenu);
		const isExpanded = trigger.getAttribute("aria-expanded") === "true";

		if (isExpanded) {
			// Fermer
			console.log("Fermeture du sous-menu");
			trigger.setAttribute("aria-expanded", "false");
			submenu.classList.remove("active");
		} else {
			// Ouvrir
			console.log("Ouverture du sous-menu");
			trigger.setAttribute("aria-expanded", "true");
			submenu.classList.add("active");
		}
	}

	function closeAllSubmenus() {
		// Fermer tous les sous-menus
		const allTriggers = document.querySelectorAll(
			".mobile-menu-trigger, .mobile-category-trigger"
		);
		const allSubmenus = document.querySelectorAll(
			".mobile-submenu, .mobile-products"
		);

		allTriggers.forEach((trigger) => {
			trigger.setAttribute("aria-expanded", "false");
		});

		allSubmenus.forEach((submenu) => {
			submenu.classList.remove("active");
		});
	}

	function initMobileSearchToggle() {
		console.log("Initialisation de la recherche mobile...");

		const mobileSearchToggle = document.getElementById("mobileSearchToggle");
		const mobileSearchDropdown = document.getElementById(
			"mobileSearchDropdown"
		);

		console.log("Éléments de recherche mobile trouvés:", {
			mobileSearchToggle: !!mobileSearchToggle,
			mobileSearchDropdown: !!mobileSearchDropdown,
			mobileSearchToggleElement: mobileSearchToggle,
			mobileSearchDropdownElement: mobileSearchDropdown,
		});

		if (mobileSearchToggle && mobileSearchDropdown) {
			// Méthode alternative : utiliser onclick directement pour éviter les conflits
			mobileSearchToggle.onclick = function (e) {
				console.log("=== CLIC SUR RECHERCHE MOBILE (onclick) ===", e);
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();

				// Toggle du dropdown
				if (mobileSearchDropdown.classList.contains("open")) {
					console.log("Fermeture de la recherche mobile");
					mobileSearchDropdown.classList.remove("open");
					document.body.style.overflow = "";
				} else {
					console.log("Ouverture de la recherche mobile");
					mobileSearchDropdown.classList.add("open");
					document.body.style.overflow = "hidden";

					// Focus sur le champ de recherche
					setTimeout(() => {
						const searchInput =
							mobileSearchDropdown.querySelector(".search-field");
						if (searchInput) {
							console.log("Focus sur le champ de recherche mobile");
							searchInput.focus();
						}
					}, 300);
				}
				return false;
			};

			console.log("Onclick handler ajouté sur l'icône de recherche mobile");
		} else {
			console.error("Éléments de recherche mobile manquants:", {
				mobileSearchToggle: !!mobileSearchToggle,
				mobileSearchDropdown: !!mobileSearchDropdown,
			});
		}
	}

	function handleMobileSearchClick(e) {
		console.log("=== CLIC SUR RECHERCHE MOBILE ===", e);
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		const mobileSearchDropdown = document.getElementById(
			"mobileSearchDropdown"
		);
		if (!mobileSearchDropdown) {
			console.error("mobileSearchDropdown non trouvé");
			return;
		}

		// Utiliser la même logique que le toggle de recherche desktop (classe "open")
		if (mobileSearchDropdown.classList.contains("open")) {
			console.log("Fermeture de la recherche mobile");
			mobileSearchDropdown.classList.remove("open");
			// Réactiver le scroll de la page
			document.body.style.overflow = "";
		} else {
			console.log("Ouverture de la recherche mobile");
			mobileSearchDropdown.classList.add("open");
			// Désactiver le scroll de la page
			document.body.style.overflow = "hidden";

			// Focus sur le champ de recherche
			setTimeout(() => {
				const searchInput = mobileSearchDropdown.querySelector(".search-field");
				if (searchInput) {
					console.log("Focus sur le champ de recherche mobile");
					searchInput.focus();
				}
			}, 300);
		}
	}

	function initMobileSearchClose() {
		const mobileCloseSearch = document.getElementById("mobileCloseSearch");
		const mobileSearchDropdown = document.getElementById(
			"mobileSearchDropdown"
		);

		if (mobileCloseSearch && mobileSearchDropdown) {
			mobileCloseSearch.addEventListener("click", function (e) {
				e.preventDefault();
				console.log("Fermeture de la recherche mobile via bouton");
				mobileSearchDropdown.classList.remove("open");
				document.body.style.overflow = "";
			});
		}

		// Fermeture avec Escape
		document.addEventListener("keydown", function (e) {
			if (
				e.key === "Escape" &&
				mobileSearchDropdown &&
				mobileSearchDropdown.classList.contains("open")
			) {
				console.log("Fermeture de la recherche mobile via Escape");
				mobileSearchDropdown.classList.remove("open");
				document.body.style.overflow = "";
			}
		});
	}

	function initMobileCart() {
		const cartButton = document.querySelector(".mobile-action-btn.cart-toggle");
		if (cartButton) {
			cartButton.addEventListener("click", function (e) {
				e.preventDefault();

				// Si le cart drawer existe, l'ouvrir
				if (typeof window.initCartDrawer === "function") {
					closeMobileMenu();
					setTimeout(() => {
						// Déclencher l'ouverture du cart drawer
						const cartDrawer = document.getElementById("cartDrawer");
						if (cartDrawer) {
							cartDrawer.classList.add("active");
							cartDrawer.setAttribute("aria-hidden", "false");
							document.body.classList.add("cart-open");
						}
					}, 300);
				} else {
					// Rediriger vers la page panier
					const cartUrl = this.getAttribute("data-cart-url");
					if (cartUrl) {
						window.location.href = cartUrl;
					}
				}
			});
		}
	}

	// Gestion des touches du clavier
	function handleKeyboard(e) {
		if (!mobileMenu.classList.contains("active")) return;

		switch (e.key) {
			case "Escape":
				closeMobileMenu();
				break;
			case "Tab":
				handleTabNavigation(e);
				break;
		}
	}

	function handleTabNavigation(e) {
		const focusableElements = mobileMenu.querySelectorAll(
			'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
		);
		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];

		if (e.shiftKey) {
			if (document.activeElement === firstElement) {
				lastElement.focus();
				e.preventDefault();
			}
		} else {
			if (document.activeElement === lastElement) {
				firstElement.focus();
				e.preventDefault();
			}
		}
	}

	// Synchronisation du compteur de panier
	function updateMobileCartCount() {
		const cartBadge = document.querySelector(".mobile-menu .cart-count-badge");
		const headerCartCount = document.querySelector(".cart-count");

		if (cartBadge && headerCartCount) {
			cartBadge.textContent = headerCartCount.textContent;
		}
	}

	// Gestion du resize pour fermer le menu si on passe en desktop
	function handleResize() {
		if (
			window.innerWidth >= 992 &&
			mobileMenu &&
			mobileMenu.classList.contains("active")
		) {
			closeMobileMenu();
		}
	}

	// Initialisation
	function init() {
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", initMobileMenu);
		} else {
			initMobileMenu();
		}

		// Event listeners globaux
		document.addEventListener("keydown", handleKeyboard);
		window.addEventListener("resize", handleResize);

		// Mise à jour du compteur de panier lors des événements WooCommerce
		if (typeof jQuery !== "undefined") {
			jQuery(document.body).on(
				"wc_fragments_refreshed wc_fragments_loaded added_to_cart removed_from_cart",
				updateMobileCartCount
			);
		}
	}

	// Exposer les fonctions pour debug
	window.mobileMenuDebug = {
		open: openMobileMenu,
		close: closeMobileMenu,
		toggleSubmenu: toggleSubmenu,
		isInitialized: () => isInitialized,
		testMobileSearch: () => {
			const element = document.getElementById("mobileSearchToggle");
			console.log("Test mobile search:", {
				element: element,
				visible: element
					? window.getComputedStyle(element).display !== "none"
					: false,
				clickable: element ? !element.disabled : false,
				hasEventListener: element ? element.onclick !== null : false,
			});
			if (element) {
				handleMobileSearchClick({
					preventDefault: () => {},
					stopPropagation: () => {},
					stopImmediatePropagation: () => {},
				});
			}
		},
	};

	// Lancer l'initialisation
	init();

	// Debug functions pour les tests
	window.debugMobileMenu = function () {
		console.log("=== DEBUG MOBILE MENU ===");
		console.log("Mobile menu found:", !!mobileMenu);
		console.log("Mobile burger found:", !!mobileMenuBurger);
		console.log("Mobile close found:", !!mobileMenuClose);
		console.log("Is initialized:", isInitialized);
	};

	// Debug function spécifique pour la recherche mobile
	window.debugMobileSearch = function () {
		console.log("=== DEBUG RECHERCHE MOBILE ===");
		const toggle = document.getElementById("mobileSearchToggle");
		const dropdown = document.getElementById("mobileSearchDropdown");

		console.log("Elements found:", {
			toggle: !!toggle,
			dropdown: !!dropdown,
			toggleElement: toggle,
			dropdownElement: dropdown,
		});

		if (toggle) {
			console.log("Toggle classes:", toggle.className);
			console.log("Toggle onclick:", !!toggle.onclick);
		}

		if (dropdown) {
			console.log("Dropdown classes:", dropdown.className);
			console.log(
				"Dropdown has 'open' class:",
				dropdown.classList.contains("open")
			);
		}
	};

	// Test direct de l'event handler
	window.testMobileSearchClick = function () {
		console.log("Test direct du clic recherche mobile");
		const dropdown = document.getElementById("mobileSearchDropdown");
		if (dropdown) {
			dropdown.classList.toggle("open");
			console.log("Toggle effectué, classes:", dropdown.className);
		}
	};
})();
