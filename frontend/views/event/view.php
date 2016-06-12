<?php
/**
 * @var yii\web\View         $this
 * @var \common\models\Event $event
 * @var Vote                 $vote
 */

use common\models\Comment;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ModalDialogsWidget;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/event/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

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

$keywords = [];
$description = $this->title;
$description .= ". " . $event->getShortDescription(100, '') . "..";
$urlVideo = '';
foreach ($tags as $tag) {
    $keywords[] = $tag->tags->getName();
}
preg_match_all('/[^\W\d][\w]*/', $this->title, $wordArr);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => join(', ', $keywords),
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
?>
    <div id="event-header">
        <h1>
            <?= Html::a($event->getTitle(), $url, ['class' => 'event-hyperlink']) ?>
            <?php
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $event->user_id) {
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
            <b><?= Lang::t("page/eventView", "location") ?></b> <span class="glyphicon glyphicon-map-marker"></span> <?= $event->getCountryCityText() ?><br/>
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
                                    $likeTitle = $event->like_count . " " .Lang::tn('main', 'vote', $event->like_count);
                                    $showTitle = $event->show_count . " " .Lang::tn('main', 'showCount', $event->show_count);
                                    ?>
                                    <span title="<?= $likeTitle ?>"><i class="glyphicon glyphicon-thumbs-up"></i> <?= $event->like_count ?></span><br/>
                                    <span title="<?= $showTitle ?>"><i class="glyphicon glyphicon-eye-open"></i> <?= $event->show_count ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="pull-right user-info">
                                    <div class="user-action-time">
                                        <?= Lang::t("main", "created") . " " . date("d.m.Y", $event->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $event->date_create) ?>
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
            <script type="text/javascript">(function () {
                    if (window.pluso)if (typeof window.pluso.start == "function") return;
                    if (window.ifpluso == undefined) {
                        window.ifpluso = 1;
                        var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                        s.type = 'text/javascript';
                        s.charset = 'UTF-8';
                        s.async = true;
                        s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
                        var h = d[g]('body')[0];
                        h.appendChild(s);
                    }
                })();</script>
            <div class="pluso" style="display: none;" data-background="transparent"
                 data-options="medium,round,line,horizontal,nocounter,theme=04"
                 data-services="vkontakte,facebook,odnoklassniki,twitter,google"></div>
            
        </div>
    </div>
    <div class="row">
        <hr/>
        <div class="col-md-12">
            <div>
                <h3><?= Lang::t('page/eventView', 'titleComment') ?></h3>
                <div>
                    <?= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_EVENT, 'entity_id' => $event->id]); ?>
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