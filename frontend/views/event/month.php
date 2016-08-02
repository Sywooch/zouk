<?php
/**
 * @var Event[] $events
 * @var int $month
 * @var int $year
 */

use common\models\Event;
use frontend\models\Lang;
use frontend\widgets\EventList;
use yii\bootstrap\Html;

$date = strtotime($year . '-' . $month . '-01');
$monthA = Lang::t('month', 'month' . date('m', $date));
$monthB = Lang::t('month', 'monthB' . date('m', $date));
$monthText = $monthA . ', ' . date('Y');

$this->registerJsFile(Yii::$app->google->getMapsGoogleJsFile());
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/maps.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/markerclusterer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/showEvents.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['imagePath'] = '../../../img/location/m';

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

$this->params['breadcrumbs'][] = ['label' => Lang::t('page/eventView', 'events'), 'url' => ['events/all']];
$this->params['breadcrumbs'][] = ['label' => $year, 'url' => ['events/year', 'year' => $year]];
$this->params['breadcrumbs'][] = $monthA;

$this->title = Lang::t('page/eventView', 'metaMonthTitle');
$this->registerMetaTag([
    'name'    => 'description',
    'content' => Lang::t('page/eventView', 'metaMonthDescription', ['year' => $year, 'month' => $monthB]),
], 'description');

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => Lang::t('page/eventView', 'metaMonthKeywords', ['year' => $year, 'month' => $monthA]),
], 'keywords');

?>
<h1><?= Lang::t('main', 'monthEvents') . ' ' . Html::a($monthText, ['event/month', 'year' => $year, 'month' => (int)$month]) ?></h1>

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
        <h4><b><?= Html::a($year, ['event/year', 'year' => $year]) ?></b></h4>
        <?php
        for ($i = 1; $i <= 12; $i++) {
            $monthText = Lang::t('month', 'month' . str_pad($i, 2, '0', STR_PAD_LEFT));
            if ($i == $month) {
                $monthText = Html::tag('b', $monthText);
            } else {
                $monthText = Html::a($monthText, ['event/month', 'year' => $year, 'month' => $i]);
            }
            echo Html::tag(
                'div',
                $monthText
            );
        }
        ?>
        <h4><b><?= Html::a($year + 1, ['event/year', 'year' => $year + 1]) ?></b></h4>
    </div>
</div>

