<?php

namespace App\Telegram\Handlers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatActions;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Throwable;

class MessageTextHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $inputMessage=$bot->message();
        $text = $inputMessage?->text;
        $chatType = $inputMessage?->chat->type;

        //ignore commands
        if (str_starts_with($text, '/')) {
            return;
        }

        //requires a start tag for non-private chats
        if ($chatType !== 'private') {
            $tag = config('mermaid.tag');
            if(preg_match("/^$tag\\s(?<text>[\\s\\S]+)\$/", $text, $matches)===false) {
                return;
            }

            if(!isset($matches['text'])) {
                return;
            }

            $text = $matches['text'];
        }

        $loadingMessage = null;
        try {
            $loadingMessage = $bot->sendMessage('Loading...', [
                'disable_notification' => true,
            ]);
            $bot->sendChatAction(ChatActions::UPLOAD_PHOTO);

            //get image
            $img = Http::baseUrl(config('mermaid.baseurl'))
                ->withBody($text, 'text/plain')
                ->post(config('mermaid.endpoint'))
                ->throw()
                ->toPsrResponse()
                ->getBody()
                ->detach();

            //send image
            $bot->sendPhoto(InputFile::make($img, Str::uuid() . '.jpg'),[
                'allow_sending_without_reply' => true,
                'reply_to_message_id' => $inputMessage?->message_id,
            ]);
        } catch (RequestException $e) {
            $bot->sendMessage($e->response->body(),[
                'allow_sending_without_reply' => true,
                'reply_to_message_id' => $inputMessage?->message_id,
            ]);
        } catch (ConnectionException) {
            $bot->sendMessage('Unable to generate diagram. Retry later.',[
                'allow_sending_without_reply' => true,
                'reply_to_message_id' => $inputMessage?->message_id,
            ]);
        } catch (Throwable $e) {
            report($e);

            $bot->sendMessage('Unknown error. Retry later.',[
                'allow_sending_without_reply' => true,
                'reply_to_message_id' => $inputMessage?->message_id,
            ]);

            sendExceptionViaTelegram($e);
        } finally {
            $loadingMessage?->delete();
        }
    }
}
