<?php
/**
 * @var \common\models\EntryModel[] $items
 * @var bool $onlyItem
 * @var string $dateCreateType
 * @var string $display
 * @var int    $limit
 * @var bool   $addModalShowVideo
 * @var bool   $addModalShowImg
 * @var bool   $addModalShowLocation
 * @var SearchEntryForm $searchEntryForm
 * @var string $blockAction
 * @var integer $countEntities
 */

use common\models\form\SearchEntryForm;
use common\models\Item;
use frontend\models\Lang;
use frontend\widgets\EntryList;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/entryList/list.js?20170122', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/entryList/entryView.css?20170122', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42/share42.js?20170122', ['depends' => [\yii\web\JqueryAsset::className()]]);

$action = isset(Yii::$app->controller->searchPath) ? Yii::$app->controller->searchPath : 'site/index';
?>
    <?php
    if (!empty($blockAction)) {
        ?>
        <div class="hide" id="blockAction">
            <div class="row margin-bottom">
                <div class="col-sm-12">
                    <?= $blockAction; ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div id="blockList" class="hide">
        <?php
        if (empty($items)) {
            $items = [];
        }
        foreach ($items as $item) {
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ($display == EntryList::ITEM_LIST_DISPLAY_MAIN) {
                        echo $this->render('entryView', ['item' => $item, 'dateCreateType' => $dateCreateType, 'countEntities' => $countEntities]);
                    } else if ($display == ItemList::ITEM_LIST_DISPLAY_MINI) {
                        echo $this->render('viewMini', ['item' => $item, 'dateCreateType' => $dateCreateType]);
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div id="blockFullList" class="row">
    </div>

<?php
if (!$onlyItem) {
    echo Html::button(
        Lang::t("main", "showMore"),
        [
            'class'       => 'btn',
            'style'       => 'width: 100%;',
            'id'          => 'loadMore',
            'data-search' => json_encode($searchEntryForm->getSearchParams()),
            'data-url'    => \yii\helpers\Url::to([$action]),
        ]
    );
    if ($addModalShowImg) {
        echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]);
    }
    if ($addModalShowVideo) {
//        echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]);
    }
    
    if ($addModalShowLocation) {
        echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_LOCATION]);
    }
}
?>