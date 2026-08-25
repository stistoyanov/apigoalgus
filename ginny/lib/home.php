<?php

declare(strict_types=1);

/**
 * Shared home page body for BG and EN.
 *
 * @param array{locale:string,base:string,home:string,news:string,gallery:string,lang_switch:string,lang_other:string} $paths
 */
function ginny_render_home(string $locale, array $paths): void
{
    $locale = $locale === 'en' ? 'en' : 'bg';
    $base = $paths['base'];
    $t = static fn (string $key): string => ginny_t($key, $locale);
    ?>
<main id="main">
    <section class="hero" aria-label="<?= ginny_esc($t('nav_home')) ?>">
        <div class="hero__bg-wrap" data-parallax="0.35">
            <div class="hero__bg" role="img" aria-hidden="true"></div>
        </div>
        <div class="hero__overlay" aria-hidden="true"></div>
        <div class="hero__inner">
            <h1 class="hero__tag">GINNY<span>ROCK BAR</span></h1>
            <p class="hero__lead"><?= ginny_esc($t('hero_tagline')) ?></p>
            <a class="reserve" href="tel:+359886060687">
                <span class="label"><?= ginny_esc($t('hero_cta_label')) ?></span>
                <span class="phone">+359 88 606 0687</span>
            </a>
        </div>
    </section>

    <section class="section story">
        <div class="story__bg-wrap" aria-hidden="true">
            <div class="story__bg" data-parallax="0.2"></div>
        </div>
        <div class="section-inner reveal">
            <h2 class="section__title"><?= ginny_esc($t('story_title')) ?></h2>
            <p><?= ginny_esc($t('story_p1')) ?></p>
            <p><?= ginny_esc($t('story_p2')) ?></p>
            <p><?= ginny_esc($t('story_p3')) ?></p>
        </div>
    </section>

    <section class="section rock-home">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= ginny_esc($t('rock_eyebrow')) ?></p>
            <h2 class="section__title"><?= ginny_esc($t('rock_title')) ?></h2>
            <p><?= ginny_esc($t('rock_body')) ?></p>
            <a class="btn" href="<?= ginny_esc($paths['news']) ?>"><?= ginny_esc($t('rock_cta')) ?></a>
            <div class="feature-cards">
                <article class="card">
                    <h3><?= ginny_esc($t('card1_title')) ?></h3>
                    <p><?= ginny_esc($t('card1_body')) ?></p>
                </article>
                <article class="card">
                    <h3><?= ginny_esc($t('card2_title')) ?></h3>
                    <p><?= ginny_esc($t('card2_body')) ?></p>
                </article>
                <article class="card">
                    <h3><?= ginny_esc($t('card3_title')) ?></h3>
                    <p><?= ginny_esc($t('card3_body')) ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section drinks">
        <div class="section-inner two-col reveal">
            <div>
                <p class="eyebrow"><?= ginny_esc($t('drinks_eyebrow')) ?></p>
                <h2 class="section__title"><?= ginny_esc($t('drinks_title')) ?></h2>
                <p><?= ginny_esc($t('drinks_body')) ?></p>
            </div>
            <div class="drinks-media">
                <img
                    src="<?= ginny_esc(ginny_asset('images/drinks.png', $base)) ?>"
                    alt="<?= ginny_esc($t('drinks_title')) ?>"
                    width="640"
                    height="850"
                    loading="lazy"
                >
            </div>
        </div>
    </section>

    <section class="section visit" id="visit">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= ginny_esc($t('visit_title')) ?></h2>
            <div class="map-wrap">
                <iframe
                    title="<?= ginny_esc($t('map_title')) ?>"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.757173457178!2d27.691852212046342!3d42.422017871066956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40a6b875f66df0a7%3A0x75a128d5cf9df64f!2sGINNY%20BAR!5e1!3m2!1sen!2sbg!4v1725567648558!5m2!1sen!2sbg"
                    allowfullscreen></iframe>
            </div>
            <div class="visit-grid">
                <div>
                    <h3><?= ginny_esc($t('visit_loc')) ?></h3>
                    <p><?= ginny_esc($t('visit_loc_body')) ?></p>
                </div>
                <div>
                    <h3><?= ginny_esc($t('visit_book')) ?></h3>
                    <p><a href="tel:+359886060687">+359 88 606 0687</a><br><a href="mailto:ginny.bar@abv.bg">ginny.bar@abv.bg</a></p>
                </div>
                <div>
                    <h3><?= ginny_esc($t('visit_hours')) ?></h3>
                    <p><?= ginny_esc($t('visit_hours_body')) ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="cheers">
        <div class="cheers__bg" data-parallax="0.25" aria-hidden="true"></div>
        <div class="cheers__inner reveal">
            <h2><?= ginny_esc($t('cheers_title')) ?></h2>
            <a class="btn" href="tel:+359886060687"><?= ginny_esc($t('cheers_cta')) ?></a>
        </div>
    </section>
</main>
    <?php
}
