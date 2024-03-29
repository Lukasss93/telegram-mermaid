<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\CancelCommand;
use App\Telegram\Commands\PrivacyCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\StatsCommand;
use App\Telegram\Conversations\FeedbackConversation;
use App\Telegram\Handlers\ExceptionHandler;
use App\Telegram\Handlers\InlineQueryHandler;
use App\Telegram\Handlers\MessageTextHandler;
use App\Telegram\Handlers\UpdateChatStatusHandler;
use App\Telegram\Middleware\CheckMaintenance;
use App\Telegram\Middleware\CollectChat;

/*
|--------------------------------------------------------------------------
| Global middlewares
|--------------------------------------------------------------------------
*/

$bot->middleware(CollectChat::class);
$bot->middleware(CheckMaintenance::class);

/*
|--------------------------------------------------------------------------
| Bot commands
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', StartCommand::class)->description('Welcome message');
$bot->onCommand('help', StartCommand::class)->description('Help message');
$bot->onCommand('about', AboutCommand::class)->description('About the bot');
$bot->onCommand('privacy', PrivacyCommand::class)->description('Privacy Policy');
$bot->onCommand('stats', StatsCommand::class)->description('Show bot statistics');
$bot->onCommand('feedback', FeedbackConversation::class)->description('Send a feedback about the bot');
$bot->onCommand('cancel', CancelCommand::class)->description('Close a conversation or a keyboard');

/*
|--------------------------------------------------------------------------
| Bot handlers
|--------------------------------------------------------------------------
*/
$bot->onMyChatMember(UpdateChatStatusHandler::class);
$bot->onText('.*', MessageTextHandler::class);
$bot->onInlineQuery([InlineQueryHandler::class, 'onInlineQuery']);
$bot->onChosenInlineResult([InlineQueryHandler::class, 'onChosenInlineResult']);
$bot->onCommand('start INVALID_TEXT', [InlineQueryHandler::class, 'onInvalidInlineText']);
$bot->onCommand('start MAX_TEXT', StartCommand::class);

/*
|--------------------------------------------------------------------------
| Exception handlers
|--------------------------------------------------------------------------
*/

$bot->onApiError(ExceptionHandler::class);
$bot->onException(ExceptionHandler::class);
