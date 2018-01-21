<?php

namespace common\components;


use common\models\EntityLink;
use common\models\Event;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\TelegramChat;
use common\models\TelegramMessage;
use common\models\Video;
use frontend\models\Lang;
use TelegramBot\Api\Botan;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;
use Yii;
use Exception;
use yii\base\Configurable;
use yii\db\Expression;

class TelegramBotComponent extends BotApi implements Configurable
{

    const LAST_COMMAND_VIDEO = 'video';
    const LAST_COMMAND_ARTICLE = 'article';
    const LAST_COMMAND_EVENTS = 'events';
    const LAST_COMMAND_START = 'start';
    const LAST_COMMAND_SETTINGS = 'settings';

    public $apiTokens;

    public $defaultToken;

    public $apiToken;

    protected $token;

    public $trackerToken = null;

    public $apiTrackerToken = null;

    public $keyToken = '';

    private $bot = null;

    const VERSION_DEV = 'dev';

    const VERSION_STAGE = 'stage';

    const VERSION_PROD = 'prod';

    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        if (empty($this->apiTokens)) {
            throw new Exception('Bot token cannot be empty');
        }
        $apiTokens = $this->apiTokens;
        $defaultToken = $this->defaultToken ?: 'zoukersbot';
        $firstApiToken = $apiTokens[$defaultToken];
        if (empty($firstApiToken)) {
            throw new Exception('Bot token cannot be empty');
        }
        parent::__construct($firstApiToken['token']);
    }

    /**
     * @param string $keyToken
     * @return Client
     */
    public function getBot($keyToken)
    {
        if (empty($this->bot)) {
            $apiTokenParams = $this->apiTokens[$keyToken] ?? '';
            $this->keyToken = $keyToken;
            $this->apiToken = $apiTokenParams['token'];
            $this->token = $apiTokenParams['token'];
            $this->apiTrackerToken = $apiTokenParams['trackerToken'] ?? null;
            $this->bot = new Client($this->token, $this->trackerToken);
        }
        return $this->bot;
    }

    public function trackMessage(Message $message, $eventName = 'Message')
    {
        $this->messageProcessed($message);
        if ($this->apiTrackerToken && false) {
            $tracker = new Botan($this->apiTrackerToken);
            $tracker->track($message, $eventName);
        }
    }

    public function messageStart(Update $update, $version = '')
    {
        $message = $update->getMessage();
        $lang = $this->getLangFromChat($message->getChat());
        if ($version == self::VERSION_DEV) {
            $answer =
                Lang::t('telegram/start', 'mainTitle', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'title', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuRandomVideo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuDemo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuShow', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuArticle', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuEvents', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuSettings', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'bottomMessage', [], $lang->local) . "\n"
            ;
        } elseif ($version == self::VERSION_STAGE) {
            $answer =
                Lang::t('telegram/start', 'mainTitle', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'title', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuRandomVideo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuDemo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuShow', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuArticle', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuEvents', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuSettings', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'bottomMessage', [], $lang->local) . "\n"
            ;
        } elseif ($version == self::VERSION_PROD) {
            $answer =
                "Привет! Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья

/events - ближайшие события
";
        } else {
            $answer = '';
        }

        if (!empty($answer)) {
            $response = $this->sendMessage($message->getChat()->getId(), $answer);
            $this->trackMessage($message, 'start');
            $this->setLastCommandToChat($message, self::LAST_COMMAND_START);
            return $response;
        }
        return false;
    }


    public function messageHelp(Update $update, $version = '')
    {
        $message = $update->getMessage();
        $lang = $this->getLangFromChat($message->getChat());
        if ($version == self::VERSION_DEV) {
            $answer =
                Lang::t('telegram/start', 'mainTitle', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'title', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuRandomVideo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuDemo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuShow', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuArticle', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuEvents', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuSettings', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'bottomMessage', [], $lang->local) . "\n"
            ;
        } elseif ($version == self::VERSION_STAGE) {
            $answer =
                Lang::t('telegram/start', 'mainTitle', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'title', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuRandomVideo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuDemo', [], $lang->local) . "\n" .
                Lang::t('telegram/start', 'menuShow', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuArticle', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuEvents', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'menuSettings', [], $lang->local) . "\n" .
                "\n" .
                Lang::t('telegram/start', 'bottomMessage', [], $lang->local) . "\n"
            ;
        } elseif ($version == self::VERSION_PROD) {
            $answer =
                "Я помогу найти интересующую тебя информацию о Зуке.
Ты можешь отправить мне эти команды:

/randomvideo - посмотреть случайное видео
/demo - случайная демка
/show - случайное видео шоу номера

/article - случайная статья

/events - ближайшие события
";
        } else {
            $answer = '';
        }

        if (!empty($answer)) {
            $response = $this->sendMessage($message->getChat()->getId(), $answer);
            $this->trackMessage($message, 'help');
            return $response;
        }
        return false;
    }

    public function messageRandomVideo(Update $update, $paramStr = '', $tag = '')
    {
        $message = $update->getMessage();

        $find = Video::find()->orderBy(new Expression('rand()'));
        if (!empty($tag)) {
            $find
                ->innerJoin(EntityLink::tableName() . ' el', [
                    'and',
                    'el.entity_2_id=video.id',
                    ['entity_1' => Item::THIS_ENTITY],
                    ['entity_2' => Video::THIS_ENTITY],
                ])
                ->innerJoin(Item::tableName() . ' item', 'el.entity_1_id=item.id')
                ->innerJoin(TagEntity::tableName() . ' te', [
                    'and',
                    'te.entity_id=item.id',
                    ['te.entity' => Item::THIS_ENTITY],
                ])
                ->innerJoin(Tags::tableName() . ' tag', 'te.tag_id=tag.id and tag.name=:tag', [':tag' => $tag]);
        }
        if (!empty($paramStr)) {
            $find->andWhere(['like', 'video.video_title', $paramStr]);
        }
        /** @var Video $video */
        $video = $find->one();
        if ($video) {
            $answer = $video->video_title . "\n" . $video->original_url;
            $response = $this->sendMessage($message->getChat()->getId(), $answer);
            $this->trackMessage($message, 'randomVideo');
            $this->setLastCommandToChat($message, self::LAST_COMMAND_VIDEO);
            return $response;
        }
        return false;
    }

    public function messageRandomItem(Update $update, $paramStr = '', $tag = '')
    {
        $message = $update->getMessage();

        $find = Item::find()
            ->orderBy(new Expression('rand()'))
            ->andWhere('item.deleted = 0');
        if (!empty($tag)) {
            $find
                ->innerJoin(TagEntity::tableName() . ' te', [
                    'and',
                    'te.entity_id=item.id',
                    ['te.entity' => Item::THIS_ENTITY],
                ])
                ->innerJoin(Tags::tableName() . ' tag', 'te.tag_id=tag.id and tag.name=:tag', [':tag' => $tag]);
        }
        if (!empty($paramStr)) {
            $find->andWhere(['like', 'item.title', $paramStr]);
        }
        /** @var Item $item */
        $item = $find->one();

        if ($item) {
            $answer = $item->title . "\n" . $item->getUrl(true, ['lang_id' => false]);
        } else {
            $answer = 'Статья не найдена';
        }
        $response = $this->sendMessage($message->getChat()->getId(), $answer);
        $this->trackMessage($message, 'randomItem');
        $this->setLastCommandToChat($message, self::LAST_COMMAND_ARTICLE);
        return $response;
    }

    public function messageEventAfter(Update $update, $paramStr = '')
    {
        $message = $update->getMessage();

        $findQuery = Event::find()
            ->andWhere('event.deleted = 0')
            ->orderBy(['date' => SORT_ASC])
            ->andWhere(['>=', 'date', (new \DateTime())->getTimestamp()])
            ->limit(7);

        /** @var Event[] $events */
        $events = $findQuery->all();
        $answer = "Ближайшие события:\n";
        $i = 0;
        foreach ($events ?? [] as $event) {
            $i++;
            $answer .= $i . ") " . date('d.m.Y', $event->date) . "(" . $event->getCity() . "): " . $event->title . "\n";
            $answer .= $event->getUrl(true, ['lang_id' => false]) . "\n\n";
        }
        $answer .= "\n prozouk.ru/events/after";
        $response = $this->sendMessage($message->getChat()->getId(), $answer);
        $this->trackMessage($message, 'eventAfter');
        $this->setLastCommandToChat($message, self::LAST_COMMAND_EVENTS);
        return $response;
    }

    /**
     * @param Update|CallbackQuery $update
     * @param string $paramStr
     * @return Message
     */
    public function messageSettings($update, $paramStr = '')
    {
        $message = $update->getMessage();

        $lang = $this->getLangFromChat($message->getChat());
        if (empty($paramStr)) {
            $answer = Lang::t('telegram/settings', 'settingLabel', [], $lang->local);

            $replyMarkup = new InlineKeyboardMarkup([[
                ['text' => Lang::t('telegram/settings', 'languageBtn', [], $lang->local), "callback_data" => '/settings lang'],
            ]]);

            $response = $this->sendMessage($message->getChat()->getId(), $answer, null, false, null, $replyMarkup);
            $this->trackMessage($message, 'settings');
            $this->setLastCommandToChat($message, self::LAST_COMMAND_SETTINGS);

            return $response;
        } else {
            $params = explode(' ', $paramStr);
            $settingGroup = $params[0] ?? '';
            if ($settingGroup == 'lang') {
                if (empty($params[1])) {
                    $answer = Lang::t('telegram/settings', 'languageSelectBtn', [], $lang->local);
                    /** @var Lang[] $langs */
                    $langs = Lang::find()->all();
                    $buttons = [];
                    foreach ($langs as $lang) {
                        $buttons[] = [
                            'text' => $lang->name,
                            'callback_data' => '/settings lang ' . $lang->id,
                        ];
                    }

                    $replyMarkup = new InlineKeyboardMarkup([$buttons]);

                    $response = $this->editMessageText($message->getChat()->getId(), $message->getMessageId(), $answer, null, false, $replyMarkup);
                    return $response;
                } else {
                    $lang = $params[1] ?? '';
                    /** @var Lang $langModel */
                    $langModel = Lang::find()->where(['id' => intval($lang)])->one();
                    if ($langModel) {
                        $answer = Lang::t('telegram/settings', 'languageSelectedLabel', [], $lang->local) . $langModel->name;

                        $chatId = $message->getChat()->getId();
                        /** @var TelegramChat $chat */
                        $chat = TelegramChat::find()->andWhere(['chat_id' => $chatId])->one();
                        $chat->lang_id = $langModel->id;
                        $chat->save();

                        $replyMarkup = new InlineKeyboardMarkup([]);

                        $response = $this->editMessageText($message->getChat()->getId(), $message->getMessageId(), $answer, null, false, $replyMarkup);
                        return $response;
                    }
                }

            }
        }

        return false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function messageRead($message)
    {
        if ($this->isNewMessage($message)) {
            $telegramMessage = new TelegramMessage();
            $telegramMessage->setAttributes([
                'user_id'    => $message->getFrom()->getId(),
                'username'   => $message->getFrom()->getUsername(),
                'chat_id'    => $message->getChat()->getId(),
                'message_id' => $message->getMessageId(),
                'text'       => $message->getText(),
                'status'     => TelegramMessage::STATUS_READ,
                'bot_name'   => $this->keyToken,
            ]);
            return $telegramMessage->save();
        }
        return true;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function messageProcessed($message)
    {
        $telegramMessage = $this->getTelegramMessage($message);
        if ($telegramMessage) {
            $telegramMessage->status = TelegramMessage::STATUS_PROCESSED;
            return $telegramMessage->save(true, ['status']);
        }
        return false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function isNewMessage($message)
    {
        $chatId = $message->getChat()->getId();
        $messageId = $message->getMessageId();
        return !TelegramMessage::find()
            ->where(['chat_id' => $chatId, 'message_id' => $messageId])
            ->exists();
    }

    /**
     * @param Message $message
     * @return TelegramMessage
     */
    public function getTelegramMessage($message)
    {
        $chatId = $message->getChat()->getId();
        $messageId = $message->getMessageId();
        return !TelegramMessage::find()
            ->where(['chat_id' => $chatId, 'message_id' => $messageId])
            ->one();
    }


    /**
     * @param Message $message
     * @param string $command
     * @param array $params
     */
    public function setLastCommandToChat($message, $command, $params = [])
    {
        $chatId = $message->getChat()->getId();
        $chat = TelegramChat::find()->andWhere(['chat_id' => $chatId])->one();
        if (empty($chat)) {
            $chat = new TelegramChat();
            $chat->setAttributes([
                'chat_id' => $chatId,
                'params'  => json_encode([]),
            ]);
        }
        $chat->setParamsByKey(TelegramChat::PARAMS_LAST_COMMAND, [
            'name'   => $command,
            'date'   => date('d.m.Y H:i:s'),
            'time'   => time(),
            'params' => $params,
        ]);
        $chat->save();
    }

    /**
     * @param Message $message
     * @return array
     */
    public function getLastCommandFromChat($message)
    {
        $chatId = $message->getChat()->getId();
        $chat = TelegramChat::find()->andWhere(['chat_id' => $chatId])->one();
        if ($chat) {
            return $chat->getPrimaryKey(TelegramChat::PARAMS_LAST_COMMAND);
        }
        return [];
    }


    /**
     * @param Chat $chat
     * @return Lang
     */
    public function getLangFromChat($chat)
    {
        /** @var TelegramChat $chat */
        $chat = TelegramChat::find()->andWhere(['chat_id' => $chat->getId()])->one();
        $lang = null;
        if ($chat) {
            $langId = $chat->lang_id;
            $lang = Lang::find()->where(['id' => $langId])->one();
        }
        if (empty($lang)) {
            $lang = Lang::find()->where(['default' => 1])->one();
        }
        return $lang;
    }

}