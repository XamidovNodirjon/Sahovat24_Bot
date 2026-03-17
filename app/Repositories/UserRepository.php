<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Find or create user by telegram ID
     */
    public function findOrCreate(array $userData): User
    {
        return User::updateOrCreate(
            ['telegram_id' => $userData['telegram_id']],
            $userData
        );
    }

    /**
     * Update user step
     */
    public function updateStep(int $telegramId, string $step): void
    {
        User::where('telegram_id', $telegramId)->update(['step' => $step]);
    }

    /**
     * Update user phone number
     */
    public function updatePhoneNumber(int $telegramId, string $phoneNumber): void
    {
        User::where('telegram_id', $telegramId)->update(['phone_number' => $phoneNumber]);
    }

    /**
     * Set user language
     */
    public function setLanguage(int $telegramId, string $language): void
    {
        User::where('telegram_id', $telegramId)->update(['language' => $language]);
    }

    /**
     * Set temporary browse category
     */
    public function setBrowseCategory(int $telegramId, ?int $categoryId): void
    {
        User::where('telegram_id', $telegramId)->update(['browse_category_id' => $categoryId]);
    }
}
