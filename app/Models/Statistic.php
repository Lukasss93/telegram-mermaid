<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Statistic
 *
 * @property int $id
 * @property int|null $chat_id
 * @property string $action
 * @property array|null $value
 * @property string|null $category
 * @property Carbon $collected_at
 * @property-read Chat|null $chat
 * @method static Builder|Statistic newModelQuery()
 * @method static Builder|Statistic newQuery()
 * @method static Builder|Statistic query()
 * @method static Builder|Statistic whereAction($value)
 * @method static Builder|Statistic whereCategory($value)
 * @method static Builder|Statistic whereChatId($value)
 * @method static Builder|Statistic whereCollectedAt($value)
 * @method static Builder|Statistic whereId($value)
 * @method static Builder|Statistic whereValue($value)
 * @mixin Eloquent
 */
class Statistic extends Model
{
    public $timestamps = false;
    protected $dates = ['collected_at'];
    protected $casts = ['value' => 'array'];
    protected static $unguarded = true;

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }
}
