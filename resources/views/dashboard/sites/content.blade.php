@extends('layouts.dashboard')

@section('title', $site->name.' — Content')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">Edit page copy for {{ $site->domain }}.</p>
            </div>
            <div class="locale-switch">
                @foreach (['bg' => 'Bulgarian', 'en' => 'English'] as $code => $label)
                    <a href="{{ route('dashboard.sites.content', ['site' => $site->slug, 'locale' => $code]) }}"
                       class="btn btn-sm {{ $locale === $code ? 'btn-primary' : 'btn-outline' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $locale])

        <form method="POST" action="{{ route('dashboard.sites.content.save', $site) }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $locale }}">

            @foreach ($sectionOrder as $section)
                @php $fields = $sections->get($section, collect()); @endphp
                @if ($fields->isNotEmpty())
                    <details class="content-section" open>
                        <summary>{{ ucfirst(str_replace('_', ' ', $section)) }}</summary>
                        <div class="content-section-body">
                            @foreach ($fields as $field)
                                <label>
                                    {{ $field->field }}
                                    @if (strlen((string) $field->value) > 120)
                                        <textarea name="content[{{ $section }}][{{ $field->field }}]" rows="3">{{ old('content.'.$section.'.'.$field->field, $field->value) }}</textarea>
                                    @else
                                        <input type="text" name="content[{{ $section }}][{{ $field->field }}]" value="{{ old('content.'.$section.'.'.$field->field, $field->value) }}">
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </details>
                @endif
            @endforeach

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save content ({{ strtoupper($locale) }})</button>
            </div>
        </form>
    </div>
@endsection
