<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\User $user
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$userDisplayName = $user->getDisplayName();
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
    </div>
</div>

