<?php

namespace frontend\controllers;

use common\components\TelegramBotComponent;
use common\models\EntityLink;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\Video;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use Yii;
use yii\db\Expression;
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
        $bot = $telegramBot->getBot('prozouk');

        $bot->on(function (Update $update) use ($bot, $telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $user = $message->getFrom();
                $chat = $message->getChat();
                if (!$user->isBot() && $chat->getType() == 'private') {
                    $mtext = $message->getText();
                    Yii::info($mtext, 'telegram');
                    $cid = $message->getChat()->getId();
                    $commands = explode(' ', $mtext, 2);
                    $command = $commands[0] ?? '';
                    $paramStr = trim($commands[1] ?? '');
                    if (in_array($command, ['/randomVideo', '/randomvideo', '/случайное-видео', '/случайноевидео'])) {
                        $telegramBot->messageRandomVideo($update, $paramStr);
                    } elseif (in_array($command, ['/demo', '/демо'])) {
                        $telegramBot->messageRandomVideo($update, $paramStr, 'demo');
                    } elseif (in_array($command, ['/show', '/шоу'])) {
                        $telegramBot->messageRandomVideo($update, $paramStr, 'show');
                    } elseif (in_array($command, ['/article', '/статья'])) {
                        $telegramBot->messageRandomItem($update, $paramStr, 'article');
                    } elseif (in_array($command, ['/events', '/события'])) {
                        $telegramBot->messageEventAfter($update, $paramStr);
                    }

                    if ($mtext == '/start') {
                        $answer =
                            "Привет! Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья

/events - ближайшие события
";
                        $telegramBot->sendMessage($message->getChat()->getId(), $answer);
                    } elseif ($mtext == '/help') {
                        $answer =
                            "Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья

/events - ближайшие события
";
                        $telegramBot->sendMessage($message->getChat()->getId(), $answer);
                    }
                }
            }

        }, function(Update $update) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $user = $message->getFrom();
                $chat = $message->getChat();
                if ($user->isBot()) {
                    return false;
                }
                if ($chat->getType() != 'private') {
                    return false;
                }
                return true;
            }
            return false;
        });

        return $bot->run();
    }

}