(function () {
  "use strict";

  var header = document.querySelector(".site-header");
  var navToggle = document.querySelector(".nav-toggle");
  var navMain = document.querySelector(".nav-main");
  var heroBgWrap = document.querySelector(".hero__bg-wrap");
  var galleryRoot = document.getElementById("gallery-grid");
  var reducedMotion =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function closeMenu() {
    if (!header) return;
    header.classList.remove("is-open");
    if (navToggle) navToggle.setAttribute("aria-expanded", "false");
    document.body.classList.remove("nav-open");
  }

  if (navToggle && navMain) {
    navToggle.addEventListener("click", function () {
      var open = !header.classList.contains("is-open");
      header.classList.toggle("is-open", open);
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.classList.toggle("nav-open", open);
    });

    navMain.querySelectorAll('a[href^="#"]').forEach(function (link) {
      link.addEventListener("click", function () {
        if (window.matchMedia("(max-width: 900px)").matches) closeMenu();
      });
    });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeMenu();
  });

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      var id = anchor.getAttribute("href");
      if (!id || id === "#") return;
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "start" });
    });
  });

  /* Parallax hero */
  var ticking = false;
  function parallaxTick() {
    ticking = false;
    if (!heroBgWrap || reducedMotion) return;
    var hero = document.querySelector(".hero");
    if (!hero) return;
    var rect = hero.getBoundingClientRect();
    var vh = window.innerHeight || 1;
    var progress = 1 - (rect.bottom / (rect.height + vh));
    progress = Math.max(0, Math.min(1, progress));
    var y = (progress - 0.35) * 55;
    heroBgWrap.style.transform = "translate3d(0, " + y + "px, 0)";
  }

  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        parallaxTick();
        ticking = false;
      });
      ticking = true;
    }
  }

  if (heroBgWrap && !reducedMotion) {
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    parallaxTick();
  }

  /* Lightbox — gallery buttons are rendered server-side; we only handle interaction. */
  function openLightbox(src, alt) {
    var lb = document.getElementById("lightbox");
    var lbImg = document.getElementById("lightbox-img");
    if (!lb || !lbImg || !src) return;
    lbImg.src = src;
    lbImg.alt = alt || "";
    lb.classList.add("is-open");
    lb.setAttribute("aria-hidden", "false");
    document.body.classList.add("lightbox-open");
  }

  function closeLightbox() {
    var lb = document.getElementById("lightbox");
    var lbImg = document.getElementById("lightbox-img");
    if (!lb) return;
    lb.classList.remove("is-open");
    lb.setAttribute("aria-hidden", "true");
    document.body.classList.remove("lightbox-open");
    if (lbImg) lbImg.removeAttribute("src");
  }

  if (galleryRoot) {
    galleryRoot.addEventListener("click", function (e) {
      var btn = e.target.closest(".gallery__item");
      if (!btn || !galleryRoot.contains(btn)) return;
      var full = btn.getAttribute("data-full");
      var img = btn.querySelector("img");
      openLightbox(full, img ? img.getAttribute("alt") : "");
    });

    galleryRoot.addEventListener("keydown", function (e) {
      if (e.key !== "ArrowLeft" && e.key !== "ArrowRight") return;
      var w = galleryRoot.clientWidth;
      if (!w) return;
      e.preventDefault();
      galleryRoot.scrollBy({
        left: e.key === "ArrowLeft" ? -w * 0.35 : w * 0.35,
        behavior: reducedMotion ? "auto" : "smooth"
      });
    });
  }

  var lbClose = document.querySelector(".lightbox__close");
  var lightbox = document.getElementById("lightbox");
  if (lbClose) lbClose.addEventListener("click", closeLightbox);
  if (lightbox) {
    lightbox.addEventListener("click", function (e) {
      if (e.target === lightbox) closeLightbox();
    });
  }
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeLightbox();
  });
})();
