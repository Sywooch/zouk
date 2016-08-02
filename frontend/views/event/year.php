<?php
/**
 * @var Event[] $events
 * @var int $year
 */

use common\models\Event;
use frontend\models\Lang;
use frontend\widgets\EventList;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->google->getMapsGoogleJsFile());
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/maps.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/markerclusterer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/showEvents.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$locations = [];
foreach ($events as $event) {
    foreach ($event->locations as $location) {
        $locations[] = [
            'lat'         => $location->lat,
            'lng'         => $location->lng,
            'event-title' => $event->title,
            'title'       => $location->title,
            'title-url'   => $event->getUrl(),
            'site-url'    => $event->site,
            'type'        => $location->getTypeLocal(),
            'description' => $location->getDescription(),
        ];
    }
}

Yii::$app->params['jsZoukVar']['locations'] = $locations;

$this->title = Lang::t('page/eventView', 'metaYearTitle');
$this->registerMetaTag([
    'name'    => 'description',
    'content' => Lang::t('page/eventView', 'metaYearDescription', ['year' => $year]),
], 'description');

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => Lang::t('page/eventView', 'metaYearKeywords', ['year' => $year]),
], 'keywords');

$this->params['breadcrumbs'][] = ['label' => Lang::t('page/eventView', 'events'), 'url' => ['events/all']];
$this->params['breadcrumbs'][] = $year;

?>
<h1><?= Lang::t('main', 'yearEvents') . ': ' . Html::a($year, ['event/year', 'year' => $year]) ?></h1>

<div id="eventMap">

</div>

<div class="row">
    <div class="col-md-10">
        <?= EventList::widget([
            'orderBy'        => EventList::ORDER_BY_DATE,
            'dateCreateType' => EventList::DATE_CREATE_AFTER,
            'events'         => $events,
            'display'        => EventList::EVENT_LIST_DISPLAY_MINI,
        ]) ?>
    </div>
    <div class="col-md-2 text-center">
        <h4><b><?= Html::a($year - 1, ['event/year', 'year' => $year - 1]) ?></b></h4>
        <h4><b><?= $year ?></b></h4>
        <?php
        for ($i = 1; $i <= 12; $i++) {
            echo Html::tag(
                'div',
                Html::a(Lang::t('month', 'month' . str_pad($i, 2, '0', STR_PAD_LEFT)), ['event/month', 'year' => $year, 'month' => $i])
            );
        }
        ?>
        <h4><b><?= Html::a($year + 1, ['event/year', 'year' => $year + 1]) ?></b></h4>
    </div>
</div>
