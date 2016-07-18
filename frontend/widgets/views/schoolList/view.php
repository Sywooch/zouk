<?php
/**
 * @var School  $school
 * @var string $dateCreateType
 */
use common\models\School;
use common\models\Tags;
use common\models\User;
use frontend\models\Lang;
use frontend\widgets\SchoolList;
use yii\helpers\Html;
use yii\helpers\Url;

$url = $school->getUrl();
$imgs = $school->getImgsSort();
$mainImg = null;
if (!empty($imgs)) {
    $mainImg = array_shift($imgs);
}
$tags = $school->tagEntity;
$locations = $school->locations;
?>
<div id="school-<?= $school->id ?>" data-date="<?= $school->date ?>" data-id="<?= $school->id ?>" class="row block-school-summary margin-bottom <?= $school->like_count < 0 ? 'bad-school' : '' ?>"">
    <div class="col-sm-1 visible-sm-block visible-md-block visible-lg-block">
        <div class="cp" onclick="window.location.href='<?= $url ?>'">
            <div class="votes">
                <div class="mini-counts">
                    <?= Html::tag('span', $school->like_count, ['title' => $school->like_count]) ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'vote', $school->like_count) ?>
                </div>
            </div>
            <div class="views">
                <div class="mini-counts">
                    <?= $school->show_count ?>
                </div>
                <div>
                    <?= Lang::tn('main', 'showCount', $school->show_count) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-11">
        <div class="summary">
            <h3><?= Html::a($school->getTitle(), $url, ['class' => 'school-hyperlink']) ?></h3>
        </div>

        <?php
        if ($school->like_count >= 0) {
            ?>
            <div class="school-short-description margin-bottom">
                <?= $school->getShortDescription(800) ?>
            </div>
            <?php
            if (count($locations)) {
                echo '<div id="locations-school-block-' . $school->id . '">';
                foreach ($locations as $location) {
                    echo "<b>" . Lang::t("page/schoolView", "location") . '</b> ';
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
            <?php if (!empty($mainImg)) { ?>
                <div class="margin-bottom">
                    <?= Html::img($mainImg->short_url, ['class' => 'main-image-school', 'data-img-url' => $mainImg->short_url]) ?>
                </div>
            <?php } ?>
            <div class="margin-bottom block-school-list-img block-imgs">
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
                    /** @var Tags $tagSchool */
                    $tagSchool = $tag->tags;
                    if ($dateCreateType == schoolList::DATE_CREATE_ALL) {
                        $urlTag = Url::to(['/', 'tag' => $tagSchool->getName()]);
                    } else {
                        $urlTag = Url::to(['list/' . $dateCreateType, 'tag' => $tagSchool->getName()]);
                    }
                    echo Html::a($tagSchool->getName(), $urlTag, ['class' => 'label label-tag-element']), " ";
                }
                ?>
            </div>
            <?php
        }
        ?>
        <?php
        $author = $school->user;
        ?>
        <div class="pull-right">
            <table>
                <tr>
                    <td>
                        <div class="mini-like-show  visible-sm-block visible-xs-block">
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
                        <div class="pull-right user-info">
                            <div class="user-action-time">
                                <?= Lang::t("main", "created") . " " . date("d.m.Y", $school->date_create) . " " . Lang::t("main", "at") . " " . date("H:i", $school->date_create) ?>
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