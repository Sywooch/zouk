<?php
/**
 *
 */

use common\models\Music;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/music/addMusic.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$music = new Music();


echo "<b>" . Lang::t('main/music', 'limitLabel') . "</b>";
echo "<ul>";
echo "<li>" . Lang::t('main/music', 'limitMaxSize') . "</li>";
echo "<li>" . Lang::t('main/music', 'limitCopyright') . "</li>";
echo "</ul>";

$form = ActiveForm::begin([
    'id'      => 'musicAddForm',
    'options' => ['enctype' => 'multipart/form-data'],
    'action'  => Url::to(['music/add'], true),
]);
echo $form->field($music, 'musicFile')->label(false)->fileInput(['id' => 'soundUpload', 'class' => 'hide']);
echo Html::button(Lang::t('main/music', 'musicFileField'), ['id' => 'btnSoundUpload', 'class' => 'btn btn-primary']);
?>
<div class="alert alert-info loading-info hide" role="alert"><?= Lang::t('main/music', 'loading') ?></div>

<?php ActiveForm::end(); ?>
