<?php
/**
 * @var Item   $item
 * @var string $dateCreateType
 */
use common\models\Item;
use common\models\Tags;
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\helpers\Html;
use yii\helpers\Url;

$url = $item->getUrl();
/** @var \common\models\Video[] $videos */
$videos = $item->videos;
/** @var \common\models\Music[] $sounds */
$sounds = $item->sounds;
$tags = $item->tagEntity;
?>
<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>" class="row block-item-summary margin-bottom">
    <div class="col-sm-1 visible-md-block visible-lg-block">
        <div class="cp" onclick="window.location.href='<?= $url ?>'">
            <div class="votes">
                <div class="mini-counts">
                    <?= Html::tag('span', $item->like_count, ['title' => $item->like_count]) ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'vote', $item->like_count) ?>
                </div>
            </div>
            <div class="views">
                <div class="mini-counts">
                    <?= $item->show_count ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'showCount', $item->show_count) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-11">
        <div class="summary">
            <h3><?= Html::a($item->getTitle(), $url, ['class' => 'item-hyperlink']) ?></h3>
        </div>
        <div class="item-short-description">
            <?= $item->getShortDescription() ?>
        </div>
        <div class="margin-bottom block-item-list-img">
            <?php
            foreach ($videos as $video) {
                if (empty($video->duration)) {
                    $duration = "";
                } else {
                    $durationS = ($video->duration % 60);
                    $durationM = ($video->duration - $durationS) / 60;
                    if ($durationS < 10) {
                        $durationS = '0' . $durationS;
                    }
                    $duration = $durationM . ':' . $durationS;
                }

                echo Html::a(
                    Html::tag(
                        'div',
                        Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
                        ['class' => 'block-video-duration']
                    ) . Html::img($video->getThumbnailUrl()),
                    $video->original_url,
                    ['target' => '_blank', 'class' => 'block-video-link margin-right-10']
                );
            }
            ?>
        </div>
        <div class="margin-botton block-item-list-sound">
            <?php
            foreach ($sounds as $sound) {
                echo \frontend\widgets\SoundWidget::widget(['music' => $sound]);
            }
            ?>
        </div>
        <div class="margin-bottom">
            <?php
            foreach ($tags as $tag) {
                /** @var Tags $tagItem */
                $tagItem = $tag->tags;
                if ($dateCreateType == ItemList::DATE_CREATE_LAST) {
                    $urlTag = Url::to(['/', 'tag' => $tagItem->getName()]);
                } else {
                    $urlTag = Url::to(['list/' . $dateCreateType, 'tag' => $tagItem->getName()]);
                }
                echo Html::a($tagItem->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>

        <?php
        $author = $item->user;
        ?>
        <div class="pull-right">
            <table>
                <tr>
                    <td>
                        <div class="mini-like-show  visible-sm-block visible-xs-block">
                            <?php
                            $likeTitle = $item->like_count . " " . Lang::tn('main', 'vote', $item->like_count);
                            $showTitle = $item->show_count . " " . Lang::tn('main', 'showCount', $item->show_count);
                            ?>
                            <span title="<?= $likeTitle ?>"><i
                                    class="glyphicon glyphicon-thumbs-up"></i> <?= $item->like_count ?></span><br/>
                            <span title="<?= $showTitle ?>"><i
                                    class="glyphicon glyphicon-eye-open"></i> <?= $item->show_count ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="pull-right user-info">
                            <div class="user-action-time">
                                <?= Lang::t("main", "created") . " " . date("d.m.Y", $item->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $item->date_create) ?>
                            </div>
                            <div class="user-gravatar32"><img src="<?= $author->getAvatarPic() ?>"></div>
                            <div class="user-details">
                                <?= $author->getDisplayName() ?> (<b><?= $author->reputation ?></b>)
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>