<?php
/**
 * @var Event[] $events
 * @var bool    $onlyEvent
 * @var string  $dateCreateType
 * @var string  $searchTag
 * @var string  $display
 */

use common\models\Event;
use frontend\models\Lang;
use frontend\widgets\EventList;
use frontend\widgets\ModalDialogsWidget;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/event/list.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
    <div id="blockList">
        <?php
        foreach ($events as $event) {
            if ($display == EventList::EVENT_LIST_DISPLAY_MAIN) {
                echo $this->render('view', ['event' => $event, 'dateCreateType' => $dateCreateType]);
            } else if ($display == EventList::EVENT_LIST_DISPLAY_MINI) {
                echo $this->render('viewMini', ['event' => $event, 'dateCreateType' => $dateCreateType]);
            }
        }
        ?>
    </div>

<?php
if (!$onlyEvent) {
    if (count($events) >= 20) {
        echo Html::button(Lang::t("main", "showMore"), ['class' => 'btn btn-primary', 'id' => 'loadMore']);
    }
    echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]);
}
?>