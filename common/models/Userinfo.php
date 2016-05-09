<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Userinfo
 *
 * @property integer $user_id
 * @property integer $country
 * @property string  $city
 * @property integer $birthday
 * @property string  $about_me
 * @property string  $telephone
 * @property string  $skype
 * @property string  $vk
 * @property string  $fb
 * @property integer $date_update
 * @property integer $date_create
 */
class Userinfo extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userinfo';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_update', 'date_create'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id'     => 'Пользователь',
            'country'     => 'Страна',
            'city'        => 'Город',
            'birthday'    => 'День рождения',
            'about_me'    => 'Обо мне',
            'telephone'   => 'Телефон',
            'skype'       => 'Skype',
            'vk'          => 'Вконтакте',
            'fb'          => 'Facebook',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getCountry()
    {
        $country = null;
        if (!empty($this->country)) {
            $country = Countries::findOne(['id' => $this->country]);
        }
        return $country;
    }

    public function getCountryText()
    {
        $country = $this->getCountry();
        if (!empty($country)) {
            $lang = Lang::$current->url;
            return $country->getLangCountries($lang);
        }
        return "";
    }

    public function getCity()
    {
        return htmlspecialchars($this->city);
    }

    public function getContactInfo($field)
    {
        return htmlspecialchars($this->$field);
    }

    public function getCountryCityText()
    {
        $countryText = $this->getCountryText();
        $city = $this->getCity();
        $countryCityText = $countryText;
        if (!empty($countryText) && !empty($city)) {
            $countryCityText .= ", ";
        }
        $countryCityText .= $city;
        if (empty($countryCityText)) {
            $countryCityText = " - ";
        }
        return $countryCityText;
    }
}
