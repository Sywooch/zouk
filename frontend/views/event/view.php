<?php
/**
 * @var yii\web\View $this
 * @var \common\models\Event $event
 * @var Vote $vote
 */

use common\models\Comment;
use common\models\Event;
use common\models\Item;
use common\models\Location;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\EntryList;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\UserInfoWidget;
use frontend\widgets\VideoList;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/event/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42/share42.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$url = $event->getUrl(true, ['lang_id' => false]);
$this->registerJs("VK.Widgets.Like('vk_like', {type: 'mini', 'pageTitle': '{$this->title}', 'pageUrl': '{$url}'});", \yii\web\View::POS_END);
$this->registerJs("VK.Widgets.Comments('vk_comments', {limit: 10, attach: '*', 'pageUrl': '{$url}'});", \yii\web\View::POS_END);

$this->title = $event->getTitle2();

$this->params['breadcrumbs'][] = Lang::t('page/eventView', 'title');

$thisUser = User::thisUser();
$voteIEvent = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_EVENT, $event->id) : null;
$voteUpHtml = '<span class="glyphicon glyphicon-thumbs-up"></span> Нравится';
$voteLeftHtml = '<span class="glyphicon glyphicon-triangle-left"></span>';
$voteDownHtml = '<span class="glyphicon glyphicon-thumbs-down"></span> Не нравится';
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
    $mainImage = array_shift($imgsEvent);
    $image_src = $mainImage->short_url;
}
$tags = $event->tagEntity;

$tagsId = [];
$tagsNames = [];
foreach ($tags as $tag) {
    $tagEvent = $tag->tags;
    if (!empty($tagEvent->getName())) {
        $tagsId[] = $tag->tag_id;
        $tagsNames[] = $tagEvent->getName();
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

if (empty($image_src)) {
    $image_src = Yii::$app->UrlManager->to('img/empty_event.png');
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
$locations = $event->locations;

$dateFrom = date("d.m.Y", $event->date);
$dateTo = date("d.m.Y", empty($event->date_to) ? $event->date : $event->date_to);
?>

    <h1 class="title truncate"
        title="<?= $event->getTitle(); ?>"><?= Html::a($event->getTitle(), $url, ['class' => 'event-hyperlink']) ?></h1>

    <div class="visible-xs-block">
        <?php
        if (!empty($event->date)) {
            ?>
            <div class="row margin-bottom block-entry-event-row">
                <div class="col-xs-6 block-entry-event-from">
                    <span class="date"><?= $dateFrom ?></span>
                </div>
                <div class="col-xs-6 block-entry-event-to-mini">
                    <span class="date"><?= $dateTo ?></span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="row margin-bottom">
        <div class="col-sm-4">
            <div class="block-view-main-img" data-url="<?= $image_src ?>">
                <?= Html::tag('div', '', ['style' => "background-image:url('{$image_src}')", 'class' => 'background-img']); ?>
            </div>
        </div>
        <div class="col-sm-8 block-event-info">
            <div class="hidden-xs">
                <?php
                if (!empty($event->date)) {
                    ?>
                    <div class="row margin-bottom block-entry-event-row big">
                        <div class="col-xs-6 block-entry-event-from">
                            <span><?= Lang::t("page/eventView", "dateFrom"); ?></span><br/>
                            <span class="date"><?= $dateFrom ?></span>
                        </div>
                        <div class="col-xs-6 block-entry-event-to">
                            <span><?= Lang::t("page/eventView", "dateTo") ?></span><br/>
                            <span class="date"><?= $dateTo ?></span>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>


            <dl class="dl-horizontal">
                <?php
                if (count($locations)) {
                    echo '<div id="locations-event-block-' . $event->id . '">';
                    foreach ($locations as $location) {
                        echo "<dt>" . $location->getTypeLocal() . "</dt> ";
                        echo '<dd class="truncate">' . Html::a(
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
                            ) . '</dd>';
                    }
                    echo '</div>';
                } else {
                    echo "<dt>" . Lang::t("page/eventView", "location") . '</dt> ';
                    echo '<dd class="truncate"><span class="glyphicon glyphicon-map-marker"></span>' . $event->getCountryCityText() . "<br/></dd>";
                }
                ?>

                <dt><?= Lang::t("page/eventView", "site") ?></dt>
                <dd class="truncate"><?= Html::a($event->site, $event->site) ?></dd>
            </dl>

            <div>
                <div class="carousel-entry-view-main">
                    <?php
                    if (count($imgsEvent) > 0) {
                        foreach ($imgsEvent as $img) {
                            echo $this->render('_slickImg', ['img' => $img]);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="event-text margin-bottom">
        <?php
        echo HtmlPurifier::process($event->description, []);
        ?>
    </div>

    <div class="tag-line-height">
        <?php
        $tagValues = [];
        foreach ($tags as $tag) {
            $tagEvent = $tag->tags;
            $urlTag = Url::to(['/', 'tag' => $tagEvent->getName()]);
            echo Html::a($tagEvent->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
        }
        ?>
    </div>

    <div class="row margin-bottom">
        <div class="col-sm-7">
            <div>
                <div id="vk_like"></div>
            </div>
            <div>

                <?php
                if (Yii::$app->user->can(User::PERMISSION_DELETE_EVENTS, ['object' => $event])) {
                    echo Html::button(
                        Lang::t('page/eventView', 'delete'),
                        [
                            'class'       => 'btn btn-link no-focus',
                            'data-toggle' => "modal",
                            'data-target' => ".modal-delete-confirm",

                        ]
                    ), ' ';
                }
                if (Yii::$app->user->can(User::PERMISSION_EDIT_EVENTS, ['object' => $event])) {
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
            </div>
            <div class="share42init hide"></div>
            <div class="vote-block">
                <?= Html::tag("span", $voteUpHtml, [
                    'data-href'   => $urlUp,
                    'class'       => $divLikeClass . ' margin-right-10',
                    'data-id'     => $event->id,
                    'data-vote'   => Vote::VOTE_UP,
                    'data-entity' => Vote::ENTITY_EVENT,
                ]); ?>
                <?= Html::tag("span", $voteDownHtml, [
                    'data-href'   => $urlDown,
                    'class'       => $divDislikeClass,
                    'data-id'     => $event->id,
                    'data-vote'   => Vote::VOTE_DOWN,
                    'data-entity' => Vote::ENTITY_EVENT,
                ]); ?>

            </div>
        </div>
        <div class="col-sm-5">
            <table class="pull-right">
                <tr>
                    <td>
                        <div class="mini-like-show">
                            <?php
                            $likeTitle = $event->like_count . " " . Lang::tn('main', 'vote', $event->like_count);
                            $showTitle = $event->show_count . " " . Lang::tn('main', 'showCount', $event->show_count);
                            ?>
                            <span title="<?= $likeTitle ?>">
                                    <i class="glyphicon glyphicon-thumbs-up"></i> <?= $event->like_count ?>
                                </span><br/>
                                <span title="<?= $showTitle ?>"><i
                                        class="glyphicon glyphicon-eye-open"></i> <?= $event->show_count ?></span>
                        </div>
                    </td>
                    <td>
                        <?= UserInfoWidget::widget(['item' => $event]); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="block-footer-event">
        <ul class="nav nav-tabs nav-main-tabs margin-bottom">
            <li class="active"><?= Html::a(Lang::t('page/eventView', 'titleList'), $event->getUrl(), ['class' => 'tab-event tab-event-list']) ?></li>
            <li class=""><?= Html::a(Lang::t('page/eventView', 'titleVideos'), $event->getUrl(), ['class' => 'tab-event tab-event-videos']) ?></a></li>
            <li class=""><?= Html::a(Lang::t('page/eventView', 'titleComment'), $event->getUrl(), ['class' => 'tab-event tab-event-comment']) ?></a></li>
        </ul>

        <div class="row block-event block-event-list">
            <div class="col-md-12">
                <?php
                if (!empty($tagsNames)) {
                    $searchEntryForm = new \common\models\form\SearchEntryForm();
                    $searchEntryForm->search_text = join(' ', $tagsNames);
                    echo EntryList::widget([
                        'orderBy'              => ItemList::ORDER_BY_LIKE_SHOW,
                        'addModalShowVideo'    => false,
                        'addModalShowImg'      => false,
                        'addModalShowLocation' => false,
                        'searchEntryForm'      => $searchEntryForm,
                        'page'                 => $page ?? 0,
                        'entityTypes'          => [Item::THIS_ENTITY],
                        'blockAction'          => '',
                    ]);
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
                    <div class="col-sm-6">
                        <div id="vk_comments"></div>
                    </div>
                    <!--                    <div>-->
                    <!--                        --><? //= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_EVENT, 'entity_id' => $event->id]); ?>
                    <!--                    </div>-->
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