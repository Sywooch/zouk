<?php
/**
 *
 */
use common\models\Music;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$thisUser = User::thisUser();
?>

<div class="modal fade modal-edit-music bs-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/dialogs', 'modalEditMusic_title') ?></h4>
            </div>
            <div class="modal-body">
                <?= Html::label(Lang::t('main/dialogs', 'modalEditMusic_labelArtist')) ?>
                <?= Html::input('text', 'modalEditMusicArtist', '', ['class' => 'form-control']) ?>
                <?= Html::label(Lang::t('main/dialogs', 'modalEditMusic_labelTitle')) ?>
                <?= Html::input('text', 'modalEditMusicTitle', '', ['class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <?= Html::a(
                    Lang::t('main/dialogs', 'modalEditMusic_save'),
                    Url::to(['music/save']),
                    [
                        'class' => 'btn btn-primary btn-music-save',
                    ]
                ), ' ';
                ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('main/dialogs', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>