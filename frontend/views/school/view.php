<?php
/**
 * @var yii\web\View $this
 * @var School $school
 * @var Vote $vote
 */

use common\models\Comment;
use common\models\Location;
use common\models\School;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use frontend\widgets\UserInfoWidget;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/school/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/findTagElement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/share42/share42.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$url = $school->getUrl(true, ['lang_id' => false]);
$this->registerJs("VK.Widgets.Like('vk_like', {type: 'mini', 'pageTitle': '{$this->title}', 'pageUrl': '{$url}'});", \yii\web\View::POS_END);
$this->registerJs("VK.Widgets.Comments('vk_comments', {limit: 10, attach: '*', 'pageUrl': '{$url}'});", \yii\web\View::POS_END);

$this->title = $school->getTitle2();

$this->params['breadcrumbs'][] = Lang::t('page/schoolView', 'title');

$thisUser = User::thisUser();
$voteSchool = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_SCHOOL, $school->id) : null;
$voteUpHtml = '<span class="glyphicon glyphicon-thumbs-up"></span>';
$voteLeftHtml = '<span class="glyphicon glyphicon-thumbs-down"></span>';
$voteDownHtml = '<span class="glyphicon glyphicon-thumbs-down"></span>';
$voteRightHtml = '<span class="glyphicon glyphicon-thumbs-up"></span>';
$urlUp = Url::to(['vote/add']);
$urlDown = Url::to(['vote/add']);

$divLikeClass = ['cp', 'vote-up-link'];
if (!empty($voteSchool) && $voteSchool->vote == Vote::VOTE_UP) {
    $divLikeClass[] = 'voted';
}
$divLikeClass = join(' ', $divLikeClass);

$divDislikeClass = ['cp', 'vote-down-link'];
if (!empty($voteSchool) && $voteSchool->vote == Vote::VOTE_DOWN) {
    $divDislikeClass[] = 'voted';
}
$divDislikeClass = join(' ', $divDislikeClass);

$url = $school->getUrl();
$imgsSchool = $school->getImgsSort();
$mainImage = null;
$image_src = '';
if (!empty($imgsSchool)) {
    reset($imgsSchool);
    $mainImage = current($imgsSchool);
    $image_src = $mainImage->short_url;
}
$tags = $school->tagEntity;

$description = $this->title;
$description .= ". " . $school->getShortDescription(500, '') . "..";
$urlVideo = '';
preg_match_all('/[^\W\d][\w]*/', $this->title, $wordArr);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => join(', ', $school->getKeywords()),
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
$locations = $school->locations;
?>
<div id="school-header">
    <h1>
        <?= Html::a($school->getTitle(), $url, ['class' => 'school-hyperlink']) ?>
        <?php
        if (Yii::$app->user->can(User::PERMISSION_EDIT_SCHOOLS, ['object' => $school])) {
            echo Html::a(
                Lang::t('page/schoolView', 'edit'),
                Url::to(['schools/edit', 'id' => $school->id]),
                ['class' => 'btn btn-success pull-right']
            );
        }
        ?>
    </h1>

</div>
<?php if (!$school->official_editor) { ?>
    <div class="alert alert-warning">
        <b><?= Lang::t('page/schoolView', 'warning') ?></b>
        <?= Lang::t('page/schoolView', 'schoolNotEditor') ?><br/>
        <?= Html::a('vk.com/prozouk', '//vk.com/prozouk', ['class' => 'margin-right-10']) ?>
        <?= Html::a('prozouk@yandex.ru', 'mailto:prozouk@yandex.ru', ['class' => 'margin-right-10']) ?>
    </div>
<?php } ?>


<div class="row">
    <div class="col-sm-1 text-center vote-block visible-md-block visible-lg-block visible-sm-block">
        <div>
            <?= Html::tag("div", $voteUpHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $school->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_SCHOOL]) ?>
        </div>
        <div>
            <span class="vote-count-school">
                <?= $school->like_count ?>
            </span>
        </div>
        <div>
            <?= Html::tag("div", $voteDownHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $school->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_SCHOOL]) ?>
        </div>
    </div>
    <div class="col-sm-1 text-center vote-block visible-xs-block">
        <?= Html::tag("i", $voteLeftHtml, ['data-href' => $urlDown, 'class' => $divDislikeClass, 'data-id' => $school->id, 'data-vote' => Vote::VOTE_DOWN, 'data-entity' => Vote::ENTITY_SCHOOL]) ?>
        <span class="vote-count-school">
            <?= $school->like_count ?>
        </span>
        <?= Html::tag("i", $voteRightHtml, ['data-href' => $urlUp, 'class' => $divLikeClass, 'data-id' => $school->id, 'data-vote' => Vote::VOTE_UP, 'data-entity' => Vote::ENTITY_SCHOOL]) ?>
    </div>


    <div class="col-sm-11 block-school-view">
        <div class="school-text">
            <?php
            echo HtmlPurifier::process($school->description, []);
            ?>
        </div>
        <?php
        if (count($imgsSchool) > 0) {
            ?>
            <h3><?= Lang::t('page/schoolView', 'titleImg') ?>:</h3>
            <?php
            echo "<div class='block-imgs'>";
            foreach ($imgsSchool as $img) {
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
        <?php
        if (count($locations)) {
            echo '<div id="locations-school-block-' . $school->id . '">';
            foreach ($locations as $location) {
                echo "<b>" . Lang::t("page/schoolView", "location") . "</b> ";
                echo Html::a(
                    '<span class="glyphicon glyphicon-map-marker"></span> ' . $location->getTitle(),
                    '',
                    [
                        'class'            => 'show-location-link',
                        'data-id'          => 'locations-school-block-' . $school->id,
                        'data-lat'         => $location->lat,
                        'data-lng'         => $location->lng,
                        'data-zoom'        => $location->zoom,
                        'data-title'       => $location->title,
                        'data-title-url'   => $school->getUrl(),
                        'data-site-url'    => $school->site,
                        'data-type'        => $location->getTypeLocal(),
                        'data-description' => $location->getDescription(),
                    ]
                );
                echo "<br/>";
            }
            echo '</div>';
        } else {
            echo "<b>" . Lang::t("page/schoolView", "location") . '</b> ';
            echo '<span class="glyphicon glyphicon-map-marker"></span>' . $school->getCountryCityText() . "<br/>";
        }
        ?>
        <b><?= Lang::t("page/schoolView", "site") ?></b> <?= Html::a($school->site, $school->site) ?><br/>
        <br/>
        <div class="margin-bottom tag-line-height">
            <?php
            $tagValues = [];
            foreach ($tags as $tag) {
                $tagSchool = $tag->tags;
                $urlTag = Url::to(['/', 'tag' => $tagSchool->getName()]);
                echo Html::a($tagSchool->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>
        <div>
            <div id="vk_like"></div>
        </div>
        <div>
            <?php
            if (Yii::$app->user->can(User::PERMISSION_DELETE_SCHOOLS, ['object' => $school])) {
                echo Html::button(
                    Lang::t('page/schoolView', 'delete'),
                    [
                        'class'       => 'btn btn-link no-focus',
                        'data-toggle' => "modal",
                        'data-target' => ".modal-delete-confirm",

                    ]
                ), ' ';
            }
            if (Yii::$app->user->can(User::PERMISSION_EDIT_SCHOOLS, ['object' => $school])) {
                echo Html::a(
                    Lang::t('page/schoolView', 'edit2'),
                    Url::to(['schools/edit', 'id' => $school->id]),
                    ['class' => 'btn btn-link no-focus']
                ), ' ';
            }
            echo Html::button(
                Lang::t('page/schoolView', 'share'),
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
            $author = $school->user;
            ?>
            <div class="pull-right">
                <table>
                    <tr>
                        <td>
                            <div class="mini-like-show">
                                <?php
                                $likeTitle = $school->like_count . " " . Lang::tn('main', 'vote', $school->like_count);
                                $showTitle = $school->show_count . " " . Lang::tn('main', 'showCount', $school->show_count);
                                ?>
                                <span title="<?= $likeTitle ?>"><i
                                        class="glyphicon glyphicon-thumbs-up"></i> <?= $school->like_count ?></span><br/>
                                <span title="<?= $showTitle ?>"><i
                                        class="glyphicon glyphicon-eye-open"></i> <?= $school->show_count ?></span>
                            </div>
                        </td>
                        <td>
                            <?= UserInfoWidget::widget(['item' => $school]); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="share42init hide"></div>
    </div>
</div>

<div class="block-footer-school">
    <ul class="nav nav-tabs nav-main-tabs">
        <li class="active"><?= Html::a(Lang::t('page/schoolView', 'titleList'), ['list/popular'], ['class' => 'tab-school tab-school-list']) ?></li>
        <li class=""><?= Html::a(Lang::t('page/schoolView', 'titleComment'), ['list/month'], ['class' => 'tab-school tab-school-comment']) ?></a></li>
    </ul>

    <div class="row block-school-list">
        <div class="col-md-12">
            <h3><?= Lang::t('page/schoolView', 'titleList') ?></h3>
            <?php
                $tagsId = [];
                foreach ($tags as $tag) {
                    $tagSchool = $tag->tags;
                    if (!empty($tagSchool->getName())) {
                        $tagsId[] = $tag->tag_id;
                    }
                }
                if (!empty($tagsId)) {
                    echo ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID, 'searchTag' => $tagsId, 'addModalShowVideo' => false, 'addModalShowImg' => false]);
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

    <div class="row block-school-comment hide">
        <div class="col-md-12">
            <div>
                <h3><?= Lang::t('page/schoolView', 'titleComment') ?></h3>
                <div class="col-sm-6">
                    <div id="vk_comments"></div>
                </div>
                <div class="col-sm-6">
                    <?= \frontend\widgets\CommentsWidget::widget(['entity' => Comment::ENTITY_SCHOOL, 'entity_id' => $school->id]); ?>
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
                <h4 class="modal-title"><?= Lang::t('page/schoolView', 'deleteConfirmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('page/schoolView', 'deleteConfirm') ?>
            </div>
            <div class="modal-footer">
                <a href="<?= Url::to(['schools/delete', 'id' => $school->id]) ?>" type="button"
                   class="btn btn-danger"><?= Lang::t('page/schoolView', 'deleteBtn') ?></a>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/schoolView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ALARM, 'id' => $school->id]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_IMG]) ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_LOCATION]) ?>
