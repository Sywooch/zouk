<?php
/**
 * @var string       $searchTag
 * @var yii\web\View $this
 */

use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

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

?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-md-8">
                <?= $this->render('/list/tabs', ['selectTab' => 1, 'searchTag' => $searchTag]) ?>
                <?php if (Yii::$app->user->isGuest) { ?>
                    <div class="row block-info-about">
                        <div class="col-sm-12">
                            <div class="info-about-mini visible-sm-block visible-xs-block">
                                <p>
                                    <?= Lang::t('main', 'infoBlockMain') ?>
                                </p>
                                <p>
                                    <?= Html::a(Lang::t('main', 'loginSignup'), Url::to(['site/login']), ['class' => 'btn btn-primary']) ?>
                                </p>
                            </div>

                            <table class="info-about visible-md-block visible-lg-block">
                                <tr>
                                    <td rowspan="2">
                                        <p>
                                            <?= Lang::t('main', 'infoBlockMain') ?>
                                        </p>
                                        <p>
                                            <?= Html::a(Lang::t('main', 'loginSignup'), Url::to(['site/login']), ['class' => 'btn btn-primary']) ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?= Html::img(Yii::$app->UrlManager->to('img/info/info1.png')) ?>
                                    </td>
                                    <td>
                                        <?= Html::img(Yii::$app->UrlManager->to('img/info/info2.png')) ?>
                                    </td>
                                    <td>
                                        <?= Html::img(Yii::$app->UrlManager->to('img/info/info3.png')) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">
                                        <p>
                                            <?= Lang::t('main', 'infoBlockStep1') ?>
                                        </p>
                                    </td>
                                    <td width="20%">
                                        <p>
                                            <?= Lang::t('main', 'infoBlockStep2') ?>
                                        </p>
                                    </td>
                                    <td width="20%">
                                        <p>
                                            <?= Lang::t('main', 'infoBlockStep3') ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php } ?>

                <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID, 'searchTag' => $searchTag]) ?>
            </div>
            <div class="col-md-4">
                <?php
                echo Html::a(
                    Lang::t('main', 'mainButtonAddRecord'),
                    ['/list/add'],
                    ['class' => 'btn btn-success btn-label-main add-item']
                );
                echo $this->render('/list/listRightBlock');
                ?>

            </div>
        </div>

    </div>
</div>
