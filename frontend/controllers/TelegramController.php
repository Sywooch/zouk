<?php

namespace frontend\controllers;

use common\components\TelegramBotComponent;
use common\models\EntityLink;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\Video;
use TelegramBot\Api\Types\CallbackQuery;
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
                        'actions' => ['init', 'stage', 'dev', 'prod'],
                        'allow'   => true,
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['init', 'stage', 'dev', 'prod'])) {
            $this->enableCsrfValidation = false;
        }
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionInit()
    {
        return $this->actionStage();
    }

    // prozouk_bot
    public function actionDev()
    {
        // https://api.telegram.org/bot$Token/getWebhookInfo
        // https://api.telegram.org/bot$Token/deleteWebhook
        // https://api.telegram.org/bot$Token/setWebhook?url=https://prozouk.ru/telegram/dev
        /** @var TelegramBotComponent $telegramBot */
        $telegramBot = Yii::$app->telegram;
        $bot = $telegramBot->getBot('prozouk_bot');

        $bot->callbackQuery(function (CallbackQuery $query) use ($bot, $telegramBot){
            $data = $query->getData();
            $commands = explode(' ', $data, 2);
            $command = $commands[0] ?? '';
            if ($command == '/settings') {
                $telegramBot->messageSettings($query, $commands[1] ?? '');
            }
            if ($command == '/events') {
                $page = intval($commands[1] ?? '1');
                $telegramBot->messageEventAfter($query, $commands[1] ?? '', $page);
            }
        });

        $bot->on(function (Update $update) use ($bot, $telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $user = $message->getFrom();
                $chat = $message->getChat();
                if (!$user->isBot()) {
                    $mtext = $message->getText();
                    Yii::info([
                        'action'     => 'dev',
                        'chat_id'    => $chat->getId(),
                        'message_id' => $message->getMessageId(),
                        'text'       => $message->getText(),
                    ], 'telegram');
                    $cid = $message->getChat()->getId();
                    $commands = explode(' ', $mtext, 2);
                    $commandDog = $commands[0] ?? '';
                    $commandDogs = explode('@', $commandDog);
                    $command = $commandDogs[0] ?? '';
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
                        $telegramBot->messageEventAfter($update, $paramStr, 1);
                    } elseif ($command == '/start') {
                        $telegramBot->messageStart($update, TelegramBotComponent::VERSION_DEV);
                    } elseif ($command == '/help') {
                        $telegramBot->messageHelp($update, TelegramBotComponent::VERSION_DEV);
                    } elseif ($command == '/settings') {
                        $telegramBot->messageSettings($update, $paramStr);
                    } elseif ($command == '/addEvent') {
                        $telegramBot->messageAddEvent($update, $paramStr);
                    }
                    
                    
                }
            }

        }, function (Update $update) use ($telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                if ($telegramBot->isNewMessage($message)) {
                    $telegramBot->messageRead($message);
                    $user = $message->getFrom();
                    $chat = $message->getChat();
                    if ($user->isBot()) {
                        return false;
                    }
                    return true;
                }
            }
            return false;
        });

        $bot->run();

        header("HTTP/1.1 200 OK");
        exit;
    }


    // zoukersbot
    public function actionStage()
    {
        // https://api.telegram.org/bot$Token/getWebhookInfo
        // https://api.telegram.org/bot$Token/deleteWebhook
        // https://api.telegram.org/bot$Token/setWebhook?url=https://prozouk.ru/telegram/stage
        /** @var TelegramBotComponent $telegramBot */
        $telegramBot = Yii::$app->telegram;
        $bot = $telegramBot->getBot('zoukersbot');

        $bot->callbackQuery(function (CallbackQuery $query) use ($bot, $telegramBot){
            $data = $query->getData();
            $commands = explode(' ', $data, 2);
            $command = $commands[0] ?? '';
            if ($command == '/settings') {
                $telegramBot->messageSettings($query, $commands[1] ?? '');
            }
            if ($command == '/events') {
                $page = intval($commands[1] ?? '1');
                $telegramBot->messageEventAfter($query, $commands[1] ?? '', $page);
            }
        });

        $bot->on(function (Update $update) use ($bot, $telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $user = $message->getFrom();
                $chat = $message->getChat();
                if (!$user->isBot()) {
                    $mtext = $message->getText();
                    Yii::info([
                        'action'     => 'stage',
                        'chat_id'    => $chat->getId(),
                        'message_id' => $message->getMessageId(),
                        'text'       => $message->getText(),
                    ], 'telegram');
                    $cid = $message->getChat()->getId();
                    $commands = explode(' ', $mtext, 2);
                    $commandDog = $commands[0] ?? '';
                    $commandDogs = explode('@', $commandDog);
                    $command = $commandDogs[0] ?? '';
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
                        $telegramBot->messageEventAfter($update, $paramStr, 1);
                    } elseif ($command == '/start') {
                        $telegramBot->messageStart($update, TelegramBotComponent::VERSION_STAGE);
                    } elseif ($command == '/help') {
                        $telegramBot->messageHelp($update, TelegramBotComponent::VERSION_STAGE);
                    } elseif ($command == '/settings') {
                        $telegramBot->messageSettings($update, $paramStr);
                    }
                }
            }

        }, function (Update $update) use ($telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                if ($telegramBot->isNewMessage($message)) {
                    $telegramBot->messageRead($message);
                    $user = $message->getFrom();
                    $chat = $message->getChat();
                    if ($user->isBot()) {
                        return false;
                    }
                    return true;
                }
            }
            return false;
        });

        $bot->run();

        header("HTTP/1.1 200 OK");
        exit;
    }

    // prozoukbot
    public function actionProd()
    {
        // https://api.telegram.org/bot$Token/getWebhookInfo
        // https://api.telegram.org/bot$Token/deleteWebhook
        // https://api.telegram.org/bot$Token/setWebhook?url=https://prozouk.ru/telegram/prod
        /** @var TelegramBotComponent $telegramBot */
        $telegramBot = Yii::$app->telegram;
        $bot = $telegramBot->getBot('prozoukbot');


        $bot->callbackQuery(function (CallbackQuery $query) use ($bot, $telegramBot){
            $data = $query->getData();
            $commands = explode(' ', $data, 2);
            $command = $commands[0] ?? '';
            if ($command == '/settings') {
                $telegramBot->messageSettings($query, $commands[1] ?? '');
            }
            if ($command == '/events') {
                $page = intval($commands[1] ?? '1');
                $telegramBot->messageEventAfter($query, $commands[1] ?? '', $page);
            }
        });

        $bot->on(function (Update $update) use ($bot, $telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                $user = $message->getFrom();
                $chat = $message->getChat();
                if (!$user->isBot()) {
                    $mtext = $message->getText();
                    Yii::info([
                        'action'     => 'prod',
                        'chat_id'    => $chat->getId(),
                        'message_id' => $message->getMessageId(),
                        'text'       => $message->getText(),
                    ], 'telegram');
                    $cid = $message->getChat()->getId();
                    $commands = explode(' ', $mtext, 2);
                    $commandDog = $commands[0] ?? '';
                    $commandDogs = explode('@', $commandDog);
                    $command = $commandDogs[0] ?? '';
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
                        $telegramBot->messageEventAfter($update, $paramStr, 1);
                    } elseif ($command == '/start') {
                        $telegramBot->messageStart($update, TelegramBotComponent::VERSION_PROD);
                    } elseif ($command == '/help') {
                        $telegramBot->messageHelp($update, TelegramBotComponent::VERSION_PROD);
                    } elseif ($command == '/settings') {
                        $telegramBot->messageSettings($update, $paramStr);
                    }
                }
            }

        }, function (Update $update) use ($telegramBot) {
            $message = $update->getMessage();
            if ($message instanceof Message) {
                if ($telegramBot->isNewMessage($message)) {
                    $telegramBot->messageRead($message);
                    $user = $message->getFrom();
                    $chat = $message->getChat();
                    if ($user->isBot()) {
                        return false;
                    }
                    return true;
                }
            }
            return false;
        });

        $bot->run();

        header("HTTP/1.1 200 OK");
        exit;
    }

}