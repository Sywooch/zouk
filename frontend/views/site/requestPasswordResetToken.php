<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Lang::t('page/siteLogin', 'titleReset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Lang::t('page/siteLogin', 'labelReset1') ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->label(Lang::t('page/siteLogin', 'email')) ?>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/siteLogin', 'send'), ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
