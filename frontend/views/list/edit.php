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

// tinymce
$this->registerJsFile('//cdn.tinymce.com/4/tinymce.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl . Lang::tinymcSrcLang(), ['depends' => [\yii\web\JqueryAsset::className()]]);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/videoEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/listEdit', 'title');

$this->params['breadcrumbs'][] = $this->title;

$videos = $item->videos;
$tags = $item->tagEntity;
$tagValues = [];
foreach ($tags as $tag) {
    $tagItem = $tag->tags;
    $tagValues[] = $tagItem->name;
}
$tagValue = join(',', $tagValues);
?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'list-edit-form']); ?>

            <?= $form->field($item, 'title')->label(Lang::t('page/listEdit', 'fieldTitle')) ?>

            <?= $form->field($item, 'description')->textarea()->label(Lang::t('page/listEdit', 'fieldDescription')) ?>

            <label style="width: 100%" class="control-label">Видео <a id="addVideoButton" class="btn btn-success btn-sm pull-right">добавить</a></label>
            <div id="blockVideos">
                <?php
                foreach ($videos as $video) {
                    ?>
                    <div class="input-group margin-bottom">
                        <input type="text" name="videos[]" class="form-control" value="<?= $video->original_url ?>" />
                        <span type="submit" class=" input-group-addon btn btn-default">X</span>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="input-group margin-bottom">
                <span class="input-group-addon" id="basic-addon1">Метки</span>
                <?= Html::textInput('tags', $tagValue, array('id' => 'tokenfield', 'data-tokens' => $tagValue, 'class' => 'form-control')) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/listEdit', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
