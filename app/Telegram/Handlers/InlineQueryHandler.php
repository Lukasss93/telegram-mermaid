<?php

namespace App\Telegram\Handlers;

use App\Exceptions\EmptyTextException;
use App\Jobs\ClearInlineFilesJob;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class InlineQueryHandler
{
    public function onInlineQuery(Nutgram $bot): void
    {
        $text = Str::of($bot->inlineQuery()->query)->trim();
        $chat_id = $bot->inlineQuery()->from->id;

        try {
            if ($text->isEmpty()) {
                throw new EmptyTextException('The text cannot be empty.');
            }

            //get md5
            $imageID = md5($text);

            //get image
            $img = Http::baseUrl(config('mermaid.baseurl'))
                ->withBody($text, 'text/plain')
                ->post(config('mermaid.endpoint'))
                ->throw()
                ->toPsrResponse()
                ->getBody()
                ->detach();

            //send image to PM
            $message = $bot->sendPhoto(InputFile::make($img, $imageID.'.jpg'), [
                'chat_id' => $chat_id,
                'disable_notification' => true,
            ]);

            //remember message
            Redis::lpush("$chat_id-inline_files", $message->message_id);

            //send message by file_id
            $bot->answerInlineQuery([
                [
                    'type' => 'photo',
                    'id' => $imageID,
                    'photo_file_id' => $message->photo[0]->file_id,
                ],
            ], [
                'switch_pm_text' => 'Max 256 chars. Use the PM to ignore limit.',
                'switch_pm_parameter' => 'MAX_TEXT',
                'cache_time' => 60 * 60 * 24,
            ]);

        } catch (RequestException $e) {
            $message = $e->response->body();
            Redis::set("$chat_id-inline_error", $message);

            $bot->answerInlineQuery([], [
                'switch_pm_text' => 'Invalid text. Click here for more info.',
                'switch_pm_parameter' => 'INVALID_TEXT',
                'cache_time' => 60 * 60 * 24,
            ]);
        } catch (EmptyTextException) {
            $bot->answerInlineQuery([], [
                'switch_pm_text' => 'Max 256 chars. Use the PM to ignore limit.',
                'switch_pm_parameter' => 'MAX_TEXT',
                'cache_time' => 60 * 60 * 24,
            ]);
        }
    }

    public function onChosenInlineResult(Nutgram $bot): void
    {
        stats('sent.inline', 'diagram');

        //delete images
        if (config('bot.clear_inline_files')) {
            $chat_id = $bot->chosenInlineResult()->from->id;
            ClearInlineFilesJob::dispatch($chat_id);
        }
    }

    public function onInvalidInlineText(Nutgram $bot): void
    {
        $chat_id = $bot->userId();
        $bot->sendMessage(Redis::get("$chat_id-inline_error"));
    }
}
