<?php
/**
 * @var int $selectTab
 */
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="">
    <ul class="nav nav-tabs">
        <li class="<?= $selectTab == 3 ? 'active ' : '' ?>navbar-right"><?= Html::a(Lang::t('main', 'listTabMonth'), ['list/month']) ?></a></li>
        <li class="<?= $selectTab == 2 ? 'active ' : '' ?>navbar-right"><?= Html::a(Lang::t('main', 'listTabWeek'), ['list/week']) ?></a></li>
        <li class="<?= $selectTab == 1 ? 'active ' : '' ?> navbar-right"><?= Html::a(Lang::t('main', 'listTabCurrent'), Url::home()) ?></li>
    </ul>
</div>
