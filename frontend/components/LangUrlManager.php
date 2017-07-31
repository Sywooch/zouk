<?php
namespace frontend\components;

use Yii;
use yii\helpers\Url;
use yii\web\UrlManager;
use frontend\models\Lang;

class LangUrlManager extends UrlManager
{
    public function createUrl($params)
    {
        $addLang = true;
        if (isset($params['lang_id']) && $params['lang_id']) {
            //Если указан идентификатор языка, то делаем попытку найти язык в БД,
            //иначе работаем с языком по умолчанию
            $lang = Lang::findOne($params['lang_id']);
            if ($lang === null) {
                $lang = Lang::getDefaultLang();
            }
            unset($params['lang_id']);
        } else {
            //Если не указан параметр языка, то работаем с текущим языком
            $lang = Lang::getCurrent();
            if (isset($params['lang_id'])) {
                $addLang = false;
                unset($params['lang_id']);
            }
        }

        //Получаем сформированный URL(без префикса идентификатора языка)
        $url = parent::createUrl($params);


        //Добавляем к URL префикс - буквенный идентификатор языка
        if ($url == '/') {
            return '/' . ($addLang ? $lang->url : '');
        } else {
            if (YII_DEBUG && !empty(Yii::$app->params['mainPathCount'])) {
                $url_list = explode('/', $url);
                $mainPath = [];
                for ($i = 0; $i <= Yii::$app->params['mainPathCount']; $i++) {
                    $mainPath[] = array_shift($url_list);
                }
                if ($addLang) {
                    $url_list = array_merge($mainPath, [$lang->url], $url_list);
                } else {
                    $url_list = array_merge($mainPath, $url_list);
                }
                return join('/', $url_list);
            }
            return '/' . ($addLang ? $lang->url : '') . $url;
        }
    }

    public function toLang($lang)
    {
        $url = Yii::$app->getRequest()->getLangUrl();
        if ($url == '/') {
            return '/' . $lang->url;
        } else {
            if (YII_DEBUG && !empty(Yii::$app->params['mainPathCount'])) {
                $url_list = explode('/', $url);
                $mainPath = [];
                for ($i = 0; $i <= Yii::$app->params['mainPathCount']; $i++) {
                    $mainPath[] = array_shift($url_list);
                }
                $url_list = array_merge($mainPath, [$lang->url], $url_list);
                return join('/', $url_list);
            }
            return '/' . $lang->url . $url;
        }
    }

    public function to($src)
    {
        $localHome = \Yii::$app->params['localHomeUrl'];
        $url = $localHome . $src;
        return Url::to($url, true);
    }
}