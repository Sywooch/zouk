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

