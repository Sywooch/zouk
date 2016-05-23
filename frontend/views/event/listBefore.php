<?php
/**
 * @var yii\web\View $this
 */

use frontend\models\Lang;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Lang::t('main/index', 'title');

echo $this->render('/event/tabs', ['selectTab' => 3]);
?>