document.addEventListener("DOMContentLoaded", function () {
	const categoriesCarousel = document.querySelector("#categories-carousel");
	if (categoriesCarousel) {
		const carouselRow = categoriesCarousel.querySelector(".carousel-row");
		const items = categoriesCarousel.querySelectorAll(".category-item");
		const totalItems = items.length;
		const itemsPerView = window.innerWidth > 768 ? 3 : 1;
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
			const newItemsPerView = window.innerWidth > 768 ? 3 : 1;
			if (newItemsPerView !== itemsPerView) {
				currentPosition = Math.min(
					currentPosition,
					totalItems - newItemsPerView
				);
				updateCarousel();
			}
		});
	}
});
