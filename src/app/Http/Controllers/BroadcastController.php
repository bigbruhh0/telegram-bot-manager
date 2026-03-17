<?php

namespace App\Http\Controllers;

use App\Services\BroadcastService;
use App\Http\Requests\BroadcastRequest;

class BroadcastController extends Controller
{
    public function __construct(
        protected BroadcastService $broadcastService
    ) {}

    /**
     * POST /bots/{id}/broadcast - отправка рассылки
     */
    public function send(BroadcastRequest $request, int $id)
    {
        $bot = $this->broadcastService->getBotForUser($id, auth()->id());

        $result = $this->broadcastService->broadcast($bot, auth()->id(), $request->validated()['message']);

        if ($request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return redirect()->back()
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
