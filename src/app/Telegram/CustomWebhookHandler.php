<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TelegraphService;
use App\Repositories\BotRepository;

class CustomWebhookHandler extends WebhookHandler
{
    protected TelegraphService $telegraphService;
    protected BotRepository $botRepository;

    public function __construct()
    {
        parent::__construct();
        $this->telegraphService = app(TelegraphService::class);
        $this->botRepository = app(BotRepository::class);
    }

    public function handle(Request $request, TelegraphBot $bot): void
    {
        try {
            $message = $request->input('message.text');
            $chatId = $request->input('message.chat.id');
            $from = $request->input('message.from');
            
            Log::info('📨 Webhook received', [
                'bot_id' => $bot->id,
                'chat_id' => $chatId,
                'text' => $message
            ]);

            $ourBot = $this->botRepository->findByToken($bot->token);
            
            if (!$ourBot) {
                Log::error('❌ Bot not found in database', ['token' => $bot->token]);
                return;
            }

            if ($message === '/start') {
                $this->telegraphService->handleStartCommand($ourBot, $from, $chatId);
            } elseif ($message === '/ping') {
                $this->telegraphService->handlePingCommand($ourBot, $chatId);
            } else {
                $this->telegraphService->handleUnknownCommand($ourBot, $chatId);
            }

        } catch (\Throwable $e) {
            Log::error('❌ Webhook handler error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}