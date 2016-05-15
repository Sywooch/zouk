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
$videos = $item->videos;
$sounds = $item->sounds;
$imgs = $item->getImgsSort();
$mainImg = null;
if (!empty($imgs)) {
    $mainImg = array_shift($imgs);
}
$tags = $item->tagEntity;
?>
<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>" class="row block-item-summary margin-bottom <?= $item->like_count < 0 ? 'bad-item' : '' ?>"">
    <div class="col-sm-1 visible-sm-block visible-md-block visible-lg-block">
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

        <?php
        if ($item->like_count >= 0) {
            ?>
            <div class="item-short-description">
                <?= $item->getShortDescription() ?>
            </div>
            <?php if (!empty($mainImg)) { ?>
                <div class="margin-bottom">
                    <?= Html::img($mainImg->short_url, ['class' => 'main-image-item', 'data-img-url' => $mainImg->short_url]) ?>
                </div>
            <?php } else {
                $mainVideo = array_shift($videos);
                if (!empty($mainVideo)) {
                    $duration = $mainVideo->getDuration();

                    echo Html::a(
                        Html::tag(
                            'div',
                            Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
                            ['class' => 'block-video-duration']
                        ) . Html::img($mainVideo->getThumbnailUrl(2), ['class' => 'main-video-image-item']),
                        $mainVideo->original_url,
                        ['target' => '_blank', 'class' => 'block-main-video-link margin-right-10']
                    );
                }
            }
            ?>
            <div class="margin-bottom block-item-list-img block-imgs">
                <?php
                if (!empty($videos)) {
                    foreach ($videos as $video) {
                        $duration = $video->getDuration();

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
                }
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
        }
        ?>
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