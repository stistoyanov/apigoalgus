(function () {
  "use strict";

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
})();
