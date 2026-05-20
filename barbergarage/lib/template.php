<?php

declare(strict_types=1);

/**
 * Shared HTML template used by index.php (BG) and en/index.php (EN).
 *
 * Caller must define: $payload (array), $locale (string), $paths (array<string, string>).
 * Use bg_render_page(...) to invoke.
 */

/** @var array<string, mixed> $payload */
/** @var string $locale */
/** @var array<string, string> $paths */

$config = bg_config();
$mapsKey = (string) ($config['maps_api_key'] ?? '');

$assets = $paths['assets'];
$homeUrl = $paths['home_url'];
$enUrl = $paths['en_url'];
$langLinkUrl = $paths['lang_link_url'];

$metaTitle = bg_content($payload, 'meta', 'title', 'Barbergarage');
$metaDescription = bg_content($payload, 'meta', 'description', '');
$canonical = $locale === 'en'
    ? 'https://barbergarage.bg/en/'
    : 'https://barbergarage.bg/';

$navLabels = $payload['content']['nav'] ?? [];
$contactLabels = $payload['content']['contact'] ?? [];

$addressKey = $locale === 'en' ? 'address_en' : 'address_bg';
$address = (string) bg_setting($payload, $addressKey, '');
$phone = (string) bg_setting($payload, 'phone', '');
$phoneE164 = (string) bg_setting($payload, 'phone_e164', '');
$email = (string) bg_setting($payload, 'email', '');
$facebook = (string) bg_setting($payload, 'facebook_url', '');
$instagram = (string) bg_setting($payload, 'instagram_url', '');

$mapLat = (string) bg_setting($payload, 'map_lat', '');
$mapLng = (string) bg_setting($payload, 'map_lng', '');
$mapZoom = (string) bg_setting($payload, 'map_zoom', '18');

$aboutPhoto = bg_media_first($payload, 'about_photo');
$video = bg_media_featured($payload, 'video');
$gallery = is_array($payload['media']['gallery'] ?? null) ? $payload['media']['gallery'] : [];

$skipLink = $locale === 'en' ? 'Skip to content' : 'Към съдържанието';
$menuLabel = $locale === 'en' ? 'Menu' : 'Меню';
$mainNavLabel = $locale === 'en' ? 'Main navigation' : 'Основна навигация';
$langSwitchLabel = $locale === 'en' ? 'Language' : 'Език';
$heroAriaLabel = $locale === 'en' ? 'Introduction' : 'Въведение';
$galleryAriaLabel = $locale === 'en' ? 'Photo gallery' : 'Галерия със снимки';
$photoPrefix = $locale === 'en' ? 'Photo' : 'Снимка';
$mapIframeTitle = $locale === 'en' ? 'Barbergarage location on Google Maps' : 'Локация на Barbergarage в Google Карти';
$closeLabel = $locale === 'en' ? 'Close' : 'Затвори';
$footerYear = date('Y');

?><!DOCTYPE html>
<html lang="<?= bg_e($locale) ?>">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= bg_e($metaTitle) ?></title>
    <meta name="description" content="<?= bg_e($metaDescription) ?>" />
    <link rel="canonical" href="<?= bg_e($canonical) ?>" />
    <link rel="icon" type="image/png" href="<?= bg_e($assets) ?>images/website/IconTransperantSmallest.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= bg_e($assets) ?>css/site.css" />
  </head>
  <body>
    <a class="skip-link" href="#welcome"><?= bg_e($skipLink) ?></a>

    <header class="site-header">
      <a class="brand" href="<?= bg_e($homeUrl) ?>">
        <img
          class="brand__logo"
          src="<?= bg_e($assets) ?>images/website/IconTransperantSmallest.png"
          width="44"
          height="44"
          alt=""
        />
        <span class="brand__text">BARBERGARAGE</span>
      </a>
      <button
        class="nav-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="site-nav"
        aria-label="<?= bg_e($menuLabel) ?>"
      >
        <span class="nav-toggle__bar"></span>
        <span class="nav-toggle__bar"></span>
        <span class="nav-toggle__bar"></span>
      </button>
      <nav class="nav-main" id="site-nav" aria-label="<?= bg_e($mainNavLabel) ?>">
        <ul class="nav-main__links">
          <li><a href="#welcome"><?= bg_e($navLabels['welcome'] ?? '') ?></a></li>
          <li><a href="#about"><?= bg_e($navLabels['about'] ?? '') ?></a></li>
          <li><a href="#gallery"><?= bg_e($navLabels['gallery'] ?? '') ?></a></li>
          <li><a href="#videos"><?= bg_e($navLabels['videos'] ?? '') ?></a></li>
          <li><a href="#hours"><?= bg_e($navLabels['hours'] ?? '') ?></a></li>
          <li><a href="#map"><?= bg_e($navLabels['map'] ?? '') ?></a></li>
        </ul>
        <div class="lang-switch" aria-label="<?= bg_e($langSwitchLabel) ?>">
          <a href="<?= bg_e($homeUrl) ?>"<?= $locale === 'bg' ? ' aria-current="page"' : '' ?>>BG</a>
          <span>/</span>
          <a href="<?= bg_e($enUrl) ?>"<?= $locale === 'en' ? ' aria-current="page"' : '' ?>>EN</a>
        </div>
      </nav>
    </header>

    <main>
      <section class="hero" aria-label="<?= bg_e($heroAriaLabel) ?>">
        <div class="hero__bg-wrap" aria-hidden="true">
          <div class="hero__bg"></div>
        </div>
        <div class="hero__overlay" aria-hidden="true"></div>
        <div class="hero__accent-line" aria-hidden="true"></div>
        <div class="hero__inner">
          <h1 class="hero__tag">BARBER<span>GARAGE</span></h1>
          <p class="hero__lead"><?= bg_e(bg_content($payload, 'hero', 'lead')) ?></p>
          <a class="btn" href="#welcome"><?= bg_e(bg_content($payload, 'hero', 'cta_label')) ?></a>
        </div>
      </section>

      <section class="section" id="welcome">
        <div class="section__inner welcome__grid">
          <div>
            <h2 class="section__title"><?= bg_e(bg_content($payload, 'welcome', 'title')) ?></h2>
            <p class="section__subtitle"><?= bg_e(bg_content($payload, 'welcome', 'body_p1')) ?></p>
          </div>
          <div>
            <p class="section__subtitle" style="margin-bottom: 1rem">
              <?= bg_e(bg_content($payload, 'welcome', 'body_p2')) ?>
            </p>
            <a class="btn btn--ghost" href="#map"><?= bg_e(bg_content($payload, 'welcome', 'cta_label')) ?></a>
          </div>
        </div>
      </section>

      <section class="section section--alt" id="about">
        <div class="section__inner">
          <h2 class="section__title"><?= bg_e(bg_content($payload, 'about', 'title')) ?></h2>
          <p class="section__subtitle"><?= bg_e(bg_content($payload, 'about', 'subtitle')) ?></p>
          <div class="about">
            <?php if ($aboutPhoto !== null && ! empty($aboutPhoto['url'])): ?>
              <div class="about__photo">
                <img
                  src="<?= bg_e($aboutPhoto['url']) ?>"
                  alt="<?= bg_e($aboutPhoto['alt'] ?? bg_content($payload, 'about', 'name')) ?>"
                  width="640"
                  height="800"
                  loading="lazy"
                />
              </div>
            <?php endif; ?>
            <div class="about__bio">
              <h3 class="about__name"><?= bg_e(bg_content($payload, 'about', 'name')) ?></h3>
              <p class="about__role"><?= bg_e(bg_content($payload, 'about', 'role')) ?></p>
              <p><?= bg_e(bg_content($payload, 'about', 'bio_p1')) ?></p>
              <p><?= bg_e(bg_content($payload, 'about', 'bio_p2')) ?></p>
            </div>
          </div>
          <dl class="contact-strip">
            <div>
              <dt><?= bg_e($contactLabels['address_label'] ?? '') ?></dt>
              <dd><?= bg_e($address) ?></dd>
            </div>
            <div>
              <dt><?= bg_e($contactLabels['phone_label'] ?? '') ?></dt>
              <dd><a href="tel:<?= bg_e($phoneE164) ?>"><?= bg_e($phone) ?></a></dd>
            </div>
            <div>
              <dt><?= bg_e($contactLabels['email_label'] ?? '') ?></dt>
              <dd><a href="mailto:<?= bg_e($email) ?>"><?= bg_e($email) ?></a></dd>
            </div>
            <div>
              <dt><?= bg_e($contactLabels['social_label'] ?? '') ?></dt>
              <dd>
                <?php if ($facebook !== ''): ?>
                  <a href="<?= bg_e($facebook) ?>" rel="noopener noreferrer">Facebook</a>
                <?php endif; ?>
                <?php if ($facebook !== '' && $instagram !== ''): ?> · <?php endif; ?>
                <?php if ($instagram !== ''): ?>
                  <a href="<?= bg_e($instagram) ?>" rel="noopener noreferrer">Instagram</a>
                <?php endif; ?>
              </dd>
            </div>
          </dl>
        </div>
      </section>

      <section class="section" id="gallery">
        <div class="section__inner">
          <h2 class="section__title"><?= bg_e(bg_content($payload, 'gallery', 'title')) ?></h2>
          <p class="section__subtitle"><?= bg_e(bg_content($payload, 'gallery', 'subtitle')) ?></p>
          <?php if (count($gallery) > 10): ?>
            <p class="gallery__scroll-hint" id="gallery-scroll-hint">
              <?= bg_e(bg_content($payload, 'gallery', 'scroll_hint')) ?>
            </p>
          <?php endif; ?>
          <div
            class="gallery__scroller"
            id="gallery-grid"
            role="region"
            aria-label="<?= bg_e($galleryAriaLabel) ?>"
            <?php if (count($gallery) > 10): ?>aria-describedby="gallery-scroll-hint"<?php endif; ?>
            tabindex="0"
          >
            <?php foreach ($gallery as $i => $item): ?>
              <?php
                $url = (string) ($item['url'] ?? '');
                if ($url === '') { continue; }
                $alt = (string) ($item['alt'] ?? '');
                $eager = $i < 10;
              ?>
              <button
                type="button"
                class="gallery__item"
                aria-label="<?= bg_e($photoPrefix.' '.($i + 1)) ?>"
                data-full="<?= bg_e($url) ?>"
              >
                <img
                  src="<?= bg_e($url) ?>"
                  alt="<?= bg_e($alt) ?>"
                  loading="<?= $eager ? 'eager' : 'lazy' ?>"
                  decoding="async"
                />
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section class="section" id="videos">
        <div class="section__inner">
          <h2 class="section__title"><?= bg_e(bg_content($payload, 'video', 'title')) ?></h2>
          <p class="section__subtitle"><?= bg_e(bg_content($payload, 'video', 'subtitle')) ?></p>
          <?php if ($video !== null && ! empty($video['url'])): ?>
            <?php $poster = bg_media_first($payload, 'gallery'); ?>
            <div class="video-ambient">
              <video
                controls
                preload="metadata"
                playsinline
                <?php if ($poster !== null && ! empty($poster['url'])): ?>poster="<?= bg_e($poster['url']) ?>"<?php endif; ?>
              >
                <source src="<?= bg_e($video['url']) ?>" type="<?= bg_e($video['mime_type'] ?? 'video/mp4') ?>" />
              </video>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section class="section section--alt hours" id="hours">
        <div class="section__inner">
          <h2 class="section__title"><?= bg_e(bg_content($payload, 'hours', 'title')) ?></h2>
          <p class="section__subtitle"><?= bg_e(bg_content($payload, 'hours', 'subtitle')) ?></p>
          <div class="hours__table-wrap">
            <table>
              <thead>
                <tr>
                  <th scope="col"><?= bg_e($locale === 'en' ? 'Day' : 'Ден') ?></th>
                  <th scope="col"><?= bg_e($locale === 'en' ? 'Hours' : 'Часове') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (bg_hours_rows($payload, $locale) as $row): ?>
                  <tr>
                    <td><?= bg_e($row['day']) ?></td>
                    <td><?= bg_e($row['value']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="hours__note"><?= bg_e(bg_content($payload, 'hours', 'note')) ?></p>
        </div>
      </section>

      <section class="section" id="map">
        <div class="section__inner">
          <h2 class="section__title"><?= bg_e(bg_content($payload, 'map', 'title')) ?></h2>
          <p class="section__subtitle"><?= bg_e(bg_content($payload, 'map', 'subtitle')) ?></p>
          <?php if ($mapsKey !== '' && $mapLat !== '' && $mapLng !== ''): ?>
            <?php
              $mapSrc = 'https://www.google.com/maps/embed/v1/place?key='.rawurlencode($mapsKey)
                .'&q='.rawurlencode($mapLat.','.$mapLng)
                .'&zoom='.rawurlencode($mapZoom)
                .'&language='.rawurlencode($locale);
            ?>
            <div class="map__frame-wrap">
              <iframe
                title="<?= bg_e($mapIframeTitle) ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="<?= bg_e($mapSrc) ?>"
              ></iframe>
            </div>
            <p class="map__hint">
              <a
                href="https://www.google.com/maps/search/?api=1&amp;query=<?= bg_e($mapLat.','.$mapLng) ?>"
                rel="noopener noreferrer"
              ><?= bg_e(bg_content($payload, 'map', 'link_label')) ?></a>
            </p>
          <?php endif; ?>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <p>© <?= bg_e($footerYear) ?> <?= bg_e(bg_content($payload, 'footer', 'rights')) ?></p>
      <p><a href="<?= bg_e($langLinkUrl) ?>"><?= bg_e(bg_content($payload, 'footer', 'lang_link')) ?></a></p>
    </footer>

    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-hidden="true">
      <button type="button" class="lightbox__close" aria-label="<?= bg_e($closeLabel) ?>">&times;</button>
      <img class="lightbox__img" id="lightbox-img" alt="" />
    </div>

    <script src="<?= bg_e($assets) ?>js/site.js" defer></script>
  </body>
</html>
