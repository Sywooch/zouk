<?php
/**
 * @var yii\web\View $this
 * @var \common\models\form\SearchEntryForm $searchEntryForm
 */

use common\models\Item;
use frontend\models\Lang;
use frontend\widgets\EntryList;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

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


$this->params['containerClass'] = 'block-entry-list';

?>
<div class="site-index">
    <div class="body-content">
        <?= $this->render('/list/tabs', ['selectTab' => 1, 'searchTag' => $searchEntryForm->search_text]) ?>
        <?= EntryList::widget([
            'orderBy' => EntryList::ORDER_BY_ID,
            'searchEntryForm' => $searchEntryForm,
            'page' => $page,
            'entityTypes' => [Item::THIS_ENTITY],
            'blockAction' => Html::a(
                Lang::t('main', 'mainButtonAddRecord'),
                ['/list/add'],
                ['class' => 'btn btn-success btn-label-main add-item']
            ),
        ]) ?>

    </div>
</div>
