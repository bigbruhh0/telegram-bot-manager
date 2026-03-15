<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $bot_id
 * @property string $telegram_chat_id
 * @property string|null $telegram_user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Bot $bot
 * @property-read string $full_name
 */
class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'bot_id',
        'telegram_chat_id',
        'telegram_user_id',
        'first_name',
        'last_name',
        'username',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Бот, на которого подписан пользователь
     */
    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    /**
     * Полное имя подписчика
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: 'Без имени';
    }
}
