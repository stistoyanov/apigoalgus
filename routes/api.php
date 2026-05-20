<?php

use App\Http\Controllers\Api\SiteContentApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.site')->group(function () {
    Route::get('/v1/site/content', [SiteContentApiController::class, 'index']);
    Route::get('/v1/site/content/{locale}', [SiteContentApiController::class, 'index'])
        ->where('locale', 'bg|en');
});
