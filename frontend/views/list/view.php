<?php
/**
 * @var yii\web\View        $this
 * @var Item $item
 * @var Vote                $vote
 */
use common\models\Comment;
use common\models\Item;
use common\models\Music;
use common\models\TagEntity;
use common\models\User;
use common\models\Video;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\SoundWidget;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/list/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42/share42.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $item->getTitle2();

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
$videos = $item->videos;
$imgsItem = $item->getImgsSort();
$mainImage = null;
$image_src = '';
if (!empty($imgsItem)) {
    reset($imgsItem);
    $mainImage = current($imgsItem);
    $image_src = $mainImage->short_url;
}
$sounds = $item->sounds;
$tags = $item->tagEntity;

$description = $this->title;
$description .= ". " . $item->getShortDescription(100, '') . "..";
$urlVideo = '';
foreach ($videos as $video) {
    $description .= ". " . $video->video_title . "; ";
    if (empty($urlVideo)) {
        $urlVideo = $video->getThumbnailUrl();
    }
}
foreach ($sounds as $sound) {
    $description .= ". " . $sound->getArtist() . " - " . $sound->getTitle() . "; ";
}
preg_match_all('/[^\W\d][\w]*/', $this->title, $wordArr);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => join(', ', $item->getKeywords()),
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

if (empty($image_src) && !empty($urlVideo)) {
    $image_src = $urlVideo;
}
if (!empty($image_src)) {
    $this->registerLinkTag([
        'rel'  => 'image_src',
        'href' => $image_src,
    ], 'linkImageSrc');

    $this->registerMetaTag([
        'property' => 'og:image',
        'content'  => $image_src,
    ], 'propertyImage');
}
?>
<div id="item-header">
    <h1>
        <?= Html::a($item->getTitle(), $url, ['class' => 'item-hyperlink']) ?>
        <?php
        if (Yii::$app->user->can(User::PERMISSION_EDIT_ITEMS, ['object' => $item])) {
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
    <div class="col-sm-1 text-center vote-block visible-md-block visible-lg-block visible-sm-block">
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
    <div class="col-sm-1 text-center vote-block visible-xs-block">
        <?= Html::tag("i", $voteLeftHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_ITEM]) ?>
        <span class="vote-count-item">
            <?= $item->like_count ?>
        </span>
        <?= Html::tag("i", $voteRightHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $item->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_ITEM]) ?>
    </div>


    <div class="col-sm-11 block-item-view">
        <div class="item-text">
            <?php
            echo HtmlPurifier::process($item->description, []);
            ?>
        </div>
        <?php
        if (count($videos) > 0) {
            ?>
            <h3><?= Lang::t('page/listView', 'titleVideo') ?>:</h3>
            <?php
            echo \frontend\widgets\VideosWidget::widget(['videos' => $videos]);
        }
        if (count($imgsItem) > 0) {
            ?>
            <h3><?= Lang::t('page/listView', 'titleImg') ?>:</h3>
            <?php
            echo "<div class='block-imgs'>";
            foreach ($imgsItem as $img) {
                echo Html::tag(
                    'div',
                    Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img', 'data-img-url' => $img->short_url]),
                    ['class' => 'img-input-group']
                );
                echo Html::img($img->short_url, ['class' => 'hide']);
            }
            echo '</div>';
        }
        if (count($sounds) > 0) {
            ?>
            <h3><?= Lang::t('page/listView', 'titleSound') ?>:</h3>
            <?php
            foreach ($sounds as $sound) {
                echo SoundWidget::widget(['music' => $sound]);
            }
        }
        ?>
        <br/>
        <div class="margin-bottom tag-line-height">
            <?php
            $tagValues = [];
            foreach ($tags as $tag) {
                $tagItem = $tag->tags;
                $urlTag = Url::to(['/', 'tag' => $tagItem->getName()]);
                echo Html::a($tagItem->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>
        <div>
            <?php
            if (Yii::$app->user->can(User::PERMISSION_DELETE_ITEMS, ['object' => $item])) {
                echo Html::button(
                    Lang::t('page/listView', 'delete'),
                    [
                        'class'       => 'btn btn-link no-focus',
                        'data-toggle' => "modal",
                        'data-target' => ".modal-delete-confirm",

                    ]
                ), ' ';
            }
            if (Yii::$app->user->can(User::PERMISSION_EDIT_ITEMS, ['object' => $item])) {
                echo Html::a(
                    Lang::t('page/listView', 'edit2'),
                    Url::to(['list/edit', 'id' => $item->id]),
                    ['class' => 'btn btn-link no-focus']
                ), ' ';
            }
            echo Html::button(
                Lang::t('page/listView', 'share'),
                [
                    'id'    => 'btnShare',
                    'class' => 'btn btn-link no-focus',
                ]
            ), ' ';
            echo Html::button(
                Lang::t('main/dialogs', 'modalAlarm_alarm'),
                [
                    'class'       => 'btn btn-link no-focus',
                    'data-toggle' => "modal",
                    'data-target' => ".modal-alarm",
                ]
            ), ' ';
            /** @var User $author */
            $author = $item->user;
            ?>
            <div class="pull-right">
                <table>
                    <tr>
                        <td>
                            <div class="mini-like-show">
                                <?php
                                $likeTitle = $item->like_count . " " .Lang::tn('main', 'vote', $item->like_count);
                                $showTitle = $item->show_count . " " .Lang::tn('main', 'showCount', $item->show_count);
                                ?>
                                <span title="<?= $likeTitle ?>"><i class="glyphicon glyphicon-thumbs-up"></i> <?= $item->like_count ?></span><br/>
                                <span title="<?= $showTitle ?>"><i class="glyphicon glyphicon-eye-open"></i> <?= $item->show_count ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="pull-right user-info">
                                <div class="user-action-time">
                                    <?= Lang::t("main", "created") . " " . date("d.m.Y", $item->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $item->date_create) ?>
                                </div>
                                <div class="user-gravatar32">
                                    <div class="background-img" style="background-image: url('<?= $author->getAvatarPic() ?>')"></div>
                                </div>
                                <div class="user-details">
                                    <?= $author->getDisplayName() ?> (<b><?= $author->reputation ?></b>)
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="share42init hide"></div>
    </div>
</div>
<div class="row">
    <hr/>
    <div class="col-md-12">
        <div>
            <h3><?= Lang::t('page/listView', 'titleComment') ?></h3>
            <div>
                <?= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_ITEM, 'entity_id' => $item->id]); ?>
            </div>
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
                   class="btn btn-danger"><?= Lang::t('page/listView', 'deleteBtn') ?></a>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/listView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ALARM, 'id' => $item->id]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]) ?>