<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class VoteModel
 */
class VoteModel extends ActiveRecord
{

    public function getVoteCount()
    {
        return 0;
    }

    public function addVote($changeVote)
    {

    }
}