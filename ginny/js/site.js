(function () {
  "use strict";

  var header = document.querySelector(".site-header");
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.querySelector("#site-nav");
  var reducedMotion =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function closeMenu() {
    if (!nav || !toggle) return;
    nav.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    document.body.classList.remove("nav-open");
  }

  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = !nav.classList.contains("is-open");
      nav.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.classList.toggle("nav-open", open);
    });

    nav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        if (window.matchMedia("(max-width: 760px)").matches) closeMenu();
      });
    });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeMenu();
  });

  /* Parallax */
  var parallaxNodes = document.querySelectorAll("[data-parallax]");
  var ticking = false;

  function parallaxTick() {
    ticking = false;
    if (reducedMotion || !parallaxNodes.length) return;
    var vh = window.innerHeight || 1;
    parallaxNodes.forEach(function (el) {
      var speed = parseFloat(el.getAttribute("data-parallax") || "0.25");
      var rect = el.parentElement
        ? el.parentElement.getBoundingClientRect()
        : el.getBoundingClientRect();
      var progress = (vh / 2 - (rect.top + rect.height / 2)) / vh;
      var y = progress * speed * 80;
      el.style.transform = "translate3d(0, " + y + "px, 0)";
    });
  }

  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(parallaxTick);
      ticking = true;
    }
  }

  if (parallaxNodes.length && !reducedMotion) {
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    parallaxTick();
  }

  /* Reveal on scroll */
  var reveals = document.querySelectorAll(".reveal");
  if (reveals.length && "IntersectionObserver" in window) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -8% 0px" }
    );
    reveals.forEach(function (el) {
      io.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add("is-visible");
    });
  }

  /* Lightbox with prev/next */
  var items = [];
  var currentIndex = -1;
  var lightbox = document.getElementById("lightbox");
  var stage = document.getElementById("lightbox-stage");
  var btnClose = document.querySelector("[data-lightbox-close]");
  var btnPrev = document.querySelector("[data-lightbox-prev]");
  var btnNext = document.querySelector("[data-lightbox-next]");

  document.querySelectorAll("[data-lightbox]").forEach(function (btn) {
    var url = btn.getAttribute("data-full") || btn.getAttribute("href");
    if (!url) return;
    var img = btn.querySelector("img");
    items.push({
      url: url,
      alt: img ? img.getAttribute("alt") || "" : ""
    });
  });

  function clearStage() {
    if (stage) stage.innerHTML = "";
  }

  function render(index) {
    if (!stage || index < 0 || index >= items.length) return;
    clearStage();
    var item = items[index];
    var img = document.createElement("img");
    img.className = "lightbox__img";
    img.src = item.url;
    img.alt = item.alt;
    stage.appendChild(img);
    var multi = items.length > 1;
    if (btnPrev) btnPrev.hidden = !multi;
    if (btnNext) btnNext.hidden = !multi;
  }

  function openAt(index) {
    if (!lightbox || index < 0 || index >= items.length) return;
    currentIndex = index;
    render(currentIndex);
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
    currentIndex = (currentIndex + delta + items.length) % items.length;
    render(currentIndex);
  }

  document.querySelectorAll("[data-lightbox]").forEach(function (btn, i) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      openAt(i);
    });
  });

  btnClose && btnClose.addEventListener("click", closeLightbox);
  btnPrev && btnPrev.addEventListener("click", function () { step(-1); });
  btnNext && btnNext.addEventListener("click", function () { step(1); });

  if (lightbox) {
    lightbox.addEventListener("click", function (e) {
      if (e.target === lightbox) closeLightbox();
    });
    var touchX = null;
    lightbox.addEventListener(
      "touchstart",
      function (e) {
        if (e.touches.length === 1) touchX = e.touches[0].clientX;
      },
      { passive: true }
    );
    lightbox.addEventListener("touchend", function (e) {
      if (touchX === null) return;
      var dx = (e.changedTouches[0] && e.changedTouches[0].clientX) - touchX;
      touchX = null;
      if (Math.abs(dx) > 40) step(dx > 0 ? -1 : 1);
    });
  }

  document.addEventListener("keydown", function (e) {
    if (!lightbox || !lightbox.classList.contains("is-open")) return;
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowLeft") {
      e.preventDefault();
      step(-1);
    }
    if (e.key === "ArrowRight") {
      e.preventDefault();
      step(1);
    }
  });

  // quiet unused
  void header;
})();
