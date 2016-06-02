<?php
/**
 * @var Comment $comment
 * @var Vote    $voteItem
 */
use common\models\Comment;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$thisUser = User::thisUser();

?>
<div class="col-md-12 hash" id="comment-<?= $comment->id ?>">
    <?php
    /** @var User $userComment */
    $userComment = $comment->user;
    echo Html::tag('span', Html::img($userComment->getAvatarPic(), ['class' => 'user-avatar']), ['class' => 'block-user-avatar pull-left']);
    ?>
    <div class="comment-content">
        <div class="comment-header"><b><?= $userComment->getDisplayName() ?></b> <?=
            Html::a(
                date("d.m.Y", $comment->date_create) . " " . Lang::t('main', 'at') . " " . date("H:i", $comment->date_create),
                $comment->getUrl(),
                []
            )
            ?>
        </div>
        <div class="comment-description">
            <?= $comment->description ?>
        </div>

        <div>
            <?php
            if (!Yii::$app->user->isGuest && $thisUser->reputation >= Comment::MIN_REPUTATION_COMMENT_CREATE) {
                echo Html::button(
                    Lang::t('main/comments', 'replyComment'),
                    [
                        'class'          => 'btn-link no-focus reply-comment',
                        'data-parent-id' => $comment->id,
                    ]
                );
            }
            if (!Yii::$app->user->isGuest && $thisUser->reputation >= Comment::MIN_REPUTATION_COMMENT_VOTE) {
                echo Html::button($comment->getVoteCount(), ['class' => 'btn-link no-focus comment-vote-count']);
                echo Html::button(
                    '<i class="glyphicon glyphicon-thumbs-up"></i>',
                    [
                        'class'       => 'btn-link no-focus comment-vote-up' . ((!empty($voteItem) && $voteItem->vote == Vote::VOTE_UP) ? ' voted' : ''),
                        'data-id'     => $comment->id,
                        'data-href'   => Url::to(['vote/add']),
                        'data-vote'   => Vote::VOTE_UP,
                        'data-entity' => Vote::ENTITY_COMMENT,
                    ]
                );
                echo Html::button(
                    '<i class="glyphicon glyphicon-thumbs-down"></i>',
                    [
                        'class'       => 'btn-link no-focus comment-vote-down' . ((!empty($voteItem) && $voteItem->vote == Vote::VOTE_DOWN) ? ' voted' : ''),
                        'data-id'     => $comment->id,
                        'data-href'   => Url::to(['vote/add']),
                        'data-vote'   => Vote::VOTE_DOWN,
                        'data-entity' => Vote::ENTITY_COMMENT,
                    ]
                );
            }
            if (!Yii::$app->user->isGuest) {
                echo Html::button(
                    '<i class="glyphicon glyphicon-flag"></i>',
                    [
                        'class'   => 'btn-link no-focus btn-show-alarm-comment',
                        'data-id' => $comment->id,
                        'title'   => Lang::t('main/comments', 'alarmComment'),
                    ]
                );
            }
            if (!Yii::$app->user->isGuest && $thisUser->id == $comment->user_id) {
                echo Html::button(
                    '<i class="glyphicon glyphicon-trash"></i>',
                    [
                        'class'    => 'btn-link no-focus btn-show-delete-comment',
                        'data-url' => Url::to(['comment/delete', 'id' => $comment->id]),
                        'title'    => Lang::t('main/comments', 'deleteComment'),
                    ]
                ), ' ';
            }
            ?>
        </div>
        <div class="comment-reply" data-parent-id="<?= $comment->id ?>">
        </div>

    </div>

</div>
