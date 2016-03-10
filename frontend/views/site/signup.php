<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Lang::t('page/siteLogin', 'titleSignup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div id="item-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p><?= Lang::t('page/siteLogin', 'labelSignup') ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->label(Lang::t('page/siteLogin', 'username')) ?>

                <?= $form->field($model, 'email')->label(Lang::t('page/siteLogin', 'email')) ?>

                <?= $form->field($model, 'password')->label(Lang::t('page/siteLogin', 'password'))->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/siteLogin', 'signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
