<?php
use common\models\Event;
use common\models\Location;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->google->getMapsGoogleJsFile());
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/maps.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/markerclusterer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/showLocation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="modal fade modal-show-location bs-example-modal-md" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content location-block">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/dialogs', 'modalShowLocation_title') ?></h4>
            </div>
            <div class="modal-body">
                <div id="mapShowLocation"></div>
                <div class="location-info-block">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-show-all-locations btn btn-default"><?= Lang::t('main/dialogs', 'modalShowLocation_btnShowAll') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Lang::t('main/dialogs', 'close') ?></button>
            </div>
        </div>
    </div>
</div>
