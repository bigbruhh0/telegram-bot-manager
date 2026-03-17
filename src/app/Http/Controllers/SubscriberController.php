<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Services\SubscriberManagementService;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function __construct(
        protected SubscriberManagementService $subscriberService
    ) {}

    public function destroy(Subscriber $subscriber)
    {
        if ($subscriber->bot->user_id !== auth()->id()) {
            abort(403);
        }

        $this->subscriberService->deleteSubscriber($subscriber->id, auth()->id());

        return redirect()->back()
            ->with('success', '✅ Подписчик удален');
    }
}
