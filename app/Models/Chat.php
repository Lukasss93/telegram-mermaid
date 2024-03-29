<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use SergiX44\Nutgram\Telegram\Types\User\User;

/**
 * App\Models\Chat
 *
 * @property int $chat_id
 * @property string $type
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language_code
 * @property Carbon|null $started_at
 * @property Carbon|null $blocked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @method static Builder|Chat whereBlockedAt($value)
 * @method static Builder|Chat whereChatId($value)
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereFirstName($value)
 * @method static Builder|Chat whereLanguageCode($value)
 * @method static Builder|Chat whereLastName($value)
 * @method static Builder|Chat whereStartedAt($value)
 * @method static Builder|Chat whereType($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 * @method static Builder|Chat whereUsername($value)
 * @mixin Eloquent
 */
class Chat extends Model
{
    protected $primaryKey = 'chat_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected static $unguarded = true;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'blocked_at' => 'datetime',
        ];
    }

    public static function findFromUser(?User $user): ?Chat
    {
        if ($user === null) {
            return null;
        }

        $chat = self::find($user->id);

        return $chat ?? null;
    }
}
