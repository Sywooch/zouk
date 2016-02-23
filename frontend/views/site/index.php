<?php

/* @var $this yii\web\View */

use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

echo $this->render('/list/tabs', ['selectTab' => 1]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID]) ?>

    </div>
</div>
