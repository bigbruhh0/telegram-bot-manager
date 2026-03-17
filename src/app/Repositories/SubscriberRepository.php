<?php

namespace App\Repositories;

use App\Models\Subscriber;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SubscriberRepository
{
    /**
     * Найти подписчика по ID
     */
    public function find(int $id): ?Subscriber
    {
        return Subscriber::find($id);
    }

    /**
     * Получить подписчиков бота с пагинацией
     */
    public function getByBot(int $botId, int $perPage = 10): LengthAwarePaginator
    {
        return Subscriber::where('bot_id', $botId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Создать или обновить подписчика
     */
    public function updateOrCreate(int $botId, string $chatId, array $data): Subscriber
    {
        return Subscriber::updateOrCreate(
            [
                'bot_id' => $botId,
                'telegram_chat_id' => $chatId,
            ],
            $data
        );
    }

    /**
     * Удалить подписчика
     */
    public function delete(Subscriber $subscriber): bool
    {
        return $subscriber->delete();
    }

    /**
     * Получить количество подписчиков бота
     */
    public function countByBot(int $botId): int
    {
        return Subscriber::where('bot_id', $botId)->count();
    }

    /**
     * Получить всех подписчиков бота (для рассылки)
     */
    public function getAllByBot(int $botId): Collection
    {
        return Subscriber::where('bot_id', $botId)->get();
    }
}
