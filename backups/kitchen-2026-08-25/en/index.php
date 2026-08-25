<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';

kitchen_header([
    'title' => "Ginny's Kitchen – Sozopol Restaurant",
    'description' => 'Your favorite restaurant by the sea in Sozopol – seafood, Bulgarian classics and international dishes.',
    'active' => 'home',
    'base' => '../',
    'locale' => 'en',
    'home_url' => './',
    'menu_url' => '../ginnys-menu/',
    'lang_url' => '../',
]);
?>
<main id="main">
    <section class="hero" aria-label="Introduction">
        <div>
            <img class="hero-logo" src="../images/hero-logo.png" alt="Ginny's Kitchen" width="280" height="120">
            <h1>Ginny’s Kitchen – Sozopol Restaurant</h1>
            <p class="tagline">Your favorite restaurant by the sea, in Sozopol</p>
            <p style="max-width:42rem;margin:0 auto 1.75rem;color:#f2ebe3">Located in the heart of Sozopol, our restaurant is among the best in town. We offer a unique mix of fresh seafood, traditional Bulgarian dishes and international specialties.</p>
            <a class="btn" href="tel:+359886060687">Reservation</a>
        </div>
    </section>

    <section class="section brunch">
        <div class="section-inner">
            <h2>Brunch by the sea!</h2>
            <p>Discover your favorite flavors among our most popular recipes, inspired by the freshness of the sea. Browse the menu and dive into our coastal culinary experience.</p>
            <p><strong>— From 9:30 to 11:00</strong></p>
            <a class="btn" href="../ginnys-menu/" style="margin-top:1rem">Hungry?</a>
        </div>
    </section>

    <section class="section story">
        <div class="section-inner">
            <p class="eyebrow">Our Story</p>
            <h2>How Ginny’s Kitchen – Sozopol was established</h2>
            <div class="story-block">
                <h3>How it all started</h3>
                <p>Our story begins not just with a restaurant in Sozopol, but with a small seaside place where the sea meets people – our Ginny Bar. Created to share the joy of beach life, Ginny Bar became a home for everyone who loves carefree sunsets, friendly conversations and cold cocktails to the sound of the waves.</p>
                <p>Over time we realized that besides great drinks, people craved something more – homemade food that carries the warmth of summer and the taste of the sea. That is how <strong>Ginny’s Kitchen</strong> was born.</p>
            </div>
            <div class="story-block">
                <h3>A warm welcome</h3>
                <p>At Ginny’s Kitchen we believe food is more than well-prepared dishes – it is a way to connect people. Our mission is to create a place where every guest feels like a friend.</p>
            </div>
            <div class="story-block">
                <h3>Our vision</h3>
                <p>We want Ginny’s Kitchen to be more than a restaurant in Sozopol – a home away from home, where memories are made and smiles are shared.</p>
            </div>
        </div>
    </section>

    <section class="section menu-teasers">
        <div class="section-inner">
            <p class="eyebrow">Our Menu</p>
            <h2>Delicious homemade food and fresh fish</h2>
            <a class="btn" href="../ginnys-menu/">See the menu</a>
            <div class="teaser-grid">
                <article class="teaser">
                    <img src="../images/2025/04/freshtstart.webp" alt="Starters" loading="lazy" width="480" height="360">
                    <h3>Starters and light appetizers</h3>
                </article>
                <article class="teaser">
                    <img src="../images/2025/04/morskahrana.webp" alt="Seafood" loading="lazy" width="480" height="360">
                    <h3>Seafood and pasta</h3>
                </article>
                <article class="teaser">
                    <img src="../images/2025/04/burgeri-sozopol.webp" alt="Burgers" loading="lazy" width="480" height="360">
                    <h3>Juicy burgers and fries</h3>
                </article>
                <article class="teaser">
                    <img src="../images/2025/04/desert.webp" alt="Desserts" loading="lazy" width="480" height="360">
                    <h3>A sweet finish to the evening</h3>
                </article>
            </div>
        </div>
    </section>

    <section class="section promises">
        <div class="section-inner">
            <h2>We deliver</h2>
            <div class="promise-grid">
                <article class="promise">
                    <img src="../images/icon-bowl.png" alt="" width="56" height="56">
                    <h3>Fresh products</h3>
                    <p>We source only fresh products to guarantee authentic taste.</p>
                </article>
                <article class="promise">
                    <img src="../images/icon-restaurant.png" alt="" width="56" height="56">
                    <h3>Restaurant on the seashore</h3>
                    <p>Enjoy the beautiful view from our restaurant on the Black Sea coast.</p>
                </article>
                <article class="promise">
                    <img src="../images/icon-lotus.png" alt="" width="56" height="56">
                    <h3>Unique taste</h3>
                    <p>Always tasty food, cooked with attention to detail.</p>
                </article>
                <article class="promise">
                    <img src="../images/icon-tray.png" alt="" width="56" height="56">
                    <h3>The best recipes</h3>
                    <p>Traditional Bulgarian dishes and international specialties for every taste.</p>
                </article>
                <article class="promise">
                    <img src="../images/icon-protection.png" alt="" width="56" height="56">
                    <h3>Family-friendly</h3>
                    <p>Perfect for shared moments with loved ones. Special kids’ menu.</p>
                </article>
                <article class="promise">
                    <img src="../images/icon-thumbs.png" alt="" width="56" height="56">
                    <h3>Friendly staff</h3>
                    <p>Our team will make you feel at home.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Book a table – for any occasion!<br>Or no occasion!</h2>
        <a class="btn" href="tel:+359886060687">Reserve a table</a>
    </section>
</main>
<?php
kitchen_footer(['base' => '../', 'locale' => 'en']);
