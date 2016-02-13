<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJsFile('//cdn.tinymce.com/4/tinymce.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl . Lang::tinymcSrcLang(), ['depends' => [\yii\web\JqueryAsset::className()]]);


$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/videoEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/add.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/listAdd', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-add-form']); ?>

            <?= $form->field($item, 'title')->label(Lang::t('page/listAdd', 'fieldTitle')) ?>

            <?= $form->field($item, 'description')->textarea()->label(Lang::t('page/listAdd', 'fieldDescription')) ?>

            <h4>Видео:</h4>
            <div id="blockVideos">

            </div>
            <a id="addVideoButton" class="btn btn-success margin-bottom">+</a>

            <div class="input-group margin-bottom">
                <span class="input-group-addon" id="basic-addon1">Метки</span>
                <?= Html::textInput('Video[tags]', '', array('id' => 'tokenfield', 'data-tokens' => '', 'class' => 'form-control')) ?>
            </div>



            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/listAdd', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
