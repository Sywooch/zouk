<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Lang::t('page/contact', 'title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <div id="item-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p><?= Lang::t('page/contact', 'labelContact') ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'name')->label(Lang::t('page/contact', 'name')) ?>

                <?= $form->field($model, 'email')->label(Lang::t('page/contact', 'email')) ?>

                <?= $form->field($model, 'subject')->label(Lang::t('page/contact', 'subject')) ?>

                <?= $form->field($model, 'body')->label(Lang::t('page/contact', 'body'))->textArea(['rows' => 6]) ?>

                <?= $form->field($model, 'verifyCode')->label(Lang::t('page/contact', 'verifyCode'))->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/contact', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
