(function () {
  "use strict";

  var header = document.querySelector(".site-header");
  var navToggle = document.querySelector(".nav-toggle");
  var navMain = document.querySelector(".nav-main");
  var heroBgWrap = document.querySelector(".hero__bg-wrap");
  var galleryRoot = document.getElementById("gallery-grid");
  var videoRoot = document.getElementById("video-grid");
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

  /* Lightbox: holds a unified list (images first, then videos) and
     navigates by index. Image items render <img>, video items <video controls>. */
  var items = [];
  var currentIndex = -1;
  var lightbox = document.getElementById("lightbox");
  var lightboxStage = document.getElementById("lightbox-stage");
  var lightboxClose = lightbox ? lightbox.querySelector(".lightbox__close") : null;
  var lightboxPrev = lightbox ? lightbox.querySelector("[data-lightbox-prev]") : null;
  var lightboxNext = lightbox ? lightbox.querySelector("[data-lightbox-next]") : null;

  function collectItems() {
    if (galleryRoot) {
      galleryRoot.querySelectorAll(".gallery__item").forEach(function (btn) {
        var url = btn.getAttribute("data-full");
        if (!url) return;
        var img = btn.querySelector("img");
        items.push({
          kind: "image",
          url: url,
          alt: img ? img.getAttribute("alt") || "" : ""
        });
      });
    }
    if (videoRoot) {
      videoRoot.querySelectorAll(".gallery__item").forEach(function (btn) {
        var url = btn.getAttribute("data-video");
        if (!url) return;
        items.push({
          kind: "video",
          url: url,
          mime: btn.getAttribute("data-mime") || "video/mp4"
        });
      });
    }
  }

  function buttonIndex(btn) {
    if (!btn) return -1;
    var url = btn.getAttribute("data-full") || btn.getAttribute("data-video");
    if (!url) return -1;
    for (var i = 0; i < items.length; i++) {
      if (items[i].url === url) return i;
    }
    return -1;
  }

  function clearStage() {
    if (!lightboxStage) return;
    var existingVideo = lightboxStage.querySelector("video");
    if (existingVideo) {
      try { existingVideo.pause(); } catch (e) { /* noop */ }
      existingVideo.removeAttribute("src");
      existingVideo.load();
    }
    lightboxStage.innerHTML = "";
  }

  function renderItem(index) {
    if (!lightboxStage || index < 0 || index >= items.length) return;
    var item = items[index];
    clearStage();
    if (item.kind === "image") {
      var img = document.createElement("img");
      img.className = "lightbox__img";
      img.src = item.url;
      img.alt = item.alt || "";
      lightboxStage.appendChild(img);
    } else {
      var video = document.createElement("video");
      video.className = "lightbox__video";
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.preload = "auto";
      var source = document.createElement("source");
      source.src = item.url;
      source.type = item.mime || "video/mp4";
      video.appendChild(source);
      lightboxStage.appendChild(video);
    }
    updateNavVisibility();
  }

  function updateNavVisibility() {
    var multi = items.length > 1;
    if (lightboxPrev) lightboxPrev.hidden = !multi;
    if (lightboxNext) lightboxNext.hidden = !multi;
  }

  function openLightbox(index) {
    if (!lightbox || index < 0 || index >= items.length) return;
    currentIndex = index;
    renderItem(currentIndex);
    lightbox.classList.add("is-open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.classList.add("lightbox-open");
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove("is-open");
    lightbox.setAttribute("aria-hidden", "true");
    document.body.classList.remove("lightbox-open");
    clearStage();
    currentIndex = -1;
  }

  function step(delta) {
    if (currentIndex < 0 || !items.length) return;
    var next = (currentIndex + delta + items.length) % items.length;
    currentIndex = next;
    renderItem(currentIndex);
  }

  collectItems();
  updateNavVisibility();

  if (galleryRoot) {
    galleryRoot.addEventListener("click", function (e) {
      var btn = e.target.closest(".gallery__item");
      if (!btn || !galleryRoot.contains(btn)) return;
      var idx = buttonIndex(btn);
      if (idx >= 0) openLightbox(idx);
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

  if (videoRoot) {
    videoRoot.addEventListener("click", function (e) {
      var btn = e.target.closest(".gallery__item");
      if (!btn || !videoRoot.contains(btn)) return;
      var idx = buttonIndex(btn);
      if (idx >= 0) openLightbox(idx);
    });

    videoRoot.addEventListener("keydown", function (e) {
      if (e.key !== "ArrowLeft" && e.key !== "ArrowRight") return;
      var w = videoRoot.clientWidth;
      if (!w) return;
      e.preventDefault();
      videoRoot.scrollBy({
        left: e.key === "ArrowLeft" ? -w * 0.35 : w * 0.35,
        behavior: reducedMotion ? "auto" : "smooth"
      });
    });
  }

  if (lightboxClose) lightboxClose.addEventListener("click", closeLightbox);
  if (lightboxPrev) lightboxPrev.addEventListener("click", function () { step(-1); });
  if (lightboxNext) lightboxNext.addEventListener("click", function () { step(1); });

  if (lightbox) {
    lightbox.addEventListener("click", function (e) {
      if (e.target === lightbox) closeLightbox();
    });

    /* Swipe support */
    var touchStartX = null;
    lightbox.addEventListener("touchstart", function (e) {
      if (e.touches.length === 1) touchStartX = e.touches[0].clientX;
    }, { passive: true });
    lightbox.addEventListener("touchend", function (e) {
      if (touchStartX === null) return;
      var dx = (e.changedTouches[0] && e.changedTouches[0].clientX) - touchStartX;
      touchStartX = null;
      if (Math.abs(dx) > 40) step(dx > 0 ? -1 : 1);
    });
  }

  document.addEventListener("keydown", function (e) {
    if (!lightbox || !lightbox.classList.contains("is-open")) return;
    if (e.key === "Escape") {
      closeLightbox();
    } else if (e.key === "ArrowLeft") {
      e.preventDefault();
      step(-1);
    } else if (e.key === "ArrowRight") {
      e.preventDefault();
      step(1);
    }
  });
})();
