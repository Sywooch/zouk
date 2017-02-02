<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

use common\models\form\SearchEntryForm;
use common\models\User;
use common\models\Video;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$var = isset(Yii::$app->params['jsZoukVar']) ? Yii::$app->params['jsZoukVar'] : [];
$this->registerJs("var jsZoukVar = " . json_encode($var) . ";", View::POS_HEAD);

// Musics Player
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/soundmanager/soundmanager2-nodebug-jsmin.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/soundmanager/music.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/soundmanager.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/slick/slick-theme.css', ['depends' => [\yii\web\JqueryAsset::className()]]);

$year = date('Y');
$month = date('m');
$thisPage = isset(Yii::$app->controller->thisPage) ? Yii::$app->controller->thisPage : 'main';
$searchForm = Yii::$app->params['searchEntryForm'] ? Yii::$app->params['searchEntryForm'] : new SearchEntryForm();
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

    $mainUrl = Url::home();
    if ($thisPage == 'list') {
        $mainUrl = Url::home();
    } else if ($thisPage == 'event') {
        $mainUrl = ['events/all'];
    } else if ($thisPage == 'school') {
        $mainUrl = ['schools/all'];
    }
    ?>
    <ul id="w1" class="navbar-nav navbar-left nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
               aria-expanded="false">
                <?= Html::img($thisLang->getImg(), ['height' => '16px']) ?>
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

    $menuItems = [];
    
    if (Yii::$app->user->isGuest || Yii::$app->user->can(User::PERMISSION_MOCK_USER)) {
        $menuItems[] = ['label' => Lang::t('main', 'loginSignup'), 'url' => ['site/login']];
    } else {
        $childItems = [];
        $childItems[] = [
            'label'       => Lang::t('main', 'profile'),
            'url'         => ['account/profile'],
        ];
        $childItems[] = [
            'label'       => Lang::t('main', 'logout'),
            'url'         => ['site/logout'],
            'linkOptions' => ['data-method' => 'post'],
        ];

        $displayName = User::thisUser()->getDisplayName();
        $displayProfile = Html::tag('div', '', ['style' => "background-image: url('" . User::thisUser()->getAvatarPic() . "');", 'class' => 'background-img nav-profile-img']) . " " .
                (empty($displayName) ? Lang::t('main', 'profile') : $displayName) . ' ' .
                '<span class="badge">' . User::thisUser()->reputation . '</span>';
        $menuItems[] = [
            'encode'  => false,
            'label'   => $displayProfile,
            'items'   => $childItems,
            'options' => ['class' => 'hidden-xs']
        ];
        $menuItems[] = [
            'encode'  => false,
            'label'   => $displayProfile,
            'url'    => ['account/profile'],
            'options' => ['class' => 'visible-xs-block']
        ];
        $menuItems[] = [
            'label'       => Lang::t('main', 'logout'),
            'url'         => ['site/logout'],
            'linkOptions' => ['data-method' => 'post'],
            'options'     => ['class' => 'visible-xs-block'],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right', 'encodeLabels' => false,],
        'items'   => $menuItems,
    ]);

    if (in_array($thisPage, ['list', 'main'])) {
        echo $this->render('_searchForm', [
            'searchForm' => $searchForm,
            'formClass' => 'navbar-form navbar-right hidden-xs',
        ]);
    }
    NavBar::end();
    ?>

    <div class="container <?= isset($this->params['containerClass']) ? $this->params['containerClass'] : '' ?>">
        <div class="row main-header carousel-promotion">
            <div class="block-promo block-promo-prozouk"
                 data-img-url="<?= Yii::$app->UrlManager->to('img/promo/interesting_block_0.png'); ?>"
            >
                <div class="block-promo-social">
                    <div>
                        <?php
                        echo Html::a('', 'https://www.facebook.com/ProZouk', ['class' => 'margin-right-10', 'target' => '_blank', 'data-img-url' => Yii::$app->UrlManager->to('img/social/fb.png')]);
                        echo Html::a('', 'https://vk.com/prozouk', ['class' => 'margin-right-10', 'target' => '_blank', 'data-img-url' => Yii::$app->UrlManager->to('img/social/vk.png')]);
                        echo Html::a('', 'https://twitter.com/pro_zouk', ['class' => 'margin-right-10', 'target' => '_blank', 'data-img-url' => Yii::$app->UrlManager->to('img/social/twitter.png')]);
                        echo Html::a('', 'https://www.youtube.com/channel/UCTDPXDsQqdMEmQ4aidSDomQ', ['class' => 'margin-right-10', 'target' => '_blank', 'data-img-url' => Yii::$app->UrlManager->to('img/social/youtube.png')]);
                        echo Html::a('', 'https://plus.google.com/+BrazilianzoukRuStyle', ['class' => '', 'target' => '_blank', 'data-img-url' => Yii::$app->UrlManager->to('img/social/googleplus.png')]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="block-promo"
                 data-img-url="<?= Yii::$app->UrlManager->to('img/promo/' . $thisLang->url . '/interesting_block_1.png'); ?>"
            >
                <?= Html::a('', ['event/month', 'year' => $year, 'month' => (int)$month]); ?>
            </div>
            <div class="block-promo"
                 data-img-url="<?= Yii::$app->UrlManager->to('img/promo/' . $thisLang->url . '/interesting_block_2.png'); ?>"
            >
                <?= Html::a('', ['list/index']); ?>
            </div>
            <div class="block-promo"
                 data-img-url="<?= Yii::$app->UrlManager->to('img/promo/' . $thisLang->url . '/interesting_block_3.png'); ?>"
            >
                <?php
                $video = Video::getRandomVideo();
                echo Html::a(
                    '',
                    $video->original_url,
                    [
                        'target'                => '_blank',
                        'class'                 => 'video-random-link',
                        'data-video-id'         => $video->entity_id,
                        'data-video-url'        => $video->getVideoUrl(true),
                        'data-title'            => $video->video_title,
                        'data-random-video-url' => Url::to(['video/random']),
                    ]
                );
                ?>
            </div>
            <div class="block-promo"
                 data-img-url="<?= Yii::$app->UrlManager->to('img/promo/' . $thisLang->url . '/interesting_block_4.png'); ?>"
            >
                <?= Html::a('', ['list/index', 'tag' => 'article']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 main-menu-block">
                <?= $this->render('/layouts/menu') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($thisPage == 'list' || $thisPage == 'main') {
                    echo $this->render('_searchForm', [
                        'searchForm' => $searchForm,
                        'formClass' => 'visible-xs-block',
                    ]);
                }
                ?>
            </div>
        </div>

        <?= Alert::widget() ?>
        <div class="row">
        <?php
        if (!empty($this->params['breadcrumbs']) && count($this->params['breadcrumbs']) > 1) {
            echo Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
            ]);
        }
        ?>
        </div>
        <?= $content ?>
        <?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]); ?>
    </div>
</div>

<footer class="footer ">
    <div class="container">
        <p class="pull-left">&copy; ProZouk <?= date('Y') ?></p>
        <?php if (!YII_DEBUG) { ?>
            <p class="pull-right" style="margin-right: 10px">
                <!-- begin of Top100 code -->

                <script id="top100Counter" type="text/javascript" src="http://counter.rambler.ru/top100.jcn?4434208"></script>
                <noscript>
                    <a href="http://top100.rambler.ru/navi/4434208/">
                        <img src="http://counter.rambler.ru/top100.cnt?4434208" alt="Rambler's Top100" border="0" />
                    </a>

                </noscript>
                <!-- end of Top100 code -->

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
