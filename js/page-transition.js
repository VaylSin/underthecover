/**
 * Gestion des transitions de pages - Fondu velvet
 * Appliqué à toutes les pages sauf la homepage
 */

document.addEventListener("DOMContentLoaded", function () {
	// Ne pas exécuter sur la homepage (qui a son propre loader)
	if (document.body.classList.contains("home")) {
		return;
	}

	const pageTransition = document.getElementById("page-transition");
	const siteContent = document.querySelector(".site");

	if (!pageTransition || !siteContent) {
		return;
	}

	// Déclencher la transition de sortie immédiatement
	setTimeout(() => {
		pageTransition.classList.add("fade-out");
		siteContent.classList.add("page-loaded");
	}, 50);

	// Nettoyer l'élément de transition plus rapidement
	setTimeout(() => {
		if (pageTransition && pageTransition.parentNode) {
			pageTransition.remove();
		}
	}, 500);
});

// Gestion des liens internes pour les transitions entre pages
document.addEventListener("click", function (e) {
	const link = e.target.closest("a");

	// Vérifier si c'est un lien interne valide
	if (
		!link ||
		!link.href ||
		link.href.startsWith("mailto:") ||
		link.href.startsWith("tel:") ||
		link.href.includes("#") ||
		link.target === "_blank" ||
		link.hostname !== window.location.hostname ||
		link.classList.contains("no-transition")
	) {
		return;
	}

	// Ne pas appliquer de transition si on va vers la homepage
	const url = new URL(link.href);
	if (url.pathname === "/" || url.pathname === window.location.pathname) {
		return;
	}

	e.preventDefault();

	// Créer et afficher la transition d'entrée
	const transitionDiv = document.createElement("div");
	transitionDiv.id = "page-transition-out";
	transitionDiv.innerHTML = '<div class="transition-overlay"></div>';

	// Styles inline pour assurer le fonctionnement
	transitionDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: 99999;
        pointer-events: none;
    `;

	const overlay = transitionDiv.querySelector(".transition-overlay");
	overlay.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #bc3365 0%, rgba(188, 51, 101, 0.9) 100%);
        opacity: 0;
        transition: opacity 0.2s ease-in;
    `;

	document.body.appendChild(transitionDiv);

	// Déclencher l'animation d'entrée
	requestAnimationFrame(() => {
		overlay.style.opacity = "1";
	});

	// Naviguer vers la nouvelle page plus rapidement
	setTimeout(() => {
		window.location.href = link.href;
	}, 200);
});
