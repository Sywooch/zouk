<?php
/**
 * @var yii\web\View $this
 */

use frontend\models\Lang;
use frontend\widgets\EventList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

Yii::$app->params['jsZoukVar']['dateCreateType'] = EventList::DATE_CREATE_AFTER;

echo $this->render('/event/tabs', ['selectTab' => 2]);
?>
<div class="site-index">
    <div class="body-content">
        <?= EventList::widget(['orderBy' => EventList::ORDER_BY_DATE, 'dateCreateType' => EventList::DATE_CREATE_AFTER]) ?>
    </div>
</div>