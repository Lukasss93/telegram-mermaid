<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\ExceptionHandler;

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', StartCommand::class)->description('Help message');

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/
$bot->onApiError(ExceptionHandler::class);
$bot->onException(ExceptionHandler::class);
