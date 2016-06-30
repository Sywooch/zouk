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

$this->title = Lang::t('main/index', 'title');

Yii::$app->params['jsZoukVar']['dateCreateType'] = SchoolList::DATE_CREATE_ALL;

echo $this->render('/school/tabs', ['selectTab' => 1]);
?>
<div class="site-index">
    <div class="body-content">
        <div id="schoolMap">

        </div>
        <?= SchoolList::widget(['orderBy' => SchoolList::ORDER_BY_LIKE_SHOW, 'dateCreateType' => SchoolList::DATE_CREATE_ALL]) ?>
    </div>
</div>