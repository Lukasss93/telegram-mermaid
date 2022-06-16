<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;

class CollectChat
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $user = $bot->user();

        if ($user === null) {
            return;
        }

        $chatType = $bot->chat()?->type;
        if ($chatType === null && $bot->chosenInlineResult() !== null) {
            $chatType = 'private';
        }

        $chat = DB::transaction(function () use ($chatType, $user) {

            //save or update chat
            $chat = Chat::updateOrCreate([
                'chat_id' => $user->id,
            ], [
                'type' => $chatType,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'language_code' => $user->language_code,
                'blocked_at' => null,
            ]);

            if (!$chat->started_at && $chatType === 'private') {
                $chat->started_at = now();
                $chat->save();
            }

            return $chat;
        });

        $bot->setData(Chat::class, $chat);

        $next($bot);
    }
}
