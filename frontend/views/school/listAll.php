<?php
/**
 * @var yii\web\View $this
 */

use frontend\models\Lang;
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

echo $this->render('/school/tabs', ['selectTab' => 1]);
?>
<div class="site-index">
    <div class="body-content">
        <div id="schoolMap">

        </div>
        <br/>
        <div class="row">
            <div class="col-md-8">
                <ul class="nav nav-tabs nav-main-tabs">
                    <li class="tab-title"><?= Lang::t('main', 'mainButtonSchools') ?></li>
                </ul>
                <?= SchoolList::widget(['orderBy' => SchoolList::ORDER_BY_LIKE_SHOW, 'dateCreateType' => SchoolList::DATE_CREATE_ALL]) ?>
            </div>
            <div class="col-md-4">
                <?php
                echo Html::a(
                    Lang::t('main', 'mainButtonAddSchool'),
                    ['/events/add'],
                    ['class' => 'btn btn-success btn-label-main add-item']
                );
                echo $this->render('/list/listRightBlock');
                ?>
            </div>
        </div>
    </div>
</div>