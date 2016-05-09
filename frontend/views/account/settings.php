<?php
/**
 * @var User               $user
 * @var ChangePasswordForm $changePasswordModel
 * @var Ulogin             $ulogins
 */

use common\models\Ulogin;
use common\models\User;
use frontend\models\ChangePasswordForm;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/account/settings.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//ulogin.ru/js/ulogin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['bindSocialUrl'] = Url::to(['site/uloginbind']);
Yii::$app->params['jsZoukVar']['unbindSocialUrl'] = Url::to(['site/uloginunbind']);
?>
<div id="item-header">
    <h1><?= Lang::t('page/accountProfile', 'titleSettings') ?></h1>
</div>

<div>
    <div class="row">
        <div class="col-sm-9">
            <h3><?= Lang::t('page/accountProfile', 'titleChangePassword') ?></h3>
            <?php
            $form = ActiveForm::begin(['id' => 'profile-settings-form']);
            echo $form->field($changePasswordModel, 'password')->label(Lang::t('page/accountProfile', 'old_password'))->passwordInput();
            echo $form->field($changePasswordModel, 'newPassword')->label(Lang::t('page/accountProfile', 'new_password'))->passwordInput();
            echo Html::submitButton(Lang::t('page/accountProfile', 'buttonChangePassword'), ['class' => 'btn btn-default', 'name' => 'list-add-button']);
            ActiveForm::end();
            ?>

            <h3><?= Lang::t('page/accountProfile', 'titleUloginLink') ?></h3>
            <table style="border-collapse: separate; border-spacing: 5px;">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
                if (!empty($ulogins)) {
                    foreach ($ulogins as $ulogin) {
                        echo "<tr>";
                        echo Html::tag('td', Html::tag('b', $ulogin->network));
                        echo Html::tag('td', Html::a($ulogin->identity, $ulogin->identity));
                        echo Html::tag(
                            'td',
                            Html::a(
                                "<span class='glyphicon glyphicon-remove'></span>",
                                Url::to(['account/settings']),
                                ['data-social' => $ulogin->id, 'class' => 'social-unbind', 'target' => '_blank']
                            )
                        );
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <label>Привязать:</label>
            <div id="uLogin"
                 data-ulogin="display=panel;fields=first_name,last_name,email;optional=nickname;providers=facebook,google,vkontakte,twitter,odnoklassniki,mailru;hidden=other;redirect_uri=;callback=bindSocial"></div>

        </div>
    </div>
</div>
