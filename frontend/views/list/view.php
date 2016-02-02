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
    <h1><?= Html::a($item->title, $url, ['class' => 'item-hyperlink']) ?></h1>

</div>


<div class="row">
    <div class="col-lg-1">
        <div>
            <span class="glyphicon glyphicon-triangle-top"></span>
        </div>
        <div>
            <span class="glyphicon glyphicon-triangle-bottom"></span>
        </div>

    </div>
</div>