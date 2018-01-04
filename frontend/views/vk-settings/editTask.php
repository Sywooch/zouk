<?php
/**
 * @var \yii\web\View $this
 * @var VkTaskForm $vkTaskForm
 */

use common\models\form\VkTaskForm;
use common\models\VkTask;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();

echo $this->render('_form', [
    'vkTaskForm' => $vkTaskForm,
    'form'       => $form,
]);

echo \yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
echo ' ';
echo \yii\helpers\Html::a('Назад', ['vk-settings/index'], ['class' => 'btn btn-danger']);

ActiveForm::end();