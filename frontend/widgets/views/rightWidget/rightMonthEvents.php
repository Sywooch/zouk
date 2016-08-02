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
<div>
    <h3 class="text-center"><?= Lang::t('main', 'monthEvents') ?></h3>
    <h4 class="text-center"><?=
        Html::a(Lang::t('month', 'month' . $month), ['event/month', 'year' => $year, 'month' => (int)$month]) .
        ', ' .
        Html::a(date('Y'), ['event/year', 'year' => $year]) ?></h4>
    <div class="text-center">
        <?php
        foreach ($events as $event) {
            $eventText = "";
            if (!empty($event->getCountryText())) {
                $eventText .= $event->getCountryText() . ', ';
            }
            $eventText .= $event->getTitle() . ' / ' . date('d.m.Y', $event->date);
            echo Html::tag(
                'div',
                Html::a(
                    $eventText,
                    $event->getUrl(),
                    ['target' => '_blank']
                )
            );
        }
        ?>
    </div>
</div>
