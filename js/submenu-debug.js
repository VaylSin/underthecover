document.addEventListener("DOMContentLoaded", function () {
	console.clear();
	console.log("submenu-debug chargé");
	// const link = document.querySelector(".menu-item-boutique");
	// const submenu = document.getElementById("submenu-boutique");
	// console.log("menu-item-boutique ->", link);
	// console.log("submenu-boutique ->", submenu);
	// if (link) {
	// 	link.addEventListener("click", (e) => {
	// 		e.preventDefault();
	// 		console.log(
	// 			"clic boutique - bounding rect:",
	// 			link.getBoundingClientRect()
	// 		);
	// 	});
	// 	link.addEventListener("mouseenter", () =>
	// 		console.log("mouseenter boutique")
	// 	);
	// 	link.addEventListener("mouseleave", () =>
	// 		console.log("mouseleave boutique")
	// 	);
	// }
	// if (submenu) {
	// 	submenu.addEventListener("mouseenter", () =>
	// 		console.log("mouseenter submenu")
	// 	);
	// 	submenu.addEventListener("mouseleave", () =>
	// 		console.log("mouseleave submenu")
	// 	);
	// 	console.log("submenu offsetHeight:", submenu.offsetHeight);
	// }
	console.log(
		"submenu script présent ?",
		!!document.querySelector('script[src*="submenu-boutique.js"]')
	);
	console.log("link:", document.querySelector(".menu-item-boutique"));
	console.log("submenu:", document.getElementById("submenu-boutique"));
});
