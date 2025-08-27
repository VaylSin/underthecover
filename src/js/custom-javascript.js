document.addEventListener("DOMContentLoaded", function () {
	AOS.init();

	// ----- Carousel des catégories -----
	const categoriesCarousel = document.querySelector("#categories-carousel");
	if (categoriesCarousel) {
		const carouselRow = categoriesCarousel.querySelector(".carousel-row");
		const items = categoriesCarousel.querySelectorAll(".category-item");
		const totalItems = items.length;
		const itemsPerView = window.innerWidth > 768 ? 4 : 1;
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
				currentPosition = Math.min(
					currentPosition,
					totalItems - newItemsPerView
				);
				updateCarousel();
			}
		});
	}

	// ----- Slider principal -----
	const slider = document.getElementById("main-slider");
	if (!slider) return;

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

	const track = document.querySelector(".bandeau-track");
	if (track) {
		track.innerHTML += track.innerHTML; // Duplique le contenu pour effet infini
	}
});
