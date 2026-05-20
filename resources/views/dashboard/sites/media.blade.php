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

        <h3 class="form-section-title">Brand assets</h3>
        <p class="muted small" style="margin-top: -0.5rem;">Single-slot images used across the public site. Uploading a new file replaces the previous one.</p>

        @php
            $brandSlots = [
                ['purpose' => 'hero_bg', 'label' => 'Hero background', 'hint' => 'Large image used behind the hero text (≥ 1920×1080 recommended).', 'accept' => 'image/*'],
                ['purpose' => 'about_photo', 'label' => 'About / barber photo', 'hint' => 'Portrait shown in the About section (around 640×800).', 'accept' => 'image/*'],
                ['purpose' => 'logo', 'label' => 'Header logo', 'hint' => 'Square mark shown in the site header (PNG with transparency recommended).', 'accept' => 'image/*'],
                ['purpose' => 'favicon', 'label' => 'Favicon', 'hint' => 'Browser tab icon (PNG, ICO or SVG; square, 256×256+ ideal).', 'accept' => 'image/png,image/x-icon,image/svg+xml,image/jpeg'],
            ];
        @endphp

        <div class="brand-grid">
            @foreach ($brandSlots as $slot)
                @php $current = $brand->get($slot['purpose']); @endphp
                <div class="brand-slot">
                    <div class="brand-slot__preview">
                        @if ($current)
                            <img src="{{ asset('storage/'.$current->relativePath()) }}" alt="{{ $slot['label'] }}">
                        @else
                            <div class="brand-slot__empty">No file</div>
                        @endif
                    </div>
                    <div class="brand-slot__body">
                        <strong>{{ $slot['label'] }}</strong>
                        <p class="muted small">{{ $slot['hint'] }}</p>
                        @if ($current)
                            <p class="muted small">Current: {{ $current->original_name }} · {{ \App\Models\UploadedFile::formatBytes((int) $current->size_bytes) }}</p>
                        @endif
                        <form method="POST" action="{{ route('dashboard.sites.media.brand', ['site' => $site->slug, 'purpose' => $slot['purpose']]) }}" enctype="multipart/form-data" class="brand-slot__form">
                            @csrf
                            <input type="file" name="file" accept="{{ $slot['accept'] }}" required>
                            <button type="submit" class="btn btn-primary btn-sm">{{ $current ? 'Replace' : 'Upload' }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <h3 class="form-section-title" style="margin-top: 2rem;">Upload gallery images</h3>
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
                        <tr><th>#</th><th>Preview</th><th>Name</th><th>Size</th><th>Alt (BG)</th><th>Alt (EN)</th><th class="col-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($gallery as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    <a href="{{ asset('storage/'.$item->relativePath()) }}" target="_blank" rel="noopener" class="media-thumb">
                                        <img src="{{ asset('storage/'.$item->relativePath()) }}" alt="" loading="lazy">
                                    </a>
                                </td>
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
                        <tr><th>#</th><th>Preview</th><th>Name</th><th>Size</th><th>Featured</th><th class="col-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($videos as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    <video src="{{ asset('storage/'.$item->relativePath()) }}" class="media-thumb media-thumb--video" preload="metadata" muted playsinline></video>
                                </td>
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
