<?php

namespace App\Telegram\Handlers;

use App\Exceptions\EmptyTextException;
use App\Exceptions\TooLongTextException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;

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

            if (mb_strlen($text) > config('mermaid.inline.max_chars')) {
                throw new TooLongTextException(sprintf(
                    "The text cannot be longer than %s characters.",
                    config('mermaid.inline.max_chars')
                ));
            }

            //generate image and get response
            $response = Http::baseUrl(config('mermaid.baseurl'))
                ->withBody($text, 'text/plain')
                ->post('render.jpg')
                ->throw()
                ->toPsrResponse();

            //get headers
            $hash = $response->getHeader('X-Hash')[0];
            $width = (int)$response->getHeader('X-Width')[0];
            $height = (int)$response->getHeader('X-Height')[0];

            //build cached image url
            $url = sprintf("%scached/%s", config('mermaid.baseurl'), $hash);
            echo $url."\n\n";

            //send message by file_id
            $bot->answerInlineQuery([
                [
                    'type' => 'photo',
                    'id' => md5($text),
                    'photo_url' => $url,
                    'thumb_url' => $url,
                    'photo_width' => $width,
                    'photo_height' => $height,
                ],
            ], [
                'switch_pm_text' => sprintf("Max %s chars. Use the PM to ignore limit.", config('mermaid.inline.max_chars')),
                'switch_pm_parameter' => 'MAX_TEXT',
                'cache_time' => config('mermaid.inline.cache_time'),
            ]);

        } catch (RequestException $e) {
            $message = $e->response->body();
            Cache::set("$chat_id-inline_error", $message);

            $bot->answerInlineQuery([], [
                'switch_pm_text' => 'Invalid text. Click here for more info.',
                'switch_pm_parameter' => 'INVALID_TEXT',
                'cache_time' => config('mermaid.inline.cache_time'),
            ]);
        } catch (EmptyTextException|TooLongTextException) {
            $bot->answerInlineQuery([], [
                'switch_pm_text' => sprintf("Max %s chars. Use the PM to ignore limit.", config('mermaid.inline.max_chars')),
                'switch_pm_parameter' => 'MAX_TEXT',
                'cache_time' => config('mermaid.inline.cache_time'),
            ]);
        }
    }

    public function onChosenInlineResult(Nutgram $bot): void
    {
        stats('sent.inline', 'diagram');
    }

    public function onInvalidInlineText(Nutgram $bot): void
    {
        $chat_id = $bot->userId();
        $bot->sendMessage(Cache::get("$chat_id-inline_error"));
    }
}
