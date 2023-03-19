<?php

namespace App\Http\Controllers\API\Telegram\Commands;

use App\Http\Controllers\API\YandexTranslate;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\FileUpload\InputFile;

class TranslateCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "ru_en";
    /**
     * @var string Command Description
     */
    protected $description = "Перевести текст с русского на английский.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        // Экземпляр telegram api
        $token = config('telegram.bots.mybot.token');
        $tg = new Api($token);

        //Получить объект сообщения
        $updates = $this->getUpdate();
        $question = str_replace('\/ru_en','', $updates->message->text);


        //Отправить думающий стикер
        $this->sendAction($tg, $updates);
        $thinking = $this->sendThinkSticker($tg, $updates);
        //Перевести текст
        $translation = (new YandexTranslate)->translate($question);

        //Удалить стикер
        $this->sendAction($tg, $updates);
        $this->deleteMessage($thinking, $tg);
        //Отправить переведенный текст
        $this->replyWithMessage($translation);

    }

    private function getThinkingPath(Api $tg): ?string
    {
        $pathFile =$tg->getFile([
            'file_id' => 'AAMCAgADGQEAAgZ6ZAzG7hAx0AeCIMWIcDc3WNzQR7cAAl8AA9vbfgABhLBUmxGuyuMBAAdtAAMvBA'
        ])->file_path;
        $token = env('TELEGRAM_BOT_TOKEN');
        return "https://api.telegram.org/file/bot$token/$pathFile";
    }

    private function sendThinkSticker($tg, $updates)
    {
        $stickerParams = [
            'chat_id' => $updates->message->chat->id,
            'sticker' => InputFile::create($this->getThinkingPath($tg)),
        ];

        if($updates->message->is_topic_message){
            $stickerParams['message_thread_id'] = $updates->message->message_thread_id;
        }

        return $tg->sendSticker($stickerParams);
    }

    private function sendAction($tg, $updates, $type = 'typing')
    {
        $actionParams = [
            'action' => $type,
            'chat_id' => $updates->message->chat->id,
        ];
        if ($updates->message->is_topic_message) {
            $actionParams['message_thread_id'] = $updates->message->message_thread_id;
        }
        $tg->sendChatAction($actionParams);
    }

    private function deleteMessage($thinking, $tg)
    {
        $answerParams = [
            'chat_id' => $thinking->chat->id,
            'message_id' => $thinking->messageId,
        ];

        if($thinking->is_topic_message){
            $answerParams['message_thread_id'] = $thinking->message_thread_id;
        }

        $tg->deleteMessage($answerParams);
    }
}
