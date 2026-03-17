<?php

namespace App\Http\Controllers;

use App\Services\BotManagementService;

class DashboardController extends Controller
{
    public function __construct(
        protected BotManagementService $botService
    ) {}

    public function index()
    {
        $bots = $this->botService->getUserBots(auth()->id());

        return view('dashboard', compact('bots'));
    }
}
