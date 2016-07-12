<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 */

use common\models\Img;
use common\models\Item;
use common\models\Music;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\SoundWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

// tinymce
$this->registerJsFile('//cdn.tinymce.com/4/tinymce.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl . Lang::tinymcSrcLang(), ['depends' => [\yii\web\JqueryAsset::className()]]);
// Tags
$this->registerJsFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/component/bootstrap-tokenfield/bootstrap-tokenfield.min.css');
// Sortable
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCss('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/videoEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/soundEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/imgEdit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/add.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Lang::t('page/listAdd', 'title');

$this->params['breadcrumbs'][] = $this->title;

$soundsItem = $item->sounds;
$imgsItem = $item->getImgsSort();

$thisUser = \common\models\User::thisUser();
/** @var Music[] $musics */
$musics = $thisUser->getLastAudio();
$userImgs = $thisUser->getUserImgs();
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

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/listEdit', 'titleVideo') ?>
                <a id="addVideoButton" class="btn btn-success btn-sm pull-right" data-max-video="<?= Item::MAX_VIDEO_ITEM ?>"><?= Lang::t('page/listEdit', 'btnAddVideo') ?></a>
            </label>
            <div id="blockVideos">

            </div>

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/listEdit', 'titleImg') ?>
                <a class="btn btn-success btn-sm btn-show-add-img pull-right" data-max-img="<?= Item::MAX_IMG_ITEM ?>"><?= Lang::t('main/img', 'btnAdd') ?></a>
            </label>
            <div class="hide">
                <?php
                ?>
            </div>
            <div id="blockImgs">
                <?php
                foreach ($imgsItem as $img) {
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

            <label style="width: 100%" class="control-label">
                <?= Lang::t('page/listEdit', 'titleAudio') ?>
                <a class="btn btn-success btn-sm btn-show-add-music pull-right"><?= Lang::t('main/music', 'btnAdd') ?></a>
            </label>
            <table id="blockSounds" class="margin-bottom">
                <?php
                foreach ($soundsItem as $sound) {
                    ?>
                    <tr class="audio-list-item">
                        <td><?= SoundWidget::widget(['music' => $sound]) ?></td>
                        <td>
                            <input type="hidden" name="sounds[]" class="form-control" value="<?= $sound->id ?>">
                            <span class="btn btn-link btn-edit-sound-link glyphicon glyphicon-pencil" data-toggle="modal" data-target=".modal-edit-music"></span>
                            <span class="btn btn-link btn-delete-sound-link glyphicon glyphicon-remove"></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <div class="input-group margin-bottom">
                <span class="input-group-addon" id="basic-addon1">Метки</span>
                <?= Html::textInput('tags', '', array('id' => 'tokenfield', 'data-tokens' => '', 'class' => 'form-control')) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/listAdd', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_MUSIC, 'musics' => $musics]) ?>
    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_IMG, 'imgs' => $userImgs]) ?>
    <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_EDIT_MUSIC]) ?>

</div>
