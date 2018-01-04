<?php
/**
 * @var \yii\web\View $this
 * @var VkTaskForm $vkTaskForm
 * @var ActiveForm $form
 */

use common\models\form\VkTaskForm;
use common\models\VkTask;
use yii\widgets\ActiveForm;

echo $form->field($vkTaskForm, 'type')->dropDownList(VkTaskForm::getTypeLabels(), ['readonly' => true]);

echo $form->field($vkTaskForm, 'group_id');

echo $form->field($vkTaskForm, 'period')->dropDownList(VkTaskForm::getPeriodLabels());

echo $form->field($vkTaskForm, 'time_start')->input('time');

echo $form->field($vkTaskForm, 'time_end')->input('time');

if ($vkTaskForm->type == VkTaskForm::TYPE_BDAY) {
    $defaultStartText = 'Администрация @prozouk(Зук-портала) поздравляет С ДНЁМ РОЖДЕНИЯ наших подписчиков: ';
    $startText = $vkTaskForm->startText ?: $defaultStartText;
    $vkTaskForm->startText = $startText;
    echo $form->field($vkTaskForm, 'startText')->textarea();

    $defaultBottomText = '#prozouk #zouk #dancezouk #congratulation #happybirthday';
    $bottomText = $vkTaskForm->bottomText ?: $defaultBottomText;
    $vkTaskForm->bottomText = $bottomText;
    echo $form->field($vkTaskForm, 'bottomText')->textarea();
}