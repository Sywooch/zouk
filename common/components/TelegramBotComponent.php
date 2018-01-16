<?php

namespace common\components;


use common\models\EntityLink;
use common\models\Event;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\Video;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;
use Yii;
use Exception;
use yii\base\Configurable;
use yii\db\Expression;

class TelegramBotComponent extends BotApi implements Configurable
{

    public $apiTokens;

    public $apiToken;

    protected $token;

    public $trackerToken = null;

    private $bot = null;

    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        if (empty($this->apiTokens)) {
            throw new Exception('Bot token cannot be empty');
        }
        $apiTokens = $this->apiTokens;
        $firstApiToken = array_shift($apiTokens);
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
            $this->apiToken = $apiTokenParams['token'];
            $this->token = $apiTokenParams['token'];
            $this->trackerToken = $apiTokenParams['trackerToken'];
            $this->bot = new Client($this->token, $this->trackerToken);
        }
        return $this->bot;
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
                    ['te.entity' => Item::THIS_ENTITY]
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
            return $this->sendMessage($message->getChat()->getId(), $answer);
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
                    ['te.entity' => Item::THIS_ENTITY]
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
        return $this->sendMessage($message->getChat()->getId(), $answer);
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
        return $this->sendMessage($message->getChat()->getId(), $answer);
    }

}