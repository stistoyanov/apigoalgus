@extends('layouts.dashboard')

@section('title', 'Files')

@php
    use App\Models\UploadedFile;
    $usagePercent = $totalLimit > 0 ? min(100, round(($totalUsed / $totalLimit) * 100, 1)) : 0;
@endphp

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>Files</h1>
                <p class="lead">
                    @if ($canManageAll)
                        Upload, share, and manage files for all users.
                    @else
                        Upload your files, download them later, or share a public link.
                    @endif
                </p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-card storage-card">
                <strong>Total storage</strong>
                <div class="storage-value">
                    {{ UploadedFile::formatBytes($totalUsed) }}
                    <span class="muted">of {{ UploadedFile::formatBytes($totalLimit) }}</span>
                </div>
                <div class="storage-bar" title="{{ $usagePercent }}% used">
                    <div class="storage-bar-fill {{ $usagePercent >= 90 ? 'is-danger' : ($usagePercent >= 75 ? 'is-warn' : '') }}"
                         style="width: {{ $usagePercent }}%"></div>
                </div>
                <span class="muted small">{{ $usagePercent }}% used</span>
            </div>
            <div class="meta-card">
                <strong>Your usage</strong>
                <div class="storage-value">{{ UploadedFile::formatBytes($userUsed) }}</div>
                <span class="muted small">across {{ $files->total() }} {{ \Illuminate\Support\Str::plural('file', $files->total()) }}{{ $canManageAll ? ' visible' : '' }}</span>
            </div>
        </div>

        <form class="file-upload" method="POST" action="{{ route('dashboard.files.store') }}" enctype="multipart/form-data" id="file-upload-form">
            @csrf
            <label for="file-input" class="file-drop">
                <span class="file-drop-title">Choose files to upload</span>
                <span class="file-drop-hint">
                    Drag &amp; drop here or click to browse. Multiple files supported.
                    @if ($perFileLimit > 0)
                        <br>Per-file limit: <strong>{{ UploadedFile::formatBytes($perFileLimit) }}</strong>.
                    @endif
                </span>
                <input type="file" id="file-input" name="files[]" multiple required
                       data-max-bytes="{{ $perFileLimit }}"
                       data-max-label="{{ $perFileLimit > 0 ? UploadedFile::formatBytes($perFileLimit) : '' }}">
            </label>

            @error('files')<p class="field-error">{{ $message }}</p>@enderror
            @error('files.*')<p class="field-error">{{ $message }}</p>@enderror

            <div class="file-upload-actions">
                <span class="muted small" id="file-input-summary">No files selected</span>
                <button type="submit" class="btn btn-primary" id="file-upload-submit">Upload</button>
            </div>
        </form>

        @if ($files->isEmpty())
            <p class="muted-block">No files yet. Upload your first file above.</p>
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            @if ($canManageAll)
                                <th>Owner</th>
                            @endif
                            <th>Uploaded</th>
                            <th>Share link</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            @php
                                $isOwner = $file->user_id === auth()->id();
                                $shareUrl = $file->isShared() ? route('files.public_download', $file->share_token) : null;
                            @endphp
                            <tr>
                                <td title="{{ $file->mime_type }}">{{ $file->original_name }}</td>
                                <td>{{ UploadedFile::formatBytes((int) $file->size_bytes) }}</td>
                                @if ($canManageAll)
                                    <td>
                                        @if ($file->user)
                                            <strong>{{ $file->user->name }}</strong>
                                            <div class="muted small">{{ $file->user->email }}</div>
                                        @else
                                            <span class="muted">(deleted user)</span>
                                        @endif
                                    </td>
                                @endif
                                <td>{{ $file->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($shareUrl)
                                        <div class="share-link">
                                            <input type="text" value="{{ $shareUrl }}" readonly aria-label="Share URL">
                                            <button type="button" class="btn btn-outline btn-sm" data-copy="{{ $shareUrl }}">Copy</button>
                                        </div>
                                        <div class="muted small">{{ $file->download_count }} download{{ $file->download_count === 1 ? '' : 's' }}</div>
                                    @else
                                        <span class="muted">Private</span>
                                    @endif
                                </td>
                                <td class="col-actions">
                                    <a class="btn btn-outline btn-sm" href="{{ route('dashboard.files.download', $file) }}">Download</a>

                                    @if ($isOwner)
                                        @if ($file->isShared())
                                            <form method="POST" action="{{ route('dashboard.files.unshare', $file) }}" class="inline-form">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-sm">Revoke link</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('dashboard.files.share', $file) }}" class="inline-form">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-sm">Share link</button>
                                            </form>
                                        @endif
                                    @endif

                                    @if ($isOwner || $canManageAll)
                                        <form method="POST" action="{{ route('dashboard.files.destroy', $file) }}" class="inline-form"
                                              onsubmit="return confirm('Delete &quot;{{ $file->original_name }}&quot;? This cannot be undone.');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $files->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('file-input');
        var summary = document.getElementById('file-input-summary');
        var submit = document.getElementById('file-upload-submit');
        var form = document.getElementById('file-upload-form');
        var drop = document.querySelector('.file-drop');

        if (input && summary) {
            input.addEventListener('change', function () {
                if (input.files.length === 0) {
                    summary.textContent = 'No files selected';
                    return;
                }

                var maxBytes = parseInt(input.getAttribute('data-max-bytes'), 10) || 0;
                var maxLabel = input.getAttribute('data-max-label') || '';
                var oversized = [];
                for (var i = 0; i < input.files.length; i++) {
                    if (maxBytes > 0 && input.files[i].size > maxBytes) {
                        oversized.push(input.files[i].name);
                    }
                }

                if (oversized.length > 0) {
                    summary.textContent = oversized.length + ' file(s) exceed ' + maxLabel + ': ' + oversized.join(', ');
                    summary.classList.add('field-error');
                    if (submit) submit.disabled = true;
                    return;
                }

                summary.classList.remove('field-error');
                if (submit) submit.disabled = false;

                if (input.files.length === 1) {
                    summary.textContent = input.files[0].name;
                } else {
                    summary.textContent = input.files.length + ' files selected';
                }
            });
        }

        if (drop && input) {
            ['dragenter', 'dragover'].forEach(function (ev) {
                drop.addEventListener(ev, function (e) {
                    e.preventDefault();
                    drop.classList.add('is-drag');
                });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                drop.addEventListener(ev, function (e) {
                    e.preventDefault();
                    drop.classList.remove('is-drag');
                });
            });
            drop.addEventListener('drop', function (e) {
                if (e.dataTransfer && e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        if (form && submit) {
            form.addEventListener('submit', function () {
                if (input && input.files.length === 0) return;
                submit.disabled = true;
                submit.textContent = 'Uploading…';
            });
        }

        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy');
                if (!navigator.clipboard) return;
                navigator.clipboard.writeText(text).then(function () {
                    var original = btn.textContent;
                    btn.textContent = 'Copied!';
                    setTimeout(function () { btn.textContent = original; }, 1500);
                });
            });
        });
    })();
</script>
@endpush
