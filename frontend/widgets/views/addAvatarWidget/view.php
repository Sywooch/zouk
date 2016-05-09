<?php
/**
 *
 */

use common\models\Img;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/img/addImg.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$user = new User();


echo "<b>" . Lang::t('main/img', 'limitLabel') . "</b>";
echo "<ul>";
echo "<li>" . Lang::t('main/img', 'limitMaxSize') . "</li>";
echo "<li>" . Lang::t('main/img', 'limitCopyright') . "</li>";
echo "</ul>";

$form = ActiveForm::begin([
    'id'      => 'imgAddForm',
    'options' => ['enctype' => 'multipart/form-data'],
    'action'  => Url::to(['account/editavatar'], true),
]);
echo $form->field($user, 'imageFile')->label(false)->fileInput(['id' => 'imgUpload', 'class' => 'hide']);
echo Html::button(Lang::t('main/img', 'avatarFileField'), ['id' => 'btnImgUpload', 'class' => 'btn btn-primary']);
?>
<div class="alert alert-info loading-info hide" role="alert"><?= Lang::t('main/img', 'loading') ?></div>

<?php ActiveForm::end(); ?>
