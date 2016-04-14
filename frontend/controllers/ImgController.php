<?php
namespace frontend\controllers;

use common\components\GetidComponent;
use common\components\YandexDiskComponent;
use common\models\Img;
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
class ImgController extends Controller
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
            $img = new Img();
            $img->user_id = $user->id;
            if ($img->save()) {
                $img->imgFile = UploadedFile::getInstance($img, 'imgFile');
                if ($img->imgFile instanceof UploadedFile && $img->validate('imgFile')) {
                    $prefix = Yii::$app->params['prefix'];
                    if (empty($prefix)) {
                        $prefix = "no_prefix";
                    }
                    $dirName = 'user' . $user->id;
                    $path = '/' . $prefix . '/img/' . $dirName . '/';
                    $fileName = 'img_' . $user->id . '_' . $img->id . '.' . pathinfo($img->imgFile->name, PATHINFO_EXTENSION);
                    if (!empty($prefix)) {
                        $fileName = $prefix . '_' . $fileName;
                    }

                    /** @var YandexDiskComponent $yandexDisk */
                    $yandexDisk = Yii::$app->yandexDisk;
                    $yandexDisk->setClientInfoImgDefault();
                    // Тест папки $path на существование
                    $pathInfo = $yandexDisk->getProperty($path);
                    if (!$pathInfo) {
                        $pathInfoMusic = $yandexDisk->getProperty('/' . $prefix . '/');
                        if (!$pathInfoMusic) {
                            $yandexDisk->createDirectory('/' . $prefix . '/');
                        }
                        $pathInfoMusic = $yandexDisk->getProperty('/' . $prefix . '/img/');
                        if (!$pathInfoMusic) {
                            $yandexDisk->createDirectory('/' . $prefix . '/img/');
                        }
                        $yandexDisk->createDirectory($path);
                    }
                    $fileInfo = $yandexDisk->getProperty($path . $fileName);
                    if (!$fileInfo) {
                        // Такого файла еще нет
                        $uploadInfo = $yandexDisk->uploadFile($path, (array)$img->imgFile, $fileName);
                    }
                    $publishInfo = $yandexDisk->startPublishing($path . $fileName);
                    if (is_string($publishInfo) && !empty($publishInfo)) {
                        $img->url = $publishInfo;
                        $shortUrl = Yii::$app->google->getShortUrl('https://getfile.dokpub.com/yandex/get/' . $publishInfo);
                        if (!empty($shortUrl['id'])) {
                            $img->short_url = $shortUrl['id'];
                        }
                    }
                    $img->key = $yandexDisk->key;
                    $img->entity_key = $yandexDisk::THIS_ENTITY;

                    $img->save();
                }
            }
            $result = [
                'id'        => $img->id,
                'short_url' => $img->short_url,
            ];
            return json_encode($result);
        }
        $this->redirect(Url::home());
    }
}