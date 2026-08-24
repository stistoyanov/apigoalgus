<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';

$posts = [
    [
        'title' => 'Нощен живот в Созопол: Защо Ginny е любимият бар на хиляди',
        'date' => '29 юли 2025',
        'image' => '../images/2025/07/ginny1-1.jpg',
        'excerpt' => 'Защо Ginny Rock Bar остава любимото място за нощен живот в Созопол – рок, плаж и атмосфера, която не се забравя.',
    ],
    [
        'title' => 'Програма за август 2025 в Ginny Rock Bar',
        'date' => '29 юли 2025',
        'image' => '../images/2025/07/photo_2025-07-29_10-19-34.jpg',
        'excerpt' => 'Жива музика, партита и специални вечери през август 2025 на Централен плаж.',
    ],
    [
        'title' => 'Посрещни July Morning 2025 в Ginny Rock Bar – Созопол',
        'date' => '29 май 2025',
        'image' => '../images/2025/05/july-morning-at-ginny.jpg',
        'excerpt' => 'Домът на July Morning в Созопол – изгрев, рок и приятели на метри от морето.',
    ],
    [
        'title' => 'Летният сезон 2025 започва на 6 юни!',
        'date' => '29 май 2025',
        'image' => '../images/2025/05/ginny-rock-bar-otkrivane.jpg',
        'excerpt' => 'Откриване на летния сезон 2025 – очакваме ви с музика, коктейли и плажна енергия.',
    ],
    [
        'title' => '8 причини да изберете Созопол за лятна почивка',
        'date' => '25 април 2025',
        'image' => '../images/2025/04/sozopol_star_grad.jpg',
        'excerpt' => 'Защо Созопол и Ginny Rock Bar са задължителна спирка за лятна почивка.',
    ],
    [
        'title' => '07/09/24 – Closing party with DJ Groovy George',
        'date' => '7 септември 2024',
        'image' => '../images/2024/09/bar-36.jpg',
        'excerpt' => 'Closing party с DJ Groovy George – финал на сезона с рок и танц.',
    ],
];

ginny_header([
    'title' => 'Новини | Ginny Rock Bar Sozopol',
    'description' => 'Новини, програма и събития в Ginny Rock Bar – Созопол.',
    'active' => 'programa',
    'base' => '../',
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1>Новини</h1>
            <p>Програма, събития и истории от Ginny Rock Bar.</p>
        </div>
    </header>
    <div class="news-list">
        <?php foreach ($posts as $post): ?>
            <article class="news-card">
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
ginny_footer(['base' => '../']);
