<?php


namespace common\components\vk;


use common\components\SimpleHtmlDom;
use common\models\helpers\RandomStringValue;
use common\models\helpers\RandomTextValue;
use common\models\Video;
use common\models\VkTask;
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

    public function attachImage($groupId, $imgUrl)
    {
        $response = $this->api('photos.getWallUploadServer', [
            'group_id' => $groupId,
        ], true);
        $response = $response['response'];
        $uploadURL = $response['upload_url'];

        $tmpfname = tempnam("/tmp", "photo");
        $handle = fopen($tmpfname, "w");
        fwrite($handle, SimpleHtmlDom::get_content($imgUrl));
        fclose($handle);


        $finfo = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($finfo, $tmpfname);
        $parts = explode(";", $mime);
        $file = new CurlFile($tmpfname, array_shift($parts), 'image.jpg');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  ['file1' => $file]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        unlink($tmpfname);

        $response = $this->api('photos.saveWallPhoto', [
            'group_id' => $groupId,
            'photo' => $response['photo'],
            'server' => $response['server'],
            'hash' => $response['hash'],
        ], true);
        $response = $response['response'] ?? [];

        if (!empty($response) && !empty($response[0]) && isset($response[0]['id'])) {
            return $response[0]['id'];
        }
        return false;
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
        $attachments[] = 'https://prozouk.ru';

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

    /**
     * @param $groupId
     * @param $offset
     * @param $limit
     * @return bool|mixed
     */
    public function getMembers($groupId, $offset = 0, $limit = 1000)
    {

        $params = [
            'group_id' => $groupId,
            'sort'     => 'id_asc',
            'offset'   => $offset,
            'count'    => $limit,
            'fields'   => 'bdate',
        ];

        return $this->apiPost('groups.getMembers', $params, true);
    }


    public function getBDayUser($groupId)
    {
        $page = 0;
        $limit = 1000;
        $users = [];

        $nowDay = date('d');
        $nowMonth = date('n');
        do {
            $offset = $page * $limit;
            $newMembers = $this->getMembers($groupId, $offset, $limit);
            foreach ($newMembers['users'] ?? [] as $user) {
                $bdate = $user['bdate'] ?? '13.13';
                $dmy = explode('.', $bdate);
                $userDay = $dmy[0] ?? 0;
                $userMonth = $dmy[1] ?? 0;
                if ($nowDay == $userDay && $nowMonth == $userMonth) {
                    $users[] = $user;
                }

            }
            $page++;
        } while (($newMembers['count'] ?? 0) > $offset + $limit);

        return $users;
    }


    /**
     * @param VkTask $vkTask
     * @param $publishDate
     * @return bool|mixed
     */
    public function congratulateBDay($vkTask, $publishDate)
    {
        $groupId = $vkTask->group_id;
        $users = $this->getBDayUser($groupId);
        if (!empty($users)) {
            $attachments = [];
            $userArr = [];
            foreach ($users as $user) {
                $userArr[] = '@id' . $user['uid'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')';
            }
            $userStr = join(', ', $userArr);

            $text = $vkTask->getParamsByKey(VkTask::PARAMS_START_TEXT, 'Администрация @prozouk(Зук-портала) поздравляет С ДНЁМ РОЖДЕНИЯ наших подписчиков: ');
            $text .= $userStr;

            $congratulation = RandomTextValue::getRandomValue(RandomTextValue::ENTITY_TYPE_BDAY);
            $randomCongratulation = $congratulation->value ?? '';

            $img = RandomStringValue::getRandomValue(RandomStringValue::ENTITY_TYPE_BDAY);
            $randomImg = $img->value ?? '';

            $attachment = $this->attachImage($groupId, $randomImg);

            if ($attachment) {
                $attachments[] = $attachment;
            }


            $text .= "\n\n" . $randomCongratulation;

            $text .= "\n\n";
            $bottomText = $vkTask->getParamsByKey(VkTask::PARAMS_BOTTOM_TEXT, "#prozouk #zouk #dancezouk #congratulation #happybirthday");
            $text .= $bottomText;

            if ($bottomText != 'ivsevolod.ru') {
                $text .= "\n\n@prozouk (сервис от ProZouk)";
                $attachments[] = 'https://prozouk.ru';
            } else {
                $attachments[] = 'https://ivsevolod.ru';
            }


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
        return false;
    }
}