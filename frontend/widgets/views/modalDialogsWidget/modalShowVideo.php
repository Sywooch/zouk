<?php
/**
 */
use frontend\models\Lang;
use yii\helpers\Html;

$this->registerJsFile('https://www.youtube.com/iframe_api', []);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/video/videos.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/video/showVideo.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="modal fade modal-show-video bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="dialog-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="block-iframe-video">
                    <div id="ytplayer"></div>
                </div>
                <div class="block-video-list">
                    <ul>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>