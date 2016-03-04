<?php
/**
 * @var int    $selectTab
 * @var string $searchTag
 */
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['selectedTab'] = $selectTab;
?>
<div>

    <ul class="nav nav-tabs">
        <?php if (!empty($searchTag)) { ?>
        <li class="navbar-left visible-md-block visible-lg-block visible-sm-block">Поиск по тегу: <span class="label label-tag-element"><?= $searchTag ?></span></li>
        <?php } ?>
        <li class="<?= $selectTab == 4 ? 'active ' : '' ?>navbar-right"><?= Html::a(Lang::t('main', 'listTabPopular'), ['list/popular']) ?></li>
        <li class="<?= $selectTab == 3 ? 'active ' : '' ?>navbar-right"><?= Html::a(Lang::t('main', 'listTabMonth'), ['list/month']) ?></a></li>
        <li class="<?= $selectTab == 1 ? 'active ' : '' ?>navbar-right"><?= Html::a(Lang::t('main', 'listTabCurrent'), Url::home()) ?></li>
    </ul>
</div>
