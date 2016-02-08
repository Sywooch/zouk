<?php
/**
 * @var yii\web\View        $this
 * @var \common\models\Item $item
 */

use frontend\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Lang::t('page/listEdit', 'title');

$this->params['breadcrumbs'][] = $this->title;

?>
<div id="item-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'list-add-form']); ?>

            <?= $form->field($item, 'title')->label(Lang::t('page/listEdit', 'fieldTitle')) ?>

            <?= $form->field($item, 'description')->textarea()->label(Lang::t('page/listEdit', 'fieldDescription')) ?>

            <div class="form-group">
                <?= Html::submitButton(Lang::t('page/listEdit', 'buttonAdd'), ['class' => 'btn btn-primary', 'name' => 'list-add-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
