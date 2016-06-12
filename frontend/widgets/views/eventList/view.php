<?php
/**
 * @var Event  $event
 * @var string $dateCreateType
 */
use common\models\Event;
use common\models\Tags;
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\EventList;
use yii\helpers\Html;
use yii\helpers\Url;

$url = $event->getUrl();
$imgs = $event->getImgsSort();
$mainImg = null;
if (!empty($imgs)) {
    $mainImg = array_shift($imgs);
}
$tags = $event->tagEntity;
?>
<div id="event-<?= $event->id ?>" data-date="<?= $event->date ?>" data-id="<?= $event->id ?>" class="row block-event-summary margin-bottom <?= $event->like_count < 0 ? 'bad-event' : '' ?>"">
    <div class="col-sm-1 visible-sm-block visible-md-block visible-lg-block">
        <div class="cp" onclick="window.location.href='<?= $url ?>'">
            <div class="votes">
                <div class="mini-counts">
                    <?= Html::tag('span', $event->like_count, ['title' => $event->like_count]) ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'vote', $event->like_count) ?>
                </div>
            </div>
            <div class="views">
                <div class="mini-counts">
                    <?= $event->show_count ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'showCount', $event->show_count) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-11">
        <div class="summary">
            <h3><?= Html::a($event->getTitle(), $url, ['class' => 'event-hyperlink']) ?></h3>
        </div>

        <?php
        if ($event->like_count >= 0) {
            ?>
            <b><?= Lang::t("page/eventView", "date") ?></b> <?= date("d.m.Y", $event->date) ?><br>
            <b><?= Lang::t("page/eventView", "location") ?></b> <span class="glyphicon glyphicon-map-marker"></span> <?= $event->getCountryCityText() ?><br/>
            <b><?= Lang::t("page/eventView", "site") ?></b> <?= Html::a($event->site, $event->site) ?><br/>
            <br/>
            <?php if (!empty($mainImg)) { ?>
                <div class="margin-bottom">
                    <?= Html::img($mainImg->short_url, ['class' => 'main-image-event', 'data-img-url' => $mainImg->short_url]) ?>
                </div>
            <?php } ?>
            <div class="margin-bottom block-event-list-img block-imgs">
                <?php
                if (!empty($imgs)) {
                    foreach ($imgs as $img) {
                        echo Html::tag(
                            'div',
                            Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img', 'data-img-url' => $img->short_url]),
                            ['class' => 'img-input-group']
                        );
                    }
                }
                ?>
            </div>
            <div class="margin-bottom">
                <?php
                foreach ($tags as $tag) {
                    /** @var Tags $tagEvent */
                    $tagEvent = $tag->tags;
                    if ($dateCreateType == eventList::DATE_CREATE_ALL) {
                        $urlTag = Url::to(['/', 'tag' => $tagEvent->getName()]);
                    } else {
                        $urlTag = Url::to(['list/' . $dateCreateType, 'tag' => $tagEvent->getName()]);
                    }
                    echo Html::a($tagEvent->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
                }
                ?>
            </div>
            <?php
        }
        ?>
        <?php
        $author = $event->user;
        ?>
        <div class="pull-right">
            <table>
                <tr>
                    <td>
                        <div class="mini-like-show  visible-sm-block visible-xs-block">
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
                                <?= Html::a('<div class="background-img" style="background-image: url(\'' . $author->getAvatarPic() . '\')"></div>', ['user/' . $author->display_name]) ?>
                            </div>
                            <div class="user-details">
                                <?= Html::a($author->getDisplayName() . ' (<b>' . $author->reputation . '</b>)', ['user/' . $author->display_name]) ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>