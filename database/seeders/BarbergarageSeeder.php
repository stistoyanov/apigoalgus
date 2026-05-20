<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteContent;
use App\Models\SiteMedia;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarbergarageSeeder extends Seeder
{
    private const DISK = 'public';

    public function run(): void
    {
        $site = Site::query()->updateOrCreate(
            ['slug' => Site::SLUG_BARBERGARAGE],
            [
                'name' => 'BarberGarage',
                'domain' => 'barbergarage.bg',
                'default_locale' => 'bg',
                'is_active' => true,
            ],
        );

        $this->seedSettings($site);
        $this->seedContents($site);
        $this->seedMedia($site);
    }

    private function seedSettings(Site $site): void
    {
        $settings = [
            'phone' => '089 980 8217',
            'phone_e164' => '+359899808217',
            'email' => 'todor@barbergarage.bg',
            'address_bg' => 'ул. „Ивайло" 17, Габрово, 5300, България',
            'address_en' => '17 Ivaylo Street, Gabrovo, 5300, Bulgaria',
            'facebook_url' => 'https://www.facebook.com/share/18HRBH5shz/',
            'instagram_url' => 'https://www.instagram.com/invites/contact/?igsh=xfbgrz3ecbt3&utm_content=pdo6qg',
            'map_lat' => 42.883936696871174,
            'map_lng' => 25.32067090377391,
            'map_zoom' => 18,
            'gallery_cap' => 150,
            'video_cap' => 10,
            'brand' => 'BARBERGARAGE',
            'hours' => [
                ['day_bg' => 'Понеделник', 'day_en' => 'Monday', 'hours' => '10:00 – 19:00'],
                ['day_bg' => 'Вторник', 'day_en' => 'Tuesday', 'hours' => '10:00 – 19:00'],
                ['day_bg' => 'Сряда', 'day_en' => 'Wednesday', 'hours' => '10:00 – 19:00'],
                ['day_bg' => 'Четвъртък', 'day_en' => 'Thursday', 'hours' => '10:00 – 20:00'],
                ['day_bg' => 'Петък', 'day_en' => 'Friday', 'hours' => '10:00 – 20:00'],
                ['day_bg' => 'Събота', 'day_en' => 'Saturday', 'hours' => '09:00 – 18:00'],
                ['day_bg' => 'Неделя', 'day_en' => 'Sunday', 'hours' => null, 'closed_bg' => 'Почивен ден', 'closed_en' => 'Closed'],
            ],
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
                    'title' => 'Barbergarage — мъжка фризьорска за ентусиасти | Габрово',
                    'description' => 'Barbergarage — барбершоп с дух на гараж и мотори в Габрово. ул. Ивайло 17. Заповядайте за стил и прецизност.',
                ],
                'hero' => [
                    'lead' => 'Място за мъже, които обичат чисти линии, уютна атмосфера и духа на пътя. Добре дошли в нашия гараж.',
                    'cta_label' => 'Разгледай',
                ],
                'welcome' => [
                    'title' => 'Добре дошли',
                    'body_p1' => 'Barbergarage в Габрово е барбершоп с характер — тъмни тонове, топла светлина и внимание към детайла. Тук не бързаме; подстригваме и оформяме с грижа, за да си тръгнете уверени и заредени.',
                    'body_p2' => 'Ориентирани сме към ентусиасти на две колела и към всеки, който оценява автентичен „гаражен" уют без компромис с хигиената и качеството.',
                    'cta_label' => 'Намери ни',
                ],
                'about' => [
                    'title' => 'За барбера и мястото',
                    'subtitle' => 'Запознайте се с човека зад ножиците и машинката — и с идеята зад Barbergarage.',
                    'name' => 'Тодор Стоянов',
                    'role' => 'Барбер · Barbergarage · Габрово',
                    'bio_p1' => 'Тодор е зад ножиците и машинката в Barbergarage — място с гаражен дух за мъже, които държат на чиста линия, прецизен fade и добре поддържана брада.',
                    'bio_p2' => 'Тук ще откриете спокойна атмосфера, внимание към детайла и разговор без излишна бързина — независимо дали идвате от Габрово или спирате по пътя с мотора.',
                ],
                'gallery' => [
                    'title' => 'Доволни клиенти',
                    'subtitle' => 'Част от усмивките, които сме срещали. Кликнете върху снимка за по-голям размер.',
                    'scroll_hint' => 'Плъзнете наляво/надясно за още снимки.',
                ],
                'video' => [
                    'title' => 'Видео',
                    'subtitle' => 'Кратък поглед към атмосферата в салона — натиснете play, когато ви е удобно.',
                ],
                'hours' => [
                    'title' => 'Работно време',
                    'subtitle' => 'Ако има промени по празници, обявяваме ги във Facebook и Instagram — или се обадете.',
                    'note' => 'По празници работим по обявен график в социалните мрежи или с предварителна уговорка.',
                ],
                'map' => [
                    'title' => 'Как да ни намерите',
                    'subtitle' => 'Карта за лесна навигация.',
                    'link_label' => 'Отвори в Google Карти',
                ],
                'nav' => [
                    'welcome' => 'Начало',
                    'about' => 'За нас',
                    'gallery' => 'Галерия',
                    'videos' => 'Видео',
                    'hours' => 'Работно време',
                    'map' => 'Карта',
                ],
                'contact' => [
                    'address_label' => 'Адрес',
                    'phone_label' => 'Телефон',
                    'email_label' => 'Имейл',
                    'social_label' => 'Социални мрежи',
                ],
                'footer' => [
                    'rights' => 'Barbergarage. Всички права запазени.',
                    'lang_link' => 'English',
                ],
            ],
            'en' => [
                'meta' => [
                    'title' => 'Barbergarage — barbershop for enthusiasts | Gabrovo',
                    'description' => 'Barbergarage — a barbershop with garage soul in Gabrovo, Bulgaria. 17 Ivaylo Street. Style and precision.',
                ],
                'hero' => [
                    'lead' => 'A place for sharp lines, a relaxed vibe, and the spirit of the road. Welcome to our garage.',
                    'cta_label' => 'Explore',
                ],
                'welcome' => [
                    'title' => 'Welcome',
                    'body_p1' => 'Barbergarage in Gabrovo is a barbershop with personality — deep tones, warm light, and attention to detail. We take our time with cuts and beard work so you leave feeling confident.',
                    'body_p2' => 'We speak the language of two wheels and welcome anyone who values authentic garage comfort without compromising hygiene or quality.',
                    'cta_label' => 'Find us',
                ],
                'about' => [
                    'title' => 'The barber & the shop',
                    'subtitle' => 'Meet the person behind the chair — and the idea behind Barbergarage.',
                    'name' => 'Todor Stoyanov',
                    'role' => 'Barber · Barbergarage · Gabrovo',
                    'bio_p1' => 'Todor runs the chair at Barbergarage — a garage-inspired spot for clean lines, sharp fades, and beards kept with care.',
                    'bio_p2' => 'Expect a calm pace, attention to detail, and easy conversation — whether you are local or passing through on two wheels.',
                ],
                'gallery' => [
                    'title' => 'Happy clients',
                    'subtitle' => 'A few smiles we have had the pleasure to host. Click a photo to enlarge.',
                    'scroll_hint' => 'Scroll sideways for more photos.',
                ],
                'video' => [
                    'title' => 'Video',
                    'subtitle' => 'A short look at the shop — press play when it suits you.',
                ],
                'hours' => [
                    'title' => 'Opening hours',
                    'subtitle' => 'If hours change on holidays, we post on Facebook and Instagram — or give us a call.',
                    'note' => 'On holidays we may follow a special schedule — check our social channels or call ahead.',
                ],
                'map' => [
                    'title' => 'How to find us',
                    'subtitle' => 'Embedded map for easy navigation.',
                    'link_label' => 'Open in Google Maps',
                ],
                'nav' => [
                    'welcome' => 'Home',
                    'about' => 'About',
                    'gallery' => 'Gallery',
                    'videos' => 'Video',
                    'hours' => 'Hours',
                    'map' => 'Map',
                ],
                'contact' => [
                    'address_label' => 'Address',
                    'phone_label' => 'Phone',
                    'email_label' => 'Email',
                    'social_label' => 'Social',
                ],
                'footer' => [
                    'rights' => 'Barbergarage. All rights reserved.',
                    'lang_link' => 'Български',
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
        $base = base_path('barbergarage');

        $this->importAsset($site, $base.'/images/thebarber/viber_image_2026-02-28_21-30-18-114.jpg', SiteMedia::PURPOSE_ABOUT_PHOTO, SiteMedia::KIND_IMAGE, 0, [
            'alt_text_bg' => 'Тодор — барбер в Barbergarage, Габрово',
            'alt_text_en' => 'Todor, barber at Barbergarage in Gabrovo',
        ]);

        $this->importAsset($site, $base.'/images/website/background.jpg', SiteMedia::PURPOSE_HERO_BG, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/website/IconTransperantSmallest.png', SiteMedia::PURPOSE_LOGO, SiteMedia::KIND_IMAGE, 0);
        $this->importAsset($site, $base.'/images/website/IconTransperantSmallest.png', SiteMedia::PURPOSE_FAVICON, SiteMedia::KIND_IMAGE, 0);

        $galleryJson = $base.'/gallery.json';
        if (File::exists($galleryJson)) {
            $data = json_decode(File::get($galleryJson), true);
            $images = $data['images'] ?? [];
            foreach ($images as $index => $relativePath) {
                $source = $base.'/'.ltrim(str_replace('\\', '/', $relativePath), '/');
                $this->importAsset($site, $source, SiteMedia::PURPOSE_GALLERY, SiteMedia::KIND_IMAGE, $index);
            }
        }

        $videoDir = $base.'/videos/clients';
        if (File::isDirectory($videoDir)) {
            $videos = File::files($videoDir);
            sort($videos);
            $featuredName = '0-02-05-24da43cfdf49c8dbfc13b0b71762f9259fd45ea2766df9309b7959072f806972_2218a5a400b.mp4';
            foreach ($videos as $index => $file) {
                $isFeatured = $file->getFilename() === $featuredName;
                $this->importAsset($site, $file->getPathname(), SiteMedia::PURPOSE_VIDEO, SiteMedia::KIND_VIDEO, $index, [
                    'is_featured' => $isFeatured,
                ]);
            }
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
    ): void {
        if (! File::exists($sourcePath)) {
            return;
        }

        $originalName = basename($sourcePath);

        if (SiteMedia::query()->where('site_id', $site->id)->where('original_name', $originalName)->where('purpose', $purpose)->exists()) {
            return;
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $storedName = (string) Str::uuid();
        if ($extension !== '') {
            $storedName .= '.'.$extension;
        }

        $folder = $kind === SiteMedia::KIND_VIDEO ? 'videos' : 'images';
        $destPath = 'sites/'.$site->slug.'/'.$folder.'/'.$storedName;

        Storage::disk(self::DISK)->put($destPath, File::get($sourcePath));

        SiteMedia::query()->create(array_merge([
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
