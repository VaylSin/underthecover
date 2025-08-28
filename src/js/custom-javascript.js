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
			const itemWidth = items[0].offsetWidth;
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

			prevButton.classList.toggle("disabled", currentPosition <= 0);
			nextButton.classList.toggle(
				"disabled",
				currentPosition >= totalItems - itemsPerView
			);
		}

		// Initialisation
		updateCarousel();

		// Gérer les événements des boutons
		categoriesCarousel
			.querySelector(".carousel-control-prev")
			.addEventListener("click", function (e) {
				e.preventDefault();
				if (currentPosition > 0) {
					currentPosition--;
					updateCarousel();
				}
			});

		categoriesCarousel
			.querySelector(".carousel-control-next")
			.addEventListener("click", function (e) {
				e.preventDefault();
				if (currentPosition < totalItems - itemsPerView) {
					currentPosition++;
					updateCarousel();
				}
			});

		// Adapter au redimensionnement
		window.addEventListener("resize", function () {
			const newItemsPerView = window.innerWidth > 768 ? 4 : 1;
			if (newItemsPerView !== itemsPerView) {
				itemsPerView = newItemsPerView;
				currentPosition = Math.min(currentPosition, totalItems - itemsPerView);
				updateCarousel();
			}
		});
	}

	// ----- Slider principal -----
	const slider = document.getElementById("main-slider");
	if (slider) {
		const dots = slider.querySelectorAll(".slider-dot");
		const carousel = new bootstrap.Carousel(slider);

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
		// Optionnel : fermer au clic hors du panneau
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

	// ----- Bandeau défilant : stabilisation & durée dynamique -----
	(function () {
		const track = document.querySelector(".bandeau-track");
		if (!track) return;

		function initBandeau() {
			// Dupliquer le contenu une seule fois (si non dupliqué)
			if (!track.dataset.duplicated) {
				track.innerHTML = track.innerHTML + track.innerHTML;
				track.dataset.duplicated = "1";
			}

			// largeur du contenu original (après duplication, /2)
			const totalWidth = track.scrollWidth;
			const originalWidth = totalWidth / 2 || totalWidth;

			// vitesse souhaitée en pixels / seconde (ajuste si besoin)
			const speedPxPerSec = 80; // 80 px/s par défaut
			const durationSeconds = Math.max(
				8,
				Math.round(originalWidth / speedPxPerSec)
			);

			// appliquer la variable CSS utilisée par la règle SCSS
			track.style.setProperty("--bandeau-duration", durationSeconds + "s");

			// restart animation proprement pour appliquer la nouvelle durée
			track.style.animation = "none";
			// force reflow
			/* eslint-disable no-unused-expressions */
			void track.offsetWidth;
			/* eslint-enable no-unused-expressions */
			track.style.animation = "";
			// ensure GPU compositing
			track.style.transform = "translate3d(0,0,0)";
			track.style.willChange = "transform";
		}

		// debounce simple pour resize
		let bandeauResizeTimer = null;
		window.addEventListener("resize", function () {
			clearTimeout(bandeauResizeTimer);
			bandeauResizeTimer = setTimeout(initBandeau, 150);
		});

		// init au chargement (après fonts)
		window.addEventListener("load", function () {
			setTimeout(initBandeau, 80);
		});

		// si DOMReady déjà atteint, init rapidement
		initBandeau();
	})();

	// ----- Sous-menu Boutique (logique existante) -----
	var trigger = document.querySelector(".menu-item-boutique");
	if (!trigger) return;
	var boutiqueWrap = trigger.closest(".boutique-container");
	var submenu = document.getElementById("submenu-boutique");
	if (!boutiqueWrap || !submenu) return;

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
});
