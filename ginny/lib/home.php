<?php

declare(strict_types=1);

/**
 * Shared home page body for BG and EN (API payload driven).
 *
 * @param array{locale:string,base:string,home:string,news:string,gallery:string,lang_switch:string,lang_other:string} $paths
 * @param array<string, mixed> $payload
 */
function ginny_render_home(string $locale, array $paths, array $payload): void
{
    $locale = $locale === 'en' ? 'en' : 'bg';
    $base = $paths['base'];
    $phone = ginny_s($payload, 'phone', '+359 88 606 0687');
    $phoneE164 = ginny_s($payload, 'phone_e164', '+359886060687');
    $email = ginny_s($payload, 'email', 'ginny.bar@abv.bg');
    $sisterUrl = $locale === 'en'
        ? ginny_s($payload, 'sister_url_en', ginny_kitchen_url('en'))
        : ginny_s($payload, 'sister_url', ginny_kitchen_url('bg'));
    $mapEmbed = ginny_s($payload, 'map_embed');
    $drinksUrl = ginny_media_url($payload, 'drinks', 'images/drinks.png', $base);
    ?>
<main id="main">
    <section class="hero" aria-label="<?= ginny_esc(ginny_c($payload, 'nav', 'home', 'Home')) ?>">
        <div class="hero__bg-wrap" data-parallax="0.35">
            <div class="hero__bg" role="img" aria-hidden="true" style="background-image:url('<?= ginny_esc(ginny_media_url($payload, 'hero_bg', 'images/hero.jpg', $base)) ?>')"></div>
        </div>
        <div class="hero__overlay" aria-hidden="true"></div>
        <div class="hero__inner">
            <h1 class="hero__tag">GINNY<span>ROCK BAR</span></h1>
            <p class="hero__lead"><?= ginny_esc(ginny_c($payload, 'hero', 'tagline')) ?></p>
            <a class="reserve" href="tel:<?= ginny_esc($phoneE164) ?>">
                <span class="label"><?= ginny_esc(ginny_c($payload, 'hero', 'cta_label')) ?></span>
                <span class="phone"><?= ginny_esc($phone) ?></span>
            </a>
        </div>
    </section>

    <section class="section story">
        <div class="story__bg-wrap" aria-hidden="true">
            <div class="story__bg" data-parallax="0.2" style="background-image:linear-gradient(100deg, rgba(10, 8, 6, 0.94) 0%, rgba(10, 8, 6, 0.82) 42%, rgba(10, 8, 6, 0.55) 100%), url('<?= ginny_esc(ginny_media_url($payload, 'story_bg', 'images/story-bg.png', $base)) ?>')"></div>
        </div>
        <div class="section-inner reveal">
            <h2 class="section__title"><?= ginny_esc(ginny_c($payload, 'story', 'title')) ?></h2>
            <p><?= ginny_esc(ginny_c($payload, 'story', 'p1')) ?></p>
            <p><?= ginny_esc(ginny_c($payload, 'story', 'p2')) ?></p>
            <p><?= ginny_esc(ginny_c($payload, 'story', 'p3')) ?></p>
        </div>
    </section>

    <section class="section rock-home">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= ginny_esc(ginny_c($payload, 'rock', 'eyebrow')) ?></p>
            <h2 class="section__title"><?= ginny_esc(ginny_c($payload, 'rock', 'title')) ?></h2>
            <p><?= ginny_esc(ginny_c($payload, 'rock', 'body')) ?></p>
            <a class="btn" href="<?= ginny_esc($paths['news']) ?>"><?= ginny_esc(ginny_c($payload, 'rock', 'cta')) ?></a>
            <div class="feature-cards">
                <article class="card">
                    <h3><?= ginny_esc(ginny_c($payload, 'card1', 'title')) ?></h3>
                    <p><?= ginny_esc(ginny_c($payload, 'card1', 'body')) ?></p>
                </article>
                <article class="card">
                    <h3><?= ginny_esc(ginny_c($payload, 'card2', 'title')) ?></h3>
                    <p><?= ginny_esc(ginny_c($payload, 'card2', 'body')) ?></p>
                </article>
                <article class="card">
                    <h3><?= ginny_esc(ginny_c($payload, 'card3', 'title')) ?></h3>
                    <p><?= ginny_esc(ginny_c($payload, 'card3', 'body')) ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section drinks">
        <div class="section-inner two-col reveal">
            <div>
                <p class="eyebrow"><?= ginny_esc(ginny_c($payload, 'drinks', 'eyebrow')) ?></p>
                <h2 class="section__title"><?= ginny_esc(ginny_c($payload, 'drinks', 'title')) ?></h2>
                <p><?= ginny_esc(ginny_c($payload, 'drinks', 'body')) ?></p>
            </div>
            <div class="drinks-media">
                <img src="<?= ginny_esc($drinksUrl) ?>" alt="<?= ginny_esc(ginny_c($payload, 'drinks', 'title')) ?>" width="640" height="850" loading="lazy">
            </div>
        </div>
    </section>

    <section class="section visit" id="visit">
        <div class="section-inner reveal">
            <h2 class="section__title"><?= ginny_esc(ginny_c($payload, 'visit', 'title')) ?></h2>
            <?php if ($mapEmbed !== ''): ?>
            <div class="map-wrap">
                <iframe title="<?= ginny_esc(ginny_c($payload, 'visit', 'map_title')) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?= ginny_esc($mapEmbed) ?>" allowfullscreen></iframe>
            </div>
            <?php endif; ?>
            <div class="visit-grid">
                <div>
                    <h3><?= ginny_esc(ginny_c($payload, 'visit', 'loc')) ?></h3>
                    <p><?= ginny_esc(ginny_c($payload, 'visit', 'loc_body')) ?></p>
                </div>
                <div>
                    <h3><?= ginny_esc(ginny_c($payload, 'visit', 'book')) ?></h3>
                    <p><a href="tel:<?= ginny_esc($phoneE164) ?>"><?= ginny_esc($phone) ?></a><br><a href="mailto:<?= ginny_esc($email) ?>"><?= ginny_esc($email) ?></a></p>
                </div>
                <div>
                    <h3><?= ginny_esc(ginny_c($payload, 'visit', 'hours')) ?></h3>
                    <p><?= ginny_esc(ginny_c($payload, 'visit', 'hours_body')) ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="section sister">
        <div class="section-inner reveal">
            <p class="eyebrow"><?= ginny_esc(ginny_c($payload, 'sister', 'eyebrow')) ?></p>
            <h2 class="section__title"><?= ginny_esc(ginny_c($payload, 'sister', 'title')) ?></h2>
            <p><?= ginny_esc(ginny_c($payload, 'sister', 'body')) ?></p>
            <a class="btn" href="<?= ginny_esc($sisterUrl) ?>" target="_blank" rel="noopener noreferrer"><?= ginny_esc(ginny_c($payload, 'sister', 'cta')) ?></a>
        </div>
    </section>

    <section class="cheers">
        <div class="cheers__bg" data-parallax="0.25" aria-hidden="true" style="background-image:linear-gradient(180deg, rgba(10, 8, 6, 0.72), rgba(10, 8, 6, 0.88)), url('<?= ginny_esc(ginny_media_url($payload, 'cheers_bg', 'images/cheers-bg.jpg', $base)) ?>')"></div>
        <div class="cheers__inner reveal">
            <h2><?= ginny_esc(ginny_c($payload, 'cheers', 'title')) ?></h2>
            <a class="btn" href="tel:<?= ginny_esc($phoneE164) ?>"><?= ginny_esc(ginny_c($payload, 'cheers', 'cta')) ?></a>
        </div>
    </section>
</main>
    <?php
}
