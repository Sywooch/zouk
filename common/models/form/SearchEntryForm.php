<?php

namespace common\models\form;

use yii\base\Model;

class SearchEntryForm extends Model
{
    /** @var int */
    public $id;
    /** @var string */
    public $search_text;
    /** @var integer */
    public $date_from = null;
    /** @var integer */
    public $date_to = null;

    public static function loadFromPost()
    {
        $searchEntryForm = new SearchEntryForm();
        $request = \Yii::$app->request;

        $search = $request->post('search', false);
        if ($search === false) {
            $formData = $request->post($searchEntryForm->formName());
            $searchEntryForm->search_text = $request->get('tag', isset($formData['search_text']) ? $formData['search_text'] : '');
        } else {
            $searchEntryForm->search_text = $search['search_text'];
        }

        \Yii::$app->params['searchEntryForm'] = $searchEntryForm;

        return $searchEntryForm;
    }

    public function attributeLabels()
    {
        return [
            'search_text' => 'Search Text',
        ];
    }

    public function getSearchParams()
    {
        return [
            'search_text' => $this->search_text,
            'date_from'   => $this->date_from,
            'date_to'     => $this->date_to,
        ];
    }

}