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
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="<?= $selectTab == 1 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabCurrent'), Url::home()) ?></li>
        <li class="<?= $selectTab == 3 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabMonth'), ['list/month']) ?></a></li>
        <li class="<?= $selectTab == 4 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabPopular'), ['list/popular']) ?></li>
    </ul>
    <br/>
    <?php if (!empty($searchTag)) { ?>
        Поиск по тегу: <span class="label label-tag-element"><?= $searchTag ?></span>
    <?php } ?>
</div>
