<?php

namespace App\Jobs;

use App\Models\Bot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BroadcastMessageToSubscribers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Количество попыток выполнения
     */
    public $tries = 3;

    /**
     * Количество секунд между попытками
     */
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Bot $bot,
        protected string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = $this->bot->subscribers;
        
        if ($subscribers->isEmpty()) {
            Log::info("📭 Нет подписчиков для бота #{$this->bot->id}");
            return;
        }

        $successCount = 0;
        $failCount = 0;
        $total = $subscribers->count();

        Log::info("🚀 Начинаем рассылку для бота #{$this->bot->id}", [
            'total' => $total,
            'message_preview' => substr($this->message, 0, 50)
        ]);

        $token = $this->bot->token;

        foreach ($subscribers as $subscriber) {
            try {
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $subscriber->telegram_chat_id,
                    'text' => $this->message,
                    'parse_mode' => 'Markdown'
                ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failCount++;
                    Log::warning("⚠️ Не удалось отправить подписчику #{$subscriber->id}", [
                        'response' => $response->body()
                    ]);
                }

                usleep(200000); // задержка для Telegram API

            } catch (\Exception $e) {
                $failCount++;
                Log::error("❌ Ошибка отправки подписчику #{$subscriber->id}: " . $e->getMessage());
            }
        }

        Log::info("✅ Рассылка завершена", [
            'bot_id' => $this->bot->id,
            'success' => $successCount,
            'fail' => $failCount,
            'total' => $total
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("💥 Job рассылки провалился", [
            'bot_id' => $this->bot->id,
            'error' => $e->getMessage()
        ]);
    }
}