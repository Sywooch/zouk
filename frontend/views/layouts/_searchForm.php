<?php
/**
 * @var \common\models\form\SearchEntryForm $searchForm
 * @var string $formClass
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
        'action'      => Yii::$app->getUrlManager()->createUrl(Yii::$app->request->pathInfo),
        'options'     => [
            'class' => $formClass,
        ],
    ]
);
?>
    <div class="input-group">
        <?= Html::activeTextInput($searchForm, 'search_text', ['placeholder' => 'Найти', 'class' => 'form-control']); ?>
        <span class="input-group-btn">
            <?= Html::submitButton(Html::tag('span', '', ['class' => 'glyphicon glyphicon-search']), ['class' => 'btn btn-default']); ?>
        </span>
    </div>
<?php
ActiveForm::end();