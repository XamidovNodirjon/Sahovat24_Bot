<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Telegram\WebhookHandler;

class TelegramBotController extends Controller
{
    public function handle(WebhookHandler $webhookHandler)
    {
        // Butun logikani WebhookHandler servisiga o'tkazib yuboramiz
        $webhookHandler->handle();

        return response('OK', 200);
    }
}
