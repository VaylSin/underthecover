document.addEventListener("DOMContentLoaded", function () {
	AOS.init();

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

	// ----- Barre de recherche header -----
	var toggle = document.getElementById("searchToggle");
	var dropdown = document.getElementById("searchDropdown");
	var closeBtn = document.getElementById("closeSearch");

	if (toggle && dropdown && closeBtn) {
		toggle.addEventListener("click", function (e) {
			e.preventDefault();
			dropdown.classList.add("active");
		});
		closeBtn.addEventListener("click", function () {
			dropdown.classList.remove("active");
		});
		// Optionnel : fermer au clic hors du panneau
		dropdown.addEventListener("click", function (e) {
			if (e.target === dropdown) {
				dropdown.classList.remove("active");
			}
		});
	}

	// Ajoute la classe "view-all-link" au bouton du widget de recherche WooCommerce
	var searchForm = document.querySelector(
		"#searchDropdown .search-form-container form"
	);
	if (searchForm) {
		var btn = searchForm.querySelector(
			'button[type="submit"], input[type="submit"]'
		);
		if (btn) {
			btn.classList.add("view-all-link");
		}
	}

	// ----- Sous-menu Boutique -----
	var trigger = document.querySelector(".menu-item-boutique");
	var boutiqueWrap = trigger ? trigger.closest(".boutique-container") : null;
	var submenu = document.getElementById("submenu-boutique");

	// Important: Ne pas arrêter le script avec un return global ici
	if (trigger && boutiqueWrap && submenu) {
		var closeTimer = null;
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
});

// ----- Bandeau défilant (version simplifiée sans conflit) -----
(function () {
	window.addEventListener("load", function () {
		const track = document.querySelector(".bandeau-track");
		if (!track) return;

		function adjustBandeau() {
			// Récupération de la largeur totale
			const trackWidth = track.scrollWidth;
			const halfWidth = Math.floor(trackWidth / 2);

			// Définition de la vitesse (ajustez selon préférence)
			const pxPerSecond = 60;
			const duration = Math.max(10, halfWidth / pxPerSecond);

			// Application
			track.style.animationDuration = duration + "s";
		}

		// Initialiser et mettre à jour au resize
		adjustBandeau();
		window.addEventListener("resize", adjustBandeau);
	});
})();
