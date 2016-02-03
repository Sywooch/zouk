<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 */
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $item->title;

$this->params['breadcrumbs'][] = Lang::t('page/listView', 'title');

$url = Url::to(['list/view', 'id' => $item->id]);
?>
<div id="item-header">
    <h1>
        <?= Html::a($item->title, $url, ['class' => 'item-hyperlink']) ?>
        <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
                echo Html::a(
                    Lang::t('page/listView', 'edit'),
                    Url::to(['list/edit', 'id' => $item->id]),
                    ['class' => 'btn btn-success pull-right']
                );
            }
        ?>
    </h1>

</div>


<div class="row">
    <div class="col-lg-1 text-center">
        <div>
            <span class="glyphicon glyphicon-triangle-top"></span>
        </div>
        <div>
            <span class="vote-count-item">
                <?= $item->like_count ?>
            </span>
        </div>
        <div>
            <span class="glyphicon glyphicon-triangle-bottom"></span>
        </div>
    </div>
    <div class="col-lg-11">
        <div class="item-text">
            <?= Html::encode($item->description) ?>
        </div>
    </div>
</div>