<?php

namespace App\Services;

use App\Models\Bot;
use App\Repositories\SubscriberRepository;
use DefStudio\Telegraph\Telegraph;
use Illuminate\Support\Facades\Log;

class TelegraphService
{
    public function __construct(
        protected SubscriberRepository $subscriberRepository
    ) {}

    /**
     * Установить вебхук для бота
     */
    public function setupWebhook(Bot $bot): bool
    {
        try {
            $bot->registerWebhook()->send();
            Log::info('✅ Webhook установлен', ['bot_id' => $bot->id]);
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Ошибка установки вебхука', [
                'bot_id' => $bot->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Обработать команду /start
     */
    public function handleStartCommand(Bot $bot, array $from, string $chatId): void
    {
        $this->subscriberRepository->updateOrCreate(
            $bot->id,
            $chatId,
            [
                'telegram_user_id' => $from['id'] ?? null,
                'first_name' => $from['first_name'] ?? '',
                'last_name' => $from['last_name'] ?? '',
                'username' => $from['username'] ?? '',
            ]
        );

        $this->sendMessage(
            $bot,
            $chatId,
            "👋 *Привет!* \n\nТы успешно подписался на обновления этого бота."
        );
    }

    /**
     * Обработать команду /ping
     */
    public function handlePingCommand(Bot $bot, string $chatId): void
    {
        $this->sendMessage($bot, $chatId, "🏓 *Pong!*\n\nChat ID: `{$chatId}`");
    }

    /**
     * Обработать неизвестную команду
     */
    public function handleUnknownCommand(Bot $bot, string $chatId): void
    {
        $this->sendMessage(
            $bot,
            $chatId,
            "🤖 *Неизвестная команда*\n\n" .
                "Используй:\n" .
                "• `/start` - подписаться на рассылку\n" .
                "• `/ping` - проверить работу бота"
        );
    }

    /**
     * Отправить сообщение через Telegraph
     */
    public function sendMessage(Bot $bot, string $chatId, string $text): bool
    {
        try {
            $telegraphBot = \DefStudio\Telegraph\Models\TelegraphBot::find($bot->id);

            if (!$telegraphBot) {
                Log::error('❌ TelegraphBot not found', ['bot_id' => $bot->id]);
                return false;
            }

            $chat = $telegraphBot->chats()->where('chat_id', $chatId)->first();

            if (!$chat) {
                try {
                    $chat = $telegraphBot->chats()->firstOrCreate(
                        ['chat_id' => $chatId],
                        ['name' => "Chat {$chatId}"]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to create chat', ['error' => $e->getMessage()]);
                    return false;
                }
            }

            $chat->markdown($text)->send();

            Log::info('✅ Message sent via Telegraph', [
                'bot_id' => $bot->id,
                'chat_id' => $chatId
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error sending message via Telegraph', [
                'bot_id' => $bot->id,
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
