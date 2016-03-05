<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Lang::t('page/siteLogin', 'title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Lang::t('page/siteLogin', 'labelLogin') ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->label(Lang::t('page/siteLogin', 'username')) ?>

                <?= $form->field($model, 'password')->label(Lang::t('page/siteLogin', 'password'))->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->label(Lang::t('page/siteLogin', 'remember'))->checkbox() ?>

                <div style="color:#999;margin:1em 0">
                    <?= Html::a(Lang::t('page/siteLogin', 'reset'), ['site/request-password-reset']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/siteLogin', 'login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
            <?= Lang::t('page/siteLogin', 'toSignup') ?> <?= Html::a(Lang::t('main', 'signup'), Url::to(['site/signup']), ['class' => 'btn btn-label']) ?>
        </div>
    </div>
</div>
