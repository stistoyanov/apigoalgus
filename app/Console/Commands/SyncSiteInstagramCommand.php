<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Models\SitePost;
use App\Models\SiteSetting;
use App\Services\ScheduleRunLogger;
use App\Support\ActivityLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SyncSiteInstagramCommand extends Command
{
    protected $signature = 'sites:sync-instagram {site=ginny : Site slug} {--limit=12 : Max posts to pull}';

    protected $description = 'Sync Instagram media into site_posts for a site (default: ginny)';

    public function handle(ScheduleRunLogger $logger): int
    {
        $slug = (string) $this->argument('site');
        $limit = max(1, min(50, (int) $this->option('limit')));

        try {
            $logger->run(
                'sites:sync-instagram',
                function () use ($slug, $limit) {
                    $site = Site::query()->where('slug', $slug)->where('is_active', true)->first();
                    if (! $site) {
                        $this->error('Site not found or inactive: '.$slug);

                        return 'skipped: site missing';
                    }

                    $settings = SiteSetting::query()
                        ->where('site_id', $site->id)
                        ->get()
                        ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->value])
                        ->all();

                    $token = (string) ($settings['instagram_access_token'] ?? config('services.instagram.access_token') ?? '');
                    $userId = (string) ($settings['instagram_user_id'] ?? config('services.instagram.user_id') ?? 'me');

                    if ($token === '') {
                        $this->warn('No Instagram access token configured for '.$slug.'. Set Settings → Instagram access token.');

                        return 'skipped: no token';
                    }

                    $url = 'https://graph.instagram.com/'.$userId.'/media';
                    $response = Http::timeout(20)->get($url, [
                        'fields' => 'id,caption,media_type,media_url,permalink,timestamp,thumbnail_url',
                        'access_token' => $token,
                        'limit' => $limit,
                    ]);

                    if (! $response->successful()) {
                        throw new \RuntimeException('Instagram API error: '.$response->status().' '.$response->body());
                    }

                    $data = $response->json('data') ?? [];
                    $upserted = 0;

                    foreach ($data as $index => $item) {
                        if (! is_array($item) || empty($item['id'])) {
                            continue;
                        }

                        $mediaType = (string) ($item['media_type'] ?? '');
                        if (! in_array($mediaType, ['IMAGE', 'CAROUSEL_ALBUM'], true)) {
                            continue;
                        }

                        $caption = trim((string) ($item['caption'] ?? ''));
                        $title = $caption !== '' ? mb_substr($caption, 0, 120) : 'Instagram · '.$item['id'];
                        $excerpt = $caption !== '' ? mb_substr($caption, 0, 400) : null;
                        $imageUrl = (string) ($item['media_url'] ?? $item['thumbnail_url'] ?? '');

                        SitePost::query()->updateOrCreate(
                            [
                                'site_id' => $site->id,
                                'external_id' => (string) $item['id'],
                            ],
                            [
                                'source' => SitePost::SOURCE_INSTAGRAM,
                                'sort_order' => $index,
                                'is_published' => true,
                                'posted_at' => isset($item['timestamp'])
                                    ? Carbon::parse((string) $item['timestamp'])
                                    : now(),
                                'title_bg' => $title,
                                'title_en' => $title,
                                'excerpt_bg' => $excerpt,
                                'excerpt_en' => $excerpt,
                                'permalink' => $item['permalink'] ?? null,
                                'image_url' => $imageUrl !== '' ? $imageUrl : null,
                            ],
                        );
                        $upserted++;
                    }

                    ActivityLogger::system(
                        action: 'sites.posts.synced',
                        description: 'Synced '.$upserted.' Instagram posts for '.$site->name.'.',
                        context: ['site' => $site->slug, 'count' => $upserted],
                    );

                    $this->info('Upserted '.$upserted.' Instagram posts for '.$site->slug);

                    return 'upserted '.$upserted;
                },
                '0 * * * *',
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
