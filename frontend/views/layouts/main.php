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
$var = isset(Yii::$app->params['jsZoukVar']) ? Yii::$app->params['jsZoukVar'] : [];
$this->registerJs("var jsZoukVar = " . json_encode($var) . ";", View::POS_HEAD);

// Musics Player
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/soundmanager/soundmanager2-nodebug-jsmin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/soundmanager/music.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/soundmanager.css', ['depends' => [\yii\web\JqueryAsset::className()]]);

$thisPage = isset(Yii::$app->controller->thisPage) ? Yii::$app->controller->thisPage : 'list';
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
        'brandLabel' => 'ProZouk',
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $thisLang = Lang::getCurrent();

    if ($thisPage == 'list') {
        $mainUrl = Url::home();
    } else if ($thisPage == 'event') {
        $mainUrl = ['events/all'];
    }
    ?>
    <ul id="w1" class="navbar-nav navbar-left nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
               aria-expanded="false">
                <?= Html::img($thisLang->getImg(), ['height' => '16px']) ?> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php
                $langs = Lang::find()->where('id != :current_id', [':current_id' => Lang::getCurrent()->id])->all();
                foreach ($langs as $lang) {
                    echo Html::tag(
                        'li',
                        Html::a(
                            Html::img($lang->getImg(), ['height' => '16px']) . ' ' . $lang->name,
                            Yii::$app->UrlManager->toLang($lang)
                        )
                    );
                }
                ?>
            </ul>
        </li>
    </ul>
    <?php

    //    echo Nav::widget([
    //        'options' => ['class' => 'navbar-nav', 'encodeLabels' => false,],
    //        'items'   => $menuLangItems,
    //    ]);

    $menuItems = [
        ['label' => Lang::t('main', 'about'), 'url' => ['site/about']],
        ['label' => Lang::t('main', 'feedback'), 'url' => ['site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Lang::t('main', 'loginSignup'), 'url' => ['site/login']];
    } else {
        $displayName = User::thisUser()->getDisplayName();
        $displayProfile = Html::tag('div', '', ['style' => "background-image: url('" . User::thisUser()->getAvatarPic() . "');", 'class' => 'background-img nav-profile-img']) . " " .
                (empty($displayName) ? Lang::t('main', 'profile') : $displayName) . ' ' .
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
                <?= Html::a(Html::img(Yii::$app->UrlManager->to('img/logo.png'), ['height' => '100px']), $mainUrl, ['class' => 'pull-left visible-md-block visible-lg-block visible-sm-block']) ?>
                <div class="main-button-block">
                    <?php
                    echo Html::a(
                        Lang::t('main', 'mainButtonList'),
                        Url::home(),
                        ['class' => 'btn-label-main' . ($thisPage == 'list' ? ' youarehere' : '')]
                    ), " ";

                    echo Html::a(
                        Lang::t('main', 'mainButtonEvents'),
                        ['/events/all'],
                        ['class' => 'btn-label-main' . ($thisPage == 'event' ? ' youarehere' : '')]
                    ), " ";
                    //                    echo Html::button(Lang::t('main', 'mainButtonTags'), ['class' => 'btn btn-default']), " ";
                    //                    echo Html::button(Lang::t('main', 'mainButtonSchools'), ['class' => 'btn btn-default']), " ";
                    if ($thisPage == 'list') {
                        echo Html::a(
                            Lang::t('main', 'mainButtonAddRecord'),
                            ['/list/add'],
                            ['class' => 'btn-label-main add-item']
                        ), " ";
                    } else if ($thisPage == 'event') {
                        echo Html::a(
                            Lang::t('main', 'mainButtonAddEvent'),
                            ['/events/add'],
                            ['class' => 'btn-label-main add-item']
                        ), " ";
                    }
                    ?>
                </div>
            </div>

        </div>
        <?= $content ?>
    </div>
</div>

<footer class="footer ">
    <div class="container">
        <p class="pull-left">&copy; ProZouk <?= date('Y') ?></p>
        <?php
        echo Html::a(Html::img(Yii::$app->UrlManager->to('img/social/vk.png'), ['height' => '30px']), 'https://vk.com/prozouk', ['class' => 'margin-right-10']);
        echo Html::a(Html::img(Yii::$app->UrlManager->to('img/social/twitter.png'), ['height' => '30px']), 'https://twitter.com/pro_zouk', ['class' => 'margin-right-10']);
        echo Html::a(Html::img(Yii::$app->UrlManager->to('img/social/youtube.png'), ['height' => '30px']), 'https://www.youtube.com/channel/UCTDPXDsQqdMEmQ4aidSDomQ', ['class' => 'margin-right-10']);
        echo Html::a(Html::img(Yii::$app->UrlManager->to('img/social/googleplus.png'), ['height' => '30px']), 'https://plus.google.com/+BrazilianzoukRuStyle', ['class' => 'margin-right-10']);
        ?>
        <?php if (!YII_DEBUG) { ?>
            <p class="pull-right" style="margin-right: 10px">
                <!--LiveInternet counter-->
                <script type="text/javascript"><!--
                    document.write("<a href='http://www.liveinternet.ru/click' " +
                        "target=_blank><img src='//counter.yadro.ru/hit?t14.7;r" +
                        escape(document.referrer) + ((typeof(screen) == "undefined") ? "" :
                        ";s" + screen.width + "*" + screen.height + "*" + (screen.colorDepth ?
                            screen.colorDepth : screen.pixelDepth)) + ";u" + escape(document.URL) +
                        ";" + Math.random() +
                        "' alt='' title='LiveInternet: показано число просмотров за 24" +
                        " часа, посетителей за 24 часа и за сегодня' " +
                        "border='0' width='88' height='31'><\/a>")
                    //--></script><!--/LiveInternet-->

                <!-- Yandex.Metrika informer -->
                <a href="https://metrika.yandex.ru/stat/?id=25259342&amp;from=informer"
                   target="_blank" rel="nofollow"><img
                        src="//bs.yandex.ru/informer/25259342/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
                        style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика"
                        title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)"
                        onclick="try{Ya.Metrika.informer({i:this,id:25259342,lang:'ru'});return false}catch(e){}"/></a>
                <!-- /Yandex.Metrika informer -->

                <!-- Yandex.Metrika counter -->
                <script type="text/javascript">
                    (function (d, w, c) {
                        (w[c] = w[c] || []).push(function () {
                            try {
                                w.yaCounter25259342 = new Ya.Metrika({
                                    id: 25259342,
                                    clickmap: true,
                                    trackLinks: true,
                                    accurateTrackBounce: true
                                });
                            } catch (e) {
                            }
                        });

                        var n = d.getElementsByTagName("script")[0],
                            s = d.createElement("script"),
                            f = function () {
                                n.parentNode.insertBefore(s, n);
                            };
                        s.type = "text/javascript";
                        s.async = true;
                        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                        if (w.opera == "[object Opera]") {
                            d.addEventListener("DOMContentLoaded", f, false);
                        } else {
                            f();
                        }
                    })(document, window, "yandex_metrika_callbacks");
                </script>
            <noscript>
                <div><img src="//mc.yandex.ru/watch/25259342" style="position:absolute; left:-9999px;" alt=""/></div>
            </noscript>
            <!-- /Yandex.Metrika counter -->
            </p>
        <?php } ?>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
