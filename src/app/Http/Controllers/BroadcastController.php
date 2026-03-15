<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Jobs\BroadcastMessageToSubscribers;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    /**
     * Отправить рассылку всем подписчикам
     *
     * @param Request $request
     * @param Bot $bot
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request, Bot $bot)
    {
        // Проверяем, что бот принадлежит текущему пользователю
        if ($bot->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этому боту');
        }
        
        $validated = $request->validate([
            'message' => 'required|string|max:4096',
        ]);

        if ($bot->subscribers()->count() === 0) {
            return redirect()->back()
                ->with('error', 'Нет подписчиков для рассылки');
        }

        // Отправляем задачу в очередь
        BroadcastMessageToSubscribers::dispatch($bot, $validated['message']);

        return redirect()->back()
            ->with('success', 'Рассылка запущена! Сообщения будут отправлены подписчикам в фоновом режиме.');
    }
}