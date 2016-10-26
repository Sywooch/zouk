<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Lang::t('page/siteLogin', 'titleSignup');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('//ulogin.ru/js/ulogin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://www.google.com/recaptcha/api.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="site-signup">
    <div id="item-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->label(Lang::t('page/siteLogin', 'username')) ?>

                <?= $form->field($model, 'email')->label(Lang::t('page/siteLogin', 'email')) ?>

                <?= $form->field($model, 'password')->label(Lang::t('page/siteLogin', 'password'))->passwordInput() ?>

                <div class="g-recaptcha" data-sitekey="<?= Yii::$app->google->googleRecaptchaPublic ?>"></div>

                <div class="form-group">
                    <?= Html::submitButton(Lang::t('page/siteLogin', 'signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
            <label>Войти через:</label>
            <div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,email;optional=nickname;providers=facebook,google,vkontakte,twitter,odnoklassniki,mailru;hidden=other;redirect_uri=;callback=connect"></div>
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