<?php
/**
 * @var yii\web\View $this
 * @var User         $user
 * @var bool         $isThisUser
 */

use common\models\User;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use frontend\widgets\ModalDialogsWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/account/view.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$userDisplayName = $user->getDisplayName();

$thisUser = User::thisUser();

$musics = $user->getLastAudio();
$imgs = [];
if ($isThisUser) {
    $imgs = $thisUser->getUserImgs();
}
$userinfo = $user->getUerinfo();
?>
<div class="site-index">
    <div class="body-content">
        <div id="item-header">
            <h1><?php
                echo $userDisplayName;
                if ($isThisUser) {
                    echo Html::a(
                        Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']),
                        Url::to(['account/edit']),
                        ['class' => 'header-pencil']
                    );
                    echo Html::a(
                        Html::tag('span', '', ['class' => 'glyphicon glyphicon-cog']) . ' ' . Lang::t('page/accountProfile', 'setting'),
                        Url::to(['account/settings']),
                        ['class' => 'btn btn-default pull-right']
                    );
                }
                ?></h1>
        </div>
        <table class="display-user-info">
            <tr>
                <td>
                    <div class="user-img">
                        <div class="background-img"
                             style="background-image: url('<?= $user->getAvatarPic() ?>');"></div>
                        <?php if ($isThisUser) { ?>
                        <div class="block-user-img-edit">
                            <span
                                class="glyphicon glyphicon-camera"></span> <?= Lang::t('page/accountProfile', 'editImage') ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="user-reputation">
                        <span class="user-reputation-number"><?= $user->reputation ?></span>
                        <span class="user-reputation-label"><?= Lang::t('page/accountProfile', 'reputation') ?></span>
                    </div>
                </td>
                <td>
                    <div class="user-name">
                        <?= $user->getFirstname() . ' ' . $user->getLastname() ?>
                    </div>
                    <div class="user-country">
                        <span class="glyphicon glyphicon-map-marker"></span>
                        <?= $userinfo->getCountryCityText() ?>
                    </div>
                    <div class="user-birthday">
                        <span class="glyphicon glyphicon-gift"></span>
                        <?= empty($userinfo->birthday) ? " - " : Lang::tdate($userinfo->birthday) ?>
                    </div>
                </td>
            </tr>
        </table>

        <div class="margin-bottom">
            <ul class="nav nav-tabs nav-main-tabs">
                <li data-tab="block-user-profile"
                    class="active"><?= Html::a(Lang::t('page/accountProfile', 'accountTabProfile'), Url::home()) ?></li>
                <li data-tab="block-user-item"
                    class=""><?= Html::a(Lang::t('page/accountProfile', 'accountTabItem'), Url::home()) ?></li>
                <li data-tab="block-user-audio"
                    class=""><?= Html::a(Lang::t('page/accountProfile', 'accountTabAudio'), Url::home()) ?></li>
                <?php if ($isThisUser) { ?>
                <li data-tab="block-user-img"
                    class=""><?= Html::a(Lang::t('page/accountProfile', 'accountTabImg'), Url::home()) ?></li>
                <?php } ?>
            </ul>
        </div>

        <div id="block-user-profile" class="block-user-tab-info">
            <h4><?= Lang::t('page/accountProfile', 'about_me') ?></h4>
            <?= $userinfo->getContactInfo('about_me') ?>
            <h4><?= Lang::t('page/accountProfile', 'contact_info') ?></h4>
            <table class="contact-table">
                <?php
                $arrContactInfo = ['telephone', 'skype', 'vk', 'fb'];
                foreach ($arrContactInfo as $info) {
                    if (!empty($userinfo->$info)) {
                        echo Html::tag(
                            'tr',
                            Html::tag('td', Html::tag('b', Lang::t('page/accountProfile', 'info_' . $info))) .
                            Html::tag('td', $userinfo->getContactInfo($info))
                        );
                    }
                }
                ?>
            </table>
        </div>
        <div id="block-user-item" class="block-user-tab-info hide">
            <?= ItemList::widget(['orderBy' => ItemList::ORDER_BY_ID, 'userId' => $user->id, 'display' => ItemList::ITEM_LIST_DISPLAY_MINI, 'onlyItem' => true, 'limit' => 50]) ?>
        </div>
        <div id="block-user-audio" class="block-user-tab-info hide">
            <div class="block-item-list-sound">
                <?php
                foreach ($musics as $music) {
                    echo \frontend\widgets\SoundWidget::widget(['music' => $music]);
                }
                ?>
            </div>
        </div>
        <?php if ($isThisUser) { ?>
        <div id="block-user-img" class="block-user-tab-info hide">
            <div class="block-imgs">
                <?php
                foreach ($imgs as $img) {
                    ?>
                    <div class="img-input-group block-img-add" data-id="<?= $img->id ?>"
                         data-url="<?= $img->short_url ?>">
                        <?= Html::tag(
                            'div',
                            Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img']),
                            ['class' => 'img-input-group']
                        ) ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php if ($isThisUser) { ?>
<?= ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_ADD_AVATAR]) ?>
<?php } ?>
