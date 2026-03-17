<?php

namespace App\Services;

use App\Repositories\BotRepository;
use App\Repositories\SubscriberRepository;
use App\Models\Bot;

class BotManagementService extends BaseService
{
    public function __construct(
        protected BotRepository $botRepository,
        protected SubscriberRepository $subscriberRepository,
        protected TelegraphService $telegraphService
    ) {}

    /**
     * Создание нового бота
     */
    public function createBot(int $userId, array $data): array
    {
        $bot = $this->botRepository->create([
            'name' => $data['name'],
            'token' => $data['token'],
            'user_id' => $userId,
        ]);

        $webhookResult = $this->telegraphService->setupWebhook($bot);

        return [
            'success' => $webhookResult,
            'bot' => $bot,
            'message' => $webhookResult
                ? '✅ Бот добавлен и вебхук настроен!'
                : '⚠️ Бот добавлен, но не удалось установить вебхук.'
        ];
    }

    /**
     * Получить бота по ID с проверкой прав
     */
    public function getBotForUser(int $botId, int $userId): Bot
    {
        $bot = $this->botRepository->find($botId);
        
        $this->checkExists($bot, 'Бот не найден');
        $this->checkOwnership($bot->user_id, $userId, 'У вас нет прав для доступа к этому боту');
        
        return $bot;
    }

    /**
     * Удаление бота с проверкой прав
     */
    public function deleteBot(Bot $bot, int $userId): void
    {
        $this->checkOwnership($bot->user_id, $userId, 'У вас нет прав для удаления этого бота');
        $this->botRepository->delete($bot);
    }

    /**
     * Получение списка ботов пользователя
     */
    public function getUserBots(int $userId)
    {
        return $this->botRepository->getUserBots($userId);
    }

    /**
     * Получение подписчиков бота с проверкой прав
     */
    public function getBotSubscribers(Bot $bot, int $userId, int $perPage = 10)
    {
        $this->checkOwnership($bot->user_id, $userId, 'У вас нет прав для просмотра подписчиков этого бота');
        return $this->subscriberRepository->getByBot($bot->id, $perPage);
    }
}