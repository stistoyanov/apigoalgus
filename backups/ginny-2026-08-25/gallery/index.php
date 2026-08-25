<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';

$photos = [
    ['src' => '../images/2025/05/ginny-bar-merch.jpg', 'alt' => 'Ginny Bar merch'],
    ['src' => '../images/2025/05/cocktails-at-ginny-bar.jpg', 'alt' => 'Коктейли в Ginny Bar'],
    ['src' => '../images/2025/05/ginny-team.jpg', 'alt' => 'Екипът на Ginny'],
    ['src' => '../images/2025/05/2.jpg', 'alt' => 'Момент от бара'],
    ['src' => '../images/2025/05/live-rock-music-in-rock-bar-on-the-beach.jpg', 'alt' => 'Жива рок музика на плажа'],
    ['src' => '../images/2025/05/ginny-rock-bar-rockers-and-motors-bicycles-bikes-bikers.jpg', 'alt' => 'Рокери и мотористи в Ginny'],
    ['src' => '../images/2025/05/july-morning-at-ginny.jpg', 'alt' => 'July Morning в Ginny'],
    ['src' => '../images/2025/05/ginny-live-concert.jpg', 'alt' => 'Концерт на живо'],
    ['src' => '../images/2025/05/5.jpg', 'alt' => 'Атмосфера в бара'],
    ['src' => '../images/2025/05/fireworks-on-the-beach-sozopol-central.jpg', 'alt' => 'Фойерверки на Централен плаж'],
    ['src' => '../images/2025/05/ginny-shots.jpg', 'alt' => 'Шотове в Ginny'],
    ['src' => '../images/2025/05/8.jpg', 'alt' => 'Вечер в Ginny Rock Bar'],
    ['src' => '../images/2025/05/9.jpg', 'alt' => 'Момент от Ginny'],
    ['src' => '../images/2025/05/4.jpg', 'alt' => 'Гости на бара'],
    ['src' => '../images/2024/09/ginny-bar-2.jpg', 'alt' => 'Ginny Bar екстериор'],
    ['src' => '../images/2024/09/ginny-bar-rock-sozopol-milena-1.jpg', 'alt' => 'Живо изпълнение'],
];

ginny_header([
    'title' => 'Моменти | Ginny Rock Bar Sozopol',
    'description' => 'Галерия с моменти от Ginny Rock Bar – музика, плаж и рок атмосфера.',
    'active' => 'gallery',
    'base' => '../',
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1>Моменти</h1>
            <p>Снимки от живота на Ginny Rock Bar.</p>
        </div>
    </header>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <a href="<?= ginny_esc($photo['src']) ?>" data-lightbox>
                <img src="<?= ginny_esc($photo['src']) ?>" alt="<?= ginny_esc($photo['alt']) ?>" loading="lazy" width="600" height="600">
            </a>
        <?php endforeach; ?>
    </div>
</main>
<div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Преглед на снимка">
    <button type="button" class="lightbox-close" aria-label="Затвори">&times;</button>
    <img id="lightbox-img" src="" alt="">
</div>
<?php
ginny_footer(['base' => '../']);
