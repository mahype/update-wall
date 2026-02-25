<?php

use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    Route::post('/report', [ReportController::class, 'store'])
        ->middleware(ValidateApiToken::class);
});
