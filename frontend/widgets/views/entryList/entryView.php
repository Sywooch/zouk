<?php
/**
 * @var \common\models\EntryModel $item
 * @var string $dateCreateType
 * @var integer $countEntities
 */
use common\models\Event;
use common\models\Item;
use common\models\School;
use common\models\Tags;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\SoundWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$url = $item->getUrl();
$videos = [];
$sounds = [];
$locations = [];

if ($item instanceof Item) {
    $videos = $item->videos;
    $sounds = $item->sounds;
} else if ($item instanceof Event) {
    $locations = $item->locations;
} else if ($item instanceof School) {
    $locations = $item->locations;

}

$imgs = $item->getImgsSort();
$mainImg = null;
if (!empty($imgs)) {
    $mainImg = array_shift($imgs);
}
$mainVideo = null;
if (!empty($videos)) {
    $mainVideo = array_shift($videos);
}
$tags = $item->tagEntity;
?>
<div id="item-<?= $item->id ?>" data-id="<?= $item->id ?>" class="block-entry margin-bottom-15 <?= $item->like_count < 0 ? 'bad-entry' : '' ?>">
    <div class="margin-bottom block-entry-title">
        <?php
        if ($item instanceof Event && $countEntities > 1) {
            echo Html::tag('span', Lang::t('page/eventView', 'title'), ['class' => 'label-title-event']);
        } else if ($item instanceof School && $countEntities > 1) {
            echo Html::tag('span', Lang::t('page/schoolView', 'title'), ['class' => 'label-title-school']);
        } else {
        ?>
            <span class="label-title-any">
                <span class="margin-right-10"><?= date('H:i', $item->date_create); ?></span>
                <?= date('j', $item->date_create); ?>
                <?= Lang::t('month', 'monthMini' . date('m', $item->date_create)); ?>
                <?= date('Y', $item->date_create); ?>
            </span>
        <?php } ?>
        <span class="pull-right">
            <?php
            $displayName = $item->user->getDisplayName();
            $displayProfile = Html::tag('div', '', ['style' => "background-image: url('" . $item->user->getAvatarPic() . "');", 'class' => 'background-img nav-profile-img']) . " " .
                (empty($displayName) ? Lang::t('main', 'profile') : $displayName) . ' ' .
                ' (' . $item->user->reputation . ')';
            echo $displayProfile;
            ?>
        </span>
    </div>
    <?php
    if ($item instanceof Event) {

        if (!empty($item->date)) {
            $dateFrom = date("d.m.Y", $item->date);
            $dateTo = date("d.m.Y", empty($item->date_to) ? $item->date : $item->date_to);
            ?>
            <div class="row margin-bottom block-entry-event-row">
                <div class="col-xs-6 block-entry-event-from" title="<?= Lang::t("page/eventView", "dateFrom") ?> <?= $dateFrom; ?>">
                    <?= $dateFrom ?>
                </div>
                <div class="col-xs-6 block-entry-event-to-mini" title="<?= Lang::t("page/eventView", "dateTo") ?> <?= $dateTo; ?>">
                    <?= $dateTo ?>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <div>
        <div class="carousel-entry-view">
            <?php
            if (!empty($mainImg)) {
                echo $this->render('_slickImg', ['img' => $mainImg]);
            }
            if (!empty($mainVideo)) {
                echo $this->render('_slickVideo', ['video' => $mainVideo]);
            }

            if (!empty($imgs)) {
                foreach ($imgs as $img) {
                    echo $this->render('_slickImg', ['img' => $img]);
                }
            }
            if (!empty($videos)) {
                foreach ($videos as $video) {
                    echo $this->render('_slickVideo', ['video' => $video]);
                }
            }
            ?>
        </div>
    </div>
    <?php if (!empty($mainVideo)) { ?>
        <div class="text-right">
            <span>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-facetime-video"></span> ' . (count($videos) + 1) . ' ' . Lang::t('main', 'video'),
                    $mainVideo->original_url,
                    [
                        'target' => '_blank',
                        'class' => 'video-link',
                        'data-video-id' => $mainVideo->entity_id,
                        'data-title' => $mainVideo->video_title,
                    ]
                ); ?>
            </span>
        </div>
    <?php } ?>
    <div class="margin-bottom">
        <h2><?= Html::a($item->title, $item->getUrl()); ?></h2>
        <?= $item->getShortDescription(250); ?>
    </div>
    <?php
    if (!empty($sounds)) {
        ?>
        <div class="margin-botton block-item-list-sound">
            <?php
            foreach ($sounds as $sound) {
                echo SoundWidget::widget(['music' => $sound]);
            }
            ?>
        </div>
        <?php
    }
    ?>
    <?php
    if ($item instanceof Event) {
        
        ?>
        <div class="margin-bottom">
            <?php
            if (count($locations)) {
                echo '<div id="locations-event-block-' . $item->id . '">';
                echo "<b>" . Lang::t("page/eventView", "location") . "</b><br/>";
                foreach ($locations as $location) {
                    echo Html::a(
                        '<span class="glyphicon glyphicon-map-marker"></span> ' . $location->getTitle(),
                        '',
                        [
                            'class'            => 'show-location-link truncate',
                            'title'              => $location->getTitle(),
                            'data-id'          => 'locations-event-block-' . $item->id,
                            'data-lat'         => $location->lat,
                            'data-lng'         => $location->lng,
                            'data-zoom'        => $location->zoom,
                            'data-title'       => $location->title,
                            'data-type'        => $location->getTypeLocal(),
                            'data-description' => $location->getDescription(),
                        ]
                    );
                }
                echo '</div>';
            } else {
                echo "<b>" . Lang::t("page/eventView", "location") . '</b> ';
                echo '<span class="glyphicon glyphicon-map-marker"></span>' . $item->getCountryCityText() . "<br/>";
            }
            ?>
        </div>
        <?php
        if (!empty($item->site)) {
            ?>
            <div class="margin-bottom">
                <b><?= Lang::t("page/eventView", "site") ?></b> <?= Html::a($item->site, $item->site) ?>
            </div>
            <?php
        }

    }
    ?>
    <?php
    if ($item instanceof School) {
    ?>
    <div class="margin-bottom">
        <?php
        if (count($locations)) {
            echo '<div id="locations-school-block-' . $item->id . '">';
            foreach ($locations as $location) {
                echo "<b>" . Lang::t("page/schoolView", "location") . '</b> ';
                echo Html::a(
                    '<span class="glyphicon glyphicon-map-marker"></span> ' . $location->getTitle(),
                    '',
                    [
                        'class'            => 'show-location-link',
                        'title'              => $location->getTitle(),
                        'data-id'          => 'locations-school-block-' . $item->id,
                        'data-lat'         => $location->lat,
                        'data-lng'         => $location->lng,
                        'data-zoom'        => $location->zoom,
                        'data-title'       => $location->title,
                        'data-title-url'   => $item->getUrl(),
                        'data-site-url'    => $item->site,
                        'data-type'        => $location->getTypeLocal(),
                        'data-description' => $location->getDescription(),
                    ]
                );
                echo "<br/>";
            }
            echo '</div>';
        } else {
            echo "<b>" . Lang::t("page/schoolView", "location") . '</b> ';
            echo '<span class="glyphicon glyphicon-map-marker"></span>' . $item->getCountryCityText() . "<br/>";
        }
        ?>
    </div>
    <?php
        if (!empty($item->site)) {
            ?>
            <div class="margin-bottom">
                <b><?= Lang::t("page/eventView", "site") ?></b> <?= Html::a($item->site, $item->site) ?>
            </div>
            <?php
        }
    }
    ?>
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
    <div>
        <?php
        $likeTitle = $item->like_count . " " . Lang::tn('main', 'vote', $item->like_count);
        $showTitle = $item->show_count . " " . Lang::tn('main', 'showCount', $item->show_count);
        ?>
        <span title="<?= $likeTitle ?>" class="margin-right-10">
            <i class="glyphicon glyphicon-thumbs-up"></i> <?= $item->like_count ?>
        </span>
        <span title="<?= $showTitle ?>">
            <i class="glyphicon glyphicon-eye-open"></i> <?= $item->show_count ?>
        </span>
        <span class="pull-right">
            <?= Html::tag('span', '<i class="glyphicon glyphicon-share-alt "></i>', ['class' => 'btn-share-entry cp']); ?>
        </span>
    </div>
    <div class="share42 hide" data-url="<?= $item->getUrl(); ?>"></div>
</div>