<?php
/**
 * @var Music[] $musics
 */
use common\models\Music;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$thisUser = User::thisUser();
?>
<div class="modal fade modal-add-music bs-example-modal-md" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" data-user-id="<?= $thisUser->id ?>">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/music', 'modalAddMusicTitle') ?></h4>
            </div>
            <div class="modal-body">
                <div class="block-sound-user-list">
                    <table class="margin-bottom" width="100%">
                        <tr>
                            <td>
                                <div class="input-group margin-right-10 input-group-search-audio" data-url="<?= Url::to(['music/searchmusicfromself']) ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><i class="glyphicon glyphicon-search"></i></button>
                                    </span>
                                    <input type="text" class="form-control " placeholder="<?= Lang::t('main/music', 'modalAddMusic_searchAudio') ?>">
                                </div>
                            </td>
                            <td>
                                <?= Html::button(Lang::t('main/music', 'buttonUploadMusic'), ['class' => 'btn btn-primary btn-sm btn-add-sound-form']) ?>
                            </td>
                        </tr>
                    </table>

                    
                    <table class="audio-list" width="100%">
                        <?php
                        foreach ($musics as $music) {
                            ?>
                            <tr class="audio-list-item">
                                <td><?= \frontend\widgets\SoundWidget::widget(['music' => $music]) ?></td>
                                <td><?= Html::button(
                                        Lang::t('main/music', 'btnAdd'),
                                        [
                                            'class'         => 'btn btn-link btn-music-add no-focus',
                                            'data-music-id' => $music->id,
                                            'data-url'      => Url::to(['music/sound', 'id' => $music->id]),
                                        ]
                                    ) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <div class="block-add-sound hide">
                    <?= \frontend\widgets\AddMusicWidget::widget([]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <?= Html::button(Lang::t('main/music', 'modalAddMusic_addFromList'), ['class' => 'btn btn-link btn-add-from-list pull-left no-focus hide']) ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/listView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>
