<?php

namespace frontend\controllers;

use common\components\TelegramBotComponent;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\Video;
use TelegramBot\Api\Types\Message;
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
        $bot = $telegramBot->getBot();

        $bot->command('start',  function (Message $message) use ($bot, $telegramBot) {
            $answer =
"Привет! Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья
";
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('help',  function (Message $message) use ($bot, $telegramBot) {
            $answer =
"Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья
";
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });


        $bot->command('randomvideo',  function (Message $message) use ($bot, $telegramBot) {
            $video = Video::getRandomVideo([]);
            $answer = $video->original_url;
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('demo',  function (Message $message) use ($bot, $telegramBot) {
            $video = \common\models\Video::find()
                ->rightJoin(\common\models\EntityLink::tableName() . ' el', [
                    'and',
                    'el.entity_2_id=video.id',
                    ['entity_1' => \common\models\Item::THIS_ENTITY],
                    ['entity_2' => \common\models\Video::THIS_ENTITY],
                ])
                ->rightJoin(\common\models\Item::tableName() . ' item', 'el.entity_1_id=item.id')
                ->rightJoin(\common\models\TagEntity::tableName() . ' te', [
                    'and',
                    'te.entity_id=item.id',
                    ['te.entity' => \common\models\Item::THIS_ENTITY]
                ])
                ->rightJoin(\common\models\Tags::tableName() . ' tag', 'te.tag_id=tag.id and tag.name=\'demo\'')
                ->orderBy(new Expression('rand()'))
                ->one();

            $answer = $video->video_title . "\n" . $video->original_url;
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });


        $bot->command('show',  function (Message $message) use ($bot, $telegramBot) {
            /** @var Video $video */
            $video = \common\models\Video::find()
                ->rightJoin(\common\models\EntityLink::tableName() . ' el', [
                    'and',
                    'el.entity_2_id=video.id',
                    ['entity_1' => \common\models\Item::THIS_ENTITY],
                    ['entity_2' => \common\models\Video::THIS_ENTITY],
                ])
                ->rightJoin(\common\models\Item::tableName() . ' item', 'el.entity_1_id=item.id')
                ->rightJoin(\common\models\TagEntity::tableName() . ' te', [
                    'and',
                    'te.entity_id=item.id',
                    ['te.entity' => \common\models\Item::THIS_ENTITY]
                ])
                ->rightJoin(\common\models\Tags::tableName() . ' tag', 'te.tag_id=tag.id and tag.name=\'show\'')
                ->orderBy(new Expression('rand()'))
                ->one();

            $answer = $video->video_title . "\n" . $video->original_url;
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('article',  function (Message $message) use ($bot, $telegramBot) {
            /** @var Item $item */
            $item = Item::find()
                ->rightJoin(\common\models\TagEntity::tableName() . ' te', [
                    'and',
                    'te.entity_id=item.id',
                    ['te.entity' => \common\models\Item::THIS_ENTITY]
                ])
                ->rightJoin(\common\models\Tags::tableName() . ' tag', 'te.tag_id=tag.id and tag.name=\'article\'')
                ->orderBy(new Expression('rand()'))
                ->one();

            $answer = $item->title . "\n" . $item->getUrl();
            $telegramBot->sendMessage($message->getChat()->getId(), $answer);
        });


        return $bot->run();
    }

}