<?php
/**
 * @var yii\web\View $this
 * @var \common\models\form\SearchEntryForm $searchEntryForm
 * @var int $page
 */

use common\models\School;
use frontend\models\Lang;
use frontend\widgets\EntryList;
use frontend\widgets\SchoolList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->google->getMapsGoogleJsFile());
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/maps.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/markerclusterer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/showAllSchool.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$keywords = 'brazilian zouk, zouk, бразильский зук, бразильский танец зук, школа, студия, потанцевать, научиться танцевать';
$description = 'Зук – это современный, романтичный и ритмичный танец. Найти школу Бразильского зука. Посмотреть расписание школы.';

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => $keywords,
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');


$this->title = Lang::t('main/index', 'title');

Yii::$app->params['jsZoukVar']['dateCreateType'] = SchoolList::DATE_CREATE_ALL;

$this->params['containerClass'] = 'block-entry-list';
?>
<div class="site-index">
    <div class="body-content">
        <div style="margin: -11px -15px 0 -15px; -webkit-box-shadow: 0 4px 2px -2px rgba(0,0,0,0.5); box-shadow: 0 4px 2px -2px rgba(0,0,0,0.3);">
            <div id="schoolMap"></div>
        </div>
        <br/>
        <?= EntryList::widget([
            'orderBy' => EntryList::ORDER_BY_LIKE_SHOW,
            'searchEntryForm' => $searchEntryForm,
            'page' => $page,
            'entityTypes' => [School::THIS_ENTITY],
            'blockAction' => Html::a(
                Lang::t('main', 'mainButtonAddSchool'),
                ['/schools/add'],
                ['class' => 'btn btn-success btn-label-main add-item']
            ),
        ]) ?>
    </div>
</div>

