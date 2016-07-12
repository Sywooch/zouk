<?php
/**
 * @var yii\web\View $this
 * @var School $school
 */

use common\models\Countries;
use common\models\School;
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
// Calendar & Sortable
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/imgEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/school/edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/schoolEdit', 'titleEdit');

$this->params['breadcrumbs'][] = $this->title;

$imgsSchool = $school->getImgsSort();

$tags = $school->tagEntity;
$tagValues = [];
foreach ($tags as $tag) {
    /** @var Tags $tagSchool */
    $tagSchool = $tag->tags;
    $tagValues[] = $tagSchool->getName();
}
$tagValue = join(',', $tagValues);

$thisUser = User::thisUser();
$userImgs = $thisUser->getUserImgs();
$countries = array_merge([0 => '-'], Countries::getCountries(Lang::getCurrent()));
$locations = $school->locations;
Yii::$app->params['jsZoukVar']['blockLocationCount'] = count($locations);
?>
<div id="school-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php
            $form = ActiveForm::begin(['id' => 'school-edit-form']);

            echo $form->field($school, 'title')->label(Lang::t('page/schoolEdit', 'fieldTitle'));

            echo $form->field($school, 'description')->textarea()->label(Lang::t('page/schoolEdit', 'fieldDescription'));

            echo $form->field($school, 'country')->label(Lang::t('page/schoolEdit', 'fieldCountry'))->dropDownList($countries, ['id' => 'selectCountry']);

            echo $form->field($school, 'city')->label(Lang::t('page/schoolEdit', 'fieldCity'));

            echo $form->field($school, 'site')->label(Lang::t('page/schoolEdit', 'fieldSite'))->textInput(['maxlength' => 120]);

            ?>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/schoolEdit', 'fieldLocation') ?>
                <a class="btn btn-success btn-sm btn-show-add-location pull-right <?= (count($locations) >= School::MAX_LOCATION_SCHOOL) ? 'hide' : '' ?>"
                   data-max-location="<?= School::MAX_LOCATION_SCHOOL ?>"><?= Lang::t('main/location', 'btnAdd') ?></a>
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
                <?= Lang::t('page/schoolEdit', 'fieldImg') ?>
                <a class="btn btn-success btn-sm btn-show-add-img pull-right"
                   data-max-img="<?= School::MAX_IMG_SCHOOL ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>

            <div id="blockImgs" class="row col-md-12">
                <?php
                foreach ($imgsSchool as $img) {
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
                <span class="input-group-addon"><?= Lang::t('page/schoolEdit', 'tags') ?></span>
                <?= Html::textInput('tags', $tagValue, ['id' => 'tokenfield', 'data-tokens' => $tagValue, 'class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/schoolEdit', 'buttonSave'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
                <?= Html::a(Lang::t('page/schoolEdit', 'buttonCancel'), $school->getUrl(), ['class' => 'btn btn-default pull-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_IMG, 'imgs' => $userImgs]) ?>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_LOCATION, 'setLocationType' => 'school']) ?>
</div>
