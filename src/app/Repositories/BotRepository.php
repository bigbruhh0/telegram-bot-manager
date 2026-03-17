<?php

namespace App\Repositories;

use App\Models\Bot;
use Illuminate\Database\Eloquent\Collection;

class BotRepository
{
    /**
     * Найти бота по ID
     */
    public function find(int $id): ?Bot
    {
        return Bot::find($id);
    }

    /**
     * Получить ботов пользователя
     */
    public function getUserBots(int $userId): Collection
    {
        return Bot::where('user_id', $userId)
            ->withCount('subscribers')
            ->latest()
            ->get();
    }

    /**
     * Найти бота по ID с проверкой владельца
     */
    public function findForUser(int $botId, int $userId): ?Bot
    {
        return Bot::where('id', $botId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Создать нового бота
     */
    public function create(array $data): Bot
    {
        return Bot::create($data);
    }

    /**
     * Удалить бота
     */
    public function delete(Bot $bot): bool
    {
        return $bot->delete();
    }

    /**
     * Получить бота по токену (для вебхуков)
     */
    public function findByToken(string $token): ?Bot
    {
        return Bot::where('token', $token)->first();
    }
}