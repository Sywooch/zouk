<?php
/**
 * @var Comment[] $comments
 * @var Comment[] $commentsParent
 * @var string    $entity
 * @var integer   $entity_id
 * @var bool      $showDialog
 * @var Vote[]    $voteItems
 */

use common\models\Comment;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$newComment = new Comment();
$newComment->entity = $entity;
$newComment->entity_id = $entity_id;
$newComment->parent_id = 0;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/comment/comment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$thisUser = User::thisUser();
?>
<div id="blockComments">
    <div class="row margin-bottom">
        <div class="col-md-12">
            <?php
            if (!Yii::$app->user->isGuest && $thisUser->reputation >= Comment::MIN_REPUTATION_COMMENT_CREATE) {
                $form = ActiveForm::begin(['action' => ['comment/add'], 'id' => 'main-comment-form']);

                echo $form->field($newComment, 'entity')->hiddenInput()->label(false);
                echo $form->field($newComment, 'entity_id')->hiddenInput()->label(false);
                echo $form->field($newComment, 'parent_id')->hiddenInput(['class' => 'comment-parent-id'])->label(false);

                echo $form->field($newComment, 'description', [
                    'inputOptions' => [
                        'class'       => 'form-control',
                        'placeholder' => Lang::t('main/comments', 'addCommentDescription'),
                    ],
                ])->textarea()->label(false);

                echo Html::submitButton(Lang::t('main/comments', 'buttonAddComment'), ['class' => 'btn btn-primary pull-right', 'name' => 'list-add-button']);
                echo Html::resetButton(Lang::t('main/comments', 'buttonCancelComment'), ['class' => 'btn btn-default pull-right btn-cancel-comment', 'style' => 'margin-right: 10px;']);

                ActiveForm::end();
            }
            ?>
        </div>
    </div>

    <div class="row">
        <?php
        foreach ($comments as $comment) {
            echo $this->render(
                'view',
                [
                    'comment' => $comment,
                    'voteItem' => isset($voteItems[$comment->id]) ? $voteItems[$comment->id] : null
                ]
            );
            echo "<div class='col-md-12'><div class='row comment-reply-block'>";
            if (!empty($commentsParent[$comment->id])) {
                foreach (array_reverse($commentsParent[$comment->id]) as $commentReply) {
                    echo $this->render(
                        'view',
                        [
                            'comment' => $commentReply,
                            'voteItem' => isset($voteItems[$commentReply->id]) ? $voteItems[$commentReply->id] : null
                        ]
                    );
                }
            }
            echo "</div></div>";
        }
        ?>
    </div>
</div>


<?php if ($showDialog) { ?>
<div class="modal fade modal-delete-comment-confirm bs-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/comments', 'deleteCommentConfirmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('main/comments', 'deleteCommentConfirm') ?>
            </div>
            <div class="modal-footer">
                <a href="" type="button" class="btn btn-danger btn-delete-comment"><?= Lang::t('main/comments', 'deleteBtn') ?></a>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Lang::t('main/comments', 'buttonCancelComment') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-comment-alarm bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/comments', 'modalCommentAlarmTitle') ?></h4>
            </div>
            <div class="modal-body">
                <?= Lang::t('main/comments', 'msgCommentAlarm') ?>
                <?= Html::input('text', 'alarmMsg', '', ['class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <?= Html::a(
                    Lang::t('main/dialogs', 'modalAlarm_alarmBtn'),
                    Url::to(['comment/alarm']),
                    [
                        'id'             => 'alarm-comment',
                        'class'          => 'btn btn-danger',
                        'data-href'      => Url::to(['comment/alarm']),
                        'data-msg-alarm' => Lang::t('main/dialogs', 'modalAlarm_msg'),
                        'data-id'        => '',
                    ]
                ), ' ';
                ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('main/dialogs', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>
<?php } ?>