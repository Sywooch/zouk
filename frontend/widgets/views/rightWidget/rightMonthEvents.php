<?php
/**
 * @var Event[] $events
 */

use common\models\Event;
use frontend\models\Lang;
use yii\bootstrap\Html;

$year = date('Y');
$month = date('m');
?>
<div class="right-block cornsilk">
    <h3><?= Lang::t('main', 'monthEvents') ?></h3>
    <h4 class="text-center"><?=
        Html::a(Lang::t('month', 'month' . $month), ['event/month', 'year' => $year, 'month' => (int)$month]) .
        ', ' .
        Html::a(date('Y'), ['event/year', 'year' => $year]) ?></h4>
    <div class="">
        <ul>
        <?php
        foreach ($events as $event) {
            $eventText = "";
            if (!empty($event->getCountryText())) {
                $eventText .= $event->getCountryText() . ', ';
            }
            $eventText .= $event->getTitle() . ' / ' . date('d.m.Y', $event->date);
            echo Html::tag(
                'li',
                Html::a(
                    $eventText,
                    $event->getUrl(),
                    ['target' => '_blank']
                )
            );
        }
        ?>
        </ul>
    </div>
</div>
