@extends('layouts.dashboard')

@section('title', $site->name.' — Menu')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">Brunch and main menu items.</p>
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $site->default_locale])

        <h3 class="form-section-title">Add item</h3>
        <form method="POST" action="{{ route('dashboard.sites.menu.store', $site) }}" class="site-settings-form">
            @csrf
            <div class="form-grid">
                <label>Category
                    <select name="category" required>
                        <option value="brunch">Brunch</option>
                        <option value="mains">Mains</option>
                    </select>
                </label>
                <label>Title (BG)<input type="text" name="title_bg" required></label>
                <label>Title (EN)<input type="text" name="title_en" required></label>
                <label>Description (BG)<textarea name="desc_bg" rows="2"></textarea></label>
                <label>Description (EN)<textarea name="desc_en" rows="2"></textarea></label>
                <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" checked> Published</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create item</button>
            </div>
        </form>

        @foreach (['brunch' => 'Brunch', 'mains' => 'Mains'] as $cat => $label)
            <h3 class="form-section-title">{{ $label }} ({{ ($items[$cat] ?? collect())->count() }})</h3>
            @forelse (($items[$cat] ?? collect()) as $item)
                <form method="POST" action="{{ route('dashboard.sites.menu.update', [$site, $item]) }}" class="site-settings-form" style="margin-bottom:1rem">
                    @csrf
                    <div class="form-grid">
                        <label>Category
                            <select name="category">
                                <option value="brunch" @selected($item->category === 'brunch')>Brunch</option>
                                <option value="mains" @selected($item->category === 'mains')>Mains</option>
                            </select>
                        </label>
                        <label>Title BG<input type="text" name="title_bg" value="{{ $item->title_bg }}" required></label>
                        <label>Title EN<input type="text" name="title_en" value="{{ $item->title_en }}" required></label>
                        <label>Desc BG<textarea name="desc_bg" rows="2">{{ $item->desc_bg }}</textarea></label>
                        <label>Desc EN<textarea name="desc_en" rows="2">{{ $item->desc_en }}</textarea></label>
                        <label>Sort<input type="number" name="sort_order" value="{{ $item->sort_order }}"></label>
                        <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" @checked($item->is_published)> Published</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('dashboard.sites.menu.destroy', [$site, $item]) }}" onsubmit="return confirm('Delete this item?');" style="margin-bottom:1.5rem">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline">Delete</button>
                </form>
            @empty
                <p class="lead">No {{ strtolower($label) }} items yet.</p>
            @endforelse
        @endforeach
    </div>
@endsection
