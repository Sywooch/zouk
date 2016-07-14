<?php
/**
 * @var int    $selectTab
 * @var string $searchTag
 */
use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['selectedTab'] = $selectTab;

$urls = [1 => '/', 3 => 'list/' . ItemList::DATE_CREATE_MONTH, 4 => 'list/' . ItemList::DATE_CREATE_ALL];
$urlNoTag = Url::to([$urls[$selectTab]]);
?>
<div>
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="tab-title"><?= Lang::t('main', 'mainButtonList') ?></li>
        <li class="pull-right <?= $selectTab == 4 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabPopular'), ['list/popular']) ?></li>
        <li class="pull-right <?= $selectTab == 3 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabMonth'), ['list/month']) ?></a></li>
        <li class="pull-right <?= $selectTab == 1 ? 'active ' : '' ?>"><?= Html::a(Lang::t('main', 'listTabCurrent'), Url::home()) ?></li>
    </ul>
    <?php if (!empty($searchTag)) { ?>
        <br/>
        <div class="">
            Поиск по тегу: <span class="label label-tag-element"><?= $searchTag ?></span> <span class="icon-x" data-href="<?= $urlNoTag ?>">&times;</span>
        </div>
    <?php } ?>
</div>
