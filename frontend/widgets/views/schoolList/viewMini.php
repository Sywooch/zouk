<?php
/**
 * @var School $school
 * @var string $dateCreateType
 */
use common\models\School;
use frontend\models\Lang;
use yii\helpers\Html;


$url = $school->getUrl();
?>
<div id="school-<?= $school->id ?>" data-id="<?= $school->id ?>" class="block-school-summary block-school-summary-mini margin-bottom">
    <div class="mini-block-school-vote">
        <span class="glyphicon glyphicon-thumbs-up"></span>
        <?= Html::tag('span', $school->like_count, ['title' => $school->like_count . ' ' . Lang::tn('main', 'vote', $school->like_count)]) ?>
    </div>
    <div class="mini-block-school-show">
        <span class="glyphicon glyphicon-eye-open"></span>
        <?= Html::tag('span', $school->show_count, ['title' => $school->show_count . ' ' . Lang::tn('main', 'showCount', $school->show_count)]) ?>
    </div>
    <div>
        <b><?= Html::a($school->getTitle(), $url, ['class' => 'school-hyperlink']) ?></b>
    </div>
    <div class="mini-block-school-date">
        <?= date("d.m.Y", $school->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $school->date_create) ?>
    </div>
</div>