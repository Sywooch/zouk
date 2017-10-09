<?php
namespace common\models\form;

use common\models\VkTask;

class VkTaskForm extends VkTask
{

    public function rules()
    {
        return [
            [['user_id', 'period', 'date_update', 'date_create'], 'integer'],
            [['type', 'group_id'], 'string'],
            [['time_start', 'time_end'], 'safe']
        ];
    }

    public function saveNewVkTask()
    {
        if ($this->validate()) {
            $newVkTask = new VkTask();
            $timeStartArr = explode(':', $this->time_start);
            $timeStart = ($timeStartArr[0] ?? 0) * 3600 + ($timeStartArr[1] ?? 0) * 60;
            $timeEndArr = explode(':', $this->time_end);
            $timeEnd = ($timeEndArr[0] ?? 0) * 3600 + ($timeEndArr[1] ?? 0) * 60;
            $newVkTask->setAttributes([
                'type'       => $this->type,
                'user_id'    => \Yii::$app->user->id,
                'group_id'   => $this->group_id,
                'period'     => $this->period,
                'time_start' => $timeStart,
                'time_end'   => $timeEnd,
            ]);
            if ($newVkTask->save()) {
                return true;
            }
            $this->addErrors($newVkTask->getErrors());
        }
        return false;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return false;
    }
}