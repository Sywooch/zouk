<?php
/**
 * @var \common\models\Item $item
 * @var \common\models\User $author
 */

use common\models\User;
use frontend\models\Lang;
use yii\bootstrap\Html;

$isMock = $author->isMock();
?>

<div class="pull-right user-info">
    <div class="user-action-time">
        <?= Lang::t("main", "created") . " " . date("d.m.Y", $item->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $item->date_create) ?>
    </div>
    <div class="user-gravatar32">
        <?php
        $htmlAvatar = Html::tag('div', '', [
            'class' => 'background-img',
            'style' => 'background-image: url(\'' . $author->getAvatarPic() . '\');'
        ]);
        if ($isMock) {
            echo $htmlAvatar;
        } else {
            echo Html::a(
                $htmlAvatar,
                ['user/' . $author->display_name]
            );
        }
        ?>
    </div>
    <div class="user-details">
        <?= $author->getAUserLink(); ?>
    </div>
</div>