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

$this->registerJsFile('//ulogin.ru/js/ulogin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="site-login">
    <div id="item-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

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
            <label>Войти через:</label>
            <div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,email;optional=nickname;providers=facebook,google,vkontakte,twitter,odnoklassniki,mailru;hidden=other;redirect_uri=;callback=connect"></div>
            <?= Lang::t('page/siteLogin', 'toSignup') ?> <?= Html::a(Lang::t('main', 'signup'), Url::to(['site/signup']), ['class' => 'btn btn-label']) ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function connect(tok) {
        jQuery.ajax({
            url: '<?= Url::to(['site/ulogin']);?>',
            type: "POST",
            data: {login_ulogin: tok},
            success: function(data) {
                data = JSON.parse(data);
                if (typeof(data.url) != "undefined") {
                    window.location = data.url;
                }
            }
        });
    }
</script>