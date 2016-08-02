<?php
/**
 * @var Event[] $events
 * @var int $year
 */

use common\models\Event;
use frontend\models\Lang;
use frontend\widgets\EventList;
use yii\bootstrap\Html;

$this->title = Lang::t('page/eventView', 'metaYearTitle');
$this->registerMetaTag([
    'name'    => 'description',
    'content' => Lang::t('page/eventView', 'metaYearDescription', ['year' => $year]),
], 'description');

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => Lang::t('page/eventView', 'metaYearKeywords', ['year' => $year]),
], 'keywords');

?>
<h1><?= Lang::t('main', 'yearEvents') . ' ' . Html::a($year, ['event/year', 'year' => $year]) ?></h1>

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
                Html::a(Lang::t('month', 'month'. str_pad($i, 2, '0', STR_PAD_LEFT)), ['event/month', 'year' => $year, 'month' => $i])
            );
        }
        ?>
        <h4><b><?= Html::a($year + 1, ['event/year', 'year' => $year + 1]) ?></b></h4>
    </div>
</div>
