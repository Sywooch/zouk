<?php

namespace frontend\controllers;

use common\components\TelegramBotComponent;
use common\models\Video;
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

    public function beforeAction($action)
    {
        if (in_array($action->id, ['init'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
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

        $bot->command('help',  function (Message $message) use ($bot, $telegramBot) {
            $answer = 'Привет! Добро пожаловать в ProZouk бот.';
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });


        $bot->command('randomVideo',  function (Message $message) use ($bot, $telegramBot) {
            $video = Video::getRandomVideo([]);
            $answer = $video->original_url;
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });


        return $bot->run();
    }

}