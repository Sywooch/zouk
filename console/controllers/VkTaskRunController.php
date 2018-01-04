<?php

namespace console\controllers;

use common\components\vk\VkontakteComponent;
use common\models\VkAccessToken;
use common\models\VkTask;
use common\models\VkTaskCompleted;
use yii\console\Controller;

class VkTaskRunController extends Controller
{

    private function typeRandomVideo($vkTask)
    {
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
                    return $vkTaskCompleted->save();
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
        return false;
    }

    /**
     * @param VkTask $vkTask
     * @return bool
     */
    private function typeBDay($vkTask)
    {
        /** @var VkAccessToken $accesses */
        $access = VkAccessToken::findOne(['user_id' => $vkTask->user_id]);
        if ($access) {
            /** @var VkontakteComponent $vkapi */
            $vkapi = \Yii::$app->vkapi;
            $vkapi->initAccessToken($access->access_token);
            $publishDate = new \DateTime('now', timezone_open('Europe/Moscow'));
            $nowTime = intval($publishDate->format('H')) * 3600 + intval($publishDate->format('i')) * 60;

            if ($vkTask->time_end > $nowTime) {
                if ($vkTask->time_start < $nowTime) {
                    $vkTask->time_start = $nowTime;
                }
                $publishDateGenerate = rand($vkTask->time_start, $vkTask->time_end);
                $h = floor($publishDateGenerate / 3600);
                $m = floor(($publishDateGenerate % 3600) / 60);
                $publishDate->setTime($h, $m);

                $response = $vkapi->congratulateBDay($vkTask, $publishDate->getTimestamp());

                if (!empty($response['post_id'])) {
                    $vkTaskCompleted = new VkTaskCompleted();
                    $vkTaskCompleted->setAttributes([
                        'type'       => $vkTask->type,
                        'user_id'    => $vkTask->user_id,
                        'vk_task_id' => $vkTask->id,
                    ]);
                    return $vkTaskCompleted->save();
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
        return false;
    }

    public function actionRun()
    {

        /** @var VkTask[] $vkTasks */
        $vkTasks = VkTask::find()->orderBy(['time_start' => SORT_ASC])->all();


        $taskCountRun = 0;
        foreach ($vkTasks as $vkTask) {
            /** @var VkTaskCompleted $vkTaskCompleted */
            $vkTaskCompleted = $vkTask->getVkTaskCompleted()->orderBy(['date_create' => SORT_DESC])->one();

            if ($vkTask->period == VkTask::PERIOD_DAY) {
                if ($vkTaskCompleted) {
                    $oldDate = (new \DateTime())->setTimestamp($vkTaskCompleted->date_create)->setTime(0, 0, 0);
                    $new = (new \DateTime())->setTime(0, 0, 0);
                    $dayInterval = $new->diff($oldDate);
                    $day = $dayInterval->d;
                    if ($day !== false && $day < 1) {
                        continue;
                    }
                }
            }
            if ($vkTask->type == VkTask::TYPE_RANDOM_VIDEO) {
                if ($this->typeRandomVideo($vkTask)) {
                    $taskCountRun++;
                }
            } elseif ($vkTask->type == VkTask::TYPE_BDAY) {
                if ($this->typeBDay($vkTask)) {
                    $taskCountRun++;
                }
            }
            if ($taskCountRun > 5) {
                break;
            }
        }
    }
}