<?php
/**
 * @var \yii\web\View $this
 * @var VkTaskForm $vkTaskForm
 */

use common\models\form\VkTaskForm;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();

echo $form->field($vkTaskForm, 'type')->dropDownList(VkTaskForm::getTypeLabels());

echo $form->field($vkTaskForm, 'group_id');

echo $form->field($vkTaskForm, 'period')->dropDownList(VkTaskForm::getPeriodLabels());

echo $form->field($vkTaskForm, 'time_start')->input('time');

echo $form->field($vkTaskForm, 'time_end')->input('time');

echo \yii\helpers\Html::submitButton('Добавить', ['class' => 'btn btn-primary']);

ActiveForm::end();