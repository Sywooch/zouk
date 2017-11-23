<?php


namespace common\components\vk;


use common\components\SimpleHtmlDom;
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


    public function congratulateBDay($groupId, $publishDate)
    {
        $users = $this->getBDayUser($groupId);
        if (!empty($users)) {
            $attachments = [];
            $userArr = [];
            foreach ($users as $user) {
                $userArr[] = '@id' . $user['uid'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')';
            }
            $userStr = join(', ', $userArr);

            $text = 'Администрация @prozouk(Зук-портала) поздравляет С ДНЁМ РОЖДЕНИЯ наших подписчиков: ';
            $text .= $userStr;

            $randomCongratulation = $this->getRandomCongratulation();
            $randomImg = $this->getRandomCongratulationImg();

            $attachment = $this->attachImage($groupId, $randomImg);

            if ($attachment) {
                $attachments[] = $attachment;
            }


            $text .= "\n\n" . $randomCongratulation;
            $text .= "\n\n #prozouk #zouk #dancezouk #congratulation #happybirthday";

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
        return false;
    }


    private function getRandomCongratulation()
    {
        $congratulation = [
            "Тебе желаю море счастья,\nУлыбок, солнца и тепла.\nЧтоб жизнь была еще прекрасней,\nУдача за руку вела!\n\nПусть в доме будет только радость,\nУют, достаток и покой.\nДрузья, родные будут рядом,\nБеда обходит стороной!\n\nЗдоровья крепкого желаю\nИ легких жизненных дорог.\nИ пусть всегда, благословляя,\nТебя хранит твой ангелок!",
            "С днем рождения поздравляем\nИ желаю день за днем\nБыть счастливее и ярче,\nСловно солнце за окном.\n\nПожелаю я здоровья,\nМного смеха и тепла,\nЧтоб родные были рядом\nИ, конечно же, добра!\n\nПусть деньжат будет побольше,\nПутешествий и любви.\nЧашу полную заботы,\nМира, света, красоты!",
            "Пусть в жизни будет все, что нужно:\nЗдоровье, мир, любовь и дружба.\nНе отвернется пусть успех,\nУдача любит больше всех.\n\nПусть счастье будет настоящим,\nК мечте и радости манящем.\nИ много-много светлых лет\nБез боли, горестей и бед!",
            "С днем рождения поздравляем\nИ от всей души желаю\nРадости, любви, успеха,\nЧтобы повод был для смеха!\n\nПусть что хочется — случится,\nНу а счастье — вечно длится,\nЕсли встретятся невзгоды —\nПусть не сделают погоды!\n\nВ доме пусть царит порядок,\nВ кошельке будет достаток,\nВсего лучшего желаю\nИ еще раз поздравляем!",
            "Хочу тебя поздравить с днем рождения\nИ очень много счастья пожелать!\nПускай отличным станет настроение,\nПусть будет все, о чем можно мечтать!\n\nОт радости глаза пускай искрятся,\nЖелаю света, солнца и добра,\nКак можно чаще ярко улыбаться,\nЧтоб стала жизнь прекрасней, чем вчера!",
            "Дней счастливых и достатка,\nПусть здоровье бьет ключом.\nА тревоги и печали\nНавсегда покинут дом.\n\nПусть удача, словно ангел,\nЛетит всюду за тобой.\nЧтобы сердце не тужило,\nПусть любовь сверкает в нем.\n\nПусть труды все станут легче,\nНе иссякнет красота.\nС днем рождения поздравляем.\nСчастья, радости, добра!",
            "Пускай сбываются мечты,\nВсё в жизни удается,\nМир будет полон красоты,\nУдача — улыбнется!\n\nЧтоб не одолевала грусть,\nПокинула тревога...\nМоментов ярких будет пусть\nНевероятно много!\n\nВ благополучии, любя,\nТебе я жить желаю\nИ с днем рождения тебя\nСердечно поздравляем!",
            "Яркого солнца, тепла и улыбок\nТебе в день рожденья хочу пожелать!\nСвой жизненный путь пройти без ошибок,\nПочаще смеяться, побольше мечтать!\n\nБогатства, любви, вдохновения, мудрости,\nПрекрасных моментов желаю тебе,\nИ пусть стороною обходят все трудности,\nДавая дорогу счастливой судьбе!\n\n",
            "Только хороших и радостных дней,\nСчастливых улыбок, веселых затей.\nПусть ангел-хранитель укроет крылом,\nНевзгоды и беды оставь за окном.\n\nПусть верных людей приближает судьба,\nЛюбви и достатка пусть будет сполна,\nЗдоровья родных, уваженья друзей!\nПрими поздравленье от нас поскорей.\n\n",
            "Пусть исполнятся все желания,\nПускай сбудутся все мечты.\nОт родных — теплоты, понимания\nИ заботы, внимания, любви.\n\nА в душе и в доме — уюта.\nИ достатка, великих красот.\nИ поддержки друзей, и совета,\nИ еще покорения высот!\n",
        ];
        $keys = array_rand($congratulation, 1);
        return $congratulation[$keys];
    }

    private function getRandomCongratulationImg()
    {
        $imgs = [
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160781_begemotik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471433750_10.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160764_deva_na_velosipede.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160754_utro.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434359_013.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434372_08.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434380_011.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434387_07.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434412_01.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160735_kapkeiki.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434379_02.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434400_03.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434341_04.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434405_021.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434371_020.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160823_kotik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434429_028.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160759_slonik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160818_sova.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160739_sovi.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160782_tortik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160773_sladkie.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434387_030.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434417_035.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/1471434894_-6.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/1471434869_-18.jpg',
        ];
        $keys = array_rand($imgs, 1);
        return $imgs[$keys];
    }

}