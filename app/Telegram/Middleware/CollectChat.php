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

        $chatType = $bot->chat()?->type->value ?? 'private';

        //collect groups/channels
        if($chatType !== 'private') {
            Chat::updateOrCreate([
                'chat_id' => $bot->chat()->id,
            ], [
                'type' => $bot->chat()->type,
                'first_name' => $bot->chat()->title ?? '',
                'username' => $bot->chat()->username,
            ]);
        }

        //collect users
        $chat = DB::transaction(function () use ($chatType, $user) {

            //save or update chat
            $chat = Chat::updateOrCreate([
                'chat_id' => $user->id,
            ], [
                'type' => 'private',
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'language_code' => $user->language_code,
            ]);

            if (!$chat->started_at && $chatType === 'private') {
                $chat->started_at = now();
                $chat->save();
            }

            return $chat;
        });

        $bot->set(Chat::class, $chat);

        $next($bot);
    }
}
