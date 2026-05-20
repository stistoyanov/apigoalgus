@extends('layouts.dashboard')

@section('title', $site->name.' — Media')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">Gallery images and videos served via signed URLs.</p>
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $site->default_locale])

        <div class="meta-grid">
            <div class="meta-card">
                <strong>Gallery</strong>
                <div class="storage-value">{{ $gallery->count() }} <span class="muted">of {{ $galleryCap }}</span></div>
            </div>
            <div class="meta-card">
                <strong>Videos</strong>
                <div class="storage-value">{{ $videos->count() }} <span class="muted">of {{ $videoCap }}</span></div>
            </div>
        </div>

        <h3 class="form-section-title">Upload gallery images</h3>
        <form class="file-upload" method="POST" action="{{ route('dashboard.sites.media.upload', $site) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="purpose" value="gallery">
            <label for="gallery-input" class="file-drop">
                <span class="file-drop-title">Choose images</span>
                <span class="file-drop-hint">Multiple files supported.@if ($perFileLimit > 0) Per-file limit: {{ \App\Models\UploadedFile::formatBytes($perFileLimit) }}.@endif</span>
                <input type="file" id="gallery-input" name="files[]" multiple accept="image/*" required>
            </label>
            <div class="file-upload-actions">
                <button type="submit" class="btn btn-primary">Upload images</button>
            </div>
        </form>

        @if ($gallery->isNotEmpty())
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Size</th><th>Alt (BG)</th><th>Alt (EN)</th><th class="col-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($gallery as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>{{ $item->original_name }}</td>
                                <td>{{ \App\Models\UploadedFile::formatBytes((int) $item->size_bytes) }}</td>
                                <td>{{ $item->alt_text_bg }}</td>
                                <td>{{ $item->alt_text_en }}</td>
                                <td class="col-actions">
                                    <form method="POST" action="{{ route('dashboard.sites.media.move', [$site, $item]) }}" class="inline-form">
                                        @csrf<input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn btn-outline btn-sm">↑</button>
                                    </form>
                                    <form method="POST" action="{{ route('dashboard.sites.media.move', [$site, $item]) }}" class="inline-form">
                                        @csrf<input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn btn-outline btn-sm">↓</button>
                                    </form>
                                    <form method="POST" action="{{ route('dashboard.sites.media.destroy', [$site, $item]) }}" class="inline-form" onsubmit="return confirm('Delete this image?');">
                                        @csrf<button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h3 class="form-section-title" style="margin-top: 2rem;">Upload videos</h3>
        <form class="file-upload" method="POST" action="{{ route('dashboard.sites.media.upload', $site) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="purpose" value="video">
            <label for="video-input" class="file-drop">
                <span class="file-drop-title">Choose videos</span>
                <span class="file-drop-hint">MP4 recommended.@if ($perFileLimit > 0) Per-file limit: {{ \App\Models\UploadedFile::formatBytes($perFileLimit) }}.@endif</span>
                <input type="file" id="video-input" name="files[]" multiple accept="video/*" required>
            </label>
            <div class="file-upload-actions">
                <button type="submit" class="btn btn-primary">Upload videos</button>
            </div>
        </form>

        @if ($videos->isNotEmpty())
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Size</th><th>Featured</th><th class="col-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($videos as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>{{ $item->original_name }}</td>
                                <td>{{ \App\Models\UploadedFile::formatBytes((int) $item->size_bytes) }}</td>
                                <td>{{ $item->is_featured ? 'Yes' : 'No' }}</td>
                                <td class="col-actions">
                                    <form method="POST" action="{{ route('dashboard.sites.media.destroy', [$site, $item]) }}" class="inline-form" onsubmit="return confirm('Delete this video?');">
                                        @csrf<button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
