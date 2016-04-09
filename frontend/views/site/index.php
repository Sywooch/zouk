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

echo $this->render('/list/tabs', ['selectTab' => 1, 'searchTag' => $searchTag]);
?>
<div class="site-index">
    <div class="body-content">

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
</div>
