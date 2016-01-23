<?php

/* @var $this yii\web\View */

use frontend\models\Lang;

$this->title = Lang::t('main/index', 'title');
?>
<br/>
<div class="">
    <ul class="nav nav-tabs">
        <li class="navbar-right"><a href="#"><?= Lang::t('main', 'listTabMonth') ?></a></li>
        <li class="navbar-right"><a href="#"><?= Lang::t('main', 'listTabWeek') ?></a></li>
        <li class="active navbar-right"><a href="#"><?= Lang::t('main', 'listTabCurrent') ?></a></li>
    </ul>
</div>
<div class="site-index">
    <div class="body-content">

        <div class="row">

        </div>

    </div>
</div>
