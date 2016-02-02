<?php
/**
 * @var \common\models\Item $item
 */
use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['list/view', 'id' => $item->id]);
?>
<div id="item-<?= $item->id ?>" class="row block-item-summary">
    <div class="col-lg-1">
        <div class="cp" onclick="window.location.href='<?= $url ?>'">
            <div class="votes">
                <div class="mini-counts">
                    <?= Html::tag('span', $item->like_count, ['title' => $item->like_count]) ?>
                </div>
                <div>Голосов</div>
            </div>
            <div class="views">
                <div class="mini-counts">
                    <?= $item->show_count ?>
                </div>
                <div>Показов</div>
            </div>
        </div>
    </div>
    <div class="col-lg-11">
        <div class="summary">
            <h3><?= Html::a($item->title, $url, ['class' => 'item-hyperlink']) ?></h3>
        </div>

        <div class="started">
            <?= Html::a("Создан " . date("d.m.Y", $item->date_create), $url, ['class' => 'started-link']) ?>
        </div>
    </div>
</div>