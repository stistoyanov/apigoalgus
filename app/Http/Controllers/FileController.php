<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\UploadedFile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    private const DISK = 'local';

    public function index(Request $request): View
    {
        $user = $request->user();
        $canManageAll = $user->canAccess('files.manage_all');

        $superAdminRoleId = Role::query()->where('slug', Role::SUPER_ADMIN)->value('id');

        $query = UploadedFile::query()->with('user')->latest();

        if (! $canManageAll) {
            $query->where('user_id', $user->id);
        } elseif (! $user->isSuperAdmin()) {
            // Admins can see all files except those owned by super admins.
            $query->where(function ($q) use ($superAdminRoleId, $user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('user', fn ($q2) => $q2->where('role_id', '!=', $superAdminRoleId)->orWhereNull('role_id'));
            });
        }

        return view('dashboard.files.index', [
            'files' => $query->paginate(20)->withQueryString(),
            'canManageAll' => $canManageAll,
            'totalUsed' => UploadedFile::totalUsedBytes(),
            'totalLimit' => UploadedFile::TOTAL_LIMIT_BYTES,
            'userUsed' => (int) UploadedFile::query()->where('user_id', $user->id)->sum('size_bytes'),
            'perFileLimit' => UploadedFile::perFileLimitBytes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $perFileLimit = UploadedFile::perFileLimitBytes();

        // If the whole request body exceeded post_max_size, PHP throws everything away
        // before validation runs. Detect that and tell the user clearly.
        if ($request->server('CONTENT_LENGTH') > 0 && $request->files->count() === 0 && ! $request->request->has('_token')) {
            return back()->with('error', sprintf(
                'Upload failed: request exceeded the server limit (post_max_size = %s). Use smaller files or fewer at once.',
                ini_get('post_max_size'),
            ));
        }

        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file'],
        ]);

        /** @var array<int, \Illuminate\Http\UploadedFile> $files */
        $files = $request->file('files');

        foreach ($files as $file) {
            if ($perFileLimit > 0 && $file->getSize() > $perFileLimit) {
                return back()->with('error', sprintf(
                    'Upload denied: "%s" (%s) exceeds the per-file limit of %s. Ask an admin to raise upload_max_filesize/post_max_size in PHP Manager.',
                    $file->getClientOriginalName(),
                    UploadedFile::formatBytes((int) $file->getSize()),
                    UploadedFile::formatBytes($perFileLimit),
                ));
            }
        }

        $incomingTotal = collect($files)->sum(fn ($f) => $f->getSize());
        $currentTotal = UploadedFile::totalUsedBytes();

        if ($currentTotal + $incomingTotal > UploadedFile::TOTAL_LIMIT_BYTES) {
            return back()->with('error', sprintf(
                'Upload denied: would exceed the %s total storage limit (currently %s used).',
                UploadedFile::formatBytes(UploadedFile::TOTAL_LIMIT_BYTES),
                UploadedFile::formatBytes($currentTotal),
            ));
        }

        $userId = $request->user()->id;
        $uploaded = 0;

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $storedName = (string) Str::uuid();
            if ($extension !== '') {
                $storedName .= '.'.$extension;
            }

            $file->storeAs('files/'.$userId, $storedName, self::DISK);

            UploadedFile::query()->create([
                'user_id' => $userId,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'stored_name' => $storedName,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);

            $uploaded++;
        }

        return back()->with('status', $uploaded.' file'.($uploaded === 1 ? '' : 's').' uploaded.');
    }

    public function download(Request $request, UploadedFile $file): StreamedResponse
    {
        $this->ensureViewable($request->user(), $file);

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($file->relativePath())) {
            abort(404, 'File missing on disk.');
        }

        return $disk->download($file->relativePath(), $file->original_name);
    }

    public function share(Request $request, UploadedFile $file): RedirectResponse
    {
        $this->ensureOwner($request->user(), $file);

        if (! $file->share_token) {
            do {
                $token = Str::random(40);
            } while (UploadedFile::query()->where('share_token', $token)->exists());

            $file->share_token = $token;
            $file->save();
        }

        return back()->with('status', 'Share link for "'.$file->original_name.'" is now active.');
    }

    public function unshare(Request $request, UploadedFile $file): RedirectResponse
    {
        $this->ensureOwner($request->user(), $file);

        $file->share_token = null;
        $file->save();

        return back()->with('status', 'Share link for "'.$file->original_name.'" revoked.');
    }

    public function destroy(Request $request, UploadedFile $file): RedirectResponse
    {
        $this->ensureManageable($request->user(), $file);

        Storage::disk(self::DISK)->delete($file->relativePath());

        $name = $file->original_name;
        $file->delete();

        return back()->with('status', 'File "'.$name.'" deleted.');
    }

    private function ensureViewable(User $user, UploadedFile $file): void
    {
        if ($file->user_id === $user->id) {
            return;
        }

        if (! $user->canAccess('files.manage_all')) {
            abort(403);
        }

        if ($file->user?->isSuperAdmin() && ! $user->isSuperAdmin()) {
            abort(403);
        }
    }

    private function ensureManageable(User $user, UploadedFile $file): void
    {
        $this->ensureViewable($user, $file);
    }

    private function ensureOwner(User $user, UploadedFile $file): void
    {
        if ($file->user_id !== $user->id) {
            abort(403, 'Only the file owner can change sharing.');
        }
    }
}
