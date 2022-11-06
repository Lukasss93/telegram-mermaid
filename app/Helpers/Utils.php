<?php

use App\Models\Statistic;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

/**
 * Return a formatted string (in C# like)
 * @param string $string
 * @param array $args
 * @return string
 */
function f(string $string, array $args = []): string
{
    preg_match_all('/(?={){(\d+)}(?!})/', $string, $matches, PREG_OFFSET_CAPTURE);
    $offset = 0;
    foreach ($matches[1] as $data) {
        $i = $data[0];
        $string = substr_replace($string, @$args[$i], $offset + $data[1] - 1, 2 + strlen($i));
        $offset += strlen(@$args[$i]) - 2 - strlen($i);
    }

    return $string;
}

/**
 * Render an HTML message
 * @param string $view
 * @param array $values
 * @return string
 */
function message(string $view, array $values = []): string
{
    return rescue(static function () use ($view, $values) {
        return (string)Str::of(view("messages.$view", $values)->render())
            ->replaceMatches('/\r\n|\r|\n/', '')
            ->replace(['<br>', '<BR>'], "\n");
    }, 'messages.'.$view);
}

/**
 * Dump a message to dev chat
 * @param $message
 * @throws JsonException
 */
function dt($message): void
{
    if (is_iterable($message)) {
        $message = json_encode(
            $message,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }

    $bot = app(Nutgram::class);
    $bot->sendMessage("<b>Debug:</b>\n<pre>$message</pre>", [
        'chat_id' => config('owner.id'),
        'parse_mode' => ParseMode::HTML,
    ]);
}

/**
 * Cast a value
 * @param string $type
 * @param mixed $value
 * @param mixed|null $default
 * @return array|bool|float|int|object|string
 */
function cast(string $type, mixed $value, mixed $default = null): array|bool|float|int|object|string
{
    if ($value === '' || $value === null) {
        return $default;
    }

    return match ($type) {
        'int', 'integer' => (int)$value,
        'real', 'float', 'double' => (float)$value,
        'string' => (string)$value,
        'bool', 'boolean' => (bool)$value,
        'object' => (object)$value,
        'array' => (array)$value,
        default => $value,
    };
}

/**
 * Save bot statistic
 * @param string $action
 * @param string|null $category
 * @param array|null $value
 * @param int|null $chat_id
 */
function stats(string $action, string $category = null, array $value = null, int $chat_id = null): void
{
    Statistic::create([
        'action' => $action,
        'category' => $category,
        'value' => $value,
        'chat_id' => $chat_id ?? app(Nutgram::class)->userId(),
    ]);
}

function sendExceptionViaTelegram(Throwable $e): void
{
    $bot = app(Nutgram::class);
    $bot->sendMessage(message('exception', [
        'name' => last(explode('\\', $e::class)),
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => str_replace(base_path(), '', $e->getFile()),
    ]), [
        'chat_id' => config('owner.id'),
    ]);
}
