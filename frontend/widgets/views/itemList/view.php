<?php
/**
 * @var Item   $item
 * @var string $dateCreateType
 */
use common\models\Item;
use common\models\Tags;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use yii\helpers\Html;
use yii\helpers\Url;

$url = $item->getUrl();
$videos = $item->videos;
$tags = $item->tagEntity;
?>
<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>" class="row block-item-summary margin-bottom">
    <div class="col-sm-1">
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
            <h3><?= Html::a($item->title, $url, ['class' => 'item-hyperlink']) ?></h3>
        </div>
        <div>
            <?php
            /** @var \common\models\Video[] $videos */
            foreach ($videos as $video) {
                ?>
                <div>
                    <?= Html::a(
                        '<i class="glyphicon glyphicon-facetime-video"></i> ' . $video->video_title,
                        $video->original_url,
                        ['target' => '_blank']
                    ) ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div>
            <?php
            foreach ($tags as $tag) {
                /** @var Tags $tagItem */
                $tagItem = $tag->tags;
                if ($dateCreateType == ItemList::DATE_CREATE_LAST) {
                    $urlTag = Url::to(['/', 'tag' => $tagItem->name]);
                } else {
                    $urlTag = Url::to(['list/' . $dateCreateType, 'tag' => $tagItem->name]);
                }
                echo Html::a($tagItem->name, $urlTag, ['class' => 'label label-tag-element']), " ";
            }
            ?>
        </div>

        <?php
        /** @var User $author */
        $author = $item->user;
        ?>
        <div class="pull-right user-info">
            <div class="user-action-time">
                <?= Lang::t("main", "created") . " " . date("d.m.Y", $item->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $item->date_create) ?>
            </div>
            <div class="user-gravatar32"><img src="<?= $author->getAvatarPic() ?>"></div>
            <div class="user-details">
                <?= $author->getDisplayName() ?> (<b><?= $author->reputation ?></b>)
            </div>
        </div>
    </div>
</div>