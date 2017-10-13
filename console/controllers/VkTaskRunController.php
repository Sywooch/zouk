<?php

namespace console\controllers;

use common\components\vk\VkontakteComponent;
use common\models\VkAccessToken;
use common\models\VkTask;
use common\models\VkTaskCompleted;
use yii\console\Controller;

class VkTaskRunController extends Controller
{

    public function actionRun()
    {
        
        /** @var VkTask[] $vkTasks */
        $vkTasks = VkTask::find()->all();
        
        
        foreach ($vkTasks as $vkTask) {
            /** @var VkTaskCompleted $vkTaskCompleted */
            $vkTaskCompleted = $vkTask->getVkTaskCompleted()->orderBy(['date_create' => SORT_DESC])->one();
            if ($vkTask->type == VkTask::TYPE_RANDOM_VIDEO) {
                if ($vkTask->period == VkTask::PERIOD_DAY) {
                    if ($vkTaskCompleted) {
                        $oldDate = (new \DateTime())->setTimestamp($vkTaskCompleted->date_create)->setTime(0,0,0);
                        $new = (new \DateTime())->setTime(0,0,0);
                        $dayInterval = $new->diff($oldDate);
                        $day = $dayInterval->d;
                        if ($day !== false && $day < 1) {
                            continue;
                        }
                    }
                }
                /** @var VkAccessToken $accesses */
                $access = VkAccessToken::findOne(['user_id' => $vkTask->user_id]);
                if ($access) {
                    /** @var VkontakteComponent $vkapi */
                    $vkapi = \Yii::$app->vkapi;
                    $vkapi->initAccessToken($access->access_token);
                    $publishDate = new \DateTime();
                    $nowTime = intval($publishDate->format('H')) * 3600 + intval($publishDate->format('m')) * 60;

                    if ($vkTask->time_end > $nowTime) {
                        if ($vkTask->time_start < $nowTime) {
                            $vkTask->time_start = $nowTime;
                        }
                        $publishDateGenerate = rand($vkTask->time_start, $vkTask->time_end);
                        $h = intval($publishDateGenerate / 3600);
                        $m = intval($publishDateGenerate / 60) % 60;
                        $publishDate->setTime($h, $m);

                        $response = $vkapi->postRandomVideo($vkTask->group_id, $publishDate->getTimestamp(), []);

                        if (!empty($response['post_id'])) {
                            $vkTaskCompleted = new VkTaskCompleted();
                            $vkTaskCompleted->setAttributes([
                                'type'       => $vkTask->type,
                                'user_id'    => $vkTask->user_id,
                                'vk_task_id' => $vkTask->id,
                            ]);
                            $vkTaskCompleted->save();
                        } else {
                            if (!empty($response['error'])) {
                                $error = $response['error'];
                                if ($error['error_code'] == 203) {
                                    // "Access to group denied: !group
                                    $vkTask->delete();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}