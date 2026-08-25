<?php

declare(strict_types=1);

require __DIR__.'/lib/layout.php';

ginny_header(['active' => 'home', 'base' => '']);
?>
<main id="main">
    <section class="hero" aria-label="Въведение">
        <div>
            <h1><span>GINNY</span><span>ROCK BAR</span></h1>
            <p class="tagline">Рок бар в сърцето на Созопол</p>
            <a class="reserve" href="tel:+359886060687">
                <span class="label">За резервации</span>
                <span class="phone">+359 88 606 0687</span>
            </a>
        </div>
    </section>

    <section class="section story">
        <div class="section-inner">
            <h2>Историята на нашият бар в Созопол</h2>
            <p>Повече от 20 години Джини бар е ключово място в летния живот на Созопол.</p>
            <p>Локацията му на Централния плаж го прави колкото приятен през деня, с коктейл в ръка, толкова и вечер, на лунна светлина.</p>
            <p>Музиката е рок, а често и на живо. Бирата винаги е студена, а бургерите толкова вкусни, че трудно ще се опишат с думи. Работи денонощно и винаги има място за още един. \m/</p>
        </div>
    </section>

    <section class="section rock-home">
        <div class="section-inner">
            <p class="eyebrow">бар на плажа</p>
            <h2>Домът на рока и любимото място на July Morning</h2>
            <p>Джини бар е домът на рока и любимата крайна дестинация на мотористите. От презареждащи изпълнения до огъня на плажа до класическите рок хитове на бара, всяка нота е част от нашия ритъм. Насладете се на вълнението от живота на пътя, докато се потапяте в енергията на нашето място до морето.</p>
            <a class="btn" href="programa/">Предстоящи събития</a>
            <div class="feature-cards" style="margin-top: 2.5rem">
                <article class="card">
                    <h3>Live Music Nights</h3>
                    <p>Насладете се на вечери с жива рок музика, която създава идеалната атмосфера за незабравима нощ край плажа.</p>
                </article>
                <article class="card">
                    <h3>Nights to Remember</h3>
                    <p>Създайте спомени, които ще останат с вас дълго след залез… или изгрев.</p>
                </article>
                <article class="card">
                    <h3>Vibe by the beach</h3>
                    <p>Усетете пясъка и се насладете на перфектното питие на метри от морето.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section drinks">
        <div class="section-inner two-col">
            <div>
                <p class="eyebrow">напитки и храна</p>
                <h2>Вкусът на лятото в най-добрия бар в Созопол – Джини бар!</h2>
                <p>В Джини бар напитките са също толкова важни, колкото и атмосферата. От свежи, ръчно приготвени коктейли до студена крафт бира, приготвена специално за нас, всяка напитка е създадена с внимание към детайла и перфектно допълва лятната атмосфера. Независимо дали предпочитате нещо класическо или искате да опитате нещо ново, барът предлага разнообразие, което да удовлетвори всеки вкус. Насладете се на освежаващата напитка в ръка, докато морето и залезът се разгръщат пред вас.</p>
            </div>
            <div class="drinks-media">
                <img src="images/drinks.png" alt="Бар в Созопол – коктейли и атмосфера" width="640" height="850">
            </div>
        </div>
    </section>

    <section class="section visit" id="visit">
        <div class="section-inner">
            <h2>Посети ни!</h2>
            <div class="map-wrap">
                <iframe
                    title="Локация на Ginny Rock Bar в Google Карти"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.757173457178!2d27.691852212046342!3d42.422017871066956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40a6b875f66df0a7%3A0x75a128d5cf9df64f!2sGINNY%20BAR!5e1!3m2!1sen!2sbg!4v1725567648558!5m2!1sen!2sbg"
                    allowfullscreen></iframe>
            </div>
            <div class="visit-grid">
                <div>
                    <h3>Локация</h3>
                    <p>Созопол, Старият град – Централен плаж</p>
                </div>
                <div>
                    <h3>За резервации</h3>
                    <p><a href="tel:+359886060687">(359) 88 606 0687</a><br>ginny.bar@abv.bg</p>
                </div>
                <div>
                    <h3>Очакваме те 24/7</h3>
                    <p>Най-добрият бар в Созопол</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cheers">
        <h2>Cheers!</h2>
        <a class="btn" href="tel:+359886060687">Направи резервация</a>
    </section>
</main>
<?php
ginny_footer(['base' => '']);
