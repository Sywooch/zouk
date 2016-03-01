<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\User $user
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$userDisplayName = $user->display_name;
if (empty($userDisplayName)) {
    $userDisplayName = "user" . $user->id;
}
$userAvatarUrl = $user->avatar_pic;
if (empty($userAvatarUrl)) {
    $userAvatarUrl = Yii::$app->UrlManager->to('img/no_avatar.png');
}
?>
<div id="item-header">
    <h1><?= Lang::t('page/accountProfile', 'title') ?></h1>
</div>
<div>
    <div class="row">
        <div class="col-lg-9">
            <?php $form = ActiveForm::begin(['id' => 'profile-edit-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($user, 'imageFile')->label(Lang::t('page/accountProfile', 'avatar_pic'))->fileInput() ?>
            <?= $form->field($user, 'display_name')->label(Lang::t('page/accountProfile', 'display_name')) ?>
            <?= $form->field($user, 'firstname')->label(Lang::t('page/accountProfile', 'firstname')) ?>
            <?= $form->field($user, 'lastname')->label(Lang::t('page/accountProfile', 'lastname')) ?>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/accountProfile', 'buttonSave'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
