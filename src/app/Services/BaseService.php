<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseService
{
    /**
     * Проверить, является ли пользователь владельцем ресурса
     * 
     * @param int $ownerId ID владельца ресурса
     * @param int $userId ID текущего пользователя
     * @param string $message Сообщение об ошибке
     * @throws HttpException
     */
    protected function checkOwnership(int $ownerId, int $userId, string $message = 'Доступ запрещен'): void
    {
        if ($ownerId !== $userId) {
            abort(403, $message);
        }
    }

    /**
     * Проверить, является ли пользователь владельцем ресурса (с возвратом bool)
     * 
     * @param int $ownerId ID владельца ресурса
     * @param int $userId ID текущего пользователя
     * @return bool
     */
    protected function isOwner(int $ownerId, int $userId): bool
    {
        return $ownerId === $userId;
    }

    /**
     * Проверить, авторизован ли пользователь
     * 
     * @throws HttpException
     */
    protected function checkAuth(string $message = 'Необходима авторизация'): void
    {
        if (!Auth::check()) {
            abort(401, $message);
        }
    }

    /**
     * Получить ID текущего пользователя
     * 
     * @return int|null
     */
    protected function getCurrentUserId(): ?int
    {
        return Auth::id();
    }

    /**
     * Проверить, что ресурс существует
     * 
     * @param mixed $resource
     * @param string $message
     * @throws HttpException
     */
    protected function checkExists($resource, string $message = 'Ресурс не найден'): void
    {
        if (!$resource) {
            abort(404, $message);
        }
    }

    /**
     * Проверить права и существование ресурса одной командой
     * 
     * @param mixed $resource
     * @param int $ownerId
     * @param int $userId
     * @param string $notFoundMessage
     * @param string $forbiddenMessage
     * @throws HttpException
     */
    protected function checkResourceAccess(
        $resource,
        int $ownerId,
        int $userId,
        string $notFoundMessage = 'Ресурс не найден',
        string $forbiddenMessage = 'Доступ запрещен'
    ): void {
        $this->checkExists($resource, $notFoundMessage);
        $this->checkOwnership($ownerId, $userId, $forbiddenMessage);
    }
}
