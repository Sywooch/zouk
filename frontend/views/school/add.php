<?php
/**
 * @var yii\web\View         $this
 * @var School $school
 */

use common\models\Countries;
use common\models\School;
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
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/school/add.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/schoolEdit', 'titleAdd');

$this->params['breadcrumbs'][] = $this->title;

$imgsSchool = $school->getImgsSort();

$thisUser = \common\models\User::thisUser();
$userImgs = $thisUser->getUserImgs();

$tagValue = '';

Yii::$app->params['jsZoukVar']['blockLocationCount'] = 0;
$countries = array_merge([0 => '-'], Countries::getCountries(Lang::getCurrent()));
?>
<div id="school-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<div>
    <div class="row">
        <div class="col-lg-9">
            <?php
            $form = ActiveForm::begin(['id' => 'list-add-form']);

            echo $form->field($school, 'title')->label(Lang::t('page/schoolEdit', 'fieldTitle'));

            echo $form->field($school, 'description')->textarea()->label(Lang::t('page/schoolEdit', 'fieldDescription'));

//            echo $form->field($school, 'date')->label(Lang::t('page/schoolEdit', 'fieldDate'))->textInput(['id' => 'datepicker', 'value' => date('d.m.Y')]);

            echo $form->field($school, 'country')->label(Lang::t('page/schoolEdit', 'fieldCountry'))->dropDownList($countries, ['id' => 'selectCountry']);

            echo $form->field($school, 'city')->label(Lang::t('page/schoolEdit', 'fieldCity'));

            echo $form->field($school, 'site')->label(Lang::t('page/schoolEdit', 'fieldSite'))->textInput(['maxlength' => 120]);

            echo $form->field($school, 'official_editor')->checkbox(['label' => Lang::t('page/schoolEdit', 'fieldOfficialEditor')]);

            ?>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/schoolEdit', 'fieldLocation') ?>
                <a class="btn btn-success btn-sm btn-show-add-location pull-right"
                   data-max-location="<?= School::MAX_LOCATION_SCHOOL ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>

            <div id="blockLocation" class="margin-bottom">

            </div>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/schoolEdit', 'fieldImg') ?>
                <a class="btn btn-success btn-sm btn-show-add-img pull-right"
                   data-max-img="<?= School::MAX_IMG_SCHOOL ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>

            <div id="blockImgs">
                <?php
                foreach ($imgsSchool as $img) {
                    ?>
                    <div class="img-input-group pull-left">
                        <div class="block-img-delete"><i class="glyphicon glyphicon-remove"></i></div>
                        <input type="hidden" name="imgs[]" class="form-control" value="<?= $img->id ?>" />
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
                <?= Html::submitButton(Lang::t('page/schoolEdit', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_IMG, 'imgs' => $userImgs]) ?>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_LOCATION, 'setLocationType' => 'school']) ?>
</div>