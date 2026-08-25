<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/webhooks', [WebhookController::class, 'index']);

Route::post('/webhooks', [WebhookController::class, 'store']);

Route::get('/webhooks/create', [WebhookController::class, 'create']);