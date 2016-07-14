<?php
/**
 * @var int $selectMenu
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$thisPage = isset(Yii::$app->controller->thisPage) ? Yii::$app->controller->thisPage : 'list';

?>
<div class="main-button-block">
    <?php
    echo Html::a(
        mb_strtoupper(Lang::t('main', 'mainButtonList')),
        Url::home(),
        ['class' => 'btn-label-main' . ($thisPage == 'list' ? ' youarehere' : '')]
    ), " ";

    echo Html::a(
        mb_strtoupper(Lang::t('main', 'mainButtonEvents')),
        ['/events/all'],
        ['class' => 'btn-label-main' . ($thisPage == 'event' ? ' youarehere' : '')]
    ), " ";

    echo Html::a(
        mb_strtoupper(Lang::t('main', 'mainButtonSchools')),
        ['/schools/all'],
        ['class' => 'btn-label-main' . ($thisPage == 'school' ? ' youarehere' : '')]
    ), " ";


    echo Html::a(
        mb_strtoupper(Lang::t('main', 'about')),
        ['site/about'],
        ['class' => 'btn-label-main' . ($thisPage == 'about' ? ' youarehere' : '')]
    ), " ";
    echo Html::a(
        mb_strtoupper(Lang::t('main', 'feedback')),
        ['site/contact'],
        ['class' => 'btn-label-main' . ($thisPage == 'contact' ? ' youarehere' : '')]
    ), " ";
    ?>
</div>
