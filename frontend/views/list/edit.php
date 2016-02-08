<?php
/**
 * @var yii\web\View        $this
 * @var Video               $videos
 * @var \common\models\Item $item
 */

use common\models\Video;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/videoEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/listEdit', 'title');

$this->params['breadcrumbs'][] = $this->title;

/** @var Video[] $videos */
$videos = $item->getVideoModels();
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'list-add-form']); ?>

            <?= $form->field($item, 'title')->label(Lang::t('page/listEdit', 'fieldTitle')) ?>

            <?= $form->field($item, 'description')->textarea()->label(Lang::t('page/listEdit', 'fieldDescription')) ?>

            <h4>Видео:</h4>
            <div id="blockVideos">
                <?php
                foreach ($videos as $video) {
                    ?>
                    <div class="input-group margin-bottom">
                        <input type="text" name="videos[]" class="form-control" value="<?= $video->originalUrl ?>" />
                        <span type="submit" class=" input-group-addon btn btn-default">X</span>
                    </div>
                    <?php
                }
                ?>
            </div>
            <a id="addVideoButton" class="btn btn-success margin-bottom">+</a>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/listEdit', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
