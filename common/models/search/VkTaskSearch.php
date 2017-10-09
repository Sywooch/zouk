<?php
namespace common\models\search;

use common\models\VkTask;
use yii\data\ActiveDataProvider;

class VkTaskSearch extends VkTask
{


    public function search($query = null, array $params = [])
    {
        if (empty($query)) {
            $query = VkTask::find();
        }

        $this->load($params);

        $query->andWhere(['user_id' => \Yii::$app->user->id ?? 0]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['id' => SORT_ASC],
                'attributes'   => [
                ]
            ]
        ]);

        return $dataProvider;
    }
}