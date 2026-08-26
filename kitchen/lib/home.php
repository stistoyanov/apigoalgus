<?php

declare(strict_types=1);

/**
 * Shared home page body for BG and EN (API payload driven).
 *
 * @param array{locale:string,base:string,home:string,menu:string,lang_switch:string,lang_other:string} $paths
 * @param array<string, mixed> $payload
 */
function kitchen_render_home(string $locale, array $paths, array $payload): void
{
    $locale = $locale === 'en' ? 'en' : 'bg';
    $base = $paths['base'];
    $phone = kitchen_s($payload, 'phone', '+359 88 606 0687');
    $phoneE164 = kitchen_s($payload, 'phone_e164', '+359886060687');
    $sisterUrl = $locale === 'en'
        ? kitchen_s($payload, 'sister_url_en', kitchen_bar_url('en'))
        : kitchen_s($payload, 'sister_url', kitchen_bar_url('bg'));

    $teaserFallbacks = [
        1 => 'images/2025/04/freshtstart.webp',
        2 => 'images/2025/04/morskahrana.webp',
        3 => 'images/2025/04/burgeri-sozopol.webp',
        4 => 'images/2025/04/desert.webp',
    ];
    $teasers = [];
    foreach ($teaserFallbacks as $n => $fallback) {
        $fromContent = kitchen_c($payload, 'menu', 'teaser'.$n.'_image');
        $src = $fromContent !== '' ? $fromContent : $fallback;
        if (strpos($src, 'http://') === 0 || strpos($src, 'https://') === 0) {
            $teasers[$n] = $src;
        } else {
            $teasers[$n] = kitchen_asset($src, $base);
        }
    }

    $heroBg = kitchen_media_url($payload, 'hero_bg', 'images/hero-bg.jpg', $base);
    $storyBg = kitchen_media_url($payload, 'story_bg', 'images/story-bg.webp', $base);
    $aboutPhoto = kitchen_media_url($payload, 'about_photo', 'images/about-photo.jpg', $base);
    ?>
<main id="main">
    <section class="hero" aria-label="<?= kitchen_esc(kitchen_c($payload, 'nav', 'home', kitchen_t('nav_home', $locale))) ?>">
        <div class="hero__bg-wrap" data-parallax="0.35">
            <div class="hero__bg" role="img" aria-hidden="true" style="background-image:url('<?= kitchen_esc($heroBg) ?>')"></div>
        </div>
        <div class="hero__overlay" aria-hidden="true"></div>
        <div class="hero__inner">
            <h1 class="hero__tag">GINNY'S<span>KITCHEN</span></h1>
            <p class="hero__lead"><?= kitchen_esc(kitchen_c($payload, 'hero', 'tagline')) ?></p>
            <a class="reserve" href="tel:<?= kitchen_esc($phoneE164) ?>">
                <span class="label"><?= kitchen_esc(kitchen_c($payload, 'hero', 'cta')) ?></span>
                <span class="phone"><?= kitchen_esc($phone) ?></span>
            </a>
        </div>
    </section>

    <section class="section brunch">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= kitchen_esc(kitchen_c($payload, 'brunch', 'title')) ?></h2>
            <p><?= kitchen_esc(kitchen_c($payload, 'brunch', 'body')) ?></p>
            <p class="brunch-hours"><?= kitchen_esc(kitchen_c($payload, 'brunch', 'hours')) ?></p>
            <a class="btn" href="<?= kitchen_esc($paths['menu']) ?>"><?= kitchen_esc(kitchen_c($payload, 'brunch', 'cta')) ?></a>
        </div>
    </section>

    <section class="section story">
        <div class="story__bg-wrap" aria-hidden="true">
            <div class="story__bg" data-parallax="0.2" style="background-image:linear-gradient(100deg, rgba(243, 247, 248, 0.96) 0%, rgba(243, 247, 248, 0.88) 40%, rgba(243, 247, 248, 0.45) 100%), url('<?= kitchen_esc($storyBg) ?>')"></div>
        </div>
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc(kitchen_c($payload, 'story', 'eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc(kitchen_c($payload, 'story', 'title')) ?></h2>
            <div class="story-block">
                <h3><?= kitchen_esc(kitchen_c($payload, 'story', 'h1')) ?></h3>
                <p><?= kitchen_esc(kitchen_c($payload, 'story', 'p1')) ?></p>
                <p><?= kitchen_esc(kitchen_c($payload, 'story', 'p2')) ?></p>
            </div>
            <div class="story-block">
                <h3><?= kitchen_esc(kitchen_c($payload, 'story', 'h2')) ?></h3>
                <p><?= kitchen_esc(kitchen_c($payload, 'story', 'p3')) ?></p>
            </div>
            <div class="story-block">
                <h3><?= kitchen_esc(kitchen_c($payload, 'story', 'h3')) ?></h3>
                <p><?= kitchen_esc(kitchen_c($payload, 'story', 'p4')) ?></p>
            </div>
        </div>
    </section>

    <section class="section menu-teasers">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc(kitchen_c($payload, 'menu', 'eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc(kitchen_c($payload, 'menu', 'title')) ?></h2>
            <a class="btn" href="<?= kitchen_esc($paths['menu']) ?>"><?= kitchen_esc(kitchen_c($payload, 'menu', 'cta')) ?></a>
            <div class="teaser-grid">
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc($teasers[1]) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'menu', 'teaser1')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc($teasers[2]) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'menu', 'teaser2')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc($teasers[3]) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'menu', 'teaser3')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc($teasers[4]) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'menu', 'teaser4')) ?></h3>
                </a>
            </div>
        </div>
    </section>

    <section class="section promises">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= kitchen_esc(kitchen_c($payload, 'promises', 'title')) ?></h2>
            <div class="promise-grid">
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-bowl.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p1_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p1_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-restaurant.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p2_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p2_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-lotus.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p3_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p3_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-tray.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p4_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p4_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-protection.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p5_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p5_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-thumbs.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc(kitchen_c($payload, 'promises', 'p6_title')) ?></h3>
                    <p><?= kitchen_esc(kitchen_c($payload, 'promises', 'p6_body')) ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section sister">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc(kitchen_c($payload, 'sister', 'eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc(kitchen_c($payload, 'sister', 'title')) ?></h2>
            <p><?= kitchen_esc(kitchen_c($payload, 'sister', 'body')) ?></p>
            <a class="btn" href="<?= kitchen_esc($sisterUrl) ?>" target="_blank" rel="noopener noreferrer"><?= kitchen_esc(kitchen_c($payload, 'sister', 'cta')) ?></a>
        </div>
    </section>

    <section class="cheers">
        <div class="cheers__bg" data-parallax="0.25" aria-hidden="true" style="background-image:linear-gradient(180deg, rgba(21, 78, 74, 0.55), rgba(21, 78, 74, 0.72)), url('<?= kitchen_esc($aboutPhoto) ?>')"></div>
        <div class="cheers__inner reveal">
            <h2><?= kitchen_esc(kitchen_c($payload, 'cta', 'title')) ?></h2>
            <p class="cheers-lead"><?= kitchen_esc(kitchen_c($payload, 'cta', 'body')) ?></p>
            <a class="btn" href="tel:<?= kitchen_esc($phoneE164) ?>"><?= kitchen_esc(kitchen_c($payload, 'cta', 'btn')) ?></a>
        </div>
    </section>
</main>
    <?php
}
