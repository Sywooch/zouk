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
        <li class="<?= $selectTab == 1 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabAll'), ['events/all']) ?></li>
        <li class="<?= $selectTab == 2 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabAfter'), ['events/after']) ?></a></li>
        <li class="<?= $selectTab == 3 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'eventTabBefore'), ['events/before']) ?></li>
    </ul>
</div>
