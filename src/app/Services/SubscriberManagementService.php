<?php

namespace App\Services;

use App\Repositories\SubscriberRepository;

class SubscriberManagementService extends BaseService
{
    public function __construct(
        protected SubscriberRepository $subscriberRepository
    ) {}

    /**
     * Удаление подписчика с проверкой прав
     */
    public function deleteSubscriber(int $subscriberId, int $userId): void
    {
        $subscriber = $this->subscriberRepository->find($subscriberId);

        $this->checkResourceAccess(
            $subscriber,
            $subscriber?->bot->user_id,
            $userId,
            'Подписчик не найден',
            'У вас нет прав для удаления этого подписчика'
        );

        $this->subscriberRepository->delete($subscriber);
    }
}
