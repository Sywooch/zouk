<?php
namespace frontend\widgets;

use common\models\Comment;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Vote;
use yii\data\Pagination;

class CommentsWidget extends \yii\bootstrap\Widget
{

    public $entity;

    public $entity_id;

    public $showDialog = true;

    public function init()
    {
    }

    public function run()
    {

        $query = Comment::find();
        if ($this->entity == Comment::ENTITY_ITEM || $this->entity == Comment::ENTITY_EVENT || $this->entity == Comment::ENTITY_SCHOOL) {
            $query = $query->andWhere(['entity' => $this->entity, 'entity_id' => $this->entity_id])->orderBy('date_create DESC');
        }
        $commentsAll = $query->all();
        $commentIds = [];
        $comments = [];
        $commentsParent = [];
        $commentsLink = [];

        /** @var Comment[] $commentsAll */
        foreach ($commentsAll as $comment) {
            if ($comment->parent_id == 0) {
                // Главный комментарий
                $commentsLink[$comment->id] = true;
                $commentIds[] = $comment->id;
                if ($comment->deleted == 0) {
                    $comments[] = $comment;
                }
            } else {
                // Ответ на комментарий
                $commentsLink[$comment->id] = $comment->parent_id;
            }
        }


        foreach ($commentsAll as $comment) {
            if ($comment->deleted == 0) {
                $commentId = $comment->parent_id;
                while (isset($commentsLink[$commentId])) {
                    if ($commentsLink[$commentId] === true) {
                        break;
                    } else if ($commentsLink[$commentId] != $commentId) {
                        $commentId = $commentsLink[$commentId];
                    } else {
                        $commentId = null;
                    }
                }
                if (!empty($commentId)) {
                    $commentsParent[$commentId][] = $comment;
                    $commentIds[] = $comment->id;
                }
            }
        }

        $thisUser = User::thisUser();
        /** @var Vote[] $voteItemsAll */
        $voteItemsAll = !empty($thisUser) ? $thisUser->getVotesByEntity(Vote::ENTITY_COMMENT, $commentIds) : [];
        $voteItems = [];
        foreach ($voteItemsAll as $voteItem) {
            $voteItems[$voteItem->entity_id] = $voteItem;
        }

        return $this->render(
            'commentsWidget/list',
            [
                'comments'       => $comments,
                'voteItems'      => $voteItems,
                'commentsParent' => $commentsParent,
                'entity'         => $this->entity,
                'entity_id'      => $this->entity_id,
                'showDialog'     => $this->showDialog,
            ]
        );
    }

}