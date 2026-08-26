<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';
require __DIR__.'/../lib/api-client.php';

$locale = 'bg';
$page = 'gallery';
$paths = ginny_paths($locale, $page);
$payload = ginny_load_content($locale);
$photos = $payload['media']['gallery'] ?? [];

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'gallery',
    'paths' => $paths,
    'title' => ginny_c($payload, 'meta', 'gallery_title', ginny_t('meta_gallery_title', $locale)),
    'description' => ginny_c($payload, 'meta', 'gallery_desc', ginny_t('meta_gallery_desc', $locale)),
    'payload' => $payload,
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= ginny_esc(ginny_c($payload, 'gallery', 'h1')) ?></h1>
            <p><?= ginny_esc(ginny_c($payload, 'gallery', 'lead')) ?></p>
        </div>
    </header>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <?php
            $src = (string) ($photo['url'] ?? '');
            $alt = (string) ($photo['alt'] ?? '');
            if ($src === '') {
                continue;
            }
            if (strpos($src, 'http://') !== 0 && strpos($src, 'https://') !== 0) {
                $src = $paths['base'].ltrim($src, '/');
            }
            ?>
            <button type="button" data-lightbox data-full="<?= ginny_esc($src) ?>">
                <img src="<?= ginny_esc($src) ?>" alt="<?= ginny_esc($alt) ?>" loading="lazy" width="600" height="600">
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
    aria-label="<?= ginny_esc(ginny_c($payload, 'lightbox', 'label')) ?>"
>
    <button type="button" class="lightbox__close" data-lightbox-close aria-label="<?= ginny_esc(ginny_c($payload, 'lightbox', 'close')) ?>">&times;</button>
    <button type="button" class="lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="<?= ginny_esc(ginny_c($payload, 'lightbox', 'prev')) ?>">&#8249;</button>
    <div id="lightbox-stage" class="lightbox__stage"></div>
    <button type="button" class="lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="<?= ginny_esc(ginny_c($payload, 'lightbox', 'next')) ?>">&#8250;</button>
</div>
<?php
ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
    'payload' => $payload,
]);
