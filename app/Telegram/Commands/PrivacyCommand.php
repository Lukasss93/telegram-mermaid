<?php


namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class PrivacyCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: message('privacy'),
            parse_mode: ParseMode::HTML,
            disable_web_page_preview: true,
            reply_markup: new InlineKeyboardMarkup()
                ->addRow(new InlineKeyboardButton('Privacy Policy', config('bot.privacy'))),
        );

        stats('privacy', 'command');
    }
}
