<?php
/**
 * @var yii\web\View $this
 * @var \common\models\Event $event
 * @var Vote $vote
 */

use common\models\Comment;
use common\models\Location;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\VideoList;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/event/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42/share42.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $event->getTitle2();

$this->params['breadcrumbs'][] = Lang::t('page/eventView', 'title');

$thisUser = User::thisUser();
$voteIEvent = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_EVENT, $event->id) : null;
$voteUpHtml = '<span class="glyphicon glyphicon-triangle-top"></span>';
$voteLeftHtml = '<span class="glyphicon glyphicon-triangle-left"></span>';
$voteDownHtml = '<span class="glyphicon glyphicon-triangle-bottom"></span>';
$voteRightHtml = '<span class="glyphicon glyphicon-triangle-right"></span>';
$urlUp = Url::to(['vote/add']);
$urlDown = Url::to(['vote/add']);

$divLikeClass = ['cp', 'vote-up-link'];
if (!empty($voteIEvent) && $voteIEvent->vote == Vote::VOTE_UP) {
    $divLikeClass[] = 'voted';
}
$divLikeClass = join(' ', $divLikeClass);

$divDislikeClass = ['cp', 'vote-down-link'];
if (!empty($voteIEvent) && $voteIEvent->vote == Vote::VOTE_DOWN) {
    $divDislikeClass[] = 'voted';
}
$divDislikeClass = join(' ', $divDislikeClass);

$url = $event->getUrl();
$imgsEvent = $event->getImgsSort();
$mainImage = null;
$image_src = '';
if (!empty($imgsEvent)) {
    reset($imgsEvent);
    $mainImage = current($imgsEvent);
    $image_src = $mainImage->short_url;
}
$tags = $event->tagEntity;

$tagsId = [];
foreach ($tags as $tag) {
    $tagEvent = $tag->tags;
    if (!empty($tagEvent->getName())) {
        $tagsId[] = $tag->tag_id;
    }
}

$description = $this->title;
$description .= ". " . $event->getShortDescription(500, '') . "..";
$urlVideo = '';
preg_match_all('/[^\W\d][\w]*/', $this->title, $wordArr);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => join(', ', $event->getKeywords()),
], 'keywords');

$this->registerMetaTag([
    'name'    => 'description',
    'content' => $description,
], 'description');

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
$locations = $event->locations;
?>
<div id="event-header">
    <h1>
        <?= Html::a($event->getTitle(), $url, ['class' => 'event-hyperlink']) ?>
        <?php
        if (!Yii::$app->user->isGuest && $thisUser->id == $event->user_id) {
            echo Html::a(
                Lang::t('page/eventView', 'edit'),
                Url::to(['events/edit', 'id' => $event->id]),
                ['class' => 'btn btn-success pull-right']
            );
        }
        ?>
    </h1>

</div>


<div class="row">
    <div class="col-sm-1 text-center vote-block visible-md-block visible-lg-block visible-sm-block">
        <div>
            <?= Html::tag("div", $voteUpHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $event->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_EVENT]) ?>
        </div>
        <div>
            <span class="vote-count-event">
                <?= $event->like_count ?>
            </span>
        </div>
        <div>
            <?= Html::tag("div", $voteDownHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $event->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_EVENT]) ?>
        </div>
    </div>
    <div class="col-sm-1 text-center vote-block visible-xs-block">
        <?= Html::tag("i", $voteLeftHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $event->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_EVENT]) ?>
        <span class="vote-count-event">
            <?= $event->like_count ?>
        </span>
        <?= Html::tag("i", $voteRightHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $event->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_EVENT]) ?>
    </div>


    <div class="col-sm-11 block-event-view">
        <div class="event-text">
            <?php
            echo HtmlPurifier::process($event->description, []);
            ?>
        </div>
        <?php
        if (count($imgsEvent) > 0) {
            ?>
            <h3><?= Lang::t('page/eventView', 'titleImg') ?>:</h3>
            <?php
            echo "<div class='block-imgs'>";
            foreach ($imgsEvent as $img) {
                echo Html::tag(
                    'div',
                    Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img', 'data-img-url' => $img->short_url]),
                    ['class' => 'img-input-group']
                );
                echo Html::img($img->short_url, ['class' => 'hide']);
            }
            echo '</div>';
        }
        ?>
        <br/>
        <b><?= Lang::t("page/eventView", "date") ?></b> <?= date("d.m.Y", $event->date) ?><br>
        <?php
        if (count($locations)) {
            echo '<div id="locations-event-block-' . $event->id . '">';
            foreach ($locations as $location) {
                echo "<b>" . $location->getTypeLocal() . "</b> ";
                echo Html::a(
                    '<span class="glyphicon glyphicon-map-marker"></span> ' . $location->getTitle(),
                    '',
                    [
                        'class'            => 'show-location-link',
                        'data-id'          => 'locations-event-block-' . $event->id,
                        'data-lat'         => $location->lat,
                        'data-lng'         => $location->lng,
                        'data-zoom'        => $location->zoom,
                        'data-title'       => $location->title,
                        'data-type'        => $location->getTypeLocal(),
                        'data-description' => $location->getDescription(),
                    ]
                );
                echo "<br/>";
            }
            echo '</div>';
        } else {
            echo "<b>" . Lang::t("page/eventView", "location") . '</b> ';
            echo '<span class="glyphicon glyphicon-map-marker"></span>' . $event->getCountryCityText() . "<br/>";
        }
        ?>
        <b><?= Lang::t("page/eventView", "site") ?></b> <?= Html::a($event->site, $event->site) ?><br/>
        <br/>
        <div class="margin-bottom tag-line-height">
            <?php
            $tagValues = [];
            foreach ($tags as $tag) {
                $tagEvent = $tag->tags;
                $urlTag = Url::to(['/', 'tag' => $tagEvent->getName()]);
                echo Html::a($tagEvent->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>
        <div>
            <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $event->user_id) {
                echo Html::button(
                    Lang::t('page/eventView', 'delete'),
                    [
                        'class'       => 'btn btn-link no-focus',
                        'data-toggle' => "modal",
                        'data-target' => ".modal-delete-confirm",

                    ]
                ), ' ';
                echo Html::a(
                    Lang::t('page/eventView', 'edit2'),
                    Url::to(['events/edit', 'id' => $event->id]),
                    ['class' => 'btn btn-link no-focus']
                ), ' ';
            }
            echo Html::button(
                Lang::t('page/eventView', 'share'),
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
            $author = $event->user;
            ?>
            <div class="pull-right">
                <table>
                    <tr>
                        <td>
                            <div class="mini-like-show">
                                <?php
                                $likeTitle = $event->like_count . " " . Lang::tn('main', 'vote', $event->like_count);
                                $showTitle = $event->show_count . " " . Lang::tn('main', 'showCount', $event->show_count);
                                ?>
                                <span title="<?= $likeTitle ?>"><i
                                        class="glyphicon glyphicon-thumbs-up"></i> <?= $event->like_count ?></span><br/>
                                <span title="<?= $showTitle ?>"><i
                                        class="glyphicon glyphicon-eye-open"></i> <?= $event->show_count ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="pull-right user-info">
                                <div class="user-action-time">
                                    <?= Lang::t("main", "created") . " " . date("d.m.Y", $event->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $event->date_create) ?>
                                </div>
                                <div class="user-gravatar32">
                                    <div class="background-img"
                                         style="background-image: url('<?= $author->getAvatarPic() ?>')"></div>
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

<div class="block-footer-event">
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="active"><?= Html::a(Lang::t('page/eventView', 'titleList'), $event->getUrl(), ['class' => 'tab-event tab-event-list']) ?></li>
        <li class=""><?= Html::a(Lang::t('page/eventView', 'titleVideos'), $event->getUrl(), ['class' => 'tab-event tab-event-videos']) ?></a></li>
        <li class=""><?= Html::a(Lang::t('page/eventView', 'titleComment'), $event->getUrl(), ['class' => 'tab-event tab-event-comment']) ?></a></li>
    </ul>

    <div class="row block-event block-event-list">
        <div class="col-md-12">
            <h3><?= Lang::t('page/eventView', 'titleList') ?></h3>
            <?php
            if (!empty($tagsId)) {
                echo ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID, 'searchTag' => $tagsId, 'addModalShowVideo' => false]);
            } else {
                echo Html::a(
                    Lang::t('main', 'mainButtonAddRecord'),
                    ['/list/add'],
                    ['class' => 'btn btn-success btn-label-main add-item']
                );
            }
            ?>
        </div>
    </div>

    <div class="row block-event block-event-videos hide">
        <div class="col-md-12">
            <h3><?= Lang::t('page/eventView', 'titleVideos') ?></h3>
            <?php
            if (!empty($tagsId)) {
                echo VideoList::widget([
                    'searchTag' => $tagsId,
                ]);
            } else {
                echo Html::a(
                    Lang::t('main', 'mainButtonAddRecord'),
                    ['/list/add'],
                    ['class' => 'btn btn-success btn-label-main add-item']
                );
            }
            ?>
        </div>
    </div>

    <div class="row block-event block-event-comment hide">
        <div class="col-md-12">
            <div>
                <h3><?= Lang::t('page/eventView', 'titleComment') ?></h3>
                <div>
                    <?= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_EVENT, 'entity_id' => $event->id]); ?>
                </div>
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
                <h4 class="modal-title"><?= Lang::t('page/eventView', 'deleteConfirmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('page/eventView', 'deleteConfirm') ?>
            </div>
            <div class="modal-footer">
                <a href="<?= Url::to(['events/delete', 'id' => $event->id]) ?>" type="button"
                   class="btn btn-danger"><?= Lang::t('page/eventView', 'deleteBtn') ?></a>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/eventView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ALARM, 'id' => $event->id]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_LOCATION]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]) ?>
