<?php

namespace App\Services\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Services\UserService;

class WebhookHandler
{
    protected $userService;
    protected $messageHandler;
    protected $callbackHandler;

    public function __construct(
        UserService $userService,
        MessageHandler $messageHandler,
        CallbackHandler $callbackHandler
    ) {
        $this->userService = $userService;
        $this->messageHandler = $messageHandler;
        $this->callbackHandler = $callbackHandler;
    }

    public function handle()
    {
        $update = Telegram::getWebhookUpdate();
        \Illuminate\Support\Facades\Log::info('Telegram Update Received: ', $update->toArray());
        
        if ($update->isType('message')) {
            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            
            // Get or create user
            $user = $this->userService->getOrCreateUser([
                'telegram_id' => $chatId,
                'first_name' => $message->getFrom()->getFirstName(),
                'last_name' => $message->getFrom()->getLastName(),
                'username' => $message->getFrom()->getUsername(),
            ]);

            // Pass to message handler
            $this->messageHandler->handle($message, $user);
        }
        
        if ($update->isType('callback_query')) {
            $callbackQuery = $update->getCallbackQuery();
            $chatId = $callbackQuery->getMessage()->getChat()->getId();
            
            \Illuminate\Support\Facades\Log::info('Handling Callback Query for Chat: ' . $chatId);

            // If the user somehow isn't found by ID, get or create them
            $user = $this->userService->getOrCreateUser([
                'telegram_id' => $chatId,
                'first_name' => $callbackQuery->getFrom()->getFirstName() ?? 'Unknown',
                'last_name' => $callbackQuery->getFrom()->getLastName(),
                'username' => $callbackQuery->getFrom()->getUsername(),
            ]);
            
            // Pass to callback handler
            $this->callbackHandler->handle($callbackQuery, $user);
        }
    }
}
