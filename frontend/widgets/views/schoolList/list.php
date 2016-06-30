<?php
/**
 * @var School[] $schools
 * @var bool    $onlySchool
 * @var string  $dateCreateType
 * @var string  $searchTag
 * @var string  $display
 */

use common\models\School;
use frontend\models\Lang;
use frontend\widgets\SchoolList;
use frontend\widgets\ModalDialogsWidget;
use yii\bootstrap\Html;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/school/list.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
    <div id="blockList">
        <?php
        foreach ($schools as $school) {
            if ($display == SchoolList::SCHOOL_LIST_DISPLAY_MAIN) {
                echo $this->render('view', ['school' => $school, 'dateCreateType' => $dateCreateType]);
            } else if ($display == SchoolList::SCHOOL_LIST_DISPLAY_MINI) {
                echo $this->render('viewMini', ['school' => $school, 'dateCreateType' => $dateCreateType]);
            }
        }
        ?>
    </div>

<?php
if (!$onlySchool) {
    if (count($schools) >= 20) {
        echo Html::button(Lang::t("main", "showMore"), ['class' => 'btn btn-primary', 'id' => 'loadMore']);
    }
    echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]);
    echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_LOCATION]);
}
?>