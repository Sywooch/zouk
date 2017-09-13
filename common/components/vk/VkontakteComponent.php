<?php


namespace common\components\vk;


use common\models\Vkpost;
use CURLFile;
use yii\base\Component;

class VkontakteComponent extends Vkontakte
{


    public function init()
    {
        parent::init();

    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function saveImg($url)
    {
        $tmpfname = tempnam("/tmp", "FOO");

        $handle = fopen($tmpfname, "w");
        fwrite($handle, file_get_contents($url));
        fclose($handle);

        return $tmpfname;
    }

    public function initAccessToken($accessToken)
    {
        $this->setAccessToken(json_encode(['access_token' => $accessToken]));
    }

    public function vkGet($groupId, $groupName, $offset, $limit)
    {
        $params = [
            'offset'   => $offset,
            'count'    => $limit,
            'owner_id' => '',
            'domain'   => '',
        ];
        if (empty($groupId)) {
            $params['owner_id'] = -$groupId;
        } else {
            $params['domain'] = $groupName;
        }

        return $this->api('wall.get', $params);
    }


    /**
     * @param $groupId
     * @param int $publishDate
     * @param array $videos
     * @param string[] $tags
     * @return bool|mixed
     */
    public function postRandomVideo($groupId, $publishDate, $videos, $tags = [])
    {
        $attachments = "";

        $params = [
            'owner_id'     => -$groupId,
            'message'      => 'тест',
            'from_group'   => 1,
            'publish_date' => $publishDate,
            'guid'         => date('YmdHis'),
            'attachments'  => $attachments,
        ];

        return $this->apiPost('wall.post', $params);
    }

}