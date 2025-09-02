(function () {
	"use strict";

	// Guard pour éviter double exécution
	if (window.__siklane_js_initialized) return;
	window.__siklane_js_initialized = true;

	/* Helpers */
	const $ = (sel, ctx = document) => ctx.querySelector(sel);
	const $$ = (sel, ctx = document) =>
		Array.from((ctx || document).querySelectorAll(sel));
	const on = (el, ev, fn, opts) =>
		el && el.addEventListener(ev, fn, opts || false);
	const isHomePath = () =>
		document.body.classList.contains("home") ||
		document.body.classList.contains("front-page") ||
		location.pathname === "/" ||
		location.pathname === "/index.html";

	/* ----- Scroll lock helpers ----- */
	let __siklane_scroll_locked = false;
	function preventDefault(e) {
		e.preventDefault();
	}
	function preventKeys(e) {
		const keys = [32, 33, 34, 35, 36, 37, 38, 39, 40];
		if (keys.includes(e.keyCode)) e.preventDefault();
	}
	function disablePageScroll() {
		if (__siklane_scroll_locked) return;
		__siklane_scroll_locked = true;
		document.documentElement.style.overflow = "hidden";
		document.body.style.overflow = "hidden";
		document.body.style.touchAction = "none";
		window.addEventListener("wheel", preventDefault, { passive: false });
		window.addEventListener("touchmove", preventDefault, { passive: false });
		window.addEventListener("keydown", preventKeys, { passive: false });
		if (
			window.__smoothScrollbar &&
			typeof window.__smoothScrollbar.stop === "function"
		) {
			try {
				window.__smoothScrollbar.stop();
			} catch (e) {
				/* silent */
			}
		}
	}
	function enablePageScroll() {
		if (!__siklane_scroll_locked) return;
		__siklane_scroll_locked = false;
		document.documentElement.style.overflow = "";
		document.body.style.overflow = "";
		document.body.style.touchAction = "";
		window.removeEventListener("wheel", preventDefault, { passive: false });
		window.removeEventListener("touchmove", preventDefault, { passive: false });
		window.removeEventListener("keydown", preventKeys, { passive: false });
		if (
			window.__smoothScrollbar &&
			typeof window.__smoothScrollbar.update === "function"
		) {
			try {
				window.__smoothScrollbar.update();
			} catch (e) {
				/* silent */
			}
		}
	}

	/* ----- AOS ----- */
	function initAOS() {
		if (typeof AOS !== "undefined" && AOS && typeof AOS.init === "function") {
			AOS.init();
		}
	}

	/* ----- Loader ----- */
	function initLoader() {
		const siteLoader = document.getElementById("site-loader");
		const siteContent = document.querySelector(".site");
		if (!siteContent) return;

		// Non-home : remove immediately (prevent overlay)
		if (!isHomePath()) {
			if (siteLoader) {
				siteLoader.classList.add("hidden");
				siteLoader.dataset.removed = "1";
				window.__siklane_loader_removed = true;
				try {
					siteLoader.parentNode &&
						siteLoader.parentNode.removeChild(siteLoader);
				} catch (e) {
					/* silent */
				}
			}
			siteContent.classList.add("loaded");
			return;
		}

		// Home: orchestrate fake progress then hide on load
		if (!siteLoader) {
			siteContent.classList.add("loaded");
			return;
		}

		const bar = siteLoader.querySelector(".loader-bar");
		let progress = 0;
		let interval = null;
		let finished = false;

		const setWidth = (n) => {
			progress = Math.max(progress, n);
			if (bar) bar.style.width = Math.min(100, progress) + "%";
		};
		const startFakeProgress = () => {
			if (!bar || interval) return;
			interval = setInterval(() => {
				if (finished) {
					clearInterval(interval);
					interval = null;
					return;
				}
				const step = Math.random() * 6 + 1;
				progress = Math.min(90, progress + step);
				bar.style.width = progress + "%";
			}, 180);
		};

		setWidth(6);
		startFakeProgress();

		on(
			window,
			"load",
			() => {
				finished = true;
				if (interval) {
					clearInterval(interval);
					interval = null;
				}
				if (bar) requestAnimationFrame(() => (bar.style.width = "100%"));
				setTimeout(() => {
					siteLoader.classList.add("hidden");
					siteContent.classList.add("loaded");
					setTimeout(() => {
						try {
							siteLoader.parentNode &&
								siteLoader.parentNode.removeChild(siteLoader);
						} catch (e) {}
					}, 600);
				}, 450);
			},
			{ once: true }
		);

		// Safety fallback
		setTimeout(() => {
			if (!finished) {
				siteLoader.classList.add("hidden");
				siteContent.classList.add("loaded");
				try {
					siteLoader.parentNode &&
						siteLoader.parentNode.removeChild(siteLoader);
				} catch (e) {}
			}
		}, 9000);
	}

	/* ----- Ensure .site.loaded class across pages ----- */
	function ensureSiteLoadedClass() {
		const site = document.querySelector(".site");
		if (!site) return;
		if (!isHomePath()) {
			site.classList.add("loaded");
			return;
		}
		// Home: wait for loader hide or load event
		const loader = document.getElementById("site-loader");
		if (!loader) {
			site.classList.add("loaded");
			return;
		}
		const mo = new MutationObserver((mutations, obs) => {
			if (loader.classList.contains("hidden")) {
				site.classList.add("loaded");
				try {
					obs.disconnect();
				} catch (e) {}
			}
		});
		mo.observe(loader, { attributes: true, attributeFilter: ["class"] });
		on(
			window,
			"load",
			() => {
				site.classList.add("loaded");
				try {
					mo.disconnect();
				} catch (e) {}
			},
			{ once: true }
		);
		setTimeout(() => {
			if (!site.classList.contains("loaded")) site.classList.add("loaded");
			try {
				mo.disconnect();
			} catch (e) {}
		}, 9000);
	}

	/* ----- Search overlay toggle ----- */
	function initSearchToggle() {
		const searchToggle = document.getElementById("searchToggle");
		const searchDropdown =
			document.getElementById("searchDropdown") || $(".search-dropdown");
		const closeSearch = document.getElementById("closeSearch");
		if (!searchToggle || !searchDropdown || !closeSearch) return;

		on(searchToggle, "click", (e) => {
			e.preventDefault();
			searchDropdown.classList.add("open");
			disablePageScroll();
			const input = searchDropdown.querySelector(".search-field");
			if (input) setTimeout(() => input.focus(), 300);
		});
		on(closeSearch, "click", (e) => {
			e.preventDefault();
			searchDropdown.classList.remove("open");
			enablePageScroll();
		});
		on(document, "keydown", (e) => {
			if (e.key === "Escape" && searchDropdown.classList.contains("open")) {
				searchDropdown.classList.remove("open");
				enablePageScroll();
			}
		});
		on(document, "click", (e) => {
			if (
				!searchDropdown.contains(e.target) &&
				!searchToggle.contains(e.target) &&
				searchDropdown.classList.contains("open")
			) {
				searchDropdown.classList.remove("open");
				enablePageScroll();
			}
		});
		// if already open at init
		if (searchDropdown.classList.contains("open")) disablePageScroll();
	}

	/* ----- Replace search submit with icon ----- */
	function replaceSearchButtonWithIcon() {
		const doReplace = () => {
			const btn = document.querySelector(
				'.search-dropdown form button[type="submit"], #searchDropdown form button[type="submit"]'
			);
			if (!btn || btn.dataset.iconified === "1") return;
			if (!btn.getAttribute("aria-label"))
				btn.setAttribute("aria-label", "Rechercher");
			btn.innerHTML = "";
			const icon = document.createElement("i");
			icon.className = "bi bi-search";
			icon.setAttribute("aria-hidden", "true");
			icon.style.fontSize = "1.4rem";
			icon.style.lineHeight = "1";
			const sr = document.createElement("span");
			sr.className = "visually-hidden";
			sr.textContent = "Rechercher";
			btn.appendChild(icon);
			btn.appendChild(sr);
			btn.dataset.iconified = "1";
		};
		doReplace();
		const mo = new MutationObserver(doReplace);
		mo.observe(document.body, { childList: true, subtree: true });
	}

	/* ----- Categories carousel (light) ----- */
	function initCategoriesCarousel() {
		const container = document.getElementById("categories-carousel");
		if (!container) return;
		const row = container.querySelector(".carousel-row");
		const items = container.querySelectorAll(".category-item");
		if (!row || items.length === 0) return;
		let itemsPerView = window.innerWidth > 768 ? 4 : 1;
		let current = 0;
		function update() {
			const w = items[0]?.offsetWidth || 0;
			if (!w) return;
			row.style.transform = `translateX(${-current * w}px)`;
			const prev = container.querySelector(".carousel-control-prev");
			const next = container.querySelector(".carousel-control-next");
			if (prev) prev.classList.toggle("disabled", current <= 0);
			if (next)
				next.classList.toggle(
					"disabled",
					current >= items.length - itemsPerView
				);
		}
		update();
		const prevBtn = container.querySelector(".carousel-control-prev");
		if (prevBtn)
			on(prevBtn, "click", (e) => {
				e.preventDefault();
				if (current > 0) {
					current--;
					update();
				}
			});
		const nextBtn = container.querySelector(".carousel-control-next");
		if (nextBtn)
			on(nextBtn, "click", (e) => {
				e.preventDefault();
				if (current < items.length - itemsPerView) {
					current++;
					update();
				}
			});
		on(
			window,
			"resize",
			() => {
				const npv = window.innerWidth > 768 ? 4 : 1;
				if (npv !== itemsPerView) {
					itemsPerView = npv;
					current = Math.min(current, items.length - itemsPerView);
					update();
				}
			},
			{ passive: true }
		);
	}

	/* ----- Main slider (bootstrap) ----- */
	function initMainSlider() {
		const slider = document.getElementById("main-slider");
		if (!slider) return;
		const dots = slider.querySelectorAll(".slider-dot");
		if (typeof bootstrap !== "undefined" && bootstrap.Carousel) {
			const carousel = new bootstrap.Carousel(slider);
			if (dots.length) {
				dots.forEach((dot, idx) =>
					on(dot, "click", () => {
						carousel.to(idx);
						dots.forEach((d) => d.classList.remove("active"));
						dot.classList.add("active");
					})
				);
				on(slider, "slid.bs.carousel", (e) => {
					const active = e.to;
					dots.forEach((d, i) => d.classList.toggle("active", i === active));
				});
			}
		}
	}

	/* ----- Boutique submenu ----- */
	function initBoutiqueMenu() {
		const trigger = document.querySelector(".menu-item-boutique");
		const boutiqueWrap = trigger
			? trigger.closest(".boutique-container")
			: null;
		const submenu = document.getElementById("submenu-boutique");
		if (!trigger || !boutiqueWrap || !submenu) return;
		let closeTimer = null;
		const open = () => {
			clearTimeout(closeTimer);
			boutiqueWrap.classList.add("open");
			trigger.setAttribute("aria-expanded", "true");
		};
		const close = () => {
			clearTimeout(closeTimer);
			closeTimer = setTimeout(() => {
				boutiqueWrap.classList.remove("open");
				trigger.setAttribute("aria-expanded", "false");
			}, 120);
		};
		on(trigger, "mouseenter", open);
		on(trigger, "focus", open);
		on(trigger, "mouseleave", close);
		on(trigger, "blur", close);
		on(submenu, "mouseenter", open);
		on(submenu, "mouseleave", close);
		on(trigger, "click", (e) => {
			if (window.innerWidth < 992) {
				e.preventDefault();
				boutiqueWrap.classList.contains("open") ? close() : open();
			}
		});
		on(document, "keyup", (e) => {
			if (e.key === "Escape") close();
		});
		on(document, "click", (e) => {
			if (!boutiqueWrap.contains(e.target) && !submenu.contains(e.target))
				close();
		});
	}

	/* ----- Smooth libraries fallback ----- */
	function initSmoothLibraries() {
		if (window.__smooth_libs_initialized) return;
		window.__smooth_libs_initialized = true;
		const wrapper =
			document.querySelector(".smooth-scroll-wrapper") ||
			document.querySelector(".site");
		if (typeof Scrollbar !== "undefined" && wrapper) {
			try {
				const sb = Scrollbar.init(wrapper, { damping: 0.07, thumbMinSize: 20 });
				window.__smoothScrollbar = sb;
				return;
			} catch (e) {
				/* fallback */
			}
		}
		try {
			document.documentElement.style.scrollBehavior = "smooth";
		} catch (e) {}
		document.querySelectorAll('a[href^="#"]').forEach((a) =>
			on(a, "click", function (ev) {
				const href = this.getAttribute("href");
				if (!href || href === "#" || href === "#!") return;
				const target = document.querySelector(href);
				if (!target) return;
				ev.preventDefault();
				target.scrollIntoView({ behavior: "smooth", block: "start" });
				history.pushState(null, "", href);
			})
		);
	}

	/* ----- detachAfterWrap ----- */
	function detachAfterWrap(optionalWrapper) {
		const wrapper =
			optionalWrapper || document.querySelector(".smooth-scroll-wrapper");
		if (!wrapper) return;
		const selectors = [
			".search-dropdown",
			"#searchDropdown",
			".social-sticky",
			".social-links",
			"#site-loader",
		];
		selectors.forEach((sel) => {
			const el = wrapper.querySelector(sel);
			if (!el) return;

			// If loader already marked removed, clean placeholder and skip
			if (
				sel === "#site-loader" &&
				(el.classList.contains("hidden") ||
					el.dataset.removed === "1" ||
					window.__siklane_loader_removed)
			) {
				const phClass = `${sel.replace(/[^a-z0-9]/gi, "_")}_placeholder`;
				const ph = wrapper.querySelector(`.${phClass}`);
				if (ph && ph.parentNode) ph.parentNode.removeChild(ph);
				try {
					if (el.parentNode) el.parentNode.removeChild(el);
				} catch (e) {}
				return;
			}

			// placeholder to preserve layout if needed
			try {
				const placeholder = document.createElement("div");
				placeholder.className = `${sel.replace(
					/[^a-z0-9]/gi,
					"_"
				)}_placeholder`;
				placeholder.style.width = el.offsetWidth + "px";
				placeholder.style.height = el.offsetHeight + "px";
				placeholder.style.display = getComputedStyle(el).display || "block";
				el.parentNode.insertBefore(placeholder, el);
			} catch (e) {
				/* ignore */
			}

			// move element to body
			document.body.appendChild(el);

			// apply inline styles appropriate to element
			if (sel.includes("search") || sel === "#site-loader") {
				Object.assign(el.style, {
					position: "fixed",
					inset: "0",
					width: "100%",
					maxHeight: "100vh",
					zIndex: "9999",
					pointerEvents: el.classList.contains("hidden") ? "none" : "auto",
					visibility: el.classList.contains("hidden") ? "hidden" : "visible",
				});
			} else if (sel.includes("social")) {
				Object.assign(el.style, {
					position: "fixed",
					left: "0",
					top: "50%",
					transform: "translateY(-50%)",
					zIndex: "3000",
					pointerEvents: "auto",
				});
			}
		});
	}

	/* ----- Custom smooth scroller (lightweight) ----- */
	function initScroller() {
		if (document.querySelector(".smooth-scroll-wrapper")) return;

		// Preserve nodes that must stay direct children of body
		const preserveSelectors = [
			"#site-loader",
			"#searchDropdown",
			".search-dropdown",
			".social-sticky",
			".social-links",
		];
		const preserved = [];
		preserveSelectors.forEach((sel) =>
			$$(sel).forEach((el) => {
				if (el.parentNode !== document.body && !preserved.includes(el)) {
					preserved.push(el);
					document.body.appendChild(el);
				}
			})
		);

		// Create wrapper and move remaining nodes inside it
		const wrapper = document.createElement("div");
		wrapper.className = "smooth-scroll-wrapper";

		const bodyNodes = Array.from(document.body.childNodes).slice();
		bodyNodes.forEach((node) => {
			if (preserved.includes(node)) return;
			if (node === wrapper) return;
			wrapper.appendChild(node);
		});

		document.body.appendChild(wrapper);

		// wrapper styles
		Object.assign(wrapper.style, {
			position: "fixed",
			width: "100%",
			top: "0",
			left: "0",
			willChange: "transform",
		});

		// scroller state
		let scrollY = window.scrollY || 0;
		let scrollTarget = scrollY;
		let rafId = null;
		const cfg = { speed: 0.06 };

		function updateHeight() {
			const h = wrapper.scrollHeight || document.documentElement.clientHeight;
			document.body.style.height = `${h}px`;
		}
		document.body.style.overflowY = "auto";
		document.body.style.position = "relative";
		updateHeight();

		// initial transform
		scrollTarget = scrollY = window.scrollY || 0;
		wrapper.style.transform = `translate3d(0,${-scrollY}px,0)`;

		// ResizeObserver to keep height updated
		if ("ResizeObserver" in window) {
			try {
				new ResizeObserver(updateHeight).observe(wrapper);
			} catch (e) {
				/* silent */
			}
		}

		function render() {
			scrollY += (scrollTarget - scrollY) * cfg.speed;
			wrapper.style.transform = `translate3d(0,${-scrollY}px,0)`;
			if (Math.abs(scrollTarget - scrollY) < 0.1) {
				rafId = null;
				return;
			}
			rafId = requestAnimationFrame(render);
		}

		function onScroll() {
			scrollTarget = window.scrollY;
			if (!rafId) rafId = requestAnimationFrame(render);
		}

		on(window, "scroll", onScroll, { passive: true });
		on(window, "resize", updateHeight, { passive: true });

		// handle hash jumping after init
		if (window.location.hash) {
			const t = document.querySelector(window.location.hash);
			if (t) setTimeout(() => t.scrollIntoView(), 500);
		}

		// detach problematic UI elements that ended in wrapper
		detachAfterWrap(wrapper);
	}

	/* ----- Watcher to detach after wrapper appears ----- */
	function watchWrapperThenDetach() {
		if (document.querySelector(".smooth-scroll-wrapper")) {
			detachAfterWrap();
			return;
		}
		const mo = new MutationObserver((mutations, obs) => {
			if (document.querySelector(".smooth-scroll-wrapper")) {
				obs.disconnect();
				detachAfterWrap();
			}
		});
		mo.observe(document.body, { childList: true, subtree: false });
	}

	/* ----- Single DOMContentLoaded orchestration ----- */
	on(document, "DOMContentLoaded", function () {
		initAOS();
		initLoader();
		ensureSiteLoadedClass();
		initSearchToggle();
		replaceSearchButtonWithIcon();
		initCategoriesCarousel();
		initMainSlider();
		initBoutiqueMenu();

		// Initialize scroller: wait loader hide on home, else init immediately
		const loader = document.getElementById("site-loader");
		if (loader && isHomePath()) {
			const check = setInterval(() => {
				const l = document.getElementById("site-loader");
				if (!l || l.classList.contains("hidden")) {
					clearInterval(check);
					try {
						initScroller();
					} catch (e) {
						console.warn("[smooth] initScroller failed:", e);
					}
				}
			}, 120);
		} else {
			try {
				initScroller();
			} catch (e) {
				console.warn("[smooth] initScroller failed:", e);
			}
		}

		setTimeout(initSmoothLibraries, 120);
		watchWrapperThenDetach();
	});

	/* Expose debug hooks */
	window.__siklane_detachAfterWrap = detachAfterWrap;
	window.__siklane_initScroller = initScroller;
	window.__siklane_initSmoothLibraries = initSmoothLibraries;
})();
