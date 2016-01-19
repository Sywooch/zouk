<?php
namespace frontend\components;

use Yii;
use yii\i18n\I18N;

class Translate extends I18N
{

    /**
     * @var string[]
     */
    public $defaultLanguage = [];

    public function translateO($category, $message, $params, $languages)
    {
        $languages = array_merge(
            $languages,
            $this->defaultLanguage
        );

        $messageSource = $this->getMessageSource($category);
        $translation = false;
        $lang = $messageSource->sourceLanguage;
        foreach ($languages as $language) {
            $translation = $messageSource->translate($category, $message, $language);
            if ($translation) {
                $lang = $language;
                break;
            }
        }
        if ($translation === false) {
            return $this->format($message, $params, $messageSource->sourceLanguage);
        } else {
            return $this->format($translation, $params, $lang);
        }
    }
}