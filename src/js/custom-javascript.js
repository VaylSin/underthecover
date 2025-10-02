(function () {
	"use strict";

	// Éviter la double exécution
	if (window.__siklane_js_initialized) return;
	window.__siklane_js_initialized = true;

	// ==========================================================================
	// UTILITAIRES
	// ==========================================================================

	const $ = (sel, ctx = document) => ctx.querySelector(sel);
	const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
	const on = (el, ev, fn, opts = false) =>
		el && el.addEventListener(ev, fn, opts);

	const isHomePage = () =>
		document.body.classList.contains("home") ||
		document.body.classList.contains("front-page") ||
		location.pathname === "/" ||
		location.pathname === "/index.html";

	// ==========================================================================
	// GESTION DU SCROLL (Lock/Unlock)
	// ==========================================================================

	let scrollLocked = false;

	const preventDefault = (e) => e.preventDefault();
	const preventKeys = (e) => {
		const keys = [32, 33, 34, 35, 36, 37, 38, 39, 40];
		if (keys.includes(e.keyCode)) e.preventDefault();
	};

	function lockScroll() {
		if (scrollLocked) return;
		scrollLocked = true;

		document.documentElement.style.overflow = "hidden";
		document.body.style.overflow = "hidden";
		document.body.style.touchAction = "none";

		window.addEventListener("wheel", preventDefault, { passive: false });
		window.addEventListener("touchmove", preventDefault, { passive: false });
		window.addEventListener("keydown", preventKeys, { passive: false });

		// Classes CSS pour les hooks
		document.documentElement.classList.add("scroll-locked");
		document.body.classList.add("scroll-locked");
	}

	function unlockScroll() {
		if (!scrollLocked) return;
		scrollLocked = false;

		document.documentElement.style.overflow = "";
		document.body.style.overflow = "";
		document.body.style.touchAction = "";

		window.removeEventListener("wheel", preventDefault);
		window.removeEventListener("touchmove", preventDefault);
		window.removeEventListener("keydown", preventKeys);

		document.documentElement.classList.remove("scroll-locked");
		document.body.classList.remove("scroll-locked");
	}

	// ==========================================================================
	// LOADER
	// ==========================================================================

	function initLoader() {
		const loader = $("#site-loader");
		const site = $(".site");

		if (!site) return;

		// Pages non-accueil : suppression immédiate
		if (!isHomePage()) {
			if (loader) {
				loader.classList.add("hidden");
				setTimeout(() => loader.remove(), 100);
			}
			site.classList.add("loaded");
			return;
		}

		const bar = loader.querySelector(".loader-bar");
		let progress = 0;
		let finished = false;

		// Animation de progression
		const updateProgress = () => {
			if (finished || !bar) return;
			progress += Math.random() * 8 + 2;
			progress = Math.min(progress, 90);
			bar.style.width = progress + "%";
			setTimeout(updateProgress, 200);
		};

		updateProgress();

		// Finalisation au chargement
		on(
			window,
			"load",
			() => {
				finished = true;
				if (bar) bar.style.width = "100%";

				setTimeout(() => {
					loader.classList.add("hidden");
					site.classList.add("loaded");
					setTimeout(() => loader.remove(), 600);
				}, 300);
			},
			{ once: true }
		);

		// Sécurité après 8 secondes
		setTimeout(() => {
			if (!finished) {
				finished = true;
				loader.classList.add("hidden");
				site.classList.add("loaded");
				setTimeout(() => loader.remove(), 600);
			}
		}, 8000);
	}

	// ==========================================================================
	// SOUS-MENU BOUTIQUE
	// ==========================================================================

	function initBoutiqueMenu() {
		const link = $(".menu-item-boutique");
		const container = link?.closest(".boutique-container");
		const submenu = $("#submenu-boutique");

		if (!link || !container || !submenu) return; // Nettoyage des styles inline
		submenu.removeAttribute("style");

		// Observer pour empêcher la ré-application de styles
		const observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				if (
					mutation.type === "attributes" &&
					mutation.attributeName === "style"
				) {
					if (submenu.style.display) {
						submenu.style.removeProperty("display");
					}
				}
			});
		});
		observer.observe(submenu, { attributes: true, attributeFilter: ["style"] });

		// Accessibilité
		link.setAttribute("aria-haspopup", "true");
		link.setAttribute("aria-expanded", "false");

		let closeTimer = null;
		const DELAY = 120;

		const openSubmenu = () => {
			clearTimeout(closeTimer);
			container.classList.add("open");
			submenu.classList.add("open");
			link.setAttribute("aria-expanded", "true");
		};

		const closeSubmenu = () => {
			clearTimeout(closeTimer);
			container.classList.remove("open");
			submenu.classList.remove("open");
			link.setAttribute("aria-expanded", "false");
		};

		// Détection des capacités hover
		const hasHover = window.matchMedia(
			"(hover: hover) and (pointer: fine)"
		).matches;

		if (hasHover) {
			// Desktop : événements hover
			on(link, "mouseenter", openSubmenu);
			on(link, "mouseleave", () => {
				closeTimer = setTimeout(() => {
					if (!submenu.matches(":hover")) closeSubmenu();
				}, DELAY);
			});

			on(submenu, "mouseenter", () => {
				clearTimeout(closeTimer);
				openSubmenu();
			});
			on(submenu, "mouseleave", () => {
				closeTimer = setTimeout(closeSubmenu, DELAY);
			});
		} else {
			// Mobile : événements click
			on(link, "click", (e) => {
				e.preventDefault();
				container.classList.toggle("open");
				link.setAttribute(
					"aria-expanded",
					container.classList.contains("open") ? "true" : "false"
				);
			});

			on(document, "click", (e) => {
				if (!container.contains(e.target) && !submenu.contains(e.target)) {
					closeSubmenu();
				}
			});

			on(window, "scroll", closeSubmenu);
		}

		// Fermeture avec Echap
		on(document, "keydown", (e) => {
			if (e.key === "Escape") closeSubmenu();
		});
	}

	// ==========================================================================
	// MOTEUR DE RECHERCHE
	// ==========================================================================

	function initSearch() {
		// Système unique pour tous les devices
		const toggles = document.querySelectorAll(".search-toggle");
		const dropdown = document.getElementById("searchDropdown");
		const mobileDropdown = document.getElementById("mobileSearchDropdown");
		const closeBtn = document.getElementById("closeSearch");
		const mobileCloseBtn = document.getElementById("mobileCloseSearch");

		console.log("Debug recherche:", {
			toggles: toggles.length,
			dropdown,
			mobileDropdown,
			closeBtn,
			mobileCloseBtn,
		});

		if (toggles.length === 0 || (!dropdown && !mobileDropdown)) {
			console.log("Système de recherche: éléments manquants");
			return;
		}

		const openSearch = () => {
			console.log("openSearch appelée");
			// Ouvrir le bon dropdown selon la taille d'écran
			const isMobile = window.innerWidth < 992;
			const targetDropdown = isMobile ? mobileDropdown : dropdown;

			console.log("Mobile:", isMobile, "Target:", targetDropdown);

			if (targetDropdown) {
				console.log("Ouverture du dropdown");
				targetDropdown.classList.add("open");
				if (!isMobile) targetDropdown.style.display = "block";

				// Focus sur le champ de recherche
				const input = targetDropdown.querySelector(".search-field");
				if (input) setTimeout(() => input.focus(), 300);
			}
		};

		const closeSearch = () => {
			if (dropdown) dropdown.classList.remove("open");
			if (mobileDropdown) mobileDropdown.classList.remove("open");
		};

		// Événements pour toutes les icônes de recherche
		toggles.forEach((toggle) => {
			console.log("Ajout event sur toggle:", toggle);
			on(toggle, "click", (e) => {
				console.log("CLIC sur recherche détecté!");
				e.preventDefault();
				openSearch();
			});
		});

		// Boutons de fermeture
		if (closeBtn) {
			on(closeBtn, "click", (e) => {
				e.preventDefault();
				closeSearch();
			});
		}

		if (mobileCloseBtn) {
			on(mobileCloseBtn, "click", (e) => {
				e.preventDefault();
				closeSearch();
			});
		}

		// Fermeture avec Echap
		on(document, "keydown", (e) => {
			if (e.key === "Escape") {
				closeSearch();
			}
		});

		// Fermeture en cliquant à l'extérieur (simplifié)
		on(document, "click", (e) => {
			const isSearchToggle = e.target.closest('.search-toggle');
			const isDropdown = e.target.closest('#searchDropdown, #mobileSearchDropdown');
			
			if (!isSearchToggle && !isDropdown) {
				closeSearch();
			}
		});

		// Version simplifiée sans lockScroll - pas de timer nécessaire

		// Remplacer le bouton submit par une icône
		const replaceSubmitIcon = () => {
			const btn = dropdown.querySelector('form button[type="submit"]');
			if (!btn || btn.dataset.iconified) return;

			btn.innerHTML =
				'<i class="bi bi-search" aria-hidden="true"></i><span class="visually-hidden">Rechercher</span>';
			btn.setAttribute("aria-label", "Rechercher");
			btn.dataset.iconified = "1";
		};

		replaceSubmitIcon();
		new MutationObserver(replaceSubmitIcon).observe(dropdown, {
			childList: true,
			subtree: true,
		});
	}

	// ==========================================================================
	// SMOOTH SCROLL
	// ==========================================================================

	function initSmoothScroll() {
		if ($(".smooth-scroll-wrapper")) return;

		// Désactiver le smooth scroll sur mobile et tablette (jusqu'à 1024px)
		if (window.innerWidth <= 1024) return;

		// Éléments à préserver
		const preserveSelectors = [
			"#site-loader",
			"#searchDropdown",
			".search-dropdown",
			".social-sticky",
			".social-links",
		];
		const preserved = [];

		preserveSelectors.forEach((sel) => {
			$$(sel).forEach((el) => {
				if (el.parentNode !== document.body && !preserved.includes(el)) {
					preserved.push(el);
					document.body.appendChild(el);
				}
			});
		});

		// Créer le wrapper
		const wrapper = document.createElement("div");
		wrapper.className = "smooth-scroll-wrapper";

		// Déplacer le contenu dans le wrapper
		Array.from(document.body.childNodes).forEach((node) => {
			if (!preserved.includes(node) && node !== wrapper) {
				wrapper.appendChild(node);
			}
		});

		document.body.appendChild(wrapper);

		// Styles du wrapper
		Object.assign(wrapper.style, {
			position: "fixed",
			width: "100%",
			top: "0",
			left: "0",
			willChange: "transform",
		});

		// État du scroll
		let scrollY = window.scrollY || 0;
		let targetY = scrollY;
		let rafId = null;
		const SMOOTH_FACTOR = 0.08;

		// Mise à jour de la hauteur
		const updateHeight = () => {
			document.body.style.height = wrapper.scrollHeight + "px";
		};

		document.body.style.overflowY = "auto";
		updateHeight();

		// Observer les changements de taille
		if ("ResizeObserver" in window) {
			new ResizeObserver(updateHeight).observe(wrapper);
		}

		// Animation du scroll
		const render = () => {
			scrollY += (targetY - scrollY) * SMOOTH_FACTOR;
			wrapper.style.transform = `translate3d(0, ${-scrollY}px, 0)`;

			if (Math.abs(targetY - scrollY) > 0.5) {
				rafId = requestAnimationFrame(render);
			} else {
				rafId = null;
			}
		};

		// Gestion du scroll
		on(
			window,
			"scroll",
			() => {
				targetY = window.scrollY;
				if (!rafId) rafId = requestAnimationFrame(render);
			},
			{ passive: true }
		);

		on(window, "resize", updateHeight, { passive: true });

		// Gestion des ancres
		if (location.hash) {
			const target = $(location.hash);
			if (target)
				setTimeout(() => target.scrollIntoView({ behavior: "smooth" }), 500);
		}

		// Détacher les éléments problématiques
		detachProblematicElements(wrapper);
	}

	function detachProblematicElements(wrapper) {
		const selectors = [
			".search-dropdown",
			"#searchDropdown",
			"#site-loader",
			".social-sticky",
			".social-links",
		];

		selectors.forEach((sel) => {
			const el = wrapper.querySelector(sel);
			if (!el) return;

			// Déplacer vers le body
			document.body.appendChild(el);

			// Appliquer les styles fixes selon le type d'élément
			if (sel.includes("search") || sel === "#site-loader") {
				Object.assign(el.style, {
					position: "fixed",
					inset: "0",
					width: "100%",
					zIndex: "9999",
				});
			} else if (sel.includes("social")) {
				// Restaurer le positionnement social sticky
				Object.assign(el.style, {
					position: "fixed",
					left: "0",
					top: "50%",
					transform: "translateY(-50%)",
					zIndex: "9999",
					pointerEvents: "auto",
				});
			}
		});
	}

	// ==========================================================================
	// CARROUSEL CATÉGORIES
	// ==========================================================================

	function initCategoriesCarousel() {
		const container = $("#categories-carousel");
		if (!container) return;

		const row = container.querySelector(".carousel-row");
		const items = $$(".category-item", container);
		if (!row || !items.length) return;

		let itemsPerView = window.innerWidth > 768 ? 4 : 1;
		let current = 0;

		const update = () => {
			const itemWidth = items[0]?.offsetWidth || 0;
			if (!itemWidth) return;

			row.style.transform = `translateX(${-current * itemWidth}px)`;

			// Mise à jour des boutons
			const prev = container.querySelector(".carousel-control-prev");
			const next = container.querySelector(".carousel-control-next");

			if (prev) prev.classList.toggle("disabled", current <= 0);
			if (next)
				next.classList.toggle(
					"disabled",
					current >= items.length - itemsPerView
				);
		};

		// Boutons de navigation
		const prevBtn = container.querySelector(".carousel-control-prev");
		const nextBtn = container.querySelector(".carousel-control-next");

		if (prevBtn) {
			on(prevBtn, "click", (e) => {
				e.preventDefault();
				if (current > 0) {
					current--;
					update();
				}
			});
		}

		if (nextBtn) {
			on(nextBtn, "click", (e) => {
				e.preventDefault();
				if (current < items.length - itemsPerView) {
					current++;
					update();
				}
			});
		}

		// Responsive
		on(
			window,
			"resize",
			() => {
				const newItemsPerView = window.innerWidth > 768 ? 4 : 1;
				if (newItemsPerView !== itemsPerView) {
					itemsPerView = newItemsPerView;
					current = Math.min(current, items.length - itemsPerView);
					update();
				}
			},
			{ passive: true }
		);

		update();
	}

	// ==========================================================================
	// PANIER DRAWER
	// ==========================================================================

	function initCartDrawer() {
		const triggers = $$(".cart-toggle");
		const drawer = $("#cartDrawer");
		if (!drawer || !triggers.length) return;

		const closeElements = $$("[data-cart-drawer-close]", drawer);

		const openDrawer = () => {
			drawer.classList.add("active");
			drawer.setAttribute("aria-hidden", "false");
			lockScroll();

			// Reset scroll et focus
			const content = drawer.querySelector(".drawer-content");
			if (content) content.scrollTop = 0;

			const firstFocusable = drawer.querySelector(
				"button, a, input, [tabindex]"
			);
			if (firstFocusable) firstFocusable.focus();
		};

		const closeDrawer = () => {
			drawer.classList.remove("active");
			drawer.setAttribute("aria-hidden", "true");
			unlockScroll();
		};

		// Événements
		triggers.forEach((btn) =>
			on(btn, "click", (e) => {
				e.preventDefault();
				openDrawer();
			})
		);

		closeElements.forEach((el) =>
			on(el, "click", (e) => {
				e.preventDefault();
				closeDrawer();
			})
		);

		// Fermeture avec Echap
		on(document, "keydown", (e) => {
			if (e.key === "Escape") closeDrawer();
		});

		// Fermeture sur backdrop
		const backdrop = drawer.querySelector(".cart-drawer-backdrop");
		if (backdrop) on(backdrop, "click", closeDrawer);

		// Auto-ouverture sur ajout produit
		on(document.body, "added_to_cart", () => {
			setTimeout(() => {
				if (!drawer.classList.contains("active")) openDrawer();
			}, 200);
		});
	}

	// ==========================================================================
	// GESTION DES BOUTONS "AJOUTER AU PANIER"
	// ==========================================================================

	function initAddToCartButtons() {
		const addToCartButtons = $$(
			".add_to_cart_button, .ajax_add_to_cart, .single_add_to_cart_button"
		);

		addToCartButtons.forEach((button) => {
			// Sauvegarder le texte original
			const originalTextDesktop =
				button.querySelector(".d-none.d-xl-inline")?.textContent || "";
			const originalTextMobile =
				button.querySelector(".d-inline.d-xl-none")?.textContent || "";

			// Écouter les clics sur le bouton
			on(button, "click", () => {
				// JUSTE AJOUTER LA CLASSE LOADING - LE CSS FAIT LE RESTE !
				button.classList.add("loading");
				console.log(
					"✅ Classe loading ajoutée - le spinner CSS devrait apparaître"
				);
			});
		});

		// Écouter l'événement global d'ajout au panier avec jQuery
		if (typeof jQuery !== "undefined") {
			jQuery(document.body).on("added_to_cart", function (e) {
				console.log("🎯 EVENT added_to_cart reçu (jQuery) !");

				// Retrouver le bouton qui a déclenché l'ajout
				const clickedButton = document.querySelector(
					".add_to_cart_button.loading, .ajax_add_to_cart.loading"
				);

				console.log("🔍 Bouton loading trouvé:", clickedButton);

				if (clickedButton) {
					// Supprimer la classe loading
					setTimeout(() => {
						clickedButton.classList.remove("loading");

						// Ajouter la classe added et changer le texte
						clickedButton.classList.add("added");

						const desktopSpan = clickedButton.querySelector(
							".d-none.d-xl-inline"
						);
						const mobileSpan = clickedButton.querySelector(
							".d-inline.d-xl-none"
						);

						console.log("📱 Spans trouvés:", {
							desktop: desktopSpan,
							mobile: mobileSpan,
						});

						if (desktopSpan) {
							desktopSpan.textContent = "ajouté";
							console.log("✅ Desktop text changé:", desktopSpan.textContent);
						}
						if (mobileSpan) {
							mobileSpan.textContent = "ajouté";
							console.log("✅ Mobile text changé:", mobileSpan.textContent);
						}

						// Revenir à l'état normal après 3 secondes
						setTimeout(() => {
							clickedButton.classList.remove("added");
							if (desktopSpan) desktopSpan.textContent = "ajouter au panier";
							if (mobileSpan) mobileSpan.textContent = "ajouter";
						}, 3000);
					}, 100);
				} else {
					console.log("❌ Aucun bouton loading trouvé !");
				}
			});
		}
	}

	// ==========================================================================
	// INITIALISATION AOS
	// ==========================================================================

	function initAOS() {
		if (typeof AOS !== "undefined" && AOS?.init) {
			AOS.init();
		}
	}

	// ==========================================================================
	// INITIALISATION PRINCIPALE
	// ==========================================================================

	function initialize() {
		// Initialisation de base
		initAOS();
		initLoader();

		// Assurer que le site a la classe loaded
		const site = $(".site");
		if (site && !isHomePage()) {
			site.classList.add("loaded");
		}

		// Fonctionnalités principales
		initSearch();
		initBoutiqueMenu();
		initCategoriesCarousel();
		initCartDrawer();
		initAddToCartButtons(); // Gestion du texte "ajouté"
		const loader = $("#site-loader");
		if (loader && isHomePage()) {
			// Attendre que le loader se cache
			const checkLoader = setInterval(() => {
				if (
					!$("#site-loader") ||
					$("#site-loader").classList.contains("hidden")
				) {
					clearInterval(checkLoader);
					setTimeout(initSmoothScroll, 100);
				}
			}, 100);
		} else {
			setTimeout(initSmoothScroll, 100);
		}

		// Smooth scroll natif en fallback
		setTimeout(() => {
			try {
				document.documentElement.style.scrollBehavior = "smooth";
			} catch (e) {}
		}, 200);
	}

	// ==========================================================================
	// POINT D'ENTRÉE
	// ==========================================================================

	if (document.readyState === "loading") {
		on(document, "DOMContentLoaded", initialize);
	} else {
		initialize();
	}

	// Exposition des fonctions pour debug
	window.__siklane_initScroller = initSmoothScroll;
	window.__siklane_lockScroll = lockScroll;
	window.__siklane_unlockScroll = unlockScroll;
})();
