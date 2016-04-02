<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\Comment;
use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ItemList;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * Site controller
 */
class CommentController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'edit', 'delete', 'alarm'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit', 'delete', 'alarm'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAdd()
    {
        if (User::thisUser()->reputation < Comment::MIN_REPUTATION_COMMENT_CREATE) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $anchor = '';
        $comment = new Comment();
        if ($comment->load(Yii::$app->request->post())) {
            $comment->description = nl2br($comment->description);
            $comment->description = \yii\helpers\HtmlPurifier::process($comment->description, []);
            $comment->entity_id = (int)$comment->entity_id;
            $comment->parent_id = (int)$comment->parent_id;
            $comment->user_id = Yii::$app->user->identity->getId();
            $comment->like_count = 0;

            if ($comment->save()) {
                $anchor = $comment->id;
            }
        }

        return json_encode([
            'content' => \frontend\widgets\CommentsWidget::widget(['entity' => $comment->entity, 'entity_id' => $comment->entity_id, 'showDialog' => false]),
            'anchor'  => $anchor,
        ]);
    }

    public function actionDelete($id)
    {
        /** @var Comment $comment */
        $comment = Comment::findOne($id);
        if ($comment && $comment->user_id == User::thisUser()->id) {
            $comment->deleted = 1;
            if ($comment->save()) {
                return json_encode([
                    'content' => \frontend\widgets\CommentsWidget::widget(['entity' => $comment->entity, 'entity_id' => $comment->entity_id, 'showDialog' => false]),
                    'anchor'  => '',
                ]);
            };
        }

        return json_encode([
            'content' => '',
            'anchor'  => '',
        ]);
    }

    public function actionAlarm()
    {
        $id = Yii::$app->request->post('id');
        $msg = Yii::$app->request->post('msg');
        $comment = Comment::findOne($id);
        if ($comment && !empty($msg)) {
            if (Alarm::addAlarm(Alarm::ENTITY_COMMENT, $comment->id, $msg)) {
                return json_encode([
                    'content' => \frontend\widgets\CommentsWidget::widget(['entity' => $comment->entity, 'entity_id' => $comment->entity_id, 'showDialog' => false]),
                    'anchor'  => $comment->id,
                ]);
            } else {
                return json_encode([
                    'content' => '',
                    'anchor'  => '',
                ]);
            }
        }
    }

}