<?php
/**
 * @var int    $selectTab
 */
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

Yii::$app->params['jsZoukVar']['selectedTab'] = $selectTab;

?>
<div>
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="tab-title"><?= Lang::t('main', 'mainButtonEvents') ?></li>
        <li class="pull-right <?= $selectTab == 3 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabBefore'), ['events/before']) ?></li>
        <li class="pull-right <?= $selectTab == 2 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabAfter'), ['events/after']) ?></a></li>
        <li class="pull-right <?= $selectTab == 1 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabAll'), ['events/all']) ?></li>
    </ul>
</div>
<?= Html::a(
    Lang::t('main', 'mainButtonAddEvent'),
    ['/events/add'],
    ['class' => 'btn btn-success btn-label-main add-item visible-sm-block visible-xs-block']
) ?>