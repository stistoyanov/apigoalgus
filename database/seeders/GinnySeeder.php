<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteContent;
use App\Models\SiteMedia;
use App\Models\SitePost;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GinnySeeder extends Seeder
{
    private const DISK = 'public';

    public function run(): void
    {
        $site = Site::query()->updateOrCreate(
            ['slug' => Site::SLUG_GINNY],
            [
                'name' => 'Ginny Rock Bar',
                'domain' => 'ginny.bg',
                'default_locale' => 'bg',
                'is_active' => true,
            ],
        );

        $this->seedSettings($site);
        $this->seedContents($site);
        $this->seedMedia($site);
        $this->seedPosts($site);
    }

    private function seedSettings(Site $site): void
    {
        $settings = [
            'phone' => '+359 88 606 0687',
            'phone_e164' => '+359886060687',
            'email' => 'ginny.bar@abv.bg',
            'address_bg' => 'Созопол, Старият град – Централен плаж',
            'address_en' => 'Sozopol Old Town – Central Beach',
            'facebook_url' => 'https://www.facebook.com/Ginny.Bar.Sozopol',
            'instagram_url' => 'https://www.instagram.com/ginnybar.sozopol/',
            'sister_url' => 'https://kitchen.ginny.bg/',
            'sister_url_en' => 'https://kitchen.ginny.bg/en/',
            'hours_label_bg' => '24/7',
            'hours_label_en' => '24/7',
            'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.757173457178!2d27.691852212046342!3d42.422017871066956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40a6b875f66df0a7%3A0x75a128d5cf9df64f!2sGINNY%20BAR!5e1!3m2!1sen!2sbg!4v1725567648558!5m2!1sen!2sbg',
            'gallery_cap' => 50,
            'video_cap' => 0,
            'brand' => 'GINNY',
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
                    'title' => 'Ginny Rock Bar Sozopol | Рок бар на Централен плаж',
                    'description' => 'Рок бар в сърцето на Созопол – Централен плаж. Жива музика, студена бира, коктейли. 24/7.',
                    'news_title' => 'Новини | Ginny Rock Bar Sozopol',
                    'news_desc' => 'Новини, програма и събития в Ginny Rock Bar – Созопол.',
                    'gallery_title' => 'Моменти | Ginny Rock Bar Sozopol',
                    'gallery_desc' => 'Галерия с моменти от Ginny Rock Bar – музика, плаж и рок атмосфера.',
                ],
                'nav' => [
                    'home' => 'Начало',
                    'news' => 'Новини',
                    'gallery' => 'Моменти',
                    'kitchen' => 'Kitchen',
                ],
                'hero' => [
                    'tagline' => 'Рок бар в сърцето на Созопол',
                    'cta_label' => 'За резервации',
                ],
                'story' => [
                    'title' => 'Историята на нашия бар',
                    'p1' => 'Повече от 20 години Джини бар е ключово място в летния живот на Созопол.',
                    'p2' => 'Локацията му на Централния плаж го прави колкото приятен през деня, с коктейл в ръка, толкова и вечер, на лунна светлина.',
                    'p3' => 'Музиката е рок, а често и на живо. Бирата винаги е студена, а бургерите толкова вкусни, че трудно ще се опишат с думи. Работи денонощно и винаги има място за още един. \m/',
                ],
                'rock' => [
                    'eyebrow' => 'бар на плажа',
                    'title' => 'Домът на рока и July Morning',
                    'body' => 'Джини бар е домът на рока и любимата крайна дестинация на мотористите. От изпълнения до огъня на плажа до класическите рок хитове на бара — всяка нота е част от нашия ритъм.',
                    'cta' => 'Предстоящи събития',
                ],
                'card1' => [
                    'title' => 'Live Music Nights',
                    'body' => 'Жива рок музика — идеалната атмосфера за незабравима нощ край плажа.',
                ],
                'card2' => [
                    'title' => 'Nights to Remember',
                    'body' => 'Създайте спомени, които остават дълго след залез… или изгрев.',
                ],
                'card3' => [
                    'title' => 'Vibe by the beach',
                    'body' => 'Пясък под краката и перфектното питие на метри от морето.',
                ],
                'drinks' => [
                    'eyebrow' => 'напитки и храна',
                    'title' => 'Вкусът на лятото в Ginny',
                    'body' => 'От ръчно приготвени коктейли до студена крафт бира — всяка напитка допълва лятната атмосфера. Класика или нещо ново: има за всеки вкус.',
                ],
                'visit' => [
                    'title' => 'Посети ни!',
                    'map_title' => 'Локация на Ginny Rock Bar в Google Карти',
                    'loc' => 'Локация',
                    'loc_body' => 'Созопол, Старият град – Централен плаж',
                    'book' => 'За резервации',
                    'hours' => 'Очакваме те 24/7',
                    'hours_body' => 'Най-добрият бар в Созопол',
                ],
                'sister' => [
                    'eyebrow' => 'същата плажна фамилия',
                    'title' => 'Гладен? Има и кухня',
                    'body' => 'Точно до бара е Ginny’s Kitchen — прясна риба, домашна храна и брънч с гледка към морето.',
                    'cta' => 'Към Ginny’s Kitchen',
                ],
                'cheers' => [
                    'title' => 'Cheers!',
                    'cta' => 'Направи резервация',
                ],
                'footer' => [
                    'line' => 'Ginny Rock Bar · Созопол, Централен плаж · 24/7',
                ],
                'news' => [
                    'h1' => 'Новини',
                    'lead' => 'Програма, събития и истории от Ginny Rock Bar.',
                ],
                'gallery' => [
                    'h1' => 'Моменти',
                    'lead' => 'Снимки от живота на Ginny Rock Bar.',
                ],
                'lightbox' => [
                    'close' => 'Затвори',
                    'prev' => 'Предишна',
                    'next' => 'Следваща',
                    'label' => 'Преглед на снимка',
                ],
            ],
            'en' => [
                'meta' => [
                    'title' => 'Ginny Rock Bar Sozopol | Rock bar on Central Beach',
                    'description' => 'Rock bar in the heart of Sozopol – Central Beach. Live music, cold beer, cocktails. Open 24/7.',
                    'news_title' => 'News | Ginny Rock Bar Sozopol',
                    'news_desc' => 'News, programme and events at Ginny Rock Bar – Sozopol.',
                    'gallery_title' => 'Moments | Ginny Rock Bar Sozopol',
                    'gallery_desc' => 'Gallery from Ginny Rock Bar – music, beach and rock vibes.',
                ],
                'nav' => [
                    'home' => 'Home',
                    'news' => 'News',
                    'gallery' => 'Moments',
                    'kitchen' => 'Kitchen',
                ],
                'hero' => [
                    'tagline' => 'Rock bar in the heart of Sozopol',
                    'cta_label' => 'Reservations',
                ],
                'story' => [
                    'title' => 'Our story',
                    'p1' => 'For more than 20 years, Ginny Bar has been a key stop in Sozopol’s summer life.',
                    'p2' => 'On Central Beach it works by day with a cocktail in hand — and by night under the moonlight.',
                    'p3' => 'The music is rock, often live. The beer is always cold, the burgers hard to put into words. Open around the clock — and there’s always room for one more. \m/',
                ],
                'rock' => [
                    'eyebrow' => 'beach bar',
                    'title' => 'Home of rock & July Morning',
                    'body' => 'Ginny is home to rock and a favourite stop for riders. From live sets to beach fire to classic hits at the bar — every note is part of our rhythm.',
                    'cta' => 'Upcoming events',
                ],
                'card1' => [
                    'title' => 'Live Music Nights',
                    'body' => 'Live rock nights — the right soundtrack for an unforgettable beach evening.',
                ],
                'card2' => [
                    'title' => 'Nights to Remember',
                    'body' => 'Make memories that last long after sunset… or sunrise.',
                ],
                'card3' => [
                    'title' => 'Vibe by the beach',
                    'body' => 'Sand underfoot and the right drink metres from the sea.',
                ],
                'drinks' => [
                    'eyebrow' => 'drinks & food',
                    'title' => 'The taste of summer at Ginny',
                    'body' => 'From hand-crafted cocktails to cold craft beer — every drink fits the summer mood. Classic or something new: there’s a pour for every taste.',
                ],
                'visit' => [
                    'title' => 'Visit us',
                    'map_title' => 'Ginny Rock Bar location on Google Maps',
                    'loc' => 'Location',
                    'loc_body' => 'Sozopol Old Town – Central Beach',
                    'book' => 'Reservations',
                    'hours' => 'Open 24/7',
                    'hours_body' => 'The rock bar in Sozopol',
                ],
                'sister' => [
                    'eyebrow' => 'same beach family',
                    'title' => 'Hungry? There’s a kitchen',
                    'body' => 'Right next to the bar is Ginny’s Kitchen — fresh fish, homemade food and brunch with a sea view.',
                    'cta' => 'Visit Ginny’s Kitchen',
                ],
                'cheers' => [
                    'title' => 'Cheers!',
                    'cta' => 'Book a table',
                ],
                'footer' => [
                    'line' => 'Ginny Rock Bar · Sozopol, Central Beach · 24/7',
                ],
                'news' => [
                    'h1' => 'News',
                    'lead' => 'Programme, events and stories from Ginny Rock Bar.',
                ],
                'gallery' => [
                    'h1' => 'Moments',
                    'lead' => 'Photos from life at Ginny Rock Bar.',
                ],
                'lightbox' => [
                    'close' => 'Close',
                    'prev' => 'Previous',
                    'next' => 'Next',
                    'label' => 'Photo viewer',
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
        $base = base_path('ginny');

        $this->importAsset($site, $base.'/images/hero.jpg', SiteMedia::PURPOSE_HERO_BG, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/logo.png', SiteMedia::PURPOSE_LOGO, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/logo.png', SiteMedia::PURPOSE_FAVICON, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/drinks.png', SiteMedia::PURPOSE_DRINKS, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/story-bg.png', SiteMedia::PURPOSE_STORY_BG, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/cheers-bg.jpg', SiteMedia::PURPOSE_CHEERS_BG, SiteMedia::KIND_IMAGE, 0);

        $altsBg = [
            'Ginny Bar merch', 'Коктейли в Ginny Bar', 'Екипът на Ginny', 'Момент от бара',
            'Жива рок музика на плажа', 'Рокери и мотористи в Ginny', 'July Morning в Ginny',
            'Концерт на живо', 'Атмосфера в бара', 'Фойерверки на Централен плаж',
            'Шотове в Ginny', 'Вечер в Ginny Rock Bar', 'Момент от Ginny', 'Гости на бара',
            'Ginny Bar екстериор', 'Живо изпълнение',
        ];
        $altsEn = [
            'Ginny Bar merch', 'Cocktails at Ginny Bar', 'The Ginny team', 'A moment at the bar',
            'Live rock on the beach', 'Riders at Ginny', 'July Morning at Ginny',
            'Live concert', 'Bar atmosphere', 'Fireworks on Central Beach',
            'Shots at Ginny', 'Night at Ginny Rock Bar', 'A Ginny moment', 'Guests at the bar',
            'Ginny Bar exterior', 'Live performance',
        ];
        $galleryFiles = [
            'images/2025/05/ginny-bar-merch.jpg',
            'images/2025/05/cocktails-at-ginny-bar.jpg',
            'images/2025/05/ginny-team.jpg',
            'images/2025/05/2.jpg',
            'images/2025/05/live-rock-music-in-rock-bar-on-the-beach.jpg',
            'images/2025/05/ginny-rock-bar-rockers-and-motors-bicycles-bikes-bikers.jpg',
            'images/2025/05/july-morning-at-ginny.jpg',
            'images/2025/05/ginny-live-concert.jpg',
            'images/2025/05/5.jpg',
            'images/2025/05/fireworks-on-the-beach-sozopol-central.jpg',
            'images/2025/05/ginny-shots.jpg',
            'images/2025/05/8.jpg',
            'images/2025/05/9.jpg',
            'images/2025/05/4.jpg',
            'images/2024/09/ginny-bar-2.jpg',
            'images/2024/09/ginny-bar-rock-sozopol-milena-1.jpg',
        ];

        foreach ($galleryFiles as $index => $relative) {
            $this->importAsset(
                $site,
                $base.'/'.$relative,
                SiteMedia::PURPOSE_GALLERY,
                SiteMedia::KIND_IMAGE,
                $index,
                [
                    'alt_text_bg' => $altsBg[$index] ?? null,
                    'alt_text_en' => $altsEn[$index] ?? null,
                ],
            );
        }
    }

    private function seedPosts(Site $site): void
    {
        $base = base_path('ginny');

        $posts = [
            [
                'title_bg' => 'Нощен живот в Созопол: Защо Ginny е любимият бар на хиляди',
                'title_en' => 'Nightlife in Sozopol: why Ginny is a favourite',
                'excerpt_bg' => 'Защо Ginny Rock Bar остава любимото място за нощен живот в Созопол – рок, плаж и атмосфера, която не се забравя.',
                'excerpt_en' => 'Why Ginny Rock Bar stays a favourite for Sozopol nights — rock, beach and an atmosphere you don’t forget.',
                'posted_at' => '2025-07-29 10:00:00',
                'image' => 'images/2025/07/ginny1-1.jpg',
            ],
            [
                'title_bg' => 'Програма за август 2025 в Ginny Rock Bar',
                'title_en' => 'August 2025 programme at Ginny Rock Bar',
                'excerpt_bg' => 'Жива музика, партита и специални вечери през август 2025 на Централен плаж.',
                'excerpt_en' => 'Live music, parties and special nights through August 2025 on Central Beach.',
                'posted_at' => '2025-07-29 10:00:00',
                'image' => 'images/2025/07/photo_2025-07-29_10-19-34.jpg',
            ],
            [
                'title_bg' => 'Посрещни July Morning 2025 в Ginny Rock Bar – Созопол',
                'title_en' => 'Meet July Morning 2025 at Ginny – Sozopol',
                'excerpt_bg' => 'Домът на July Morning в Созопол – изгрев, рок и приятели на метри от морето.',
                'excerpt_en' => 'Home of July Morning in Sozopol — sunrise, rock and friends metres from the sea.',
                'posted_at' => '2025-05-29 10:00:00',
                'image' => 'images/2025/05/july-morning-at-ginny.jpg',
            ],
            [
                'title_bg' => 'Летният сезон 2025 започва на 6 юни!',
                'title_en' => 'Summer season 2025 opens 6 June!',
                'excerpt_bg' => 'Откриване на летния сезон 2025 – очакваме ви с музика, коктейли и плажна енергия.',
                'excerpt_en' => 'Season opening 2025 — music, cocktails and beach energy.',
                'posted_at' => '2025-05-29 10:00:00',
                'image' => 'images/2025/05/ginny-rock-bar-otkrivane.jpg',
            ],
            [
                'title_bg' => '8 причини да изберете Созопол за лятна почивка',
                'title_en' => '8 reasons to choose Sozopol for summer',
                'excerpt_bg' => 'Защо Созопол и Ginny Rock Bar са задължителна спирка за лятна почивка.',
                'excerpt_en' => 'Why Sozopol and Ginny Rock Bar belong on your summer list.',
                'posted_at' => '2025-04-25 10:00:00',
                'image' => 'images/2025/04/sozopol_star_grad.jpg',
            ],
            [
                'title_bg' => '07/09/24 – Closing party with DJ Groovy George',
                'title_en' => '07/09/24 – Closing party with DJ Groovy George',
                'excerpt_bg' => 'Closing party с DJ Groovy George – финал на сезона с рок и танц.',
                'excerpt_en' => 'Closing party with DJ Groovy George — end the season with rock and dance.',
                'posted_at' => '2024-09-07 10:00:00',
                'image' => 'images/2024/09/bar-36.jpg',
            ],
        ];

        foreach ($posts as $index => $post) {
            $media = $this->importAsset(
                $site,
                $base.'/'.$post['image'],
                SiteMedia::PURPOSE_GALLERY,
                SiteMedia::KIND_IMAGE,
                100 + $index,
            );

            SitePost::query()->updateOrCreate(
                [
                    'site_id' => $site->id,
                    'external_id' => 'seed-'.$index,
                ],
                [
                    'source' => SitePost::SOURCE_MANUAL,
                    'sort_order' => $index,
                    'is_published' => true,
                    'posted_at' => $post['posted_at'],
                    'title_bg' => $post['title_bg'],
                    'title_en' => $post['title_en'],
                    'excerpt_bg' => $post['excerpt_bg'],
                    'excerpt_en' => $post['excerpt_en'],
                    'media_id' => $media?->id,
                    'image_url' => null,
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
