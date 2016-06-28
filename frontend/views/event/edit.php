<?php
/**
 * @var yii\web\View $this
 * @var Event $event
 */

use common\models\Countries;
use common\models\Event;
use common\models\Location;
use common\models\Tags;
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// tinymce
$this->registerJsFile('//cdn.tinymce.com/4/tinymce.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl . Lang::tinymcSrcLang(), ['depends' => [\yii\web\JqueryAsset::className()]]);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css');
// Calendar
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');
// Sortable
$this->registerCssFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCss('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/imgEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/event/edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/eventEdit', 'titleEdit');

$this->params['breadcrumbs'][] = $this->title;

$imgsEvent = $event->getImgsSort();

$tags = $event->tagEntity;
$tagValues = [];
foreach ($tags as $tag) {
    /** @var Tags $tagEvent */
    $tagEvent = $tag->tags;
    $tagValues[] = $tagEvent->getName();
}
$tagValue = join(',', $tagValues);

$thisUser = User::thisUser();
$userImgs = $thisUser->getUserImgs();
$countries = array_merge([0 => '-'], Countries::getCountries(Lang::getCurrent()));
$locations = $event->locations;
Yii::$app->params['jsZoukVar']['blockLocationCount'] = count($locations);
?>
<div id="event-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php
            $form = ActiveForm::begin(['id' => 'event-edit-form']);

            echo $form->field($event, 'title')->label(Lang::t('page/eventEdit', 'fieldTitle'));

            echo $form->field($event, 'description')->textarea()->label(Lang::t('page/eventEdit', 'fieldDescription'));

            echo $form->field($event, 'date')->label(Lang::t('page/eventEdit', 'fieldDate'))->textInput(['id' => 'datepicker', 'value' => date('d.m.Y', $event->date)]);

            echo $form->field($event, 'country')->label(Lang::t('page/eventEdit', 'fieldCountry'))->dropDownList($countries, ['id' => 'selectCountry']);

            echo $form->field($event, 'city')->label(Lang::t('page/eventEdit', 'fieldCity'));

            echo $form->field($event, 'site')->label(Lang::t('page/eventEdit', 'fieldSite'))->textInput(['maxlength' => 120]);

            ?>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/eventEdit', 'fieldLocation') ?>
                <a class="btn btn-success btn-sm btn-show-add-location pull-right"
                   data-max-img="<?= Event::MAX_LOCATION_EVENT ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>

            <div id="blockLocation" class="margin-bottom">
                <?php
                $i = 0;
                $hiddenFields = ['lat', 'lng', 'zoom', 'title', 'description', 'type'];
                foreach ($locations as $location) {
                    ?>
                    <div class="block-location" id="blockLocation<?= $i ?>">
                        <i class="glyphicon glyphicon-map-marker"></i>
                        <b><?= $location->getTypeLocal() ?></b>: <?= $location->getTitle() ?>
                        <i class="btn-edit-location-link btn btn-link glyphicon glyphicon-pencil"></i>
                        <i class="btn-delete-location-link btn btn-link glyphicon glyphicon-remove"></i>
                        <?php
                        foreach ($hiddenFields as $hiddenField) {
                            echo Html::activeHiddenInput(
                                $location,
                                $hiddenField,
                                [
                                    'class' => 'field-' . $hiddenField,
                                    'name'  => 'location[' . $i . '][' . $hiddenField . ']',
                                    'id'    => false,
                                ]
                            );
                        }
                        ?>

                    </div>
                    <?php
                    $i++;
                }
                ?>

            </div>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/eventEdit', 'fieldImg') ?>
                <a class="btn btn-success btn-sm btn-show-add-img pull-right"
                   data-max-img="<?= Event::MAX_IMG_EVENT ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>

            <div id="blockImgs" class="row col-md-12">
                <?php
                foreach ($imgsEvent as $img) {
                    ?>
                    <div class="img-input-group pull-left">
                        <div class="block-img-delete"><i class="glyphicon glyphicon-remove"></i></div>
                        <input type="hidden" name="imgs[]" class="form-control" value="<?= $img->id ?>"/>
                        <div style="background-image: url('<?= $img->short_url ?>')" class="background-img"></div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="input-group margin-bottom">
                <span class="input-group-addon"><?= Lang::t('page/eventEdit', 'tags') ?></span>
                <?= Html::textInput('tags', $tagValue, ['id' => 'tokenfield', 'data-tokens' => $tagValue, 'class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/eventEdit', 'buttonSave'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
                <?= Html::a(Lang::t('page/eventEdit', 'buttonCancel'), $event->getUrl(), ['class' => 'btn btn-default pull-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_IMG, 'imgs' => $userImgs]) ?>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_LOCATION]) ?>
</div>
