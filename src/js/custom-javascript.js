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

// Bandeau défilant amélioré - évite l'effet "affolé" au chargement
(function () {
	// S'assurer que tout est chargé avant d'initialiser
	window.addEventListener("load", function () {
		// Référence au bandeau
		const bandeauContainer = document.querySelector(".bandeau_deroulant");
		const track = document.querySelector(".bandeau-track");
		if (!track || !bandeauContainer) return;

		// Marquer comme en chargement
		bandeauContainer.classList.add("loading");

		// Initialiser avec un délai pour permettre le rendu complet
		setTimeout(function () {
			// Stopper toute animation existante
			track.style.animation = "none";

			// Forcer reflow
			void track.offsetWidth;

			// Réappliquer l'animation avec les valeurs optimales
			const trackWidth = track.scrollWidth;
			const duration = Math.max(10, Math.round(trackWidth / 100));

			// Appliquer avec RAF pour garantir stabilité
			requestAnimationFrame(function () {
				track.style.animationDuration = duration + "s";
				track.style.animationName = "bandeau-scroll";
				track.style.animationTimingFunction = "linear";
				track.style.animationIterationCount = "infinite";
				track.style.transform = "translate3d(0,0,0)";

				// Marquer comme prêt et afficher progressivement
				bandeauContainer.classList.remove("loading");
				bandeauContainer.classList.add("ready");
			});
		}, 300); // Délai avant initialisation
	});
})();
// Loader pour tout le site (uniquement sur la homepage)
(function () {
	// Éléments DOM
	const siteLoader = document.getElementById("site-loader");
	// Si pas de loader, on sort immédiatement (pages autres que homepage)
	if (!siteLoader) {
		// Révéler immédiatement le contenu sur les autres pages
		const siteContent = document.getElementById("page");
		if (siteContent) siteContent.classList.add("loaded");
		return;
	}

	const loaderBar = document.querySelector(".loader-bar");
	const siteContent = document.getElementById("page");

	// Progression simulée pendant le chargement
	let progress = 0;
	const progressInterval = setInterval(function () {
		progress += Math.random() * 5;
		if (progress > 70) progress = 70; // max 70% avant chargement complet
		if (loaderBar) loaderBar.style.width = progress + "%";
	}, 150);

	// Fonction qui masque le loader et montre le site
	function revealSite() {
		clearInterval(progressInterval);

		// Finaliser la barre de progression
		if (loaderBar) loaderBar.style.width = "100%";

		// Attendre un peu pour que l'utilisateur voie la barre complète
		setTimeout(function () {
			// Masquer le loader
			siteLoader.classList.add("hidden");

			// Afficher le contenu du site avec transition
			if (siteContent) siteContent.classList.add("loaded");

			// Initialiser le bandeau défilant quand tout est prêt
			setupBandeau();

			// Nettoyer les événements
			window.removeEventListener("load", onLoadHandler);
		}, 600);
	}

	// Attendre que tout soit chargé
	const onLoadHandler = function () {
		// Attendre que les polices soient chargées
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(function () {
				setTimeout(revealSite, 300);
			});
		} else {
			// Fallback pour les navigateurs sans API fonts
			setTimeout(revealSite, 800);
		}
	};

	window.addEventListener("load", onLoadHandler);
})();
