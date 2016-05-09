<?php
/**
 * @var Item   $item
 * @var string $dateCreateType
 */
use common\models\Item;
use common\models\Tags;
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\helpers\Html;
use yii\helpers\Url;


$url = $item->getUrl();
?>
<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>" class="block-item-summary block-item-summary-mini margin-bottom">
    <div class="mini-block-item-vote">
        <span class="glyphicon glyphicon-thumbs-up"></span>
        <?= Html::tag('span', $item->like_count, ['title' => $item->like_count . ' ' . Lang::tn('main', 'vote', $item->like_count)]) ?>
    </div>
    <div class="mini-block-item-show">
        <span class="glyphicon glyphicon-eye-open"></span>
        <?= Html::tag('span', $item->show_count, ['title' => $item->show_count . ' ' . Lang::tn('main', 'showCount', $item->show_count)]) ?>
    </div>
    <div>
        <b><?= Html::a($item->getTitle(), $url, ['class' => 'item-hyperlink']) ?></b>
    </div>
    <div class="mini-block-item-date">
        <?= date("d.m.Y", $item->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $item->date_create) ?>
    </div>
</div>