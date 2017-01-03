<?php
/**
 * @var Item[] $items
 * @var bool $onlyItem
 * @var string $dateCreateType
 * @var string $searchTag
 * @var string $display
 * @var int    $limit
 * @var bool   $addModalShowVideo
 * @var bool   $addModalShowImg
 */

use common\models\Item;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/list.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
    <div id="blockList">
        <?php
        foreach ($items as $item) {
            if ($display == ItemList::ITEM_LIST_DISPLAY_MAIN) {
                echo $this->render('view', ['item' => $item, 'dateCreateType' => $dateCreateType]);
            } else if ($display == ItemList::ITEM_LIST_DISPLAY_MINI) {
                echo $this->render('viewMini', ['item' => $item, 'dateCreateType' => $dateCreateType]);
            }
        }
        ?>
    </div>

<?php
if (!$onlyItem) {
    if ($dateCreateType == ItemList::DATE_CREATE_LAST || !empty($limit)) {
        if (empty($limit)) {
            $limit = 10;
        }
        if (count($items) >= $limit) {
            echo Html::button(
                Lang::t("main", "showMore"),
                [
                    'class'    => 'btn btn-primary',
                    'id'       => 'loadMore',
                    'data-tag' => $searchTag,
                    'data-url' => \yii\helpers\Url::to(['list/items']),
                ]
            );
        }
    }
    if ($addModalShowImg) {
        echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]);
    }
    if ($addModalShowVideo) {
//        echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]);
    }
}
?>