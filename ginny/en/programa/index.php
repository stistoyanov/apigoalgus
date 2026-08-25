<?php

declare(strict_types=1);

require __DIR__.'/../../lib/layout.php';

$locale = 'en';
$page = 'programa';
$paths = ginny_paths($locale, $page);
$posts = ginny_news_posts($locale, $paths['base']);

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'programa',
    'paths' => $paths,
    'title' => ginny_t('meta_news_title', $locale),
    'description' => ginny_t('meta_news_desc', $locale),
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= ginny_esc(ginny_t('news_h1', $locale)) ?></h1>
            <p><?= ginny_esc(ginny_t('news_lead', $locale)) ?></p>
        </div>
    </header>
    <div class="news-list">
        <?php foreach ($posts as $post): ?>
            <article class="news-card reveal">
                <img src="<?= ginny_esc($post['image']) ?>" alt="" width="440" height="330" loading="lazy">
                <div>
                    <p class="date"><?= ginny_esc($post['date']) ?></p>
                    <h2><?= ginny_esc($post['title']) ?></h2>
                    <p><?= ginny_esc($post['excerpt']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php
ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
]);
