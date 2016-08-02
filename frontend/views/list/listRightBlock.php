<?php
/**
 *
 */

use frontend\widgets\RightBlocksWidget;

?>
<?= RightBlocksWidget::widget(
    [
        'action' => [
            RightBlocksWidget::ACTION_MONTH_EVENTS,
            RightBlocksWidget::ACTION_RANDOM_VIDEO,
        ],
    ]) ?>
