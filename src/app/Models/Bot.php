<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot as BaseTelegraphBot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends BaseTelegraphBot
{
    protected $table = 'telegraph_bots';

    protected $fillable = [
        'token',
        'name',
        'user_id',
    ];

    /**
     * Пользователь-владелец бота
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Подписчики бота
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class, 'bot_id');
    }
}
