<?php

namespace App\Services;

use App\Jobs\BroadcastMessageToSubscribers;
use App\Repositories\SubscriberRepository;
use App\Models\Bot;

class BroadcastService extends BaseService
{
    public function __construct(
        protected SubscriberRepository $subscriberRepository,
        protected BotManagementService $botManagementService
    ) {}

    /**
     * Получить бота по ID с проверкой прав
     */
    public function getBotForUser(int $botId, int $userId): Bot
    {
        return $this->botManagementService->getBotForUser($botId, $userId);
    }

    /**
     * Отправка рассылки с проверкой прав
     */
    public function broadcast(Bot $bot, int $userId, string $message): array
    {
        $this->checkOwnership($bot->user_id, $userId, 'У вас нет прав для рассылки от этого бота');

        $subscribersCount = $this->subscriberRepository->countByBot($bot->id);

        if ($subscribersCount === 0) {
            return [
                'success' => false,
                'message' => 'Нет подписчиков для рассылки'
            ];
        }

        BroadcastMessageToSubscribers::dispatch($bot, $message);

        return [
            'success' => true,
            'message' => "Рассылка запущена! Подписчиков: {$subscribersCount}"
        ];
    }
}