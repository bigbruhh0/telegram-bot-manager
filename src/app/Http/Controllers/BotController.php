<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DefStudio\Telegraph\Telegraph;

class BotController extends Controller
{
    /**
     * Сохранить нового бота
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'required|string|unique:telegraph_bots,token',
        ]);

        $bot = auth()->user()->bots()->create([
            'name' => $validated['name'],
            'token' => $validated['token'],
        ]);

        try {
            $bot->registerWebhook()->send();

            return redirect()->route('dashboard')
                ->with('success', '✅ Бот добавлен и вебхук настроен на ' . env('APP_URL'));
        } catch (\Exception $e) {
            Log::error('Ошибка установки вебхука: ' . $e->getMessage());

            return redirect()->route('dashboard')
                ->with('warning', '⚠️ Бот добавлен, но вебхук не установлен: ' . $e->getMessage());
        }
    }
    /**
     * Показать страницу управления ботом
     */
    public function show(Bot $bot)
    {
        if ($bot->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этому боту');
        }

        $subscribers = $bot->subscribers()->latest()->paginate(10);

        return view('bots.show', compact('bot', 'subscribers'));
    }

    /**
     * Удалить бота
     */
    public function destroy(Bot $bot)
    {
        if ($bot->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этому боту');
        }

        $bot->delete();

        return redirect()->route('dashboard')
            ->with('success', '✅ Бот успешно удален');
    }
}
