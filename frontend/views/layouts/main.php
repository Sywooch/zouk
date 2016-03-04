<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$var = isset(Yii::$app->params['jsZoukVar']) ? Yii::$app->params['jsZoukVar'] : ["hello"];
$this->registerJs("var jsZoukVar = " . json_encode($var) . ";", View::POS_HEAD);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="icon" type="image/png" href="<?= Yii::$app->UrlManager->to('img/sunZ.png') ?>">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Brazilian Zouk'  ,
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => Lang::t('main', 'about'), 'url' => ['site/about']],
        ['label' => Lang::t('main', 'feedback'), 'url' => ['site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Lang::t('main', 'signup'), 'url' => ['site/signup']];
        $menuItems[] = ['label' => Lang::t('main', 'login'), 'url' => ['site/login']];
    } else {
        $displayProfile =  Html::img(User::thisUser()->getAvatarPic(), ['height' => 24]) . " " .
           (empty(User::thisUser()->display_name) ? Lang::t('main', 'profile') : User::thisUser()->display_name) . ' ' .
           '<span class="badge">' . User::thisUser()->reputation . '</span>';
        $menuItems[] = [
            'encode' => false,
            'label'  => $displayProfile,
            'url'    => ['account/profile'],
        ];
        $menuItems[] = [
            'label'       => Lang::t('main', 'logout'),
            'url'         => ['site/logout'],
            'linkOptions' => ['data-method' => 'post'],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right', 'encodeLabels' => false,],
        'items'   => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-md-12">
                <a class="pull-left visible-md-block visible-lg-block visible-sm-block" href="<?= Url::home() ?>"><img src="<?= Yii::$app->UrlManager->to('img/logo.png') ?>" height="100px"/></a>
                <div class="main-button-block">
                    <?php
                    echo Html::a(
                        Lang::t('main', 'mainButtonList'),
                        Url::home(),
                        ['class' => 'btn btn-info']
                    ), " ";
                    //                    echo Html::button(Lang::t('main', 'mainButtonTags'), ['class' => 'btn btn-default']), " ";
                    //                    echo Html::button(Lang::t('main', 'mainButtonEvents'), ['class' => 'btn btn-default']), " ";
                    //                    echo Html::button(Lang::t('main', 'mainButtonSchools'), ['class' => 'btn btn-default']), " ";
                    echo Html::a(
                        Lang::t('main', 'mainButtonAddRecord'),
                        ['/list/add'],
                        ['class' => 'btn btn-info']
                    ), " ";
                    ?>
                </div>
            </div>

        </div>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
