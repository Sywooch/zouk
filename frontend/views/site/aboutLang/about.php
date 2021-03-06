<?php

/* @var $this yii\web\View */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Lang::t('page/about', 'title');

$keywords = 'brazilian zouk, zouk, бразильский зук, бразильский танец зук, бразильский зук школа танцев, научиться танцевать бразильский зук, видео бразильский зук, онлайн бразильсий зук, посмотреть бразильский зук, бразильский зук для начинающих, уроки зука базовые шаги';
$description = 'Зук – это современный, романтичный и ритмичный танец. Как музыкальное направление, зук появился приблизительно в 80-х годах XX-го века. Его ритмы начали звучать на французских островах Гваделупа, Мартиника, Гаити, Сент-Люсия и приобрели большую популярность за счет исполнителей Grammacks и Exile One. Однако бразильцы считают что эта музыка появилась во Французкой Полинезии.';

$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => $keywords,
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');


$this->registerJs("VK.Widgets.Group('vk_groups', {mode: 3}, 117236846);", \yii\web\View::POS_END);

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-about">
    <div style="margin: -10px -15px 0 -15px;">
        <?= Html::img(Yii::$app->UrlManager->to('img/about.png'), ['class' => 'about-logo']); ?>
    </div>
    <div>
        <h1 class="title truncate"><?= Html::encode($this->title) ?></h1>
    </div>

    <p>
        Дорогие друзья,мы рады видеть Вас на сайте Зук-портала <b>"ProZOUK"</b>!
    </p>
    <p>
        Мы развиваем этот проект с целью объединения зукеров всего мира. А также тех, кто еще не познал этот танец.
        Каждый из Вас может помочь нам в осуществлении цели проекта! На сайте Вы сможете:
        <ul>
        <li>добавлять аудио и видеозаписи;</li>
        <li>информировать о занятиях по зуку в Ваших школах;</li>
        <li>создавать анонсы зук-мероприятий;</li>
        <li>писать статьи про зук;</li>
        <li>и многое другое.</li>
        </ul>
    </p>
    <p>
        Если у Вас есть желание стать частью нашей команды или просто появились предложения,
        пожалуйста, <?= Html::a('свяжитесь с нами', Url::to(['site/contact'])) ?>.
    </p>

    <div class="row">
        <div class="col-md-offset-9 col-sm-offset-8 col-md-3 col-sm-4 text-centr">
            <div id="vk_groups" style="margin: auto"></div>
        </div>
    </div>
</div>
