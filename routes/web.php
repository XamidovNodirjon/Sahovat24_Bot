<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    if (request()->has('lang')) {
        $lang = request()->get('lang');
        if (in_array($lang, ['uz', 'ru', 'en'])) {
            session(['locale' => $lang]);
        }
    }

    app()->setLocale(session('locale', config('app.locale')));

    return view('welcome');
});

Route::get('setwebhook', function () {
    // config('app.url') avtomatik ravishda .env dagi APP_URL ni oladi
    $url = config('app.url') . '/api/telegram/webhook';

    $response = Telegram::setWebhook(['url' => $url]);

    return $response; // Natijani ekranda ko'rish uchun
});
