<?php

namespace common\models\form;

use yii\base\Model;

class SearchEntryForm extends Model
{
    /** @var int */
    public $id;
    /** @var string */
    public $search_text;

    public static function loadFromPost()
    {
        $searchEntryForm = new SearchEntryForm();
        $request = \Yii::$app->request;
        $formData = $request->post($searchEntryForm->formName());
        $searchEntryForm->search_text = $request->get('tag', $formData['search_text'] ? $formData['search_text'] : '');

        \Yii::$app->params['searchEntryForm'] = $searchEntryForm;

        return $searchEntryForm;
    }

    public function attributeLabels()
    {
        return [
            'search_text'   => 'Search Text',
        ];
    }

}