<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Удалить подписчика
     *
     * @param Subscriber $subscriber
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Subscriber $subscriber)
    {
        // Проверяем, что подписчик принадлежит боту текущего пользователя
        if ($subscriber->bot->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этому подписчику');
        }
        
        $subscriber->delete();
        
        return redirect()->back()
            ->with('success', 'Подписчик успешно удален');
    }
}