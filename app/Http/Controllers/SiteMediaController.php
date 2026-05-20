<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteMedia;
use App\Models\SiteSetting;
use App\Models\UploadedFile;
use App\Services\SiteContent\SiteContentRepository;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteMediaController extends Controller
{
    private const DISK = 'public';

    public function __construct(
        private SiteContentRepository $repository,
    ) {}

    public function index(Site $site): View
    {
        $settings = $this->repository->settingsMap($site);

        return view('dashboard.sites.media', [
            'site' => $site,
            'gallery' => $site->media()->where('purpose', SiteMedia::PURPOSE_GALLERY)->orderBy('sort_order')->get(),
            'videos' => $site->media()->where('purpose', SiteMedia::PURPOSE_VIDEO)->orderBy('sort_order')->get(),
            'galleryCap' => (int) ($settings['gallery_cap'] ?? 150),
            'videoCap' => (int) ($settings['video_cap'] ?? 10),
            'perFileLimit' => UploadedFile::perFileLimitBytes(),
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $request->validate([
            'purpose' => ['required', 'in:gallery,video'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file'],
        ]);

        $purpose = $request->string('purpose')->toString();
        $kind = $purpose === 'video' ? SiteMedia::KIND_VIDEO : SiteMedia::KIND_IMAGE;
        $capKey = $purpose === 'video' ? 'video_cap' : 'gallery_cap';
        $defaultCap = $purpose === 'video' ? 10 : 150;

        $cap = (int) (SiteSetting::query()
            ->where('site_id', $site->id)
            ->where('key', $capKey)
            ->value('value') ?? $defaultCap);

        $currentCount = SiteMedia::query()
            ->where('site_id', $site->id)
            ->where('purpose', $purpose)
            ->count();

        $files = $request->file('files', []);
        $perFileLimit = UploadedFile::perFileLimitBytes();

        if ($currentCount + count($files) > $cap) {
            return back()->with('error', sprintf(
                'Upload denied: %s limit is %d (currently %d).',
                $purpose,
                $cap,
                $currentCount,
            ));
        }

        $uploaded = 0;
        $maxSort = (int) SiteMedia::query()
            ->where('site_id', $site->id)
            ->where('purpose', $purpose)
            ->max('sort_order');

        foreach ($files as $file) {
            if ($perFileLimit > 0 && $file->getSize() > $perFileLimit) {
                return back()->with('error', sprintf(
                    '"%s" exceeds the per-file limit of %s.',
                    $file->getClientOriginalName(),
                    UploadedFile::formatBytes($perFileLimit),
                ));
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $storedName = (string) Str::uuid();
            if ($extension !== '') {
                $storedName .= '.'.$extension;
            }

            $folder = $kind === SiteMedia::KIND_VIDEO ? 'videos' : 'images';
            $file->storeAs('sites/'.$site->slug.'/'.$folder, $storedName, self::DISK);

            SiteMedia::query()->create([
                'site_id' => $site->id,
                'kind' => $kind,
                'purpose' => $purpose,
                'sort_order' => ++$maxSort,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'stored_name' => $storedName,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);

            $uploaded++;
        }

        ActivityLogger::log(
            action: 'sites.media.uploaded',
            user: $request->user(),
            description: 'Uploaded '.$uploaded.' '.$purpose.' file(s) to '.$site->name.'.',
            context: ['site' => $site->slug, 'purpose' => $purpose, 'count' => $uploaded],
        );

        return back()->with('status', $uploaded.' file(s) uploaded.');
    }

    public function destroy(Request $request, Site $site, SiteMedia $media): RedirectResponse
    {
        if ((int) $media->site_id !== (int) $site->id) {
            abort(404);
        }

        if (! in_array($media->purpose, [SiteMedia::PURPOSE_GALLERY, SiteMedia::PURPOSE_VIDEO], true)) {
            return back()->with('error', 'This media item cannot be deleted from here.');
        }

        Storage::disk(self::DISK)->delete($media->relativePath());
        $name = $media->original_name;
        $media->delete();

        ActivityLogger::log(
            action: 'sites.media.deleted',
            user: $request->user(),
            description: 'Deleted media "'.$name.'" from '.$site->name.'.',
            context: ['site' => $site->slug, 'media_id' => $media->id],
        );

        return back()->with('status', 'Media deleted.');
    }

    public function move(Request $request, Site $site, SiteMedia $media): RedirectResponse
    {
        if ((int) $media->site_id !== (int) $site->id) {
            abort(404);
        }

        $direction = $request->string('direction')->toString();
        if (! in_array($direction, ['up', 'down'], true)) {
            abort(422);
        }

        $siblingQuery = SiteMedia::query()
            ->where('site_id', $site->id)
            ->where('purpose', $media->purpose);

        $swap = $direction === 'up'
            ? (clone $siblingQuery)->where('sort_order', '<', $media->sort_order)->orderByDesc('sort_order')->first()
            : (clone $siblingQuery)->where('sort_order', '>', $media->sort_order)->orderBy('sort_order')->first();

        if ($swap) {
            $currentOrder = $media->sort_order;
            $media->sort_order = $swap->sort_order;
            $swap->sort_order = $currentOrder;
            $media->save();
            $swap->save();
        }

        ActivityLogger::log(
            action: 'sites.media.reordered',
            user: $request->user(),
            description: 'Reordered media in '.$site->name.'.',
            context: ['site' => $site->slug, 'media_id' => $media->id, 'direction' => $direction],
        );

        return back();
    }
}
