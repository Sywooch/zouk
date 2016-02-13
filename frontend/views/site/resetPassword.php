<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Lang::t('page/siteLogin', 'titleReset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Lang::t('page/siteLogin', 'labelReset2') ?>Please choose your new password:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->label(Lang::t('page/siteLogin', 'password'))->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/siteLogin', 'save'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
