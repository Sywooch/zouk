<?php


use frontend\widgets\ItemList;

echo $this->render('/list/tabs', ['selectTab' => 3]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_LIKE_SHOW, 'dateCreateType' => ItemList::DATE_CREATE_MONTH]) ?>

    </div>
</div>
