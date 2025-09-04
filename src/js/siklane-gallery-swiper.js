document.addEventListener("DOMContentLoaded", function () {
	if (typeof Swiper === "undefined") return;

	document.querySelectorAll(".siklane-swiper-gallery").forEach(function (root) {
		if (root._siklaneSimpleInited) return;
		root._siklaneSimpleInited = true;

		var thumbsEl = root.querySelector(".siklane-swiper-thumbs.swiper");
		var mainEl = root.querySelector(".siklane-swiper-main.swiper");
		if (!thumbsEl || !mainEl) return;

		// thumbs rapide (ne pas activer slideToClickedSlide)
		var thumbs = new Swiper(thumbsEl, {
			direction: window.innerWidth < 992 ? "horizontal" : "vertical",
			slidesPerView: "auto",
			spaceBetween: 8,
			freeMode: true,
			watchSlidesProgress: true,
			slideToClickedSlide: false,
			observer: true,
			observeParents: true,
		});

		// helper : wait images loaded (with timeout)
		function imagesLoadedWithTimeout(imgs, timeout) {
			var promises = imgs.map(function (img) {
				return new Promise(function (res) {
					if (!img) return res();
					if (img.complete && img.naturalWidth > 1) return res();
					var done = function () {
						img.removeEventListener("load", done);
						img.removeEventListener("error", done);
						res();
					};
					img.addEventListener("load", done);
					img.addEventListener("error", done);
					// safety timeout
					setTimeout(res, timeout || 1200);
				});
			});
			return Promise.all(promises);
		}

		// variable main accessible pour le handler click
		var main = null;

		// init main AFTER images loaded to avoid recalculs/décalages
		var mainImgs = Array.from(mainEl.querySelectorAll("img"));
		imagesLoadedWithTimeout(mainImgs, 1400)
			.then(function () {
				// create main with slidesPerView:1 and fade (stacked slides)
				main = new Swiper(mainEl, {
					initialSlide: 0,
					slidesPerView: 1,
					effect: "fade",
					fadeEffect: { crossFade: true },
					speed: 0,
					spaceBetween: 10,
					loop: false,
					thumbs: { swiper: thumbs },
					navigation: {
						nextEl: root.querySelector(".siklane-slide-next"),
						prevEl: root.querySelector(".siklane-slide-prev"),
					},
					observer: true,
					observeParents: true,
				});

				root.siklaneSwiper = main;
				console.log(
					"[siklane] main init after images loaded, slides:",
					main.slides.length
				);

				// post-init update
				setTimeout(function () {
					try {
						main.update();
						thumbs.update && thumbs.update();
					} catch (e) {
						console.warn("[siklane] post-init update failed", e);
					}
				}, 60);
			})
			.catch(function () {
				// fallback init
				main = new Swiper(mainEl, {
					initialSlide: 0,
					slidesPerView: 1,
					effect: "fade",
					fadeEffect: { crossFade: true },
					speed: 0,
					spaceBetween: 10,
					loop: false,
					thumbs: { swiper: thumbs },
					navigation: {
						nextEl: root.querySelector(".siklane-slide-next"),
						prevEl: root.querySelector(".siklane-slide-prev"),
					},
					observer: true,
					observeParents: true,
				});
				root.siklaneSwiper = main;
			});

		// queue pour clics pendant animation
		var pendingClick = null;

		// preload single main image by DOM index then slide (simplifié)
		function preloadMainAndGo(domIndex) {
			var mainSlides = Array.from(mainEl.querySelectorAll(".swiper-slide"));
			var s = mainSlides[domIndex];
			if (!s) return;
			var img = s.querySelector("img");
			var loadPromise = Promise.resolve();
			if (img && !(img.complete && img.naturalWidth > 1)) {
				loadPromise = new Promise(function (res) {
					var done = function () {
						img.removeEventListener("load", done);
						img.removeEventListener("error", done);
						res();
					};
					img.addEventListener("load", done);
					img.addEventListener("error", done);
					setTimeout(res, 1000);
				});
			}
			loadPromise.then(function () {
				try {
					if (!main) return;
					// si Swiper est en train d'animer, queue la demande
					if (main.animating) {
						pendingClick = domIndex;
						return;
					}
					// demander une seule fois le slide (Swiper gère le fade)
					var duration =
						main.params && main.params.speed ? main.params.speed : 1000;
					main.slideTo(domIndex, duration);
				} catch (e) {
					console.warn("[siklane] preloadMainAndGo failed", e);
				}
			});
		}

		// écouter la fin de transition pour traiter la queue
		function attachPendingHandler() {
			if (!main) return;
			main.on &&
				main.on("slideChangeTransitionEnd", function () {
					if (pendingClick !== null && pendingClick !== undefined) {
						var to = pendingClick;
						pendingClick = null;
						// petit délai pour laisser Swiper stabiliser
						setTimeout(function () {
							preloadMainAndGo(to);
						}, 30);
					}
				});
		}

		// après init main (dans then et fallback) -> attacher handler
		// ... dans le then() juste après root.siklaneSwiper = main; ajoute :
		// attachPendingHandler();

		// click handler : on précharge et on demande le slide (ne pas forcer update)
		thumbsEl.addEventListener(
			"click",
			function (e) {
				var btn = e.target.closest && e.target.closest(".swiper-slide");
				if (!btn) return;
				e.preventDefault();

				// logical index from thumb
				var raw =
					(btn.dataset && btn.dataset.siklaneIndex) ||
					btn.getAttribute("data-index") ||
					null;
				var idxCandidate = raw !== null ? parseInt(raw, 10) : null;
				var currentThumbs = Array.from(
					thumbsEl.querySelectorAll(".swiper-slide")
				);
				var mappedThumbIdx = currentThumbs.indexOf(btn);
				var logicalIdx =
					mappedThumbIdx >= 0
						? mappedThumbIdx
						: Number.isFinite(idxCandidate)
						? idxCandidate
						: currentThumbs.indexOf(btn);

				var mainSlidesNow = Array.from(
					mainEl.querySelectorAll(".swiper-slide")
				);
				var mappedMainNow = mainSlidesNow.findIndex(function (s) {
					return (
						s.dataset && parseInt(s.dataset.siklaneIndex, 10) === logicalIdx
					);
				});
				var targetDom = mappedMainNow >= 0 ? mappedMainNow : logicalIdx;

				console.log(
					"[siklane] thumb click -> logicalIdx:",
					logicalIdx,
					"targetDom:",
					targetDom
				);

				// preload and go (this will queue if animating)
				preloadMainAndGo(targetDom);

				// optimistic UI for thumbs
				try {
					thumbs.slideTo(logicalIdx, 200);
				} catch (e) {}
				currentThumbs.forEach(function (s, i) {
					s.classList.toggle("swiper-slide-thumb-active", i === logicalIdx);
				});
			},
			false
		);
	});
});
