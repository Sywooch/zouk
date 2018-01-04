<?php
namespace common\models\form;

use common\models\VkTask;

class VkTaskForm extends VkTask
{

    public $startText = '';

    public $bottomText = '';

    /** @var VkTask|null */
    private $vkTaskModel = null;

    public function rules()
    {
        return [
            [['user_id', 'period', 'date_update', 'date_create'], 'integer'],
            [['type', 'group_id', 'startText', 'bottomText'], 'string'],
            [['time_start', 'time_end'], 'safe'],
        ];
    }

    public static function loadById($id)
    {
        $vkTaskForm = new VkTaskForm();
        $vkTask = VkTask::findOne(['id' => $id]);
        if ($vkTask) {
            $timeStart = str_pad(floor($vkTask->time_start / 3600), 2, '0', STR_PAD_LEFT)
                . ':'
                . str_pad(floor(($vkTask->time_start % 3600) / 60), 2, '0', STR_PAD_LEFT);
            $timeEnd = str_pad(floor($vkTask->time_end / 3600), 2, '0', STR_PAD_LEFT)
                . ':'
                . str_pad(floor(($vkTask->time_end % 3600) / 60), 2, '0', STR_PAD_LEFT);
            $vkTaskForm->setAttributes([
                'type'       => $vkTask->type,
                'group_id'   => $vkTask->group_id,
                'period'     => $vkTask->period,
                'time_start' => $timeStart,
                'time_end'   => $timeEnd,
            ]);
            $vkTaskForm->bottomText = $vkTask->getParamsByKey(VkTask::PARAMS_BOTTOM_TEXT);
            $vkTaskForm->startText = $vkTask->getParamsByKey(VkTask::PARAMS_START_TEXT);
            $vkTaskForm->vkTaskModel = $vkTask;
        }
        return $vkTaskForm;
    }

    public function saveVkTask()
    {
        if ($this->validate()) {
            /** @var VkTask $newVkTask */
            $newVkTask = $this->vkTaskModel;
            if ($newVkTask) {
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
                $newVkTask->setParamsByKey(VkTask::PARAMS_BOTTOM_TEXT, $this->bottomText);
                $newVkTask->setParamsByKey(VkTask::PARAMS_START_TEXT, $this->startText);
                if ($newVkTask->save()) {
                    return true;
                }
                $this->addErrors($newVkTask->getErrors());
            }
        }
        return false;
    }

    public function saveNewVkTask()
    {
        if ($this->validate()) {
            $this->vkTaskModel = new VkTask();
            return $this->saveVkTask();
        }
        return false;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return false;
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'startText'  => 'Текст в заголовке',
                'bottomText' => 'Подпись',
            ]
        );
    }
}