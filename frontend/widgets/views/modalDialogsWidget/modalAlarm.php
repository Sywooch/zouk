<?php
/**
 * @var int $itemId
 */
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="modal fade modal-alarm bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/dialogs', 'modalAlarm_title') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('main/dialogs', 'modalAlarm_msg') ?>
                <?= Html::input('text', 'alarmMsg', '', ['class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <?= Html::a(
                    Lang::t('main/dialogs', 'modalAlarm_alarmBtn'),
                    Url::to(['list/alarm']),
                    [
                        'id'             => 'alarm-item',
                        'class'          => 'btn btn-danger',
                        'data-href'      => Url::to(['list/alarm']),
                        'data-msg-alarm' => Lang::t('main/dialogs', 'modalAlarm_msg'),
                        'data-id'        => $itemId,
                    ]
                ), ' ';
                ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('main/dialogs', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>