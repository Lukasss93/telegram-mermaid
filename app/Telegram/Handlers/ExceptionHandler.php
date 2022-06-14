<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use Throwable;

class ExceptionHandler
{
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
        report($e);

        sendExceptionViaTelegram($e);
    }
}
