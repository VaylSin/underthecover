/* =============================================
   FOOTER ACCORDÉON - MOBILE/TABLETTE
   ============================================= */

document.addEventListener("DOMContentLoaded", function () {
	// Fonction pour initialiser les accordéons footer
	function initFooterAccordion() {
		const headers = document.querySelectorAll("[data-footer-toggle]");

		headers.forEach((header) => {
			header.addEventListener("click", function () {
				// Seulement sur mobile (< 768px)
				if (window.innerWidth < 768) {
					const targetId = this.getAttribute("data-footer-toggle");
					const content = document.getElementById(targetId);
					const icon = this.querySelector(".footer-accordion-icon");

					if (content) {
						// Toggle des classes
						this.classList.toggle("active");
						content.classList.toggle("show");

						// Force l'affichage du contenu
						if (content.classList.contains("show")) {
							content.style.maxHeight = content.scrollHeight + "px";
							content.style.opacity = "1";
						} else {
							content.style.maxHeight = "0";
							content.style.opacity = "0";
						}

						// Animation de l'icône
						if (icon) {
							if (content.classList.contains("show")) {
								icon.style.transform = "rotate(180deg)";
							} else {
								icon.style.transform = "rotate(0deg)";
							}
						}
					}
				}
			});
		});
	}

	// Fonction pour réinitialiser sur resize
	function handleResize() {
		const contents = document.querySelectorAll(".footer-accordion-content");
		const headers = document.querySelectorAll(".footer-accordion-header");

		if (window.innerWidth >= 768) {
			// Desktop : tout ouvert
			contents.forEach((content) => {
				content.classList.remove("show");
				content.style.maxHeight = "none";
			});

			headers.forEach((header) => {
				header.classList.remove("active");
				const icon = header.querySelector(".footer-accordion-icon");
				if (icon) {
					icon.style.transform = "rotate(0deg)";
				}
			});
		} else {
			// Mobile : fermer tout par défaut
			contents.forEach((content) => {
				if (!content.classList.contains("show")) {
					content.style.maxHeight = "0";
					content.style.opacity = "0";
				} else {
					content.style.maxHeight = content.scrollHeight + "px";
					content.style.opacity = "1";
				}
			});
		}
	}

	// Initialiser
	initFooterAccordion();

	// Écouter le resize avec debounce
	let resizeTimer;
	window.addEventListener("resize", function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(handleResize, 250);
	});

	// Initialiser l'état selon la taille d'écran
	handleResize();
});

/* Debug function */
function debugFooterAccordion() {
	console.log("=== DEBUG FOOTER ACCORDION ===");
	console.log("Screen width:", window.innerWidth);
	console.log("Is mobile:", window.innerWidth < 768);

	const headers = document.querySelectorAll("[data-footer-toggle]");
	console.log("Found headers:", headers.length);

	headers.forEach((header, index) => {
		const targetId = header.getAttribute("data-footer-toggle");
		const content = document.getElementById(targetId);
		console.log(`Header ${index}:`, {
			targetId: targetId,
			hasContent: !!content,
			isActive: header.classList.contains("active"),
			contentVisible: content ? content.classList.contains("show") : false,
			contentHeight: content ? content.scrollHeight : 0,
			actualContent: content ? content.innerHTML.trim().length : 0,
			maxHeight: content ? content.style.maxHeight : "none",
			opacity: content ? content.style.opacity : "none",
		});

		if (content) {
			console.log(
				`Content HTML for ${targetId}:`,
				content.innerHTML.substring(0, 200)
			);
		}
	});
}
