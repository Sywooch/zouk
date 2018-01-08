<?php

namespace frontend\controllers;

use common\components\TelegramBotComponent;
use TelegramBot\Api\Types\Message;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Telegram controller
 */
class TelegramController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['init'],
                        'allow'   => true,
                    ],
                ],
            ],
        ];
    }


    public function actionInit()
    {
        /** @var TelegramBotComponent $telegramBot */
        $telegramBot = Yii::$app->telegram;
        $bot = $telegramBot->getBot();

        $bot->command('start',  function (Message $message) use ($bot, $telegramBot) {
            $answer = 'Привет! Добро пожаловать в ProZouk бот.';
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->run();
    }

}