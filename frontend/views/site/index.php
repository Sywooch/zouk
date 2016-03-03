<?php
/**
 * @var string       $searchTag
 * @var yii\web\View $this
 */

use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

echo $this->render('/list/tabs', ['selectTab' => 1, 'searchTag' => $searchTag]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID, 'searchTag' => $searchTag]) ?>

    </div>
</div>
