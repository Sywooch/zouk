<?php

/* @var $this yii\web\View */

use frontend\models\Lang;
use yii\helpers\Html;

$this->title = Lang::t('about/index', 'title');

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>
</div>
