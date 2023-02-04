<?php

namespace App\Http\Controllers\API\Telegram\WebHook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebHookController extends Controller
{
    public function index(Request $request): void
    {
        Log::debug($request->all());
    }
}
