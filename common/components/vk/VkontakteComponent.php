<?php


namespace common\components\vk;


use common\models\Item;
use common\models\Video;
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

    public function uploadVideo($options = [], $file = false)
    {
        if (!is_array($options)) return false;

        $responseApi = $this->api('video.save', $options, true);
        $response = $responseApi['response'];

        if (!isset($response['upload_url'])) return $responseApi;

        $attachment = 'video' . $response['owner_id'] . '_' . $response['vid'];
        $upload_url = $response['upload_url'];
        $ch = curl_init($upload_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-type: multipart/form-data"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($file) {

        }
        curl_exec($ch);

        return $attachment;
    }

    /**
     * @param $groupId
     * @param int $publishDate
     * @param string[] $tags
     * @return bool|mixed
     */
    public function postRandomVideo($groupId, $publishDate, $tags = [])
    {
        $attachments = [];

        /** @var Video[] $videos */
        $videos = Video::find()->orderBy('RAND()')->limit(1)->all();
        $text = "Случайное видео от @prozouk (Зук портала — \"ProZouk\")\n";
        foreach ($videos as $video) {
            $attachment = $this->uploadVideo([
                'group_id'   => abs($groupId),
                'is_private' => true,
                'link'       => $video->original_url,
                'title'      => 'ProZouk. ' . $video->video_title,
            ]);

            if (is_string($attachment) && $attachment) {
                $attachments[] = $attachment;
                $text .= $video->video_title;
            } else {
                $error = $attachment['error'] ?? [];
                if (!empty($error)) {
                    if ($error['error_code'] == 203) {
                        // "Access to group denied: !group
                        return $attachment;
                    }
                }
            }

        }
        $text .= "\n\nСайт проекта prozouk.ru\n#prozouk #zouk #brazilianzouk\n";

        if (empty($attachments)) {
            return false;
        }
        $attachments[] = 'http://prozouk.ru';

        $params = [
            'owner_id'     => -$groupId,
            'message'      => $text,
            'from_group'   => 1,
            'publish_date' => $publishDate,
            'guid'         => date('YmdHis'),
            'attachments'  => join(',', $attachments),
        ];

        return $this->apiPost('wall.post', $params, true);
    }

}