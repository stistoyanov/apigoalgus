(() => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('#site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  const lightbox = document.querySelector('#lightbox');
  const lightboxImg = document.querySelector('#lightbox-img');
  const closeBtn = document.querySelector('.lightbox-close');

  document.querySelectorAll('[data-lightbox]').forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      if (!lightbox || !lightboxImg) return;
      lightboxImg.src = link.getAttribute('href') || '';
      lightboxImg.alt = link.querySelector('img')?.alt || '';
      lightbox.classList.add('is-open');
    });
  });

  const close = () => lightbox?.classList.remove('is-open');
  closeBtn?.addEventListener('click', close);
  lightbox?.addEventListener('click', (event) => {
    if (event.target === lightbox) close();
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') close();
  });
})();
