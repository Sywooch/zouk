<?php


use frontend\widgets\ItemList;

$keywords = 'brazilian zouk, zouk, бразильский зук, бразильский танец зук, бразильский зук школа танцев, научиться танцевать бразильский зук, видео бразильский зук, онлайн бразильсий зук, посмотреть бразильский зук, бразильский зук для начинающих, уроки зука базовые шаги';
$description = 'Зук – это современный, романтичный и ритмичный танец. Как музыкальное направление, зук появился приблизительно в 80-х годах XX-го века. Его ритмы начали звучать на французских островах Гваделупа, Мартиника, Гаити, Сент-Люсия и приобрели большую популярность за счет исполнителей Grammacks и Exile One. Однако бразильцы считают что эта музыка появилась во Французкой Полинезии.';

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => $keywords,
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

echo $this->render('/list/tabs', ['selectTab' => 3, 'searchTag' => $searchTag]);
?>
<div class="site-index">
    <div class="body-content">

        <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_LIKE_SHOW, 'dateCreateType' => ItemList::DATE_CREATE_MONTH, 'searchTag' => $searchTag]) ?>

    </div>
</div>
