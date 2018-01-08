<?php

namespace common\components;


use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use Yii;
use Exception;
use yii\base\Configurable;

class TelegramBotComponent extends BotApi implements Configurable
{

    public $apiToken;

    public $trackerToken = null;

    private $bot = null;

    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        if (empty($this->apiToken)) {
            throw new Exception('Bot token cannot be empty');
        }
        parent::__construct($this->apiToken);
    }

    /**
     * @return Client
     */
    public function getBot()
    {
        if (empty($this->bot)) {
            $this->bot = new Client($this->apiToken, $this->trackerToken);
        }
        return $this->bot;
    }
}