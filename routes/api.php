<?php

use App\Http\Controllers\API\Telegram\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'tg'], function () {
    Route::get('getMe', [TelegramController::class, 'getMe']);
    Route::get('getCommands', [TelegramController::class, 'getCommands']);
    Route::get('getUpdates', [TelegramController::class, 'getUpdates']);
    Route::get('sendMessage/{chatId}/{message}', [TelegramController::class, 'sendMessage']);
    Route::get('sendExampleMessage/{chatId}', [TelegramController::class, 'sendExampleMessage']);

    Route::post('setWebhook', [TelegramController::class, 'setWebhook']);
});

Route::post('/webhook', [TelegramController::class, 'getWebhookUpdates']);

