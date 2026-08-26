<?php

declare(strict_types=1);

/**
 * Shared home page body for BG and EN.
 *
 * @param array{locale:string,base:string,home:string,menu:string,lang_switch:string,lang_other:string} $paths
 */
function kitchen_render_home(string $locale, array $paths): void
{
    $locale = $locale === 'en' ? 'en' : 'bg';
    $base = $paths['base'];
    $t = static fn (string $key): string => kitchen_t($key, $locale);
    ?>
<main id="main">
    <section class="hero" aria-label="<?= kitchen_esc($t('nav_home')) ?>">
        <div class="hero__bg-wrap" data-parallax="0.35">
            <div class="hero__bg" role="img" aria-hidden="true"></div>
        </div>
        <div class="hero__overlay" aria-hidden="true"></div>
        <div class="hero__inner">
            <h1 class="hero__tag">GINNY'S<span>KITCHEN</span></h1>
            <p class="hero__lead"><?= kitchen_esc($t('hero_tagline')) ?></p>
            <a class="reserve" href="tel:+359886060687">
                <span class="label"><?= kitchen_esc($t('hero_cta')) ?></span>
                <span class="phone">+359 88 606 0687</span>
            </a>
        </div>
    </section>

    <section class="section brunch">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= kitchen_esc($t('brunch_title')) ?></h2>
            <p><?= kitchen_esc($t('brunch_body')) ?></p>
            <p class="brunch-hours"><?= kitchen_esc($t('brunch_hours')) ?></p>
            <a class="btn" href="<?= kitchen_esc($paths['menu']) ?>"><?= kitchen_esc($t('brunch_cta')) ?></a>
        </div>
    </section>

    <section class="section story">
        <div class="story__bg-wrap" aria-hidden="true">
            <div class="story__bg" data-parallax="0.2"></div>
        </div>
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc($t('story_eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc($t('story_title')) ?></h2>
            <div class="story-block">
                <h3><?= kitchen_esc($t('story_h1')) ?></h3>
                <p><?= kitchen_esc($t('story_p1')) ?></p>
                <p><?= kitchen_esc($t('story_p2')) ?></p>
            </div>
            <div class="story-block">
                <h3><?= kitchen_esc($t('story_h2')) ?></h3>
                <p><?= kitchen_esc($t('story_p3')) ?></p>
            </div>
            <div class="story-block">
                <h3><?= kitchen_esc($t('story_h3')) ?></h3>
                <p><?= kitchen_esc($t('story_p4')) ?></p>
            </div>
        </div>
    </section>

    <section class="section menu-teasers">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc($t('menu_eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc($t('menu_title')) ?></h2>
            <a class="btn" href="<?= kitchen_esc($paths['menu']) ?>"><?= kitchen_esc($t('menu_cta')) ?></a>
            <div class="teaser-grid">
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc(kitchen_asset('images/2025/04/freshtstart.webp', $base)) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc($t('teaser1')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc(kitchen_asset('images/2025/04/morskahrana.webp', $base)) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc($t('teaser2')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc(kitchen_asset('images/2025/04/burgeri-sozopol.webp', $base)) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc($t('teaser3')) ?></h3>
                </a>
                <a class="teaser" href="<?= kitchen_esc($paths['menu']) ?>">
                    <img src="<?= kitchen_esc(kitchen_asset('images/2025/04/desert.webp', $base)) ?>" alt="" loading="lazy" width="480" height="360">
                    <h3><?= kitchen_esc($t('teaser4')) ?></h3>
                </a>
            </div>
        </div>
    </section>

    <section class="section promises">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= kitchen_esc($t('promises_title')) ?></h2>
            <div class="promise-grid">
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-bowl.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p1_title')) ?></h3>
                    <p><?= kitchen_esc($t('p1_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-restaurant.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p2_title')) ?></h3>
                    <p><?= kitchen_esc($t('p2_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-lotus.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p3_title')) ?></h3>
                    <p><?= kitchen_esc($t('p3_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-tray.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p4_title')) ?></h3>
                    <p><?= kitchen_esc($t('p4_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-protection.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p5_title')) ?></h3>
                    <p><?= kitchen_esc($t('p5_body')) ?></p>
                </article>
                <article class="promise">
                    <img src="<?= kitchen_esc(kitchen_asset('images/icon-thumbs.png', $base)) ?>" alt="" width="56" height="56">
                    <h3><?= kitchen_esc($t('p6_title')) ?></h3>
                    <p><?= kitchen_esc($t('p6_body')) ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section sister">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= kitchen_esc($t('sister_eyebrow')) ?></p>
            <h2 class="section__title"><?= kitchen_esc($t('sister_title')) ?></h2>
            <p><?= kitchen_esc($t('sister_body')) ?></p>
            <a class="btn" href="<?= kitchen_esc(kitchen_bar_url($locale)) ?>" target="_blank" rel="noopener noreferrer"><?= kitchen_esc($t('sister_cta')) ?></a>
        </div>
    </section>

    <section class="cheers">
        <div class="cheers__bg" data-parallax="0.25" aria-hidden="true"></div>
        <div class="cheers__inner reveal">
            <h2><?= kitchen_esc($t('cta_title')) ?></h2>
            <p class="cheers-lead"><?= kitchen_esc($t('cta_body')) ?></p>
            <a class="btn" href="tel:+359886060687"><?= kitchen_esc($t('cta_btn')) ?></a>
        </div>
    </section>
</main>
    <?php
}
