<?php

declare(strict_types=1);

require __DIR__.'/lib/layout.php';

kitchen_header([
    'active' => 'home',
    'base' => '',
    'locale' => 'bg',
    'home_url' => './',
    'menu_url' => 'ginnys-menu/',
    'lang_url' => 'en/',
]);
?>
<main id="main">
    <section class="hero" aria-label="Въведение">
        <div>
            <img class="hero-logo" src="images/hero-logo.png" alt="Ginny's Kitchen" width="280" height="120">
            <h1>„Ginny’s Kitchen“ – Ресторант в Созопол</h1>
            <p class="tagline">Любимият ресторант до морето в Созопол</p>
            <p style="max-width:42rem;margin:0 auto 1.75rem;color:#f2ebe3">Разположен в сърцето на Созопол, нашият ресторант е сред най-добрите в града. Предлагаме уникално съчетание от свежи морски дарове, традиционни български ястия и международни специалитети.</p>
            <a class="btn" href="tel:+359886060687">Резервация</a>
        </div>
    </section>

    <section class="section brunch">
        <div class="section-inner">
            <h2>Вкусен брънч – в ресторант на брега на морето!</h2>
            <p>Открий любимите вкусове сред нашите най-популярни рецепти, вдъхновени от свежестта на морето. Разгледай менюто и се потопи в кулинарното ни крайбрежно изживяване.</p>
            <p><strong>— От 9:30 до 11:00</strong></p>
            <a class="btn" href="ginnys-menu/" style="margin-top:1rem">Гладен ли си?</a>
        </div>
    </section>

    <section class="section story">
        <div class="section-inner">
            <p class="eyebrow">Our Story</p>
            <h2>Историята на нашия ресторант в Созопол</h2>
            <div class="story-block">
                <h3>Как се роди нашият ресторант в Созопол?</h3>
                <p>Нашата история започва не просто с ресторант в Созопол, а с едно малко крайбрежно място, където морето среща хората – нашият Джини бар. Създаден с идеята да споделяме радостта от живота на плажа, Ginny Bar стана дом за всички, които обичат безгрижните залези, приятелските разговори и студените коктейли под звуците на морските вълни.</p>
                <p>С времето обаче разбрахме, че освен хубавите напитки, хората жадуват за нещо повече – за домашно приготвена храна, която носи топлината на лятото и вкуса на морето. Така се роди <strong>Ginny’s Kitchen</strong> – естествено продължение на духа на нашия бар.</p>
            </div>
            <div class="story-block">
                <h3>Топло посрещане у дома</h3>
                <p>В Ginny’s Kitchen вярваме, че храната е нещо повече от добре приготвени ястия – тя е начин да свързваме хората. Нашата мисия е да създаваме място, където всеки гост се чувства като приятел.</p>
            </div>
            <div class="story-block">
                <h3>Нашата визия</h3>
                <p>Искаме Ginny’s Kitchen да бъде повече от ресторант в Созопол – искаме да е дом далеч от дома, където спомените се създават и усмивките се споделят.</p>
            </div>
        </div>
    </section>

    <section class="section menu-teasers">
        <div class="section-inner">
            <p class="eyebrow">Our Menu</p>
            <h2>Вкусна домашна храна и прясна риба</h2>
            <a class="btn" href="ginnys-menu/">Виж менюто</a>
            <div class="teaser-grid">
                <article class="teaser">
                    <img src="images/2025/04/freshtstart.webp" alt="Свежо начало" loading="lazy" width="480" height="360">
                    <h3>Свежо начало и леки предястия</h3>
                </article>
                <article class="teaser">
                    <img src="images/2025/04/morskahrana.webp" alt="Морски изкушения" loading="lazy" width="480" height="360">
                    <h3>Морски изкушения и паста</h3>
                </article>
                <article class="teaser">
                    <img src="images/2025/04/burgeri-sozopol.webp" alt="Бургери" loading="lazy" width="480" height="360">
                    <h3>Сочни бургери и картофки</h3>
                </article>
                <article class="teaser">
                    <img src="images/2025/04/desert.webp" alt="Десерти" loading="lazy" width="480" height="360">
                    <h3>Сладки финали на вечерта</h3>
                </article>
            </div>
        </div>
    </section>

    <section class="section promises">
        <div class="section-inner">
            <h2>Нашите обещания към вас, скъпи приятели!</h2>
            <div class="promise-grid">
                <article class="promise">
                    <img src="images/icon-bowl.png" alt="" width="56" height="56">
                    <h3>Свежи продукти</h3>
                    <p>Зареждаме само пресни и свежи продукти, за да гарантираме автентичен вкус!</p>
                </article>
                <article class="promise">
                    <img src="images/icon-restaurant.png" alt="" width="56" height="56">
                    <h3>Ресторант на брега на морето</h3>
                    <p>Насладете се на красивата гледка от нашия ресторант, на брега на Черно море.</p>
                </article>
                <article class="promise">
                    <img src="images/icon-lotus.png" alt="" width="56" height="56">
                    <h3>Уникален вкус</h3>
                    <p>Винаги вкусна храна и продукти, сготвени с внимание към детайла.</p>
                </article>
                <article class="promise">
                    <img src="images/icon-tray.png" alt="" width="56" height="56">
                    <h3>Най-добрите рецепти</h3>
                    <p>Традиционни български ястия и международни специалитети за всеки вкус.</p>
                </article>
                <article class="promise">
                    <img src="images/icon-protection.png" alt="" width="56" height="56">
                    <h3>Подходящо за семейства</h3>
                    <p>Идеално място за споделени моменти с близките. Специално меню за деца.</p>
                </article>
                <article class="promise">
                    <img src="images/icon-thumbs.png" alt="" width="56" height="56">
                    <h3>Любезен персонал</h3>
                    <p>Нашият екип ще ви накара да се почувствате като у дома.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Резервирай маса в нашия ресторант край морето – за всеки повод!<br>Или без повод!</h2>
        <a class="btn" href="tel:+359886060687">Запази маса!</a>
    </section>
</main>
<?php
kitchen_footer(['base' => '', 'locale' => 'bg']);
