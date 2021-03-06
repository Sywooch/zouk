<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "lang".
 *
 * @property integer $id
 * @property string  $url
 * @property string  $local
 * @property string  $name
 * @property integer $default
 * @property integer $date_update
 * @property integer $date_create
 */
class Lang extends \yii\db\ActiveRecord
{

    /** @var Lang Переменная, для хранения текущего объекта языка */
    static $current = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'local', 'name', 'date_update', 'date_create'], 'required'],
            [['default', 'date_update', 'date_create'], 'integer'],
            [['url', 'local', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'url'         => 'Url',
            'local'       => 'Local',
            'name'        => 'Name',
            'default'     => 'Default',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }
    
    /**
     * Получение текущего объекта языка
     * @return array|Lang|null|\yii\db\ActiveRecord
     */
    static function getCurrent()
    {
        $cookies = \Yii::$app->response->cookies;
        if ($cookies->has('language')) {
            self::$current = Lang::getLangByUrl($cookies->get('language'));
        }
        if (self::$current === null) {
            self::$current = self::getDefaultLang();
        }
        return self::$current;
    }

    //Установка текущего объекта языка и локаль пользователя
    static function setCurrent($url = null)
    {
        $language = self::getLangByUrl($url);
        if ($language === null) {
            $cookiesRequest = \Yii::$app->request->cookies;
            if ($cookiesRequest->has('language')) {
                $language = self::getLangByUrl($cookiesRequest->get('language'));
            }
        }
        $language = empty($language) ? self::getDefaultLang() : $language;
        self::$current = $language;
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name'  => 'language',
            'value' => $language->url,
        ]));
        Yii::$app->language = self::$current->local;
    }

    //Получения объекта языка по умолчанию
    static function getDefaultLang()
    {
        return Lang::find()->where('`default` = :default', [':default' => 1])->one();
    }

    //Получения объекта языка по буквенному идентификатору
    static function getLangByUrl($url = null)
    {
        if ($url === null) {
            return null;
        } else {
            $language = Lang::find()->where('url = :url', [':url' => $url])->one();
            if ($language === null) {
                return null;
            } else {
                return $language;
            }
        }
    }

    static function t($category, $message, $params = [], $language = null)
    {
        if (Yii::$app !== null) {
            if (!is_array($language)) {
                $language = [$language ?: Yii::$app->language];
            }
            return Yii::$app->translate->translateO($category, $message, $params, $language);
        } else {
            $p = [];
            foreach ((array) $params as $name => $value) {
                $p['{' . $name . '}'] = $value;
            }

            return ($p === []) ? $message : strtr($message, $p);
        }
    }

    static function tn($category, $message, $number, $params = [], $language = null)
    {
        if (Yii::$app !== null) {
            if (!is_array($language)) {
                $language = [$language ?: Yii::$app->language];
            }
            //ключи массива suffix
            $keys = array(2, 0, 1, 1, 1, 2);
            //берем 2 последние цифры
            $mod = abs($number) % 100;
            //определяем ключ окончания
            $suffixKey = $mod > 4 && $mod < 21 ? 2 : $keys[min($mod%10, 5)];
            $message = $message . $suffixKey;
            return Yii::$app->translate->translateO($category, $message, $params, $language);
        } else {
            $p = [];
            foreach ((array) $params as $name => $value) {
                $p['{' . $name . '}'] = $value;
            }

            return ($p === []) ? $message : strtr($message, $p);
        }
    }

    static function tdate($date, $language = null)
    {
        if (Yii::$app !== null) {
            $d = date("d", $date);
            $m = "monthB" . date("m", $date);
            $y = date("Y", $date);

            return $d . " " . self::t('month', $m, [], $language) . " " . $y;
        } else {
            return date("d.m.Y", $date);
        }
    }

    static function tinymcSrcLang($language = null)
    {
        if (Yii::$app !== null) {
            $language = $language ?: Yii::$app->language;
            return '/js/tinymcLang/' . $language . '.js';
        }
    }

    public function getImg()
    {
        return Yii::$app->UrlManager->to('img/lang/' . $this->url . '.png');
    }
}
