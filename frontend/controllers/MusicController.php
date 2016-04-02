<?php
namespace frontend\controllers;

use common\components\GetidComponent;
use common\components\YandexDiskComponent;
use common\models\Music;
use common\models\User;
use frontend\widgets\SoundWidget;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Music controller
 */
class MusicController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'save'],
                'rules' => [
                    [
                        'actions' => ['add', 'save'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAdd()
    {
        $user = User::thisUser();
        if (Yii::$app->request->isPost) {
            $music = new Music();
            if ($music->load(Yii::$app->request->post())) {
                $music->user_id = $user->id;
                if (Yii::$app->request->post('Music[artist]')) {
                    $music->artist = Yii::$app->request->post('Music[artist]');
                }
                if (Yii::$app->request->post('Music[title]')) {
                    $music->title = Yii::$app->request->post('Music[title]');
                }
                if ($music->save()) {
                    $music->musicFile = UploadedFile::getInstance($music, 'musicFile');
                    if ($music->musicFile instanceof UploadedFile && $music->validate('musicFile')) {
                        $dirName = 'user' . $user->id;
                        $path = '/music/' . $dirName . '/';
                        $fileName = 'music_' . $user->id . '_' . $music->id . '.' . pathinfo($music->musicFile->name, PATHINFO_EXTENSION);
                        if (!empty(Yii::$app->params['prefix'])) {
                            $fileName = Yii::$app->params['prefix'] . '_' . $fileName;
                        }

                        /** @var GetidComponent $audioInfoComponent */
                        $audioInfoComponent = Yii::$app->audioInfo;
                        $audioInfo = $audioInfoComponent->getInfo($music->musicFile->tempName);
                        $music->playtime = (float)$audioInfo['playtime_seconds'];
                        $audioArtist = !empty($audioInfo['id3v1']['artist']) ? $audioInfo['id3v1']['artist'] : '';
                        $audioTitle = !empty($audioInfo['id3v1']['title']) ? $audioInfo['id3v1']['title'] : '';
                        if (empty($music->artist)) {
                            $music->artist = $audioArtist;
                        }
                        if (empty($music->title)) {
                            $music->title = $audioTitle;
                        }
                        if (empty($music->title)) {
                            $music->title = $music->musicFile->name;
                        }

                        /** @var YandexDiskComponent $yandexDisk */
                        $yandexDisk = Yii::$app->yandexDisk;
                        // Тест папки $path на существование
                        $pathInfo = $yandexDisk->getProperty($path);
                        if (!$pathInfo) {
                            $pathInfoMusic = $yandexDisk->getProperty('/music/');
                            if (!$pathInfoMusic) {
                                $yandexDisk->createDirectory('/music/');
                            }
                            $yandexDisk->createDirectory($path);
                        }
                        $fileInfo = $yandexDisk->getProperty($path . $fileName);
                        if (!$fileInfo) {
                            // Такого файла еще нет
                            $uploadInfo = $yandexDisk->uploadFile($path, (array)$music->musicFile, $fileName);
                        }
                        $publishInfo = $yandexDisk->startPublishing($path . $fileName);
                        if (is_string($publishInfo) && !empty($publishInfo)) {
                            $music->url = $publishInfo;
                            $shortUrl = Yii::$app->google->getShortUrl('https://getfile.dokpub.com/yandex/get/' . $publishInfo);
                            if (!empty($shortUrl['id'])) {
                                $music->short_url = $shortUrl['id'];
                            }
                        }
                        $music->key = $yandexDisk->key;
                        $music->entity_key = $yandexDisk::THIS_ENTITY;

                        $music->save();
                    }
                }
            }
            $result = [
                'id'  => $music->id,
                'url' => Url::to(['music/sound', 'id' => $music->id]),
            ];
            return json_encode($result);
        }
        $this->redirect(Url::home());
    }

    public function actionSound($id)
    {
        $music = Music::findOne($id);
        return SoundWidget::widget(['music' => $music]);
    }

    public function actionSearchmusicfromself()
    {
        $thisUser = User::thisUser();
        $value = Yii::$app->request->post('value');

        $data = [
            'userId'        => $thisUser->id,
            'artistOrTitle' => $value,
        ];
        $musicModels = Music::getMusic($data);
        $results = [];
        foreach ($musicModels as $music) {
            $results[] = [
                'musicHtml' => SoundWidget::widget(['music' => $music]),
                'musicId'   => $music->id,
                'musicUrl'  => Url::to(['music/sound', 'id' => $music->id]),
            ];
        }

        return json_encode($results);
    }

    public function actionSave()
    {
        $thisUser = User::thisUser();
        $id = Yii::$app->request->post('id');
        $music = Music::findOne($id);
        if ($music->user_id == $thisUser->id) {
            $music->artist = Yii::$app->request->post('artist');
            $music->title = Yii::$app->request->post('title');
            $music->save();
        }
        return json_encode([
            'musicHtml' => SoundWidget::widget(['music' => $music]),
            'musicId'   => $music->id,
        ]);
    }
}