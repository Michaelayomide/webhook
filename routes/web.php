<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/webhooks', [WebhookController::class, 'index']);

Route::post('/webhooks', [WebhookController::class, 'store']);

Route::get('/webhooks/create', [WebhookController::class, 'create']);

Route::get('/webhooks/{webhook}/edit',[WebhookController::class,'edit']);

Route::put('/webhooks/{webhook}',[WebhookController::class,'update']);