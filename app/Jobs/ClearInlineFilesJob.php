<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class ClearInlineFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $chat_id;

    public function __construct(int $chat_id)
    {
        $this->chat_id = $chat_id;
    }

    public function handle(Nutgram $bot)
    {
        $key = "$this->chat_id-inline_files";
        $messageIDs = Redis::lrange($key, 0, -1);

        foreach ($messageIDs as $messageID) {
            try {
                $bot->deleteMessage($this->chat_id, $messageID);
            } catch (Throwable) {
                //ignore
            }

            //sleep for 500 milliseconds
            usleep(500 * 1000);
        }

        Redis::del($key);
    }
}
