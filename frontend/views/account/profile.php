<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\User $user
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile('//ulogin.ru/js/ulogin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$userDisplayName = $user->getDisplayName();
$ulogins = \common\models\Ulogin::findAll(['user_id' => $user->id]);
?>
<div class="site-index">
    <div class="body-content">
        <div class="col-md-12">
            <h1><?php
                echo $userDisplayName;
                echo Html::a(
                    Lang::t('page/accountProfile', 'edit'),
                    Url::to(['account/edit', 'id' => $user->id]),
                    ['class' => 'btn btn-success pull-right']
                );
                ?></h1>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <img src="<?= $user->getAvatarPic() ?>">
            </div>
        </div>
        <div class="col-md-8">
            <div><b><?= Lang::t('page/accountProfile', 'firstname') ?>:</b> <?= $user->getFirstname() ?></div>
            <div><b><?= Lang::t('page/accountProfile', 'lastname') ?>:</b> <?= $user->getLastname() ?></div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <ul>
                    <?php
                    if (!empty($ulogins)) {
                        foreach ($ulogins as $ulogin) {
                            echo "<li>" . $ulogin->network . " - " . $ulogin->identity . Html::a("×", "javascript:socialUnbind({$ulogin->id});", ['data-social' => $ulogin->id, 'class' => 'social-unbind']) . "</li>";
                        }
                    }
                    ?>
                </ul>
                Привязать:
                <div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,email;optional=nickname;providers=facebook,google,vkontakte,twitter,odnoklassniki,mailru;hidden=other;redirect_uri=;callback=connect"></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function bindSocial(tok) {
        jQuery.ajax({
            url: '<?= Url::to(['site/uloginbind']);?>',
            type: "POST",
            data: {login_ulogin: tok},
            success: function (data) {
            }
        });
    }

    function socialUnbind(social) {
        jQuery.ajax({
            url: '<?= Url::to(['site/uloginunbind']);?>',
            type: "POST",
            data: {social: social},
            success: function (data) {
            }
        });
    }
    ;
</script>