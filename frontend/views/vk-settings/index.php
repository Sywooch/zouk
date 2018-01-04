<?php
/**
 * @var \yii\web\View $this
 * @var \common\models\VkAccessToken $vkAccessToken
 * @var \common\models\search\VkTaskSearch $searchModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
use common\models\form\VkTaskForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if (!empty($vkAccessToken)) {
    ?>
    <h3>Токен для вк</h3>
    <label>
        Группа <b><?= $vkAccessToken->group_id; ?></b>: <code><?= $vkAccessToken->access_token; ?></code>.
    </label>
    <?php
}
?>

<h3>Предоставить доступ к vk</h3>
<?php
$form = ActiveForm::begin(['action' => ['vk-settings/get-access-token'], 'id' => 'profile-settings-form', 'options' => ['target' => '_blank']]);

echo Html::tag('label', 'ID группы', ['class' => 'control-label']);
?>
<div class="input-group">
    <span class="input-group-btn">
        <?= Html::submitButton('Получить code', ['class' => 'btn btn-default', 'name' => 'list-add-button']); ?>
    </span>
</div>
<?php
echo Html::tag('label', 'Code', ['class' => 'control-label']);
?>
<div class="input-group">
    <?= Html::textInput('code', '', ['class' => 'form-control']); ?>
    <span class="input-group-btn">
        <?= Html::submitButton('Получить access_token', ['class' => 'btn btn-default', 'name' => 'list-add-button']); ?>
    </span>
</div>
<?php
echo Html::tag('p', 'После нажатия на кнопку "Получить code" Вас перенаправит на страницу доступа, в адресе будет написан code, который необходимо скопировать в поле и нажать на "Получить access_token"');


ActiveForm::end();
?>


<h3>Список заданий</h3>

<h4>Добавить задание:</h4>
<div class="btn-group">
    <?php
    echo Html::a(
        'Случайное видео',
        ['vk-settings/add-task', 'type' => VkTaskForm::TYPE_RANDOM_VIDEO],
        ['class' => 'btn btn-default']
    );
    echo Html::a(
        'Поздравление с Днём Рождения',
        ['vk-settings/add-task', 'type' => VkTaskForm::TYPE_BDAY],
        ['class' => 'btn btn-default']
    );
    ?>
</div>
<?php

echo $this->render('_vkTaskGridView', [
    'searchModel'  => $searchModel,
    'dataProvider' => $dataProvider,
]);
?>

