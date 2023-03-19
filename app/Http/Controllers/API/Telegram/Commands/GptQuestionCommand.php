<?php

namespace App\Http\Controllers\API\Telegram\Commands;

class GptQuestionCommand extends \Telegram\Bot\Commands\Command
{

    /**
     * @var string Command Name
     */
    protected $name = 'question';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['question'];

    /**
     * @var string Command Description
     */
    protected $description = 'Ответить на заданный вопрос.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->replyWithMessage("В разработке 🫠");
    }
}
