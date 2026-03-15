<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Показать дашборд пользователя со списком ботов
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Получаем всех ботов текущего пользователя
        $bots = auth()->user()->bots()->latest()->get();
        
        return view('dashboard', compact('bots'));
    }
}