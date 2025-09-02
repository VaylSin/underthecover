document.addEventListener("DOMContentLoaded", function () {
	// ----- Initialisation des animations AOS -----
	if (typeof AOS !== "undefined") {
		AOS.init();
	}

	// ----- Loader plein écran -----
	const siteLoader = document.getElementById("site-loader");
	const siteContent = document.querySelector(".site");

	if (siteLoader && siteContent) {
		// Masquer le loader après le chargement
		window.addEventListener("load", function () {
			siteLoader.classList.add("hidden");
			siteContent.classList.add("loaded");
		});
	}

	// ----- Barre de recherche header -----
	const searchToggle = document.getElementById("searchToggle");
	const searchDropdown = document.getElementById("searchDropdown");
	const closeSearch = document.getElementById("closeSearch");

	if (searchToggle && searchDropdown && closeSearch) {
		// Ouvrir la barre de recherche
		searchToggle.addEventListener("click", function (e) {
			e.preventDefault();
			searchDropdown.classList.add("open");
			const searchField = searchDropdown.querySelector(".search-field");
			if (searchField) {
				setTimeout(() => searchField.focus(), 300); // Focus sur le champ après l'animation
			}
		});

		// Fermer la barre de recherche
		closeSearch.addEventListener("click", function (e) {
			e.preventDefault();
			searchDropdown.classList.remove("open");
		});

		// Fermer avec la touche "Escape"
		document.addEventListener("keydown", function (e) {
			if (e.key === "Escape" && searchDropdown.classList.contains("open")) {
				searchDropdown.classList.remove("open");
			}
		});

		// Fermer au clic en dehors de la barre de recherche
		document.addEventListener("click", function (e) {
			if (
				!searchDropdown.contains(e.target) &&
				!searchToggle.contains(e.target) &&
				searchDropdown.classList.contains("open")
			) {
				searchDropdown.classList.remove("open");
			}
		});
	}

	// ----- Carousel des catégories -----
	const categoriesCarousel = document.querySelector("#categories-carousel");
	if (categoriesCarousel) {
		const carouselRow = categoriesCarousel.querySelector(".carousel-row");
		const items = categoriesCarousel.querySelectorAll(".category-item");
		const totalItems = items.length;
		let itemsPerView = window.innerWidth > 768 ? 4 : 1;
		let currentPosition = 0;

		// Fonction pour mettre à jour la position du carousel
		function updateCarousel() {
			const itemWidth = items[0]?.offsetWidth || 0;
			if (carouselRow && itemWidth) {
				carouselRow.style.transform = `translateX(${
					-currentPosition * itemWidth
				}px)`;

				// Mettre à jour l'état des boutons
				const prevButton = categoriesCarousel.querySelector(
					".carousel-control-prev"
				);
				const nextButton = categoriesCarousel.querySelector(
					".carousel-control-next"
				);

				if (prevButton)
					prevButton.classList.toggle("disabled", currentPosition <= 0);
				if (nextButton)
					nextButton.classList.toggle(
						"disabled",
						currentPosition >= totalItems - itemsPerView
					);
			}
		}

		// Initialisation si on a des items et une row
		if (items.length > 0 && carouselRow) {
			updateCarousel();

			// Gérer les événements des boutons
			const prevBtn = categoriesCarousel.querySelector(
				".carousel-control-prev"
			);
			if (prevBtn) {
				prevBtn.addEventListener("click", function (e) {
					e.preventDefault();
					if (currentPosition > 0) {
						currentPosition--;
						updateCarousel();
					}
				});
			}

			const nextBtn = categoriesCarousel.querySelector(
				".carousel-control-next"
			);
			if (nextBtn) {
				nextBtn.addEventListener("click", function (e) {
					e.preventDefault();
					if (currentPosition < totalItems - itemsPerView) {
						currentPosition++;
						updateCarousel();
					}
				});
			}

			// Adapter au redimensionnement
			window.addEventListener("resize", function () {
				const newItemsPerView = window.innerWidth > 768 ? 4 : 1;
				if (newItemsPerView !== itemsPerView) {
					itemsPerView = newItemsPerView;
					currentPosition = Math.min(
						currentPosition,
						totalItems - itemsPerView
					);
					updateCarousel();
				}
			});
		}
	}

	// ----- Slider principal -----
	const slider = document.getElementById("main-slider");
	if (slider) {
		const dots = slider.querySelectorAll(".slider-dot");

		// Vérifier que bootstrap est chargé avant d'utiliser
		if (typeof bootstrap !== "undefined" && bootstrap.Carousel) {
			const carousel = new bootstrap.Carousel(slider);

			if (dots.length) {
				dots.forEach((dot, idx) => {
					dot.addEventListener("click", function () {
						carousel.to(idx);
						// Met à jour la classe active sur les dots
						dots.forEach((d) => d.classList.remove("active"));
						dot.classList.add("active");
					});
				});

				// Synchronise les dots quand le slide change (flèches, auto, swipe)
				slider.addEventListener("slid.bs.carousel", function (e) {
					const activeIdx = e.to;
					dots.forEach((dot, idx) => {
						dot.classList.toggle("active", idx === activeIdx);
					});
				});
			}
		}
	}

	// ----- Sous-menu Boutique -----
	const trigger = document.querySelector(".menu-item-boutique");
	const boutiqueWrap = trigger ? trigger.closest(".boutique-container") : null;
	const submenu = document.getElementById("submenu-boutique");

	if (trigger && boutiqueWrap && submenu) {
		let closeTimer = null;

		function open() {
			clearTimeout(closeTimer);
			boutiqueWrap.classList.add("open");
			trigger.setAttribute("aria-expanded", "true");
		}

		function close() {
			clearTimeout(closeTimer);
			closeTimer = setTimeout(function () {
				boutiqueWrap.classList.remove("open");
				trigger.setAttribute("aria-expanded", "false");
			}, 120);
		}

		// desktop / keyboard
		trigger.addEventListener("mouseenter", open);
		trigger.addEventListener("focus", open);
		trigger.addEventListener("mouseleave", close);
		trigger.addEventListener("blur", close);

		// keep open when hovering submenu
		submenu.addEventListener("mouseenter", open);
		submenu.addEventListener("mouseleave", close);

		// mobile tap : toggle (préserve comportement lien si large écran)
		trigger.addEventListener("click", function (e) {
			if (window.innerWidth < 992) {
				e.preventDefault();
				if (boutiqueWrap.classList.contains("open")) close();
				else open();
			}
		});

		// close on ESC or click outside
		document.addEventListener("keyup", function (e) {
			if (e.key === "Escape") close();
		});
		document.addEventListener("click", function (e) {
			if (!boutiqueWrap.contains(e.target) && !submenu.contains(e.target))
				close();
		});
	}

	(function replaceSearchButtonWithIcon() {
		function doReplace() {
			const btn = document.querySelector(
				'.search-dropdown form button[type="submit"]'
			);
			if (!btn) return;
			if (btn.dataset.iconified === "1") return; // déjà fait

			// préserver aria-label ou en créer un
			if (!btn.getAttribute("aria-label"))
				btn.setAttribute("aria-label", "Rechercher");

			// vider le contenu visible
			btn.innerHTML = "";

			// créer l'icône <i>
			const icon = document.createElement("i");
			icon.className = "bi bi-search";
			icon.setAttribute("aria-hidden", "true");
			// garantir la taille si le bouton a font-size:0
			icon.style.fontSize = "1.4rem";
			icon.style.lineHeight = "1";

			// texte caché pour lecteurs d'écran
			const sr = document.createElement("span");
			sr.className = "visually-hidden";
			sr.textContent = "Rechercher";

			btn.appendChild(icon);
			btn.appendChild(sr);
			btn.dataset.iconified = "1";
		}

		// exécuter au chargement du DOM et au load (sécurité)
		document.addEventListener("DOMContentLoaded", doReplace);
		window.addEventListener("load", doReplace);

		// observer les ajouts dynamiques (ex : injection via JS)
		const observer = new MutationObserver(function () {
			doReplace();
		});
		observer.observe(document.body, { childList: true, subtree: true });
	})();
});
