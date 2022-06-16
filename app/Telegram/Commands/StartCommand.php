<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class StartCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(message('start'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);

        stats('start', 'command');
    }
}
