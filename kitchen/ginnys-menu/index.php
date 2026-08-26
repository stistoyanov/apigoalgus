<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';

$locale = 'bg';
$page = 'ginnys-menu';
$paths = kitchen_paths($locale, $page);
$payload = kitchen_load_content($locale);
$brunch = is_array($payload['menu']['brunch'] ?? null) ? $payload['menu']['brunch'] : [];
$mains = is_array($payload['menu']['mains'] ?? null) ? $payload['menu']['mains'] : [];
$phoneE164 = kitchen_s($payload, 'phone_e164', '+359886060687');

kitchen_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'menu',
    'paths' => $paths,
    'title' => kitchen_c($payload, 'meta', 'menu_title', kitchen_t('meta_menu_title', $locale)),
    'description' => kitchen_c($payload, 'meta', 'menu_desc', kitchen_t('meta_menu_desc', $locale)),
    'payload' => $payload,
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1><?= kitchen_esc(kitchen_c($payload, 'menu', 'h1', kitchen_t('menu_h1', $locale))) ?></h1>
            <p><?= kitchen_esc(kitchen_c($payload, 'menu', 'lead', kitchen_t('menu_lead', $locale))) ?></p>
        </div>
    </header>

    <div class="menu-sections">
        <section class="menu-section reveal">
            <h2><?= kitchen_esc(kitchen_c($payload, 'menu', 'brunch', kitchen_t('menu_brunch', $locale))) ?></h2>
            <p class="note"><?= kitchen_esc(kitchen_c($payload, 'menu', 'brunch_note', kitchen_t('menu_brunch_note', $locale))) ?></p>
            <div class="menu-items">
                <?php foreach ($brunch as $item): ?>
                    <?php
                    $title = is_string($item['title'] ?? null) ? $item['title'] : '';
                    $desc = is_string($item['desc'] ?? null) ? $item['desc'] : '';
                    ?>
                    <article class="menu-item">
                        <div>
                            <h3><?= kitchen_esc($title) ?></h3>
                            <p><?= kitchen_esc($desc) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="menu-section reveal">
            <h2><?= kitchen_esc(kitchen_c($payload, 'menu', 'mains', kitchen_t('menu_mains', $locale))) ?></h2>
            <p class="note"><?= kitchen_esc(kitchen_c($payload, 'menu', 'mains_note', kitchen_t('menu_mains_note', $locale))) ?></p>
            <div class="menu-items has-images">
                <?php foreach ($mains as $item): ?>
                    <?php
                    $title = is_string($item['title'] ?? null) ? $item['title'] : '';
                    $desc = is_string($item['desc'] ?? null) ? $item['desc'] : '';
                    $imageUrl = is_string($item['image_url'] ?? null) ? $item['image_url'] : '';
                    if ($imageUrl !== '' && strpos($imageUrl, 'http://') !== 0 && strpos($imageUrl, 'https://') !== 0) {
                        $imageUrl = kitchen_asset($imageUrl, $paths['base']);
                    }
                    ?>
                    <article class="menu-item has-photo">
                        <?php if ($imageUrl !== ''): ?>
                            <img src="<?= kitchen_esc($imageUrl) ?>" alt="" loading="lazy" width="280" height="280">
                        <?php endif; ?>
                        <div>
                            <h3><?= kitchen_esc($title) ?></h3>
                            <p><?= kitchen_esc($desc) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="menu-section menu-cta reveal">
            <h2><?= kitchen_esc(kitchen_c($payload, 'menu', 'see_you', kitchen_t('menu_see_you', $locale))) ?></h2>
            <a class="btn" href="tel:<?= kitchen_esc($phoneE164) ?>"><?= kitchen_esc(kitchen_c($payload, 'nav', 'reserve', kitchen_t('nav_reserve', $locale))) ?></a>
        </section>
    </div>
</main>
<?php
kitchen_footer([
    'locale' => $locale,
    'paths' => $paths,
    'payload' => $payload,
]);
