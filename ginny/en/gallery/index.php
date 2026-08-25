<?php

declare(strict_types=1);

require __DIR__.'/../../lib/layout.php';

$locale = 'en';
$page = 'gallery';
$paths = ginny_paths($locale, $page);
$photos = ginny_gallery_photos($locale, $paths['base']);

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'gallery',
    'paths' => $paths,
    'title' => ginny_t('meta_gallery_title', $locale),
    'description' => ginny_t('meta_gallery_desc', $locale),
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= ginny_esc(ginny_t('gallery_h1', $locale)) ?></h1>
            <p><?= ginny_esc(ginny_t('gallery_lead', $locale)) ?></p>
        </div>
    </header>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <button type="button" data-lightbox data-full="<?= ginny_esc($photo['src']) ?>">
                <img src="<?= ginny_esc($photo['src']) ?>" alt="<?= ginny_esc($photo['alt']) ?>" loading="lazy" width="600" height="600">
            </button>
        <?php endforeach; ?>
    </div>
</main>
<div
    id="lightbox"
    class="lightbox"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-label="<?= ginny_esc(ginny_t('lightbox_label', $locale)) ?>"
>
    <button type="button" class="lightbox__close" data-lightbox-close aria-label="<?= ginny_esc(ginny_t('lightbox_close', $locale)) ?>">&times;</button>
    <button type="button" class="lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="<?= ginny_esc(ginny_t('lightbox_prev', $locale)) ?>">&#8249;</button>
    <div id="lightbox-stage" class="lightbox__stage"></div>
    <button type="button" class="lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="<?= ginny_esc(ginny_t('lightbox_next', $locale)) ?>">&#8250;</button>
</div>
<?php
ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
]);
