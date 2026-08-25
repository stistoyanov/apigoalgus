<?php

declare(strict_types=1);

require __DIR__.'/../../lib/layout.php';

$locale = 'en';
$page = 'ginnys-menu';
$paths = kitchen_paths($locale, $page);
$brunch = kitchen_brunch_items($locale);
$mains = kitchen_main_items($locale, $paths['base']);

kitchen_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'menu',
    'paths' => $paths,
    'title' => kitchen_t('meta_menu_title', $locale),
    'description' => kitchen_t('meta_menu_desc', $locale),
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= kitchen_esc(kitchen_t('menu_h1', $locale)) ?></h1>
            <p><?= kitchen_esc(kitchen_t('menu_lead', $locale)) ?></p>
        </div>
    </header>

    <div class="menu-sections">
        <section class="menu-section reveal">
            <h2><?= kitchen_esc(kitchen_t('menu_brunch', $locale)) ?></h2>
            <p class="note"><?= kitchen_esc(kitchen_t('menu_brunch_note', $locale)) ?></p>
            <div class="menu-items">
                <?php foreach ($brunch as $item): ?>
                    <article class="menu-item">
                        <div>
                            <h3><?= kitchen_esc($item['title']) ?></h3>
                            <p><?= kitchen_esc($item['desc']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="menu-section reveal">
            <h2><?= kitchen_esc(kitchen_t('menu_mains', $locale)) ?></h2>
            <p class="note"><?= kitchen_esc(kitchen_t('menu_mains_note', $locale)) ?></p>
            <div class="menu-items has-images">
                <?php foreach ($mains as $item): ?>
                    <article class="menu-item has-photo">
                        <img src="<?= kitchen_esc($item['img']) ?>" alt="" loading="lazy" width="280" height="280">
                        <div>
                            <h3><?= kitchen_esc($item['title']) ?></h3>
                            <p><?= kitchen_esc($item['desc']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="menu-section menu-cta reveal">
            <h2><?= kitchen_esc(kitchen_t('menu_see_you', $locale)) ?></h2>
            <a class="btn" href="tel:+359886060687"><?= kitchen_esc(kitchen_t('nav_reserve', $locale)) ?></a>
        </section>
    </div>
</main>
<?php
kitchen_footer([
    'locale' => $locale,
    'paths' => $paths,
]);
