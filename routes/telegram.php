<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\PrivacyCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\ExceptionHandler;

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', StartCommand::class)->description('Help message');
$bot->onCommand('about', AboutCommand::class)->description('About the bot');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/
$bot->onApiError(ExceptionHandler::class);
$bot->onException(ExceptionHandler::class);
