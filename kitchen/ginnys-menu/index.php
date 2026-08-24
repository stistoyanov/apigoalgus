<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';

$brunch = [
    ['title' => 'Омлет', 'desc' => 'Предлагаме няколко вида омлети, с различни съставки и гарнитура от свежи зеленчуци.', 'img' => null],
    ['title' => 'Палачинки с шоколад и плодове', 'desc' => 'Насладете се на топли палачинки, допълнени със свежи плодове и гладък шоколад.', 'img' => null],
    ['title' => 'Чабата с гуакамоле и яйце', 'desc' => 'Мека чабата с хрупкава коричка, домашно гуакамоле и сготвени до съвършенство яйца.', 'img' => null],
    ['title' => 'Домашни мекици', 'desc' => 'Пухкави мекици с краве сирене и домашно сладко от зелени смокини – от екипа на Ginny’s : )', 'img' => null],
];

$mains = [
    ['title' => 'Гръцка салата', 'desc' => 'Класическа гръцка салата със свежи зеленчуци и сирене.', 'img' => '../images/2025/04/freshtstart.webp'],
    ['title' => 'Салата Капрезе Бурата', 'desc' => 'Кремообразна бурата с домати и босилек.', 'img' => '../images/2025/04/burata.webp'],
    ['title' => 'Домашни разядки', 'desc' => 'Домашни разядки за споделяне – идеално начало на вечерята.', 'img' => '../images/2025/04/tarama.webp'],
    ['title' => 'Пресен черноморски сафрид', 'desc' => 'Прясно уловен черноморски сафрид.', 'img' => '../images/2025/04/safrid.webp'],
    ['title' => 'Пържен скорпид', 'desc' => 'Хрупкав пържен скорпид.', 'img' => '../images/2025/04/skorpid.webp'],
    ['title' => 'Студено – пушен чернокоп', 'desc' => 'Студено пушен чернокоп.', 'img' => '../images/2025/04/chernokop.webp'],
    ['title' => 'Бургери', 'desc' => 'Избор от вегетариански, пилешки, телешки, свински – бриош питка, свежи салати, домашни сосове и пресни картофи.', 'img' => '../images/2025/04/burgerr.webp'],
    ['title' => 'Прясна паста', 'desc' => 'Паста с морски дарове – миди или скариди, болонезе, карбонара и други.', 'img' => '../images/2025/04/shrimp-pasta.webp'],
    ['title' => 'Супи', 'desc' => 'Избор от супи, сред които – пилешка супа, таратор и класическата рибена чорба.', 'img' => '../images/2025/04/soup.webp'],
    ['title' => 'Хавиар / специалитети', 'desc' => 'Избрани специалитети от кухнята.', 'img' => '../images/2025/04/chaviar.webp'],
];

kitchen_header([
    'title' => 'Меню | Ginny\'s Kitchen Sozopol',
    'description' => 'Брънч и основно меню на Ginny\'s Kitchen – Созопол, Централен плаж.',
    'active' => 'menu',
    'base' => '../',
    'locale' => 'bg',
    'home_url' => '../',
    'menu_url' => './',
    'lang_url' => '../en/',
]);
?>
<main id="main">
    <header class="page-hero">
        <div class="section-inner">
            <h1>Нашето меню</h1>
            <p>Вкусна домашна храна и прясна риба на Централен плаж, Созопол.</p>
        </div>
    </header>

    <div class="menu-sections">
        <section class="menu-section">
            <h2>Брънч</h2>
            <p class="note">От 9:30 до 11:00 часа. Разгледай нашето специално брънч меню и се наслади на вкусове, вдъхновени от морето.</p>
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

        <section class="menu-section">
            <h2>Основно меню</h2>
            <p class="note">Угоди на стомаха си и се наслади на лятото. Екипът на Ginny’s Kitchen те очаква!</p>
            <div class="menu-items has-images">
                <?php foreach ($mains as $item): ?>
                    <article class="menu-item has-photo">
                        <?php if ($item['img']): ?>
                            <img src="<?= kitchen_esc($item['img']) ?>" alt="" loading="lazy" width="280" height="280">
                        <?php endif; ?>
                        <div>
                            <h3><?= kitchen_esc($item['title']) ?></h3>
                            <p><?= kitchen_esc($item['desc']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="menu-section" style="text-align:center">
            <h2>Очакваме те!</h2>
            <a class="btn" href="tel:+359886060687">Резервация</a>
        </section>
    </div>
</main>
<?php
kitchen_footer(['base' => '../', 'locale' => 'bg']);
