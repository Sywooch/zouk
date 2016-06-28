<?php
/**
 */
use frontend\models\Lang;
use yii\helpers\Html;

?>
<div class="modal fade modal-show-img bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-body">
                <?= Html::img(Yii::$app->UrlManager->to('img/logo.png')) ?>
            </div>
        </div>
    </div>
</div>