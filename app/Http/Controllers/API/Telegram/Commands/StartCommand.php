<?php

namespace App\Http\Controllers\API\Telegram\Commands;

use Faker\Core\File;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\FileUpload\InputFile;

class StartCommand extends Command
{

    /**
     * @var string Command Name
     */
    protected $name = "вопрос";
    /**
     * @var string Command Description
     */
    protected $description = "Задай вопрос, а я на него отвечу.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
        $token = env('TELEGRAM_BOT_TOKEN');
        $tg = new Api($token);

        //Получить объект сообщения
        $updates = $this->getUpdate();
        $question = str_replace('\/вопрос','', $updates->message->text);

        $this->sendAction($tg, $updates);
        $thinking = $this->sendThinkSticker($tg, $updates);

        $this->sendAction($tg, $updates);
        sleep(4);
        $this->deleteMessage($thinking, $tg);


        /*
         * 1) Сценарий команда вопрос после которой идет сам вопрос
         * 2) Получить кекст вопроса и id чата куда отправлять
         * 3) Ответить думающим стикером и получить Id думающего стикера, чтобы заменить его на ответ потом
         * 4) Как придет ответ заменить стикер
        */

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        //$this->triggerCommand('subscribe');
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
