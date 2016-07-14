<?php
/**
 * @var yii\web\View $this
 */

use frontend\models\Lang;
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

Yii::$app->params['jsZoukVar']['dateCreateType'] = EventList::DATE_CREATE_ALL;

?>
<div class="site-index">
    <div class="body-content">

        <div class="row">
            <div class="col-md-8">
                <?= $this->render('/event/tabs', ['selectTab' => 1]) ?>
                <?= EventList::widget(['orderBy' => EventList::ORDER_BY_DATE, 'dateCreateType' => EventList::DATE_CREATE_ALL]) ?>
            </div>
            <div class="col-md-4">
                <?php
                echo Html::a(
                    Lang::t('main', 'mainButtonAddEvent'),
                    ['/events/add'],
                    ['class' => 'btn btn-success btn-label-main add-item']
                );
                echo $this->render('/list/listRightBlock');
                ?>
            </div>
        </div>
    </div>
</div>