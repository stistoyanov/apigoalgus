@extends('layouts.dashboard')

@section('title', $site->name.' — Settings')

@php
    $hours = $settings['hours'] ?? [];
@endphp

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>{{ $site->name }}</h1>
                <p class="lead">Contact, sister site, map, and optional Instagram sync token.</p>
            </div>
        </div>

        @include('dashboard.sites._tabs', ['locale' => $site->default_locale])

        <form method="POST" action="{{ route('dashboard.sites.settings.save', $site) }}" class="site-settings-form">
            @csrf

            <h3 class="form-section-title">Contact</h3>
            <div class="form-grid">
                <label>Phone display<input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" required></label>
                <label>Phone E.164<input type="text" name="phone_e164" value="{{ old('phone_e164', $settings['phone_e164'] ?? '') }}" required></label>
                <label>Email<input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" required></label>
                <label>Address (BG)<input type="text" name="address_bg" value="{{ old('address_bg', $settings['address_bg'] ?? '') }}" required></label>
                <label>Address (EN)<input type="text" name="address_en" value="{{ old('address_en', $settings['address_en'] ?? '') }}" required></label>
                <label>Facebook URL<input type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"></label>
                <label>Instagram URL<input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"></label>
            </div>

            <h3 class="form-section-title">Sister site & hours labels</h3>
            <div class="form-grid">
                <label>Sister URL (BG)<input type="url" name="sister_url" value="{{ old('sister_url', $settings['sister_url'] ?? '') }}"></label>
                <label>Sister URL (EN)<input type="url" name="sister_url_en" value="{{ old('sister_url_en', $settings['sister_url_en'] ?? '') }}"></label>
                <label>Hours label (BG)<input type="text" name="hours_label_bg" value="{{ old('hours_label_bg', $settings['hours_label_bg'] ?? '') }}"></label>
                <label>Hours label (EN)<input type="text" name="hours_label_en" value="{{ old('hours_label_en', $settings['hours_label_en'] ?? '') }}"></label>
                <label>Brunch hours (BG)<input type="text" name="brunch_hours_bg" value="{{ old('brunch_hours_bg', $settings['brunch_hours_bg'] ?? '') }}"></label>
                <label>Brunch hours (EN)<input type="text" name="brunch_hours_en" value="{{ old('brunch_hours_en', $settings['brunch_hours_en'] ?? '') }}"></label>
            </div>

            <h3 class="form-section-title">Map</h3>
            <div class="form-grid">
                <label>Map embed URL<textarea name="map_embed" rows="2">{{ old('map_embed', $settings['map_embed'] ?? '') }}</textarea></label>
                <label>Latitude<input type="text" name="map_lat" value="{{ old('map_lat', $settings['map_lat'] ?? '') }}"></label>
                <label>Longitude<input type="text" name="map_lng" value="{{ old('map_lng', $settings['map_lng'] ?? '') }}"></label>
                <label>Zoom<input type="number" name="map_zoom" min="1" max="21" value="{{ old('map_zoom', $settings['map_zoom'] ?? '') }}"></label>
            </div>

            <h3 class="form-section-title">Media limits</h3>
            <div class="form-grid">
                <label>Gallery cap<input type="number" name="gallery_cap" min="0" max="500" value="{{ old('gallery_cap', $settings['gallery_cap'] ?? 50) }}"></label>
                <label>Video cap<input type="number" name="video_cap" min="0" max="50" value="{{ old('video_cap', $settings['video_cap'] ?? 10) }}"></label>
            </div>

            @if ($site->slug === 'ginny')
                <h3 class="form-section-title">Instagram sync</h3>
                <div class="form-grid">
                    <label>Instagram user id<input type="text" name="instagram_user_id" value="{{ old('instagram_user_id', $settings['instagram_user_id'] ?? '') }}"></label>
                    <label>Access token (leave blank to keep current)<input type="password" name="instagram_access_token" autocomplete="off" placeholder="{{ !empty($settings['instagram_access_token']) ? '•••• saved' : '' }}"></label>
                </div>
                <p class="lead">Hourly command: <code>php artisan sites:sync-instagram ginny</code></p>
            @endif

            @if (is_array($hours) && count($hours) > 0)
                <h3 class="form-section-title">Working hours</h3>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Day (BG)</th>
                                <th>Day (EN)</th>
                                <th>Hours</th>
                                <th>Closed (BG)</th>
                                <th>Closed (EN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hours as $i => $row)
                                <tr>
                                    <td><input type="text" name="hours[{{ $i }}][day_bg]" value="{{ old('hours.'.$i.'.day_bg', $row['day_bg'] ?? '') }}"></td>
                                    <td><input type="text" name="hours[{{ $i }}][day_en]" value="{{ old('hours.'.$i.'.day_en', $row['day_en'] ?? '') }}"></td>
                                    <td><input type="text" name="hours[{{ $i }}][hours]" value="{{ old('hours.'.$i.'.hours', $row['hours'] ?? '') }}"></td>
                                    <td><input type="text" name="hours[{{ $i }}][closed_bg]" value="{{ old('hours.'.$i.'.closed_bg', $row['closed_bg'] ?? '') }}"></td>
                                    <td><input type="text" name="hours[{{ $i }}][closed_en]" value="{{ old('hours.'.$i.'.closed_en', $row['closed_en'] ?? '') }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </form>
    </div>
@endsection
