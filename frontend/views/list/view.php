<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 * @var Vote                $vote
 */
use common\models\User;
use common\models\Video;
use common\models\Vote;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $item->title;

$this->params['breadcrumbs'][] = Lang::t('page/listView', 'title');

$thisUser = User::thisUser();
$voteItem = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_ITEM, $item->id) : null;
$voteUpHtml = '<span class="glyphicon glyphicon-triangle-top"></span>';
$voteLeftHtml = '<span class="glyphicon glyphicon-triangle-left"></span>';
$voteDownHtml = '<span class="glyphicon glyphicon-triangle-bottom"></span>';
$voteRightHtml = '<span class="glyphicon glyphicon-triangle-right"></span>';
$urlUp = Url::to(['vote/add']);
$urlDown = Url::to(['vote/add']);

$divLikeClass = ['cp', 'vote-up-link'];
if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_UP) {
    $divLikeClass[] = 'voted';
}
$divLikeClass = join(' ', $divLikeClass);

$divDislikeClass = ['cp', 'vote-down-link'];
if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_DOWN) {
    $divDislikeClass[] = 'voted';
}
$divDislikeClass = join(' ', $divDislikeClass);

$url = $item->getUrl();
?>
<div id="item-header">
    <h1>
        <?= Html::a($item->title, $url, ['class' => 'item-hyperlink']) ?>
        <?php
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
            echo Html::a(
                Lang::t('page/listView', 'edit'),
                Url::to(['list/edit', 'id' => $item->id]),
                ['class' => 'btn btn-success pull-right']
            );
        }
        ?>
    </h1>

</div>


<div class="row">
    <div class="col-lg-1 text-center vote-block visible-md-block visible-lg-block">
        <div>
            <?= Html::tag("div", $voteUpHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        </div>
        <div>
            <span class="vote-count-item">
                <?= $item->like_count ?>
            </span>
        </div>
        <div>
            <?= Html::tag("div", $voteDownHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        </div>
    </div>
    <div class="col-lg-1 text-center vote-block visible-xs-block visible-sm-block">
        <?= Html::tag("i", $voteLeftHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        <span class="vote-count-item">
            <?= $item->like_count ?>
        </span>
        <?= Html::tag("i", $voteRightHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]) ?>
    </div>


    <div class="col-lg-11">
        <div class="item-text">
            <?php
            echo \yii\helpers\HtmlPurifier::process($item->description, []);
            ?>
        </div>
        <?php
        $videos = $item->videos;
        if (count($videos) > 0) {
            ?>
            <h3>Видео:</h3>
            <?php
            echo \frontend\widgets\VideosWidget::widget(['videos' => $videos]);
        }
        ?>
        <br/>
        <div class="margin-bottom">
            <?php
            $tags = $item->tagEntity;
            $tagValues = [];
            foreach ($tags as $tag) {
                $tagItem = $tag->tags;
                echo Html::tag('span', $tagItem->name, ['class' => 'label label-primary']), " ";
            }
            ?>
        </div>
        <div>
            <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $item->user_id) {
                echo Html::button(
                    Lang::t('page/listView', 'delete'),
                    [
                        'class'       => 'btn btn-link',
                        'data-toggle' => "modal",
                        'data-target' => ".modal-delete-confirm",

                    ]
                ), ' ';
            }
            echo Html::button(
                Lang::t('page/listView', 'alarm'),
                [
                    'class'       => 'btn btn-link',
                    'data-toggle' => "modal",
                    'data-target' => ".modal-alarm",
                ]
            ), ' ';
            ?>
        </div>
    </div>
</div>


<div class="modal fade modal-delete-confirm bs-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('page/listView', 'deleteConfirmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('page/listView', 'deleteConfirm') ?>
            </div>
            <div class="modal-footer">
                <a href="<?= Url::to(['list/delete', 'id' => $item->id]) ?>" type="button"
                   class="btn btn-danger"><?= Lang::t('page/listView', 'delete') ?></a>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/listView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-alarm bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('page/listView', 'modalAlarmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('page/listView', 'msgAlarm') ?>
                <?= Html::input('text', 'alarmMsg', '', ['class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <?= Html::a(
                    Lang::t('page/listView', 'alarm'),
                    Url::to(['list/alarm']),
                    [
                        'id'             => 'alarm-item',
                        'class'          => 'btn btn-danger',
                        'data-href'      => Url::to(['list/alarm']),
                        'data-msg-alarm' => Lang::t('page/listView', 'msgAlarm'),
                        'data-id'        => $item->id,
                    ]
                ), ' ';
                ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/listView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>