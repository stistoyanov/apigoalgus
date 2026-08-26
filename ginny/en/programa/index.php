<?php

declare(strict_types=1);

require __DIR__.'/../../lib/layout.php';
require __DIR__.'/../../lib/api-client.php';

$locale = 'en';
$page = 'programa';
$paths = ginny_paths($locale, $page);
$payload = ginny_load_content($locale);
$posts = $payload['posts'] ?? [];

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'programa',
    'paths' => $paths,
    'title' => ginny_c($payload, 'meta', 'news_title', ginny_t('meta_news_title', $locale)),
    'description' => ginny_c($payload, 'meta', 'news_desc', ginny_t('meta_news_desc', $locale)),
    'payload' => $payload,
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= ginny_esc(ginny_c($payload, 'news', 'h1')) ?></h1>
            <p><?= ginny_esc(ginny_c($payload, 'news', 'lead')) ?></p>
        </div>
    </header>
    <div class="news-list">
        <?php foreach ($posts as $post): ?>
            <?php
            $title = (string) ($post['title'] ?? '');
            $excerpt = (string) ($post['excerpt'] ?? '');
            $image = (string) ($post['image_url'] ?? '');
            $dateLabel = (string) ($post['date_label'] ?? '');
            if ($dateLabel === '' && ! empty($post['posted_at'])) {
                $ts = strtotime((string) $post['posted_at']);
                $dateLabel = $ts ? date('j M Y', $ts) : '';
            }
            $permalink = (string) ($post['permalink'] ?? '');
            ?>
            <article class="news-card reveal">
                <?php if ($image !== ''): ?>
                    <img src="<?= ginny_esc($image) ?>" alt="" width="440" height="330" loading="lazy">
                <?php endif; ?>
                <div>
                    <?php if ($dateLabel !== ''): ?>
                        <p class="date"><?= ginny_esc($dateLabel) ?></p>
                    <?php endif; ?>
                    <h2>
                        <?php if ($permalink !== ''): ?>
                            <a href="<?= ginny_esc($permalink) ?>" target="_blank" rel="noopener noreferrer"><?= ginny_esc($title) ?></a>
                        <?php else: ?>
                            <?= ginny_esc($title) ?>
                        <?php endif; ?>
                    </h2>
                    <?php if ($excerpt !== ''): ?>
                        <p><?= ginny_esc($excerpt) ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php
ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
    'payload' => $payload,
]);
