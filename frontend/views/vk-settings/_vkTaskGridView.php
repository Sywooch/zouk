<?php
/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \common\models\search\VkTaskSearch $searchModel
 */

use common\models\VkTask;
use yii\grid\ActionColumn;
use yii\helpers\Html;

\yii\widgets\Pjax::begin();

echo \yii\grid\GridView::widget([
    'dataProvider'            => $dataProvider,
    'id'                      => 'vkTaskGrid',
    'filterModel'             => $searchModel,
    'columns'                 => [
        [
            'attribute' => 'type',
            'value' => function(VkTask $vkTask) {
                return $vkTask->getTypeLabel();
            }
        ],
        [
            'attribute' => 'group_id',
        ],
        [
            'attribute' => 'period',
            'value' => function(VkTask $vkTask) {
                return $vkTask->getPeriodLabel();
            }
        ],
        [
            'attribute' => 'startEnd',
            'label' => 'Время',
            'value' => function(VkTask $vkTask) {
                $start = intval($vkTask->time_start / 3600) . ':' . str_pad(intval($vkTask->time_start /60 % 60), 2, '0', STR_PAD_LEFT);
                $end = intval($vkTask->time_end / 3600) . ':' . str_pad(intval($vkTask->time_end /60 % 60), 2, '0', STR_PAD_LEFT);

                return "с {$start} до {$end}";
            }
        ],
        [
            'class'    => ActionColumn::class,
            'header'   => 'Действия',
            'template' => '{update-task} {delete}',
            'buttons' => [
                'update-task' => function ($url, $model, $key) {
                    $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                    $title = 'Изменить';
                    return Html::a($icon, $url, [
                        'title' => $title,
                        'aria-label' => $title,
                        'data-pjax' => '0',
                    ]);
                },
            ],
        ],
    ]
]);

\yii\widgets\Pjax::end();