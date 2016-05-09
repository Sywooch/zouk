<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Countries
 *
 * @property integer $id
 * @property string  $title_ru
 * @property string  $title_ua
 * @property string  $title_be
 * @property string  $title_en
 * @property string  $title_es
 * @property string  $title_pt
 * @property string  $title_de
 * @property string  $title_fr
 * @property string  $title_it
 * @property string  $title_pl
 * @property string  $title_ja
 * @property string  $title_lt
 * @property string  $title_lv
 * @property string  $title_cz
 */
class Countries extends ActiveRecord
{

    const COUNTRIES_LANG_RU = 'ru';
    const COUNTRIES_LANG_UA = 'ua';
    const COUNTRIES_LANG_BE = 'be';
    const COUNTRIES_LANG_EN = 'en';
    const COUNTRIES_LANG_ES = 'es';
    const COUNTRIES_LANG_PT = 'pt';
    const COUNTRIES_LANG_DE = 'de';
    const COUNTRIES_LANG_FR = 'fr';
    const COUNTRIES_LANG_IT = 'it';
    const COUNTRIES_LANG_PL = 'pl';
    const COUNTRIES_LANG_JA = 'ja';
    const COUNTRIES_LANG_LT = 'lt';
    const COUNTRIES_LANG_LV = 'lv';
    const COUNTRIES_LANG_CZ = 'cz';

    private function getLangLinkColumns()
    {
        return [
            self::COUNTRIES_LANG_RU => 'title_ru',
            self::COUNTRIES_LANG_UA => 'title_ua',
            self::COUNTRIES_LANG_BE => 'title_be',
            self::COUNTRIES_LANG_EN => 'title_en',
            self::COUNTRIES_LANG_ES => 'title_es',
            self::COUNTRIES_LANG_PT => 'title_pt',
            self::COUNTRIES_LANG_DE => 'title_de',
            self::COUNTRIES_LANG_FR => 'title_fr',
            self::COUNTRIES_LANG_IT => 'title_it',
            self::COUNTRIES_LANG_PL => 'title_pl',
            self::COUNTRIES_LANG_JA => 'title_ja',
            self::COUNTRIES_LANG_LT => 'title_lt',
            self::COUNTRIES_LANG_LV => 'title_lv',
            self::COUNTRIES_LANG_CZ => 'title_cz',
        ];
    }

    public function getLangCountries($lang)
    {
        $langLink = $this->getLangLinkColumns();
        $column = null;
        if (isset($langLink[$lang])) {
            $column = $langLink[$lang];
        }
        if (empty($column)) {
            $column = $langLink[self::COUNTRIES_LANG_EN];
        }
        if (empty($column)) {
            $column = $langLink[self::COUNTRIES_LANG_RU];
        }
        if (!empty($column)) {
            return $this->{$column};
        } else {
            return "";
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public static function getCountries($lang)
    {
        $countries = Countries::find()->all();
        $countriesData = [];
        foreach ($countries as $country) {
            $countriesData[$country->id] = $country->getLangCountries($lang->url);
        }
        return $countriesData;
    }
    
}
