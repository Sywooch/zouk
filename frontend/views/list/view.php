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
$voteDownHtml = '<span class="glyphicon glyphicon-triangle-bottom"></span>';
$urlUp = Url::to(['vote/add']);
$urlDown = Url::to(['vote/add']);

$url = Url::to(['list/view', 'id' => $item->id]);
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
    <div class="col-lg-1 text-center vote-block hidden-xs hidden-sm">
        <div>
            <?php
            $divClass = ['cp', 'vote-up-link'];
            if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_UP) {
                $divClass[] = 'voted';
            }
            echo Html::tag("div", $voteUpHtml, ['data-href' => $urlUp, 'class' => join(' ', $divClass), 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]);
            ?>
        </div>
        <div>
            <span class="vote-count-item">
                <?= $item->like_count ?>
            </span>
        </div>
        <div>
            <?php
            $divClass = ['cp', 'vote-down-link'];
            if (!empty($voteItem) && $voteItem->vote == Vote::VOTE_DOWN) {
                $divClass[] = 'voted';
            }
            echo Html::tag("div", $voteDownHtml, ['data-href' => $urlDown, 'class' => join(' ', $divClass), 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]);
            ?>
        </div>
    </div>
    <div class="col-lg-11">
        <div class="item-text">
            <?= Html::encode($item->description) ?>
        </div>
        <?php
        /** @var Video[] $videos */
        $videos = $item->getVideos()->all();
        if (count($videos) > 0) {
            ?>
            <h3>Видео:</h3>
            <?php
            echo \frontend\widgets\VideosWidget::widget(['videos' => $videos]);
        }
        ?>
    </div>
</div>