<?php
/**
 * @var \common\models\Item[] $items
 */
?>
<div>
    <?php
    foreach ($items as $item) {
        echo $this->render('view', ['item' => $item]);
    }
    ?>
</div>
