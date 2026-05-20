<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicFileDownloadController extends Controller
{
    public function show(string $token): StreamedResponse
    {
        $file = UploadedFile::query()->where('share_token', $token)->first();

        if (! $file) {
            abort(404, 'Share link is invalid or has been revoked.');
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($file->relativePath())) {
            abort(404, 'File missing on disk.');
        }

        $file->increment('download_count');

        return $disk->download($file->relativePath(), $file->original_name);
    }
}
