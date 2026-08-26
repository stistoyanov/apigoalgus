@extends('layouts.dashboard')

@section('title', $site->name.' — Posts')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">News / programme posts (manual + Instagram sync).</p>
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $site->default_locale])

        <h3 class="form-section-title">Add post</h3>
        <form method="POST" action="{{ route('dashboard.sites.posts.store', $site) }}" class="site-settings-form">
            @csrf
            <div class="form-grid">
                <label>Title (BG)<input type="text" name="title_bg" required></label>
                <label>Title (EN)<input type="text" name="title_en" required></label>
                <label>Excerpt (BG)<textarea name="excerpt_bg" rows="2"></textarea></label>
                <label>Excerpt (EN)<textarea name="excerpt_en" rows="2"></textarea></label>
                <label>Posted at<input type="datetime-local" name="posted_at"></label>
                <label>Image URL<input type="url" name="image_url" placeholder="https://…"></label>
                <label>Permalink<input type="url" name="permalink" placeholder="https://instagram.com/…"></label>
                <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" checked> Published</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create post</button>
            </div>
        </form>

        <h3 class="form-section-title">Existing posts ({{ $posts->count() }})</h3>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Title BG</th>
                        <th>Posted</th>
                        <th>Published</th>
                        <th>Sort</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td colspan="6">
                                <form method="POST" action="{{ route('dashboard.sites.posts.update', [$site, $post]) }}" class="inline-edit-form">
                                    @csrf
                                    <div class="form-grid">
                                        <label>Source<input type="text" value="{{ $post->source }}" disabled></label>
                                        <label>Title BG<input type="text" name="title_bg" value="{{ $post->title_bg }}" required></label>
                                        <label>Title EN<input type="text" name="title_en" value="{{ $post->title_en }}" required></label>
                                        <label>Excerpt BG<textarea name="excerpt_bg" rows="2">{{ $post->excerpt_bg }}</textarea></label>
                                        <label>Excerpt EN<textarea name="excerpt_en" rows="2">{{ $post->excerpt_en }}</textarea></label>
                                        <label>Posted at<input type="datetime-local" name="posted_at" value="{{ optional($post->posted_at)->format('Y-m-d\TH:i') }}"></label>
                                        <label>Image URL<input type="url" name="image_url" value="{{ $post->image_url }}"></label>
                                        <label>Permalink<input type="url" name="permalink" value="{{ $post->permalink }}"></label>
                                        <label>Sort<input type="number" name="sort_order" value="{{ $post->sort_order }}"></label>
                                        <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" @checked($post->is_published)> Published</label>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('dashboard.sites.posts.destroy', [$site, $post]) }}" onsubmit="return confirm('Delete this post?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No posts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
