<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function() {
    return 'pong';
});

Route::group(['prefix' => 'videos', 'as' => 'videos.'], function () {
    Route::get('{id}', [VideoController::class, 'show']);
    Route::post('', [VideoController::class, 'create']);
});
