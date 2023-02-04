<?php

namespace App\Http\Controllers\API\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $tg;
    protected $token;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->tg = new Api($this->token);
    }

    public function getMe(): object
    {
        return $this->tg->getMe();
    }

    public function getCommands(): array
    {
        return $this->tg->getCommands();
    }

    public function sendMessage(int|string $chatId, string $message): object
    {
        /*
        * $params = [
        *       'chat_id'                     => '',  // int|string - Required. Unique identifier for the target chat or username of the target channel (in the format "@channelusername")
        *       'text'                        => '',  // string     - Required. Text of the message to be sent
        *       'parse_mode'                  => '',  // string     - (Optional). Send Markdown or HTML, if you want Telegram apps to show bold, italic, fixed-width text or inline URLs in your bot's message.
        *       'entities'                    => '',  // array      - (Optional). List of special entities that appear in the caption, which can be specified instead of parse_mode
        *       'disable_web_page_preview'    => '',  // bool       - (Optional). Disables link previews for links in this message
        *       'protect_content'             => '',  // bool       - (Optional). Protects the contents of the sent message from forwarding and saving
        *       'disable_notification'        => '',  // bool       - (Optional). Sends the message silently. iOS users will not receive a notification, Android users will receive a notification with no sound.
        *       'reply_to_message_id'         => '',  // int        - (Optional). If the message is a reply, ID of the original message
        *       'allow_sending_without_reply' => '',  // bool       - (Optional). Pass True, if the message should be sent even if the specified replied-to message is not found
        *       'reply_markup'                => '',  // object     - (Optional). One of either InlineKeyboardMarkup|ReplyKeyboardMarkup|ReplyKeyboardRemove|ForceReply for an inline keyboard, custom reply keyboard, instructions to remove reply keyboard or to force a reply from the user.
        * ]*/
        //618997696
        $message = $this->tg->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,

        ]);
        return $message;
    }

    public function sendExampleMessage(int|string $chatId): Response
    {
        $example = '
        <b>bold</b>
        <i>italic</i>
        <u>underline</u>
        <s>strikethrough</s>
        <tg-spoiler>spoiler</tg-spoiler>
        <b>bold <i>italic bold <s>italic bold strikethrough <span class="tg-spoiler">italic bold strikethrough spoiler</span></s> <u>underline italic bold</u></i> bold</b>
        <a href="http://www.example.com/">inline URL</a>
        <code>inline fixed-width code</code>
        <pre>pre-formatted fixed-width code block</pre>
        ';
        $message = $this->tg->sendMessage([
            'chat_id' => $chatId,
            'text' => $example,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,

        ]);
        return response($message);
    }

    public function getUpdates(): bool|string
    {
        $updates = $this->tg->getUpdates();
        return (json_encode($updates));
    }

    public function setWebhook(): string
    {
         return $this->tg->setWebhook(['url' => 'https://telegram-api.bankai.fun/'.$this->token.'/webhook']);
    }

    public function getWebhookUpdates()
    {
        return $this->tg->getWebhookUpdate();
    }


}
