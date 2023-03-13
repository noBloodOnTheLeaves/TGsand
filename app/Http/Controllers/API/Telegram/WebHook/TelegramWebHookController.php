<?php

namespace App\Http\Controllers\API\Telegram\WebHook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebHookController extends Controller
{
    public function index(Request $request)
    {
        Telegram::commandsHandler(true);
        Log::debug($request->all());
        return 'ok';
    }
}
