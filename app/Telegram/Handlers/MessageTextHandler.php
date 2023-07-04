<?php

namespace App\Telegram\Handlers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use SergiX44\Nutgram\Telegram\Properties\ChatType;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Throwable;

class MessageTextHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $inputMessage = $bot->message();
        $text = $inputMessage?->text;
        $chatType = $inputMessage?->chat->type;

        //ignore commands
        if (str_starts_with($text, '/')) {
            return;
        }

        //requires a start tag for non-private chats
        if ($chatType !== ChatType::PRIVATE) {
            $tag = config('mermaid.tag');
            if (preg_match("/^$tag\\s(?<text>[\\s\\S]+)\$/", $text, $matches) === false) {
                return;
            }

            if (!isset($matches['text'])) {
                return;
            }

            $text = $matches['text'];
        }

        $loadingMessage = null;
        try {
            $loadingMessage = $bot->sendMessage(
                text: 'Loading...',
                disable_notification: true,
            );
            $bot->sendChatAction(ChatAction::UPLOAD_PHOTO);

            //get image
            $img = Http::baseUrl(config('mermaid.baseurl'))
                ->withBody($text, 'text/plain')
                ->post('render.jpg')
                ->throw()
                ->toPsrResponse()
                ->getBody()
                ->detach();

            //send image
            $bot->sendPhoto(
                photo: InputFile::make($img, Str::uuid().'.jpg'),
                reply_to_message_id: $inputMessage?->message_id,
                allow_sending_without_reply: true,
            );

            stats('sent.pm', 'diagram');
        } catch (RequestException $e) {
            $bot->sendMessage(
                text: $e->response->body(),
                reply_to_message_id: $inputMessage?->message_id,
                allow_sending_without_reply: true,
            );
        } catch (ConnectionException) {
            $bot->sendMessage(
                text: 'Unable to generate diagram. Retry later.',
                reply_to_message_id: $inputMessage?->message_id,
                allow_sending_without_reply: true,
            );
        } catch (Throwable $e) {
            report($e);

            $bot->sendMessage(
                text: 'Unknown error. Retry later.',
                reply_to_message_id: $inputMessage?->message_id,
                allow_sending_without_reply: true,
            );

            sendExceptionViaTelegram($e);
        } finally {
            $loadingMessage?->delete();
        }
    }
}
