<?php
/**
 * @var yii\web\View $this
 * @var \common\models\form\SearchEntryForm $searchEntryForm
 */

use common\models\Event;
use frontend\models\Lang;
use frontend\widgets\EntryList;
use frontend\widgets\EventList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

$keywords = 'brazilian zouk, zouk, бразильский зук, бразильский танец зук, конгресс, congress, мастер класс, фестиваль, потанцевать, научиться';
$description = 'Зук – это современный, романтичный и ритмичный танец. Найти вечиринку, конгресс по бразильскому зуку. Разместить своё мероприятие.';

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => $keywords,
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

Yii::$app->params['jsZoukVar']['dateCreateType'] = EventList::DATE_CREATE_AFTER;

$this->params['containerClass'] = 'block-entry-list';
?>
<div class="site-index">
    <div class="body-content">
        <?= $this->render('/event/tabs', ['selectTab' => 2, 'searchTag' => $searchEntryForm->search_text]) ?>
        <?= EntryList::widget([
            'orderBy'         => EntryList::ORDER_BY_DATE,
            'searchEntryForm' => $searchEntryForm,
            'page'            => $page,
            'entityTypes'     => [Event::THIS_ENTITY],
            'blockAction'     => Html::a(
                Lang::t('main', 'mainButtonAddEvent'),
                ['/events/add'],
                ['class' => 'btn btn-success btn-label-main add-item']
            ),
        ]) ?>

    </div>
</div>