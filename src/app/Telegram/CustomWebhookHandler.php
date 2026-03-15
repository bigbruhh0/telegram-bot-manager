<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscriber;

class CustomWebhookHandler extends WebhookHandler
{
    public function handle(Request $request, TelegraphBot $bot): void
    {
        try {
            $updateId = $request->input('update_id');
            $message = $request->input('message.text');
            $chatId = $request->input('message.chat.id');
            $from = $request->input('message.from');

            Log::info('📨 Webhook received', [
                'update_id' => $updateId,
                'bot_id' => $bot->id,
                'chat_id' => $chatId,
                'text' => $message
            ]);

            // Сохраняем ID бота для использования в других методах
            $this->bot = $bot;

            if ($message === '/start') {
                $this->handleStart($chatId, $from);
            } elseif ($message === '/ping') {
                $this->handlePing($chatId);
            } else {
                $this->handleUnknown($chatId);
            }
        } catch (\Throwable $e) {
            Log::error('❌ Webhook error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Обработка команды /start
     */
    private function handleStart($chatId, $from): void
    {
        try {
            Subscriber::updateOrCreate(
                [
                    'bot_id' => $this->bot->id,
                    'telegram_chat_id' => $chatId,
                ],
                [
                    'telegram_user_id' => $from['id'] ?? null,
                    'first_name' => $from['first_name'] ?? '',
                    'last_name' => $from['last_name'] ?? '',
                    'username' => $from['username'] ?? '',
                ]
            );

            Log::info('✅ New subscriber added', [
                'bot_id' => $this->bot->id,
                'chat_id' => $chatId
            ]);

            $this->sendMessage($chatId, "👋 *Привет!* \n\nТы успешно подписался на обновления этого бота.");
        } catch (\Exception $e) {
            Log::error('❌ Error in handleStart: ' . $e->getMessage());
        }
    }

    /**
     * Обработка команды /ping
     */
    private function handlePing($chatId): void
    {
        try {
            Log::info('🏓 Ping received', [
                'bot_id' => $this->bot->id,
                'chat_id' => $chatId
            ]);

            $this->sendMessage($chatId, "🏓 *Pong!*\n\nChat ID: `{$chatId}`");
        } catch (\Exception $e) {
            Log::error('❌ Error in handlePing: ' . $e->getMessage());
        }
    }

    /**
     * Обработка неизвестной команды
     */
    private function handleUnknown($chatId): void
    {
        try {
            Log::info('❓ Unknown command', [
                'bot_id' => $this->bot->id,
                'chat_id' => $chatId
            ]);

            $this->sendMessage(
                $chatId,
                "🤖 *Неизвестная команда*\n\n" .
                    "Используй:\n" .
                    "• `/start` - подписаться на рассылку\n" .
                    "• `/ping` - проверить работу бота"
            );
        } catch (\Exception $e) {
            Log::error('❌ Error in handleUnknown: ' . $e->getMessage());
        }
    }

    /**
     * Отправка сообщения через Telegram API
     */
    private function sendMessage($chatId, $text): void
    {
        try {
            // Находим или создаем чат для этого бота
            $chat = TelegraphChat::firstOrCreate(
                [
                    'chat_id' => $chatId,
                    'telegraph_bot_id' => $this->bot->id,
                ],
                [
                    'name' => "Chat $chatId",
                ]
            );

            $chat->markdown($text)->send();

            Log::info('✅ Message sent', [
                'chat_id' => $chatId,
                'bot_id' => $this->bot->id,
                'text_preview' => substr($text, 0, 30)
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error sending message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
