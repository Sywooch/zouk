<?php
/**
 * @var Item[] $items
 * @var bool   $onlyItem
 * @var string $dateCreateType
 */

use common\models\Item;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/list.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
    <div id="blockList">
        <?php
        foreach ($items as $item) {
            echo $this->render('view', ['item' => $item]);
        }
        ?>
    </div>

<?php
if (!$onlyItem && $dateCreateType == ItemList::DATE_CREATE_LAST) {
    echo Html::button('Показать еще...', ['class' => 'btn btn-primary', 'id' => 'loadMore']);
}
?>