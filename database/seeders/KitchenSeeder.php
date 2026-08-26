<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteContent;
use App\Models\SiteMedia;
use App\Models\SiteMenuItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KitchenSeeder extends Seeder
{
    private const DISK = 'public';

    public function run(): void
    {
        $site = Site::query()->updateOrCreate(
            ['slug' => Site::SLUG_KITCHEN],
            [
                'name' => "Ginny's Kitchen",
                'domain' => 'kitchen.ginny.bg',
                'default_locale' => 'bg',
                'is_active' => true,
            ],
        );

        $this->seedSettings($site);
        $this->seedContents($site);
        $this->seedMedia($site);
        $this->seedMenuItems($site);
    }

    private function seedSettings(Site $site): void
    {
        $settings = [
            'phone' => '+359 88 606 0687',
            'phone_e164' => '+359886060687',
            'email' => 'ginnys.kitchen@abv.bg',
            'address_bg' => 'Созопол, Централен плаж',
            'address_en' => 'Sozopol, Central Beach',
            'facebook_url' => 'https://www.facebook.com/Ginny.Bar.Sozopol',
            'instagram_url' => 'https://www.instagram.com/ginnybar.sozopol/',
            'sister_url' => 'https://ginny.bg/',
            'sister_url_en' => 'https://ginny.bg/en/',
            'hours_label_bg' => 'Понеделник – Неделя: 9:00–23:00',
            'hours_label_en' => 'Monday – Sunday: 9:00–23:00',
            'brunch_hours_bg' => 'От 9:30 до 11:00',
            'brunch_hours_en' => 'From 9:30 to 11:00',
            'gallery_cap' => 20,
            'video_cap' => 0,
            'brand' => "GINNY'S KITCHEN",
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['site_id' => $site->id, 'key' => $key],
                ['value' => $value],
            );
        }
    }

    private function seedContents(Site $site): void
    {
        $contents = [
            'bg' => [
                'meta' => [
                    'title' => "Ginny's Kitchen – Любимият ресторант в Созопол!",
                    'description' => 'Морски дарове, българска кухня и международни специалитети до морето в Созопол.',
                    'menu_title' => "Меню | Ginny's Kitchen Sozopol",
                    'menu_desc' => "Брънч и основно меню на Ginny's Kitchen – Созопол, Централен плаж.",
                ],
                'nav' => [
                    'home' => 'Начало',
                    'menu' => 'Меню',
                    'bar' => 'Rock Bar',
                    'reserve' => 'Резервация',
                ],
                'hero' => [
                    'tagline' => 'Любимият ресторант до морето в Созопол',
                    'cta' => 'Резервация',
                ],
                'brunch' => [
                    'title' => 'Вкусен брънч на брега',
                    'body' => 'Открий любимите вкусове сред нашите най-популярни рецепти, вдъхновени от свежестта на морето.',
                    'hours' => 'От 9:30 до 11:00',
                    'cta' => 'Гладен ли си?',
                ],
                'story' => [
                    'eyebrow' => 'Нашата история',
                    'title' => 'От Ginny Bar до Ginny’s Kitchen',
                    'h1' => 'Как се роди ресторантът',
                    'p1' => 'Историята започва с едно малко крайбрежно място, където морето среща хората – нашият Ginny Bar. Място за залези, приятелски разговори и студени коктейли под звуците на вълните.',
                    'p2' => 'С времето разбрахме, че хората жадуват и за домашно приготвена храна с вкуса на лятото и морето. Така се роди Ginny’s Kitchen – естествено продължение на духа на бара.',
                    'h2' => 'Топло посрещане',
                    'p3' => 'Вярваме, че храната е начин да свързваме хората. Мисията ни е всеки гост да се чувства като приятел.',
                    'h3' => 'Нашата визия',
                    'p4' => 'Искаме Ginny’s Kitchen да е дом далеч от дома – място, където се създават спомени и се споделят усмивки.',
                ],
                'menu' => [
                    'eyebrow' => 'Менюто',
                    'title' => 'Домашна храна и прясна риба',
                    'cta' => 'Виж менюто',
                    'teaser1' => 'Свежо начало и леки предястия',
                    'teaser2' => 'Морски изкушения и паста',
                    'teaser3' => 'Сочни бургери и картофки',
                    'teaser4' => 'Сладки финали на вечерта',
                    'h1' => 'Нашето меню',
                    'lead' => 'Домашна храна и прясна риба на Централен плаж, Созопол.',
                    'brunch' => 'Брънч',
                    'brunch_note' => 'От 9:30 до 11:00. Специално брънч меню с вкусове от морето.',
                    'mains' => 'Основно меню',
                    'mains_note' => 'Угоди на стомаха си и се наслади на лятото. Очакваме те!',
                    'see_you' => 'Очакваме те!',
                ],
                'promises' => [
                    'title' => 'Нашите обещания към вас',
                    'p1_title' => 'Свежи продукти',
                    'p1_body' => 'Зареждаме само пресни продукти за автентичен вкус.',
                    'p2_title' => 'На брега на морето',
                    'p2_body' => 'Насладете се на гледката от ресторанта на Черно море.',
                    'p3_title' => 'Уникален вкус',
                    'p3_body' => 'Ястия, сготвени с внимание към детайла.',
                    'p4_title' => 'Най-добрите рецепти',
                    'p4_body' => 'Българска класика и международни специалитети.',
                    'p5_title' => 'За семейства',
                    'p5_body' => 'Идеално за споделени моменти. Специално меню за деца.',
                    'p6_title' => 'Любезен персонал',
                    'p6_body' => 'Екипът ни ще ви накара да се почувствате у дома.',
                ],
                'sister' => [
                    'eyebrow' => 'същата плажна фамилия',
                    'title' => 'След вечерята — рок',
                    'body' => 'Точно до ресторанта е Ginny Rock Bar — жива музика, студена бира и атмосфера 24/7.',
                    'cta' => 'Към Ginny Rock Bar',
                ],
                'cta' => [
                    'title' => 'Резервирай маса край морето',
                    'body' => 'За всеки повод — или без повод.',
                    'btn' => 'Запази маса',
                ],
                'footer' => [
                    'loc' => 'Созопол, Централен плаж',
                    'hours_label' => 'Работно време',
                    'hours' => 'Понеделник – Неделя: 9:00–23:00',
                    'book' => 'За резервации',
                ],
            ],
            'en' => [
                'meta' => [
                    'title' => "Ginny's Kitchen – Your favorite restaurant in Sozopol!",
                    'description' => 'Seafood, Bulgarian classics and international dishes by the sea in Sozopol.',
                    'menu_title' => "Menu | Ginny's Kitchen Sozopol",
                    'menu_desc' => "Brunch and main menu at Ginny's Kitchen – Sozopol, Central Beach.",
                ],
                'nav' => [
                    'home' => 'Home',
                    'menu' => 'Menu',
                    'bar' => 'Rock Bar',
                    'reserve' => 'Reservation',
                ],
                'hero' => [
                    'tagline' => 'Your favorite restaurant by the sea in Sozopol',
                    'cta' => 'Reservation',
                ],
                'brunch' => [
                    'title' => 'Brunch by the sea',
                    'body' => 'Discover your favorite flavors among our most popular recipes, inspired by the freshness of the sea.',
                    'hours' => 'From 9:30 to 11:00',
                    'cta' => 'Hungry?',
                ],
                'story' => [
                    'eyebrow' => 'Our story',
                    'title' => 'From Ginny Bar to Ginny’s Kitchen',
                    'h1' => 'How it started',
                    'p1' => 'It began with a small seaside place where the sea meets people – Ginny Bar. A home for sunsets, friendly talks and cold cocktails to the sound of the waves.',
                    'p2' => 'Over time we realized people also craved homemade food with the taste of summer and the sea. That is how Ginny’s Kitchen was born – a natural extension of the bar’s spirit.',
                    'h2' => 'A warm welcome',
                    'p3' => 'We believe food connects people. Our mission is to make every guest feel like a friend.',
                    'h3' => 'Our vision',
                    'p4' => 'We want Ginny’s Kitchen to be a home away from home — where memories are made and smiles are shared.',
                ],
                'menu' => [
                    'eyebrow' => 'The menu',
                    'title' => 'Homemade food & fresh fish',
                    'cta' => 'See the menu',
                    'teaser1' => 'Fresh starters & light bites',
                    'teaser2' => 'Seafood & pasta',
                    'teaser3' => 'Juicy burgers & fries',
                    'teaser4' => 'Sweet endings',
                    'h1' => 'Our menu',
                    'lead' => 'Homemade food and fresh fish on Central Beach, Sozopol.',
                    'brunch' => 'Brunch',
                    'brunch_note' => 'From 9:30 to 11:00. A special brunch menu with flavours from the sea.',
                    'mains' => 'Main menu',
                    'mains_note' => 'Treat yourself and enjoy the summer. We can’t wait to see you!',
                    'see_you' => 'See you soon!',
                ],
                'promises' => [
                    'title' => 'Our promises to you',
                    'p1_title' => 'Fresh produce',
                    'p1_body' => 'We stock only fresh ingredients for authentic taste.',
                    'p2_title' => 'By the sea',
                    'p2_body' => 'Enjoy the view from our restaurant on the Black Sea.',
                    'p3_title' => 'Unique flavour',
                    'p3_body' => 'Dishes cooked with care for every detail.',
                    'p4_title' => 'Best recipes',
                    'p4_body' => 'Bulgarian classics and international specialties.',
                    'p5_title' => 'Family friendly',
                    'p5_body' => 'Perfect for shared moments. Kids’ menu available.',
                    'p6_title' => 'Warm hospitality',
                    'p6_body' => 'Our team will make you feel at home.',
                ],
                'sister' => [
                    'eyebrow' => 'same beach family',
                    'title' => 'After dinner — rock',
                    'body' => 'Right next to the restaurant is Ginny Rock Bar — live music, cold beer and vibes 24/7.',
                    'cta' => 'Visit Ginny Rock Bar',
                ],
                'cta' => [
                    'title' => 'Book a table by the sea',
                    'body' => 'For any occasion — or no occasion at all.',
                    'btn' => 'Reserve a table',
                ],
                'footer' => [
                    'loc' => 'Sozopol, Central Beach',
                    'hours_label' => 'Hours',
                    'hours' => 'Monday – Sunday: 9:00–23:00',
                    'book' => 'Reservations',
                ],
            ],
        ];

        foreach ($contents as $locale => $sections) {
            foreach ($sections as $section => $fields) {
                foreach ($fields as $field => $value) {
                    SiteContent::query()->updateOrCreate(
                        [
                            'site_id' => $site->id,
                            'locale' => $locale,
                            'section' => $section,
                            'field' => $field,
                        ],
                        ['value' => $value],
                    );
                }
            }
        }
    }

    private function seedMedia(Site $site): void
    {
        $base = base_path('kitchen');

        $this->importAsset($site, $base.'/images/hero-bg.jpg', SiteMedia::PURPOSE_HERO_BG, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/logo.png', SiteMedia::PURPOSE_LOGO, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/logo.png', SiteMedia::PURPOSE_FAVICON, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/about-photo.jpg', SiteMedia::PURPOSE_ABOUT_PHOTO, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/story-bg.webp', SiteMedia::PURPOSE_STORY_BG, SiteMedia::KIND_IMAGE, 0);
    }

    private function seedMenuItems(Site $site): void
    {
        $base = base_path('kitchen');

        $brunch = [
            [
                'title_bg' => 'Омлет',
                'title_en' => 'Omelette',
                'desc_bg' => 'Предлагаме няколко вида омлети, с различни съставки и гарнитура от свежи зеленчуци.',
                'desc_en' => 'Several omelette styles with fresh vegetable garnish.',
            ],
            [
                'title_bg' => 'Палачинки с шоколад и плодове',
                'title_en' => 'Pancakes with chocolate & fruit',
                'desc_bg' => 'Насладете се на топли палачинки, допълнени със свежи плодове и гладък шоколад.',
                'desc_en' => 'Warm pancakes with fresh fruit and smooth chocolate.',
            ],
            [
                'title_bg' => 'Чабата с гуакамоле и яйце',
                'title_en' => 'Ciabatta with guacamole & egg',
                'desc_bg' => 'Мека чабата с хрупкава коричка, домашно гуакамоле и сготвени до съвършенство яйца.',
                'desc_en' => 'Soft ciabatta with a crisp crust, homemade guacamole and perfectly cooked eggs.',
            ],
            [
                'title_bg' => 'Домашни мекици',
                'title_en' => 'Homemade mekitsi',
                'desc_bg' => 'Пухкави мекици с краве сирене и домашно сладко от зелени смокини – от екипа на Ginny’s : )',
                'desc_en' => 'Fluffy mekitsi with cow’s cheese and homemade green fig jam — from the Ginny’s team :)',
            ],
        ];

        foreach ($brunch as $index => $item) {
            SiteMenuItem::query()->updateOrCreate(
                [
                    'site_id' => $site->id,
                    'category' => SiteMenuItem::CATEGORY_BRUNCH,
                    'sort_order' => $index,
                ],
                [
                    'is_published' => true,
                    'title_bg' => $item['title_bg'],
                    'title_en' => $item['title_en'],
                    'desc_bg' => $item['desc_bg'],
                    'desc_en' => $item['desc_en'],
                    'media_id' => null,
                ],
            );
        }

        $mains = [
            [
                'title_bg' => 'Гръцка салата',
                'title_en' => 'Greek salad',
                'desc_bg' => 'Класическа гръцка салата със свежи зеленчуци и сирене.',
                'desc_en' => 'Classic Greek salad with fresh vegetables and cheese.',
                'image' => 'images/2025/04/freshtstart.webp',
            ],
            [
                'title_bg' => 'Салата Капрезе Бурата',
                'title_en' => 'Caprese Burrata',
                'desc_bg' => 'Кремообразна бурата с домати и босилек.',
                'desc_en' => 'Creamy burrata with tomatoes and basil.',
                'image' => 'images/2025/04/burata.webp',
            ],
            [
                'title_bg' => 'Домашни разядки',
                'title_en' => 'Homemade spreads',
                'desc_bg' => 'Домашни разядки за споделяне – идеално начало на вечерята.',
                'desc_en' => 'Homemade spreads for sharing — the perfect start to dinner.',
                'image' => 'images/2025/04/tarama.webp',
            ],
            [
                'title_bg' => 'Пресен черноморски сафрид',
                'title_en' => 'Fresh Black Sea horse mackerel',
                'desc_bg' => 'Прясно уловен черноморски сафрид.',
                'desc_en' => 'Freshly caught Black Sea horse mackerel.',
                'image' => 'images/2025/04/safrid.webp',
            ],
            [
                'title_bg' => 'Пържен скорпид',
                'title_en' => 'Fried scorpionfish',
                'desc_bg' => 'Хрупкав пържен скорпид.',
                'desc_en' => 'Crispy fried scorpionfish.',
                'image' => 'images/2025/04/skorpid.webp',
            ],
            [
                'title_bg' => 'Студено – пушен чернокоп',
                'title_en' => 'Cold-smoked bluefish',
                'desc_bg' => 'Студено пушен чернокоп.',
                'desc_en' => 'Cold-smoked black sea bluefish.',
                'image' => 'images/2025/04/chernokop.webp',
            ],
            [
                'title_bg' => 'Бургери',
                'title_en' => 'Burgers',
                'desc_bg' => 'Избор от вегетариански, пилешки, телешки, свински – бриош питка, свежи салати, домашни сосове и пресни картофи.',
                'desc_en' => 'Veggie, chicken, beef or pork — brioche bun, fresh salads, homemade sauces and fresh fries.',
                'image' => 'images/2025/04/burgerr.webp',
            ],
            [
                'title_bg' => 'Прясна паста',
                'title_en' => 'Fresh pasta',
                'desc_bg' => 'Паста с морски дарове – миди или скариди, болонезе, карбонара и други.',
                'desc_en' => 'Seafood pasta with mussels or shrimp, bolognese, carbonara and more.',
                'image' => 'images/2025/04/shrimp-pasta.webp',
            ],
            [
                'title_bg' => 'Супи',
                'title_en' => 'Soups',
                'desc_bg' => 'Избор от супи, сред които – пилешка супа, таратор и класическата рибена чорба.',
                'desc_en' => 'Chicken soup, tarator and classic fish soup among others.',
                'image' => 'images/2025/04/soup.webp',
            ],
            [
                'title_bg' => 'Хавиар / специалитети',
                'title_en' => 'Caviar / specialties',
                'desc_bg' => 'Избрани специалитети от кухнята.',
                'desc_en' => 'Selected specialties from the kitchen.',
                'image' => 'images/2025/04/chaviar.webp',
            ],
        ];

        foreach ($mains as $index => $item) {
            $media = $this->importAsset(
                $site,
                $base.'/'.$item['image'],
                SiteMedia::PURPOSE_MENU,
                SiteMedia::KIND_IMAGE,
                $index,
            );

            SiteMenuItem::query()->updateOrCreate(
                [
                    'site_id' => $site->id,
                    'category' => SiteMenuItem::CATEGORY_MAINS,
                    'sort_order' => $index,
                ],
                [
                    'is_published' => true,
                    'title_bg' => $item['title_bg'],
                    'title_en' => $item['title_en'],
                    'desc_bg' => $item['desc_bg'],
                    'desc_en' => $item['desc_en'],
                    'media_id' => $media?->id,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function importAsset(
        Site $site,
        string $sourcePath,
        string $purpose,
        string $kind,
        int $sortOrder,
        array $extra = [],
    ): ?SiteMedia {
        if (! File::exists($sourcePath)) {
            return null;
        }

        $originalName = basename($sourcePath);

        $existing = SiteMedia::query()
            ->where('site_id', $site->id)
            ->where('original_name', $originalName)
            ->where('purpose', $purpose)
            ->first();

        if ($existing) {
            return $existing;
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $storedName = (string) Str::uuid();
        if ($extension !== '') {
            $storedName .= '.'.$extension;
        }

        $folder = $kind === SiteMedia::KIND_VIDEO ? 'videos' : 'images';
        $destPath = 'sites/'.$site->slug.'/'.$folder.'/'.$storedName;

        Storage::disk(self::DISK)->put($destPath, File::get($sourcePath));

        return SiteMedia::query()->create(array_merge([
            'site_id' => $site->id,
            'kind' => $kind,
            'purpose' => $purpose,
            'sort_order' => $sortOrder,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'mime_type' => File::mimeType($sourcePath) ?: null,
            'size_bytes' => File::size($sourcePath),
            'is_featured' => false,
        ], $extra));
    }
}
