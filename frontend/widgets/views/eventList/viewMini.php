<?php
/**
 * @var Event   $event
 * @var string $dateCreateType
 */
use common\models\Event;
use frontend\models\Lang;
use yii\helpers\Html;


$title = "";
if (!empty($event->getCountryText())) {
    $title .= $event->getCountryText() . ', ';
}
$title .= $event->getTitle();
$url = $event->getUrl();
?>
<div id="event-<?= $event->id ?>" data-id="<?= $event->id ?>" class="block-event-summary block-event-summary-mini margin-bottom">
    <div class="mini-block-event-vote">
        <span class="glyphicon glyphicon-thumbs-up"></span>
        <?= Html::tag('span', $event->like_count, ['title' => $event->like_count . ' ' . Lang::tn('main', 'vote', $event->like_count)]) ?>
    </div>
    <div class="mini-block-event-show">
        <span class="glyphicon glyphicon-eye-open"></span>
        <?= Html::tag('span', $event->show_count, ['title' => $event->show_count . ' ' . Lang::tn('main', 'showCount', $event->show_count)]) ?>
    </div>
    <div>
        <b><?= Html::a($title, $url, ['class' => 'event-hyperlink']) ?></b>
    </div>
    <div class="mini-block-event-date">
        <?= date("d.m.Y", $event->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $event->date) ?>
    </div>
</div>