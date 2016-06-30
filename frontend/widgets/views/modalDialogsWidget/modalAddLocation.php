<?php
/**
 * @var string $setLocationType
 */

use common\models\Event;
use common\models\Location;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->google->getMapsGoogleJsFile());
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/maps.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/markerclusterer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/location/addLocation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['searchBoxText'] = Lang::t('main/dialogs', 'modalAddLocation_searchBox');

$thisUser = User::thisUser();
$location = new Location();
$location->lat = 55.7522200;
$location->lng = 37.6155600;
$location->zoom = 9;
?>
<div class="modal fade modal-add-location bs-example-modal-md" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content location-block">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/dialogs', 'modalAddLocation_title') ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $form = ActiveForm::begin(['id' => 'locationAddForm']);
                echo $form->field($location, 'lat')->label(false)->hiddenInput();
                echo $form->field($location, 'lng')->label(false)->hiddenInput();
                echo $form->field($location, 'zoom')->label(false)->hiddenInput();
                if (!empty($setLocationType)) {
                    $location->type = $setLocationType;
                    echo $form->field($location, 'type')->label(false)->hiddenInput();
                    echo Html::hiddenInput('type_local', Lang::t('main/location', 'school'), ['id' => 'location-type-local']);
                } else {
                    echo $form->field($location, 'type')->dropDownList(Location::getLocationTypeLocal());
                }
                echo $form->field($location, 'title')->label(Lang::t('main/dialogs', 'modalAddLocation_fieldTitle'));
                echo $form->field($location, 'description')->textarea(['maxlength' => 255])->label(Lang::t('main/dialogs', 'modalAddLocation_fieldDescription'));
                ActiveForm::end();
                ?>
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnAddLocation" class="btn btn-primary" data-dismiss="modal"><?= Lang::t('main/dialogs', 'modalAddLocation_btn') ?></button>
                <button type="button" id="btnEditLocation" class="btn btn-primary" data-dismiss="modal"><?= Lang::t('main/dialogs', 'modalEditLocation_btn') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Lang::t('main/dialogs', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>
