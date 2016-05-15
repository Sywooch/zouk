<?php
/**
 * @var yii\web\View $this
 * @var User         $user
 * @var Userinfo     $userinfo
 */

use common\models\Countries;
use common\models\User;
use common\models\Userinfo;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

// Country
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css');
// Calendar
$this->registerJsFile('//code.jquery.com/ui/1.11.4/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/account/edit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$userDisplayName = $user->getDisplayName();
$userAvatarUrl = $user->avatar_pic;
if (empty($userAvatarUrl)) {
    $userAvatarUrl = Yii::$app->UrlManager->to('img/no_avatar.png');
}

$countries = array_merge([0 => '-'], Countries::getCountries(Lang::getCurrent()));
?>
<div id="item-header">
    <h1><?= Lang::t('page/accountProfile', 'title') ?></h1>
</div>
<div>
    <div class="row">
        <div class="col-lg-9">
            <h3><?= Lang::t('page/accountProfile', 'titleMainInfo') ?></h3>
            <?php $form = ActiveForm::begin(['id' => 'profile-edit-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($user, 'display_name')->label(Lang::t('page/accountProfile', 'display_name')) ?>
            <?= $form->field($user, 'firstname')->label(Lang::t('page/accountProfile', 'firstname')) ?>
            <?= $form->field($user, 'lastname')->label(Lang::t('page/accountProfile', 'lastname')) ?>

            <?= $form->field($userinfo, 'country')->label(Lang::t('page/accountProfile', 'country'))->dropDownList($countries, ['id' => 'selectCountry']) ?>
            <?= $form->field($userinfo, 'city')->label(Lang::t('page/accountProfile', 'city')) ?>
            <?= $form->field($userinfo, 'birthday')->label(Lang::t('page/accountProfile', 'birthday'))->textInput(['id' => 'datepicker', 'value' => date('d.m.Y', $userinfo->birthday)]) ?>

            <h3><?= Lang::t('page/accountProfile', 'titleProfileInfo') ?></h3>
            <?= $form->field($userinfo, 'about_me')->label(Lang::t('page/accountProfile', 'about_me'))->textarea(['maxlength' => 1024]) ?>
            <?= $form->field($userinfo, 'telephone')->label(Lang::t('page/accountProfile', 'info_telephone'))->textInput(['maxlength' => 25]) ?>
            <?= $form->field($userinfo, 'skype')->label(Lang::t('page/accountProfile', 'info_skype'))->textInput(['maxlength' => 40]) ?>
            <?= $form->field($userinfo, 'vk')->label(Lang::t('page/accountProfile', 'info_vk'))->textInput(['maxlength' => 60]) ?>
            <?= $form->field($userinfo, 'fb')->label(Lang::t('page/accountProfile', 'info_fb'))->textInput(['maxlength' => 60]) ?>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/accountProfile', 'buttonSave'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
                <?= Html::a(Lang::t('page/accountProfile', 'buttonCancel'), ['account/profile'], ['class' => 'btn btn-default pull-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
