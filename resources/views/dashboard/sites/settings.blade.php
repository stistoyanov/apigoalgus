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
                <p class="lead">Contact details, map coordinates, and working hours.</p>
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

            <h3 class="form-section-title">Map</h3>
            <div class="form-grid">
                <label>Latitude<input type="text" name="map_lat" value="{{ old('map_lat', $settings['map_lat'] ?? '') }}" required></label>
                <label>Longitude<input type="text" name="map_lng" value="{{ old('map_lng', $settings['map_lng'] ?? '') }}" required></label>
                <label>Zoom<input type="number" name="map_zoom" min="1" max="21" value="{{ old('map_zoom', $settings['map_zoom'] ?? 18) }}" required></label>
            </div>

            <h3 class="form-section-title">Media limits</h3>
            <div class="form-grid">
                <label>Gallery cap<input type="number" name="gallery_cap" min="1" max="500" value="{{ old('gallery_cap', $settings['gallery_cap'] ?? 150) }}" required></label>
                <label>Video cap<input type="number" name="video_cap" min="1" max="50" value="{{ old('video_cap', $settings['video_cap'] ?? 10) }}" required></label>
            </div>

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
                                <td><input type="text" name="hours[{{ $i }}][day_bg]" value="{{ old('hours.'.$i.'.day_bg', $row['day_bg'] ?? '') }}" required></td>
                                <td><input type="text" name="hours[{{ $i }}][day_en]" value="{{ old('hours.'.$i.'.day_en', $row['day_en'] ?? '') }}" required></td>
                                <td><input type="text" name="hours[{{ $i }}][hours]" value="{{ old('hours.'.$i.'.hours', $row['hours'] ?? '') }}"></td>
                                <td><input type="text" name="hours[{{ $i }}][closed_bg]" value="{{ old('hours.'.$i.'.closed_bg', $row['closed_bg'] ?? '') }}"></td>
                                <td><input type="text" name="hours[{{ $i }}][closed_en]" value="{{ old('hours.'.$i.'.closed_en', $row['closed_en'] ?? '') }}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </form>
    </div>
@endsection
