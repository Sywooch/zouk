<?php
/**
 * @var User               $user
 * @var ChangePasswordForm $changePasswordModel
 * @var Ulogin             $ulogins
 */

use common\models\Ulogin;
use common\models\User;
use frontend\assets\UloginAsset;
use frontend\models\ChangePasswordForm;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

UloginAsset::register($this);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/account/settings.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Yii::$app->params['jsZoukVar']['bindSocialUrl'] = Url::to(['site/uloginbind']);
Yii::$app->params['jsZoukVar']['unbindSocialUrl'] = Url::to(['site/uloginunbind']);

$userDisplayName = $user->getDisplayName();
?>
<div id="item-header">
    <h1><?= Html::a($userDisplayName, ['account/profile']) ?> / <?= Lang::t('page/accountProfile', 'titleSettings') ?></h1>
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
                                "<span class='glyphicon glyphicon-remove btn btn-link'></span>",
                                Url::to(['account/settings']),
                                ['data-social' => $ulogin->id, 'class' => 'social-unbind', 'target' => '_blank']
                            )
                        );
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <label><?= Lang::t('page/accountProfile', 'socialConnect') ?></label>
            <div id="uLogin"
                 data-ulogin="display=panel;fields=first_name,last_name,email;optional=nickname;providers=facebook,google,vkontakte,twitter,odnoklassniki,mailru;hidden=other;redirect_uri=;callback=bindSocial">
            </div>
            <div class="clearfix"></div>

            <?php
            $vkAccessToken = \common\models\VkAccessToken::findOne(['user_id' => $user->id]);
            if (!empty($vkAccessToken)) {
                ?>
                <h3>Дуступ к группе вк</h3>
                <label>
                    Группа <b><?= $vkAccessToken->group_id; ?></b>: <code><?= $vkAccessToken->access_token; ?></code>.
                </label>
                <?php
            }
            ?>


            <h3>Дать доступ для постинга вк</h3>
            <?php
            $form = ActiveForm::begin(['action' => ['account/get-access-token'], 'id' => 'profile-settings-form', 'options' => ['target' => '_blank']]);

            echo Html::tag('label', 'ID группы', ['class' => 'control-label']);
            ?>
            <div class="input-group">
                <?= Html::textInput('group_id', '',['class' => 'form-control']); ?>
                <span class="input-group-btn">
                    <?= Html::submitButton('Получить code', ['class' => 'btn btn-default', 'name' => 'list-add-button']); ?>
                </span>
            </div>
            <?php
            echo Html::tag('label', 'Code', ['class' => 'control-label']);
            ?>
            <div class="input-group">
                <?= Html::textInput('code', '',['class' => 'form-control']); ?>
                <span class="input-group-btn">
                    <?= Html::submitButton('Получить access_token', ['class' => 'btn btn-default', 'name' => 'list-add-button']); ?>
                </span>
            </div>
            <?php
            echo Html::tag('p', 'После нажатия на кнопку "Получить code" Вас перенаправит на страницу доступа, в адресе будет написан code, который необходимо скопировать в поле и нажать на "Получить access_token"');


            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
