<?php

namespace App\Http\Controllers;

use App\Services\BotManagementService;
use App\Http\Requests\StoreBotRequest;

class BotController extends Controller
{
    public function __construct(
        protected BotManagementService $botService
    ) {}

    /**
     * POST /bots - создание бота
     */
    public function store(StoreBotRequest $request)
    {
        $result = $this->botService->createBot(
            auth()->id(),
            $request->validated()
        );

        return redirect()->route('dashboard')
            ->with($result['success'] ? 'success' : 'warning', $result['message']);
    }

    /**
     * GET /bots/{id} - страница управления ботом (безопасно: принимает ID)
     */
    public function show(int $id)
    {
        $bot = $this->botService->getBotForUser($id, auth()->id());
        $subscribers = $this->botService->getBotSubscribers($bot, auth()->id());

        return view('bots.show', compact('bot', 'subscribers'));
    }

    /**
     * DELETE /bots/{id} - удаление бота (безопасно: принимает ID)
     */
    public function destroy(int $id)
    {
        $bot = $this->botService->getBotForUser($id, auth()->id());
        $this->botService->deleteBot($bot, auth()->id());

        return redirect()->route('dashboard')
            ->with('success', '✅ Бот удален');
    }
}