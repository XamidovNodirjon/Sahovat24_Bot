<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getOrCreateUser(array $data): User
    {
        return $this->userRepository->findOrCreate($data);
    }

    public function getUser(int $telegramId): ?User
    {
        return User::where('telegram_id', $telegramId)->first();
    }

    public function setStep(int $telegramId, string $step)
    {
        $this->userRepository->updateStep($telegramId, $step);
    }

    public function setLanguage(int $telegramId, string $language)
    {
        $this->userRepository->setLanguage($telegramId, $language);
    }

    public function setPhoneNumber(int $telegramId, string $phoneNumber)
    {
        $this->userRepository->updatePhoneNumber($telegramId, $phoneNumber);
    }

    public function setBrowseCategory(int $telegramId, ?int $categoryId)
    {
        $this->userRepository->setBrowseCategory($telegramId, $categoryId);
    }
}
