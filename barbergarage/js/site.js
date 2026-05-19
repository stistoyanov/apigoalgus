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

  var GALLERY_FIRST_EAGER = 10;

  /* gallery.json uses paths like images/...; on /en/ relative URLs would wrongly become /en/images/... */
  function siteRootPath(p) {
    if (!p) return p;
    if (/^(https?:|data:|\/\/)/i.test(p)) return p;
    if (p.charAt(0) === "/") return p;
    var base = document.body.getAttribute("data-site-base");
    if (base != null && base !== "" && base !== "/") {
      return base.replace(/\/?$/, "/") + p.replace(/^\//, "");
    }
    return "/" + p;
  }

  /* Gallery from gallery.json — horizontal scroller, ~10 visible on large viewports */
  function buildGallery(images) {
    if (!galleryRoot || !images || !images.length) return;

    var lang = (document.documentElement.getAttribute("lang") || "bg").toLowerCase();
    var labelPrefix = lang.indexOf("en") === 0 ? "Photo " : "Снимка ";

    var hint = document.getElementById("gallery-scroll-hint");
    if (hint) {
      if (images.length > GALLERY_FIRST_EAGER) {
        hint.removeAttribute("hidden");
        galleryRoot.setAttribute("aria-describedby", "gallery-scroll-hint");
      } else {
        hint.setAttribute("hidden", "");
        galleryRoot.removeAttribute("aria-describedby");
      }
    }

    images.forEach(function (src, i) {
      var abs = siteRootPath(src);
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "gallery__item";
      btn.setAttribute("aria-label", labelPrefix + (i + 1));
      var img = document.createElement("img");
      img.src = abs;
      img.alt = "";
      img.loading = i < GALLERY_FIRST_EAGER ? "eager" : "lazy";
      img.decoding = "async";
      btn.appendChild(img);
      btn.addEventListener("click", function () {
        openLightbox(abs);
      });
      galleryRoot.appendChild(btn);
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

  function openLightbox(src) {
    var lb = document.getElementById("lightbox");
    var lbImg = document.getElementById("lightbox-img");
    if (!lb || !lbImg) return;
    lbImg.src = siteRootPath(src);
    lbImg.alt = "";
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

  var galleryUrl = document.body.getAttribute("data-gallery") || "gallery.json";
  fetch(galleryUrl)
    .then(function (r) {
      if (!r.ok) throw new Error("gallery");
      return r.json();
    })
    .then(function (data) {
      buildGallery(data.images || []);
    })
    .catch(function () {
      if (galleryRoot) {
        galleryRoot.innerHTML =
          '<p class="section__subtitle">' +
          (document.documentElement.lang && document.documentElement.lang.indexOf("en") === 0
            ? "Gallery could not load. Check the path to gallery.json."
            : "Галерията не може да се зареди. Проверете пътя към gallery.json.") +
          "</p>";
      }
    });
})();
